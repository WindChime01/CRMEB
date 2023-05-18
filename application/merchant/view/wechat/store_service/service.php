{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label i {
        display: inline-block;
        width: 18px;
        height: 18px;
        font-size: 18px;
        color: #fff;
    }

    .layui-form-item .label-box p {
        line-height: inherit;
    }

    .m-t-5 {
        margin-top: 5px;
    }

    #app .layui-barrage-box {
        margin-bottom: 10px;
        margin-top: 10px;
        margin-left: 10px;
        border: 1px solid #0092DC;
        border-radius: 5px;
        cursor: pointer;
        position: relative;
    }

    #app .layui-barrage-box.border-color {
        border-color: #0bb20c;
    }

    #app .layui-barrage-box .del-text {
        position: absolute;
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.5);
        color: #ffffff;
        width: 92%;
        text-align: center;
    }

    #app .layui-barrage-box p {
        padding: 5px 5px;
    }

    #app .layui-empty-text {
        text-align: center;
        font-size: 18px;
    }

    #app .layui-empty-text p {
        padding: 10px 10px;
    }

    .layui-checkbox-disbaled span,
    .layui-form-checkbox span {
        background-color: #0092DC !important;
    }

    .layui-form-checkbox i {
        box-sizing: content-box;
    }

    .layui-form-checked i,
    .layui-form-checked:hover i {
        color: #0092DC;
    }

    .layui-form-checked.layui-checkbox-disbaled:hover i {
        color: #0092DC !important;
    }
</style>
{/block}
{block name="content"}
<div v-cloak class="layui-fluid" id="app">
    <div class="layui-card">
        <div class="layui-card-header">客服设置</div>
        <div class="layui-card-body">
            <form action="" class="layui-form" lay-filter="form">
                <div class="layui-form-item">
                    <label class="layui-form-label">客服配置：</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="is_phone_service[1]" title="微信客服" disabled lay-filter="is_phone_service">
                        <input type="checkbox" name="is_phone_service[2]" title="CRMChat客服" disabled lay-filter="is_phone_service">
                        <input type="checkbox" name="is_phone_service[3]" title="拨打电话" lay-filter="is_phone_service">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">客服电话：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="service_phone" v-model="formData.service_phone" required lay-verify="required|phone" placeholder="请输入电话" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" type="button" @click="save">立即提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/request.js"></script>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var merchat =<?= isset($merchat) ? $merchat : [] ?>;
    var configuration = {$configuration};
    require(['vue'], function (Vue) {
        new Vue({
            el: "#app",
            data: {
                formData: {
                    is_phone_service: merchat.is_phone_service || 0,
                    service_phone: merchat.service_phone || '',
                },
                id: merchat.id,
            },
            methods: {
                save: function () {
                    var that = this;
                    if (that.formData.is_phone_service == 1) {
                        if (!that.formData.service_phone) return layList.msg('请输入客服电话');
                    }
                    layList.loadFFF();
                    layList.basePost(layList.U({a: 'save_phone', q: {id: that.id}}), that.formData, function (res) {
                        layList.loadClear();
                        layList.msg('修改成功', function () {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                        })
                    }, function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                }
            },
            mounted: function () {
                this.$nextTick(function () {
                    var vm = this;
                    var formData = {};
                    var form = layui.form;
                    form.render();
                    if (merchat.is_phone_service) {
                        formData['is_phone_service[3]'] = true;
                    } else {
                        formData['is_phone_service[' + configuration + ']'] = true;
                    }
                    form.val('form', formData);

                    form.on('checkbox(is_phone_service)', function (data) {
                        vm.formData.is_phone_service = Number(data.elem.checked);
                        if (configuration !== 3) {
                            formData['is_phone_service[' + configuration + ']'] = !data.elem.checked;
                            form.val('form', formData);
                        }
                    });
                });
            }
        });
    });
</script>
{/block}