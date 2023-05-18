{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label i{display: inline-block;width: 18px;height: 18px;font-size: 18px;color: #fff;}
    .layui-form-item .label-box p{line-height: inherit;}
    .m-t-5{margin-top:5px;}
    #app .layui-barrage-box{margin-bottom: 10px;margin-top: 10px;margin-left: 10px;border: 1px solid #0092DC;border-radius: 5px;cursor: pointer;position: relative;}
    #app .layui-barrage-box.border-color{border-color: #0bb20c;}
    #app .layui-barrage-box .del-text{position: absolute;top: 0;left: 0;background-color: rgba(0,0,0,0.5);color: #ffffff;width: 92%;text-align: center;}
    #app .layui-barrage-box p{padding:5px 5px; }
    #app .layui-empty-text{text-align: center;font-size: 18px;}
    #app .layui-empty-text p{padding: 10px 10px;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app" v-cloak>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form action="" class="layui-form">
                        <div class="layui-form-item">
                        <div class="layui-form-item">
                            <label class="layui-form-label required">分类名称：</label>
                            <div class="layui-input-block">
                                <input type="text" name="cate_name" v-model.trim="formData.cate_name" autocomplete="off" placeholder="请输入分类名称" maxlength="6" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item" v-show="show">
                            <label class="layui-form-label">图标：</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.pic" >
                                    <img :src="formData.pic" alt="">
                                    <div class="mask"  style="display: block">
                                        <p>
                                            <i class="fa fa-eye" @click="look(formData.pic)"></i>
                                            <i class="fa fa-trash-o" @click="delect('pic')"></i>
                                        </p>
                                    </div>
                                </div>
                                <div class="upload-image" v-show="!formData.pic" @click="upload('pic')">
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>选择图片</p>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序：</label>
                            <div class="layui-input-block">
                                <input type="number" name="sort" v-model="formData.sort" min="0" max="9999" class="layui-input" v-sort>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">状态：</label>
                            <div class="layui-input-block">
                                <input type="radio" name="is_show" value="1" title="显示" v-model="formData.is_show" lay-filter="is_show" >
                                <input type="radio" name="is_show" value="0" title="隐藏" v-model="formData.is_show" lay-filter="is_show">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <div class="layui-btn-container">
                                    <button class="layui-btn layui-btn-normal" type="button" @click="save">{{id ? '立即修改':'立即提交'}}</button>
                                    <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var id={$id},cate=<?=isset($cate) ? $cate : []?>;
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            directives: {
                sort: {
                    bind: function (el, binding, vnode) {
                        var vm = vnode.context;
                        el.addEventListener('change', function () {
                            if (!this.value || this.value < 0) {
                                vm.formData.sort = 0;
                            } else if (this.value > 9999) {
                                vm.formData.sort = 9999;
                            } else {
                                vm.formData.sort = parseInt(this.value);
                            }
                        });
                    }
                }
            },
            data: {
                cateList:[],
                formData:{
                    cate_name:cate.cate_name || '',
                    pic: cate.pic || '',
                    pid:cate.pid || 0,
                    sort:cate.sort >0 ? cate.sort : 0,
                    is_show:cate.is_show || 0
                },
                show:cate.pid >0 ? true : false
            },
            methods:{
                //查看图片
                look: function (pic) {
                    parent.$eb.openImage(pic);
                },
                //上传图片
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                clone_form: function () {
                    if (parseInt(id) == 0) {
                        parent.layer.closeAll();
                    }
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                },
                save:function () {
                    var that=this;
                    if(!that.formData.cate_name) return layList.msg('请输入分类名称');
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save',q:{id:id}}),that.formData,function (res) {
                        layList.loadClear();
                        if(parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加分类吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function (index) {
                                layList.layer.close(index);
                            }, function () {
                                parent.layer.closeAll();
                            });
                        }else{
                            layList.msg('修改成功',function () {
                                parent.layer.closeAll();
                            })
                        }
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                },
                delect:function(key){
                    var that=this;
                    that.formData[key]='';
                },
                changeIMG: function (key, value, multiple) {
                    if (multiple) {
                        var that = this;
                        value.map(function (v) {
                            that.formData[key].push({pic: v, is_show: false});
                        });
                        this.$set(this.formData, key, this.formData[key]);
                    } else {
                        this.$set(this.formData, key, value);
                    }
                },
            },
            mounted:function () {
                var that=this;
                this.$nextTick(function () {
                    layList.form.render();

                });
                window.changeIMG = that.changeIMG;
                layList.form.on('radio(is_show)',function (data) {
                    that.formData.is_show=data.value;
                });
            }
        })
    })
</script>
{/block}
