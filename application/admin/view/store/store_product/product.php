{extend name="public/container" /}
{block name="head"}
<style>
    .layui-form-item img~img {
        margin-left: 10px;
    }

    .layui-form-radioed.layui-radio-disbaled>i {
        color: #0092DC !important;
    }

    .layui-disabled,
    .layui-disabled:hover {
        color: #333 !important;
        cursor: auto !important;
    }

    .layui-form-radioed.layui-disabled,
    .layui-form-radioed.layui-disabled:hover {
        color: #0092DC !important;
    }

    .rich-text {
        min-height: 100px;
        padding: 4px 7px;
        border: 1px solid #dddee1;
    }
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-form">
                <div class="layui-tab layui-tab-brief">
                    <ul class="layui-tab-title">
                        <li v-for="(item, index) in tabTitle" :key="item.value" :class="{ 'layui-this': !index }">{{ item.title }}</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品名称：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.store_name" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品简介：</label>
                                <div class="layui-input-block">
                                    <textarea disabled class="layui-textarea">{{ detail.store_info }}</textarea>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">关键字：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.keyword" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品单位：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.unit_name" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品封面：</label>
                                <div class="layui-input-block">
                                    <img width="60" height="60" :src="detail.image" @click="look(detail.image)">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品Banner：</label>
                                <div class="layui-input-block">
                                    <img v-for="item in banner" width="60" height="60" :src="item" @click="look(item)">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品售价：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.price" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品划线价：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.ot_price" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品成本价：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.cost" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">排序：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.sort" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">会员免费：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="member_pay_type" value="0" title="是" :checked="detail.member_pay_type == 0" disabled>
                                    <input type="radio" name="member_pay_type" value="1" title="否" :checked="detail.member_pay_type == 1" disabled>
                                </div>
                            </div>
                            <div v-if="detail.member_pay_type" class="layui-form-item">
                                <label class="layui-form-label">会员价：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.vip_price" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">包邮：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_postage" value="1" title="是" :checked="detail.is_postage == 1" disabled>
                                    <input type="radio" name="is_postage" value="0" title="否" :checked="detail.is_postage == 0" disabled>
                                </div>
                            </div>
                            <div v-if="!detail.is_postage" class="layui-form-item">
                                <label class="layui-form-label">商品邮费：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.postage" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="!detail.is_postage" class="layui-form-item">
                                <label class="layui-form-label">满减包邮：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.free_shipping" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">虚拟销量：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.ficti" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品库存：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.stock" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">状态：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_show" value="1" title="上架" :checked="detail.is_show == 1" disabled>
                                    <input type="radio" name="is_show" value="0" title="下架" :checked="detail.is_show == 0" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="rich-text" v-html="detail.description"></div>
                        </div>
                    </div>
                </div>
            </div>
            <form class="layui-form" lay-filter="form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">审核状态：</label>
                    <div class="layui-form-block">
                        <input type="radio" name="status" value="1" title="通过" lay-filter="status">
                        <input type="radio" name="status" value="-1" title="拒绝" lay-filter="status">
                    </div>
                </div>
                <div v-if="status === -1" class="layui-form-item">
                    <label class="layui-form-label">拒绝原因：</label>
                    <div class="layui-input-block">
                        <textarea name="fail_message" required lay-verify="required" placeholder="请输入拒绝原因" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-normal" lay-submit lay-filter="*">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}
{block name="foot"}
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    require(['vue'], function (Vue) {
        var detail = {$details},
            form = layui.form,
            layer = layui.layer,
            parentLayer = parent.layui.layer;
        new Vue({
            el: '#app',
            data: {
                detail: detail,
                tabTitle: [
                    {
                        title: '商品信息',
                        value: 1
                    },
                    {
                        title: '商品详情',
                        value: 2
                    }
                ],
                status: 1
            },
            computed: {
                banner: function () {
                    if (typeof this.detail.slider_image === 'string') {
                        return JSON.parse(this.detail.slider_image);
                    }
                    return this.detail.slider_image;
                }
            },
            mounted: function () {
                this.$nextTick(function () {
                    var vm = this;
                    form.val('form', {
                        status: this.status
                    });
                    form.render();
                    form.on('radio(status)', function (data) {
                        vm.status = Number(data.value);
                    });
                    form.on('submit(*)', function (data) {
                        vm.status === 1 ? vm.success() : vm.fail(data.field.fail_message);
                        return false;
                    });
                });
            },
            methods: {
                // 审核通过
                success: function () {
                    layList.baseGet(layList.U({
                        a: 'succ',
                        q: {
                            id: this.detail.id
                        }
                    }), function (res) {
                        layer.msg(res.msg, {
                            icon: 1,
                            time: 2000
                        }, function () {
                            parentLayer.close(parentLayer.getFrameIndex(window.name));
                        });
                    });
                },
                // 审核拒绝
                fail: function (message) {
                    layList.basePost(layList.U({
                        a: 'fail',
                        q: {
                            id: this.detail.id
                        }
                    }), {
                        message: message
                    }, function (res) {
                        layer.msg(res.msg, {
                            icon: 1,
                            time: 2000
                        }, function () {
                            parentLayer.close(parentLayer.getFrameIndex(window.name));
                        });
                    });
                },
                look: function () {
                    var data = [];
                    if (arguments.length === 1) {
                        data.push({
                            src: arguments[0]
                        });
                    } else {
                        arguments[0].forEach(function (item) {
                            data.push({
                                src: item.pic
                            });
                        });
                    }
                    layer.photos({
                        photos: {
                            start: arguments[1] || 0,
                            data: data
                        },
                        anim: 5
                    });
                }
            }
        });
    });
</script>
{/block}