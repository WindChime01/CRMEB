{extend name="public/container" /}
{block name="head"}
<style>
    .layui-col-md12 img {
        max-width: 100%;
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

    .layui-form-label {
        width: 130px;
    }

    .layui-input-block {
        margin-left: 130px;
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
                                <label class="layui-form-label">资料名称：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.title" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">资料排序：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.sort" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">商品封面：</label>
                                <div class="layui-input-block">
                                    <img width="60" height="60" :src="detail.image" @click="look(detail.image)">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">推广海报：</label>
                                <div class="layui-input-block">
                                    <img width="60" height="60" :src="detail.poster_image" @click="look(detail.poster_image)">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">上传方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="type" value="0" title="全部" :checked="detail.type == 0" disabled>
                                    <input type="radio" name="type" value="1" title="OSS上传" :checked="detail.type == 1" disabled>
                                    <input type="radio" name="type" value="2" title="百度网盘" :checked="detail.type == 2" disabled>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">百度网盘链接：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.network_disk_link" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">百度网盘提取码：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.network_disk_pwd" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">付费方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="pay_type" value="0" title="免费" :checked="detail.pay_type == 0" disabled>
                                    <input type="radio" name="pay_type" value="1" title="付费" :checked="detail.pay_type == 1" disabled>
                                </div>
                            </div>
                            <div v-if="detail.pay_type" class="layui-form-item">
                                <label class="layui-form-label">购买金额：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.money" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">会员付费方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="member_pay_type" value="0" title="免费" :checked="detail.member_pay_type == 0" disabled>
                                    <input type="radio" name="member_pay_type" value="1" title="付费" :checked="detail.member_pay_type == 1" disabled>
                                </div>
                            </div>
                            <div v-if="detail.member_pay_type" class="layui-form-item">
                                <label class="layui-form-label">会员购买金额：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.member_money" disabled type="text" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="rich-text" v-html="detail.abstract"></div>
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
        var form = layui.form,
            layer = layui.layer,
            parentLayer = parent.layui.layer,
            detail = {$details};
        new Vue({
            el: '#app',
            data: {
                detail: detail,
                tabTitle: [
                    {
                        title: '资料信息',
                        value: 1
                    },
                    {
                        title: '资料详情',
                        value: 2
                    }
                ],
                status: 1
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
                        p: {
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
                        p: {
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