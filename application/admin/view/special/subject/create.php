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
<div v-cloak class="layui-fluid" id="app">
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="" class="layui-form" lay-filter="form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label required">分类名称：</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" v-model.trim="formData.name" autocomplete="off" placeholder="最多6个字" class="layui-input" maxlength="6">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">顶级分类：</label>
                        <div class="layui-input-inline">
                            <select name="grade_id" lay-search="" lay-filter="grade_id" v-model="formData.grade_id">
                                <option v-for="item in cateList" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item submit" v-show="show">
                    <label class="layui-form-label required">图标：（100*100）</label>
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
                    <div class="layui-inline">
                        <label class="layui-form-label">排序：</label>
                        <div class="layui-input-inline">
                            <input type="number" name="sort" v-model="formData.sort" autocomplete="off" class="layui-input" v-sort>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">状态：</label>
                        <div class="layui-input-inline">
                            <input type="checkbox" name="is_show" lay-skin="switch" lay-text="显示|隐藏" lay-filter="is_show" :checked="formData.is_show">
                        </div>
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
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
<script type="text/javascript" src="{__MODULE_PATH}widget/OssUpload.js"></script>
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
                cateList: [],
                formData:{
                    name:cate.name || '',
                    pic: cate.pic || '',
                    grade_id:cate.grade_id || 0,
                    sort: Array.isArray(cate) ? 0 : cate.sort,
                    is_show: Array.isArray(cate) ? 1 : cate.is_show
                },
                show:cate.grade_id >0 ? true : false,
                id: id,
            },
            watch: {
                cateList: function () {
                    this.$nextTick(function () {
                        layui.form.render();
                    });
                }
            },
            methods:{
                //查看图片
                look: function (pic) {
                    $eb.openImage(pic);
                },
                //上传图片
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                clone_form: function (id) {
                    var that = this;
                    if (parseInt(id) == 0) {
                        parent.layer.closeAll();
                    }
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引

                    parent.layer.close(index); //再执行关闭
                },
                //获取分类
                get_subject_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'get_cate_list'}), function (res) {
                        that.cateList = res.data;
                    });
                },
                save:function () {
                    var that=this;
                    if(!that.formData.name) return layList.msg('请输入分类名称');
                    if(that.formData.grade_id>0){
                        if(!that.formData.pic) return layList.msg('请输入分类图标');
                    }
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
                                var index = parent.layer.getFrameIndex(window.name);
                                parent.layer.close(index);

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
                }
            },
            created: function () {
                this.get_subject_list();
                window.changeIMG = this.changeIMG;
            },
            mounted:function () {
                var that = this;
                this.$nextTick(function () {
                    layList.form.render();
                    layList.form.on('switch(is_show)', function (data) {
                        that.formData.is_show = Number(data.elem.checked);
                    });
                    layList.form.on('select(grade_id)', function (data) {
                        if (id && id === Number(data.value)) {
                            layList.layer.msg('上级分类不能是自己', function () {
                                window.location.reload();
                            });
                        } else {
                            that.formData.grade_id = data.value;
                            that.show = data.value !== '0';
                        }
                    });
                });
            }
        });
    });
</script>
{/block}
