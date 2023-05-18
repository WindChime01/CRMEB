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
<div class="layui-fluid" style="background: #fff">
    <div class="layui-row layui-col-space15" id="app" v-cloak>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body" style="padding: 10px;">
                    <form action="" class="layui-form">
                        <div class="layui-form-item m-t-5">
                            <div class="layui-form-item" >
                                <label class="layui-form-label">选择：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="type" value="0" title="选择会员" v-model="formData.type" lay-filter="type">
                                    <input type="radio" name="type" value="1" title="自定义赠送会员" v-model="formData.type" lay-filter="type" >
                                </div>
                            </div>
                         <div class="layui-form-item submit" v-show="formData.type==1">
                            <label class="layui-form-label">会员有效期：</label>
                            <div class="layui-input-block">
                                <input type="number" name="day" min="0" style="width: 50%" v-model.number="formData.day" autocomplete="off"  class="layui-input">
                         </div>
                        </div>
                        <div class="layui-form-item" v-show="formData.type==0">
                            <label class="layui-form-label">选择会员：</label>
                            <div class="layui-input-block">
                                <select name="member" v-model="formData.member" lay-search="" lay-filter="member">
                                    <option value="0">选择会员</option>
                                    <option  :value="item.id"  v-for="item in member_list">{{item.title}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" type="button" @click="save">确定</button>
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
    var uid={$uid};
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
                member_list:[],
                formData:{
                    uid:uid,
                    type:0,
                    day:0,
                    member:0
                }
            },
            watch: {
                'formData.day': function (v) {
                    this.$nextTick(function () {
                        layList.form.render();
                    });
                }
            },
            methods:{
                save:function () {
                    var that=this;
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'gift_members'}),that.formData,function (res) {
                        layList.loadClear();
                        layList.msg('赠送成功',function () {
                            parent.layer.closeAll();
                            location.reload();
                        })
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                },
                get_member_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'get_member_list'}), function (res) {
                        that.$set(that, 'member_list', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
            },
            mounted:function () {
                var that=this;
                that.get_member_list();
                this.$nextTick(function () {
                    layList.form.render();
                    layList.select('member', function (obj) {
                        that.formData.member = obj.value;
                    });
                    layList.form.on('radio(type)',function (data) {
                        that.formData.type=data.value;
                    });
                });

            }
        })
    })
</script>
{/block}
