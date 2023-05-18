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
    .layui-form-item .label-box p {
        line-height: inherit;
    }
    .upload-image-box .mask p i:first-child {
        padding-right: 0;
    }
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">会员名：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="title" v-model.trim="formData.title" autocomplete="off" maxlength="10" placeholder="请输入昵称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">图标：（50*45）</label>
                    <div class="layui-input-block">
                        <div class="upload-image-box" v-if="formData.img">
                            <img :src="formData.img" alt="">
                            <div class="mask">
                                <p>
                                    <i class="fa fa-eye" @click="look(formData.img)"></i>
                                    <i class="fa fa-trash-o" @click="delect('img')"></i>
                                </p>
                            </div>
                        </div>
                        <div class="upload-image" v-show="!formData.img" @click="upload('img')">
                            <div class="fiexd"><i class="fa fa-plus"></i></div>
                            <p>选择图片</p>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item" v-show="!free || formData.free_day<0">
                    <label class="layui-form-label">有效时间：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="vip_day" value="30" title="月" v-model="formData.vip_day" lay-filter="vip_day"  >
                        <input type="radio" name="vip_day" value="90" title="季" v-model="formData.vip_day" lay-filter="vip_day">
                        <input type="radio" name="vip_day" value="365" title="年" v-model="formData.vip_day" lay-filter="vip_day" >
                        <input type="radio" name="vip_day" value="-1" title="永久" v-model="formData.vip_day" lay-filter="vip_day">
                    </div>
                </div>
                <div class="layui-form-item" v-show="!free || formData.free_day<0">
                    <label class="layui-form-label">会员原价：</label>
                    <div class="layui-input-inline">
                        <input type="number" name="original_price" min="0" v-model.number="formData.original_price" autocomplete="off"  class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item" v-show="!free || formData.free_day<0">
                    <label class="layui-form-label">优惠后价格：</label>
                    <div class="layui-input-inline">
                        <input type="number" name="price" min="0" v-model.number="formData.price" autocomplete="off"  class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">排序：</label>
                    <div class="layui-input-inline">
                        <input type="number" name="sort" min="0" v-model="formData.sort" autocomplete="off" class="layui-input" v-sort>
                    </div>
                </div>
                <div class="layui-form-item" >
                    <label class="layui-form-label">免费：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="is_free" value="1" title="是" v-model="formData.is_free" lay-filter="is_free" >
                        <input type="radio" name="is_free" value="0" title="否" v-model="formData.is_free" lay-filter="is_free">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">单独分销：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="is_alone" lay-filter="is_alone" v-model="formData.is_alone" :disabled="formData.is_free == 1" value="1" title="开启">
                        <input type="radio" name="is_alone" lay-filter="is_alone" v-model="formData.is_alone" :disabled="formData.is_free == 1" value="0" title="关闭">
                    </div>
                </div>
                <div class="layui-form-item" v-show="formData.is_alone == 1">
                    <label class="layui-form-label">一级返佣比例[5%=5]：</label>
                    <div class="layui-input-inline">
                        <input type="number" name="brokerage_ratio" lay-verify="number" v-model="formData.brokerage_ratio" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item" v-show="formData.is_alone == 1">
                    <label class="layui-form-label">二级返佣比例[5%=5]：</label>
                    <div class="layui-input-inline">
                        <input type="number" name="brokerage_two" lay-verify="number" v-model="formData.brokerage_two" autocomplete="off" class="layui-input" min="0">
                    </div>
                </div>
                <div class="layui-form-item" v-show="free || formData.free_day>0">
                    <label class="layui-form-label">免费使用时间：</label>
                    <div class="layui-input-inline">
                        <input type="number" name="free_day" v-model="formData.free_day" autocomplete="off"  class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">天</div>
                </div>
                <div class="layui-form-item" v-show="!free || formData.free_day<0">
                    <label class="layui-form-label">永久：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="is_permanent" value="1" title="是" v-model="formData.is_permanent" lay-filter="is_permanent" >
                        <input type="radio" name="is_permanent" value="0" title="否" v-model="formData.is_permanent" lay-filter="is_permanent">
                    </div>
                </div>
                    <div class="layui-form-item">
                    <label class="layui-form-label">发布：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="is_publish" value="1" title="是" v-model="formData.is_publish" lay-filter="is_publish">
                        <input type="radio" name="is_publish" value="0" title="否" v-model="formData.is_publish" lay-filter="is_publish">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-normal" @click="save">{{id ? '立即修改':'立即提交'}}</button>
                        <button type="button" class="layui-btn layui-btn-primary clone" @click="clone_form">取消</button>
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
    var id={$id},membership=<?=isset($membership) ? $membership : []?>;
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
                formData:{
                    title:membership.title || '',
                    img:membership.img || '',
                    vip_day:membership.is_permanent ? -1 : (membership.vip_day || 30),
                    free_day:membership.free_day || 0,
                    original_price:membership.original_price || 0,
                    price:membership.price || 0,
                    sort:membership.sorts || 0,
                    is_permanent:membership.is_permanent || 0,
                    is_publish:membership.is_publish || 0,
                    is_free:membership.is_free || 0,
                    is_alone:membership.is_free == 0 ? (membership.is_alone == 1 ? 1 : 0) : 0,
                    brokerage_ratio:membership.is_free == 0 ? (membership.brokerage_ratio || 0) : 0,
                    brokerage_two:membership.is_free == 0 ? (membership.brokerage_two || 0) : 0
                },
                free:membership.is_free ? true : false,
                mask: {
                    image: false
                },
            },
            watch: {
                'formData.vip_day': function (v) {
                    this.$nextTick(function () {
                        layList.form.render();
                    });
                }
            },
            methods:{
                //删除图片
                delect: function (key, index) {
                    var that = this;
                    if (index != undefined) {
                        that.formData[key].splice(index, 1);
                        that.$set(that.formData, key, that.formData[key]);
                    } else {
                        that.$set(that.formData, key, '');
                    }
                },
                //查看图片
                look: function (pic) {
                    $eb.openImage(pic);
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
                //上传图片
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                clone_form: function () {
                    var that = this;
                    if (parseInt(id) == 0) {
                        parent.layer.closeAll();
                    }
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引

                    parent.layer.close(index); //再执行关闭
                },
                save:function () {
                    var that=this;
                    if(!that.formData.title) return layList.msg('请输入会员名');
                    if(that.formData.vip_day<0 && that.formData.is_permanent==0) return layList.msg('会员有效时间有误');
                    if(that.formData.free_day<=0 && that.formData.is_free>0) return layList.msg('免费会员有效时间有误');
                    if(Number(that.formData.original_price)<0) return layList.msg('请输入会员原价');
                    if(Number(that.formData.price) <0) return layList.msg('请输入会员优惠后价格');
                    if (that.formData.price > that.formData.original_price) {
                        return layList.msg('优惠后价格需不大于会员原价');
                    }
                    if(that.formData.is_permanent>0 && that.formData.vip_day>0) return layList.msg('永久会员有效时间有误');
                    if (that.formData.is_alone == 1) {
                        if (!that.formData.brokerage_ratio || !that.formData.brokerage_two) return layList.msg('请填写推广人返佣比例');
                    }
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save_sytem_vip',q:{id:id}}),that.formData,function (res) {
                        layList.loadClear();
                        if(parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加会员设置吗?', {
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
                this.$nextTick(function () {
                    layList.form.render();
                    layList.form.on('radio(is_permanent)',function (data) {
                        that.formData.is_permanent=data.value;
                        if(data.value==1) {
                            that.formData.vip_day= -1;
                            that.formData.free_day=0;
                        }else{
                            that.formData.vip_day=30;
                        }
                    });
                    layList.form.on('radio(vip_day)',function (data) {
                        that.formData.vip_day=data.value;
                        that.formData.is_free=0;
                        that.free=false;
                    });
                    layList.form.on('radio(is_publish)',function (data) {
                        that.formData.is_publish=data.value;
                    });
                    layList.form.on('radio(is_free)',function (data) {
                        that.formData.is_free=data.value;
                        if(data.value==1) {
                            that.free=true;
                            that.formData.vip_day= 0;
                            that.formData.original_price= 0;
                            that.formData.price= 0;
                            that.formData.is_permanent= 0;
                            that.formData.is_alone = 0;
                            that.formData.brokerage_ratio = 0;
                            that.formData.brokerage_two = 0;
                        }else{
                            that.free=false;
                            that.formData.vip_day=30;
                            that.formData.free_day=0;
                            that.formData.is_permanent= 0;
                        }
                    });
                    layList.form.on('radio(is_alone)', function (data) {
                        that.formData.is_alone = parseInt(data.value);
                        if (that.formData.is_alone != 1) {
                            that.formData.brokerage_ratio = 0;
                            that.formData.brokerage_two = 0;
                        };
                        that.$nextTick(function () {
                            layList.form.render('radio');
                        });
                    });
                });

            }
        })
    })
</script>
{/block}
