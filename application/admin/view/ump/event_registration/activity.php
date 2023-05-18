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
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-form">
                <div class="layui-tab layui-tab-brief" lay-filter="tab">
                    <ul class="layui-tab-title">
                        <li v-for="(item, index) in tabTitle" :key="item.value" :class="{ 'layui-this': !index }">{{ item.title }}</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item">
                                <label class="layui-form-label">活动名称：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.title" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">报名时间：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.signup_start_time + '~' + detail.signup_end_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">活动时间：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.start_time + '~' + detail.end_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">活动人数：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.number" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">排序：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.sort" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">限购：</label>
                                <div class="layui-input-inline">
                                    <input :value="detail.restrictions" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">活动封面：</label>
                                <div class="layui-input-block">
                                    <img width="60" height="60" :src="detail.image" @click="look(detail.image)">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">二维码：</label>
                                <div class="layui-input-block">
                                    <img width="60" height="60" :src="detail.qrcode_img" @click="look(detail.qrcode_img)">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">活动地址：</label>
                                <div class="layui-input-block">
                                    <input :value="detail.province + detail.city + detail.district + detail.detail" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">活动状态：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_show" value="0" title="开启" :checked="detail.is_show == 0" disabled>
                                    <input type="radio" name="is_show" value="1" title="关闭" :checked="detail.is_show == 1" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="rich-text" v-html="detail.activity_rules"></div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="rich-text" v-html="detail.content"></div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">活动状态：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_fill" value="1" title="开启" :checked="detail.is_fill == 1" disabled>
                                    <input type="radio" name="is_fill" value="0" title="关闭" :checked="detail.is_fill == 0" disabled>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <table id="event"></table>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-row layui-col-space15">
                                <div class="layui-col-md12">
                                    <table id="price"></table>
                                </div>
                            </div>
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
<script type="text/html" id="type">
    {{# if (d.event_type === 1) { }}
    文本框
    {{# } else if (d.event_type === 2) { }}
    单选框
    {{# } else if (d.event_type === 3) { }}
    多选框
    {{# } else if (d.event_type === 4) { }}
    手机号
    {{# } else { }}
    下拉框
    {{# } }}
</script>
<script type="text/html" id="required">
    {{# if (d.is_required) { }}
    是
    {{# } else { }}
    否
    {{# } }}
</script>
<script type="text/html" id="value">
    {{# if (d.event_value) { }}
    {{d.event_value}}
    {{# } else { }}
    ——
    {{# } }}
</script>
{/block}
{block name="foot"}
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    require(['vue', 'moment'], function (Vue, moment) {
        var form = layui.form,
            table = layui.table,
            layer = layui.layer,
            element = layui.element,
            parentLayer = parent.layui.layer,
            detail = {$details},
            event = {$event},
            price = {$price};
        detail.signup_start_time = moment(detail.signup_start_time * 1000).format('YYYY-MM-DD HH:mm');
        detail.signup_end_time = moment(detail.signup_end_time * 1000).format('YYYY-MM-DD HH:mm');
        detail.start_time = moment(detail.start_time * 1000).format('YYYY-MM-DD HH:mm');
        detail.end_time = moment(detail.end_time * 1000).format('YYYY-MM-DD HH:mm');
        new Vue({
            el: '#app',
            data: {
                detail: detail,
                tabTitle: [
                    {
                        title: '基础信息',
                        value: 1
                    },
                    {
                        title: '规则信息',
                        value: 2
                    },
                    {
                        title: '详情信息',
                        value: 3
                    },
                    {
                        title: '资料信息',
                        value: 4
                    },
                    {
                        title: '价格信息',
                        value: 5
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
                    table.render({
                        elem: '#event',
                        cols: [[
                            {field: 'sort', title: '排序', align: 'center'},
                            {field: 'event_name', title: '标题', align: 'center'},
                            {field: 'event_type', title: '类型', align: 'center', templet: '#type'},
                            {field: 'is_required', title: '必填', align: 'center', templet: '#required'},
                            {field: 'event_value', title: '选项', align: 'center', templet: '#value'}
                        ]],
                        data: event
                    });
                    table.render({
                        elem: '#price',
                        cols: [[
                            {field: 'sort', title: '排序', align: 'center'},
                            {field: 'event_number', title: '人数', align: 'center'},
                            {field: 'event_price', title: '价格', align: 'center'},
                            {field: 'event_mer_price', title: '会员价格', align: 'center'}
                        ]],
                        data: price
                    });
                    element.on('tab(tab)', function (data) {
                        if (data.index === 3) {
                            table.resize('event');
                        } else if (data.index === 4) {
                            table.resize('price');
                        }
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