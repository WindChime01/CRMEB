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
                                <div class="layui-inline">
                                    <label class="layui-form-label required">分类名称：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="title" v-model.trim="formData.title" autocomplete="off" placeholder="最多6个字" class="layui-input" maxlength="6">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <label class="layui-form-label">顶级分类：</label>
                                    <div class="layui-input-inline">
                                        <select name="pid" v-model="formData.pid" lay-search="" lay-filter="pid">
                                                <option v-for="item in cateList"  :value="item.id">{{item.title}}</option>
                                        </select>
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
                                        <input type="radio" name="is_show" value="1" title="显示" v-model="formData.is_show" lay-filter="is_show" >
                                        <input type="radio" name="is_show" value="0" title="隐藏" v-model="formData.is_show" lay-filter="is_show">
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
                    title:cate.title || '',
                    pid:Number(cate.pid) || 0,
                    sort:Number(cate.sort) || 0,
                    is_show:cate.is_show || 1
                }
            },
            watch: {
                'formData.pid': function (v) {
                    this.$nextTick(function () {
                        layList.form.render();
                    });
                }
            },
            methods:{
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
                        that.$set(that, 'cateList', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                save:function () {
                    var that=this;
                    if(!that.formData.title) return layList.msg('请输入分类名称');
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
                }
            },
            mounted:function () {
                var that=this;
                window.changeIMG = that.changeIMG;
                that.get_subject_list();
                this.$nextTick(function () {
                    layList.form.render();
                    layList.select('pid', function (obj) {
                        if(id==obj.value && id>0){
                            layList.msg('上级分类不能是自己',function () {
                                location.reload();
                            });
                        }else{
                            that.formData.pid = obj.value;
                        }
                    });
                    layList.form.on('radio(is_show)',function (data) {
                        that.formData.is_show=data.value;
                    });
                });

            }
        })
    })
</script>
{/block}
