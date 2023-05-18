{extend name="public/container" /}
{block name="head_top"}
<link rel="stylesheet" href="//g.alicdn.com/de/prismplayer/2.9.21/skins/default/aliplayer-min.css">
<script src="//g.alicdn.com/de/prismplayer/2.9.21/aliplayer-min.js"></script>
<style>
    .xl-chrome-ext-bar {
        display: none;
    }

    .prism-player .prism-cc-btn,
    .prism-player .prism-setting-list .prism-setting-cc,
    .prism-player .prism-setting-list .prism-setting-audio,
    .prism-player .prism-setting-list .prism-setting-quality {
        display: none;
    }

    .layui-col-md12 img {
        max-width: 100%;
        max-height: 500px;
        vertical-align: top;
    }

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
                <div class="layui-tab layui-tab-brief" lay-filter="tab">
                    <ul class="layui-tab-title">
                        <li v-for="(item, index) in tabTitle" :key="item.value" :class="{ 'layui-this': !index }">{{ item.title }}</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题名称：</label>
                                <div class="layui-input-block">
                                    <input :value="special.title" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题简介：</label>
                                <div class="layui-input-block">
                                    <textarea disabled class="layui-textarea">{{ special.abstract }}</textarea>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题类型：</label>
                                <div class="layui-input-block">
                                    <input :value="type" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题标签：</label>
                                <div class="layui-input-block">
                                    <button v-for="(item, index) in special.label" :key="index" type="button" class="layui-btn layui-btn-normal layui-btn-sm">{{item}}</button>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题排序：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.sort" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题封面：</label>
                                <div class="layui-input-block">
                                    <img width="60" height="60" :src="special.image" @click="look(special.image)">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">推广海报：</label>
                                <div class="layui-input-block">
                                    <img width="60" height="60" :src="special.poster_image" @click="look(special.poster_image)">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">客服二维码：</label>
                                <div class="layui-input-block">
                                    <img width="60" height="60" :src="special.service_code" @click="look(special.service_code)">
                                </div>
                            </div>
                            <div v-if="!special.is_light" class="layui-form-item">
                                <label class="layui-form-label">专题Banner：</label>
                                <div class="layui-input-block">
                                    <img v-for="(item, index) in special.banner" width="60" height="60" :src="item.pic" @click="look(special.banner, index)">
                                </div>
                            </div>
                            <div v-if="special.type === 4" class="layui-form-item">
                                <label class="layui-form-label">直播时间：</label>
                                <div class="layui-input-inline">
                                    <input :value="liveInfo.start_play_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.type === 4" class="layui-form-item">
                                <label class="layui-form-label">直播时长：</label>
                                <div class="layui-input-inline">
                                    <input :value="liveInfo.start_play_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.type === 4" class="layui-form-item">
                                <label class="layui-form-label">开播提醒：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_remind" disabled value="1" title="是" :checked="liveInfo.is_remind == 1">
                                    <input type="radio" name="is_remind" disabled value="0" title="否" :checked="liveInfo.is_remind == 0">
                                </div>
                            </div>
                            <div v-if="special.type === 4 && liveInfo.is_remind" class="layui-form-item">
                                <label class="layui-form-label">提醒时间：</label>
                                <div class="layui-input-inline">
                                    <input :value="liveInfo.remind_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.type === 4" class="layui-form-item">
                                <label class="layui-form-label">直播录制：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_recording" disabled value="1" title="是" :checked="liveInfo.is_recording == 1">
                                    <input type="radio" name="is_recording" disabled value="0" title="否" :checked="liveInfo.is_recording == 0">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">仅会员可见：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_mer_visible" disabled value="1" title="是" :checked="special.is_mer_visible == 1">
                                    <input type="radio" name="is_mer_visible" disabled value="0" title="否" :checked="special.is_mer_visible == 0">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">有效期：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.validity" disabled type="text" class="layui-input">
                                </div>
                                <div class="layui-form-mid layui-word-aux">天</div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">付费方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="pay_type" disabled value="0" title="免费" :checked="special.pay_type == 0">
                                    <input type="radio" name="pay_type" disabled value="1" title="付费" :checked="special.pay_type == 1">
                                    <input type="radio" name="pay_type" disabled value="2" title="加密" :checked="special.pay_type == 2">
                                </div>
                            </div>
                            <div v-if="special.pay_type" class="layui-form-item">
                                <label class="layui-form-label">购买金额：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.money" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">会员付费方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="member_pay_type" disabled value="0" title="免费" :checked="special.member_pay_type == 0">
                                    <input type="radio" name="member_pay_type" disabled value="1" title="付费" :checked="special.member_pay_type == 1">
                                </div>
                            </div>
                            <div v-if="special.member_pay_type" class="layui-form-item">
                                <label class="layui-form-label">会员购买金额：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.member_money" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.pay_type" class="layui-form-item">
                                <label class="layui-form-label">单独分销：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_alone" disabled value="1" title="开启" :checked="special.is_alone == 1">
                                    <input type="radio" name="is_alone" disabled value="0" title="关闭" :checked="special.is_alone == 0">
                                </div>
                            </div>
                            <div v-if="special.pay_type && special.is_alone" class="layui-form-item">
                                <label class="layui-form-label">一级返佣比例：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.brokerage_ratio" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.pay_type && special.is_alone" class="layui-form-item">
                                <label class="layui-form-label">二级返佣比例：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.brokerage_two" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.pay_type" class="layui-form-item">
                                <label class="layui-form-label">拼团状态：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_pink" disabled value="1" title="开启" :checked="special.is_pink == 1">
                                    <input type="radio" name="is_pink" disabled value="0" title="关闭" :checked="special.is_pink == 0">
                                </div>
                            </div>
                            <div v-if="special.pay_type && special.is_pink" class="layui-form-item">
                                <label class="layui-form-label">拼团金额：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.pink_money" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.pay_type && special.is_pink" class="layui-form-item">
                                <label class="layui-form-label">拼团人数：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.pink_number" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.is_pink" class="layui-form-item">
                                <label class="layui-form-label">拼团开始时间：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.pink_strar_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.is_pink" class="layui-form-item">
                                <label class="layui-form-label">拼团结束时间：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.pink_end_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.is_pink" class="layui-form-item">
                                <label class="layui-form-label">拼团时间：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.pink_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.is_pink" class="layui-form-item">
                                <label class="layui-form-label">模拟成团：</label>
                                <div class="layui-input-inline">
                                    <input type="radio" name="is_fake_pink" disabled value="1" title="开启" :checked="special.is_fake_pink == 1">
                                    <input type="radio" name="is_fake_pink" disabled value="0" title="关闭" :checked="special.is_fake_pink == 0">
                                </div>
                            </div>
                            <div v-if="special.is_pink" class="layui-form-item">
                                <label class="layui-form-label">补齐比例：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.fake_pink_number" disabled type="text" class="layui-input">
                                </div>
                            </div>
                            <div v-if="special.is_light" class="layui-form-item">
                                <label class="layui-form-label">试看：</label>
                                <div class="layui-input-inline">
                                    <input type="radio" name="is_try" disabled value="1" title="开启" :checked="special.singleProfile.is_try == 1">
                                    <input type="radio" name="is_try" disabled value="0" title="关闭" :checked="special.singleProfile.is_try == 0">
                                </div>
                            </div>
                            <div v-if="special.is_light && special.light_type !== 1 && special.singleProfile.is_try" class="layui-form-item">
                                <label class="layui-form-label">试看时长：</label>
                                <div class="layui-input-inline">
                                    <input :value="special.singleProfile.try_time" disabled type="text" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div v-if="special.is_light" class="rich-text" v-html="special.abstract"></div>
                            <div v-else class="rich-text" v-html="special.profile.content"></div>
                        </div>
                        <div v-if="special.is_light" class="layui-tab-item">
                            <div class="layui-row layui-col-space15">
                                <div v-if="special.light_type === 1 && special.singleProfile.is_try" class="layui-col-md12" v-html="special.singleProfile.try_content"></div>
                                <div v-if="special.light_type === 1" class="layui-col-md12" v-html="special.singleProfile.content"></div>
                                <div v-else class="layui-col-md12">
                                    <div id="J_prismPlayer"></div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="layui-tab-item">
                            <div class="layui-row layui-col-space15">
                                <div class="layui-col-md12">
                                    <table id="source" lay-filter="source"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form class="layui-form" lay-filter="form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">审核状态：</label>
                    <div class="layui-input-block">
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
<script type="text/html" id="image">
    <img width="60" height="60" src="{{d.image}}" lay-event="image">
</script>
<script type="text/html" id="type">
    {{# if(d.type === 1) { }}
    图文
    {{# } else if (d.type === 2) { }}
    音频
    {{# } else { }}
    视频
    {{# } }}
</script>
<script type="text/html" id="toolbar">
    <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="detail">详情</a>
</script>
{/block}
{block name="foot"}
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    require(['vue'], function (Vue) {
        var special = {$special},
            liveInfo = {$liveInfo},
            form = layui.form,
            layer = layui.layer,
            parentLayer = parent.layui.layer,
            table = layui.table,
            element = layui.element,
            tabTitle = [
                {
                    title: '信息',
                    value: 1
                },
                {
                    title: '详情',
                    value: 2
                },
                {
                    title: '素材',
                    value: 3
                },
                {
                    title: '内容',
                    value: 4
                }
            ];
        for (var index = tabTitle.length; index--;) {
            if (special.type === 4) {
                tabTitle[index].title = '直播' + tabTitle[index].title;
                if (tabTitle[index].value === 3 || tabTitle[index].value === 4) {
                    tabTitle.splice(index, 1);
                }
            } else if (special.type === 5) {
                tabTitle[index].title = '专栏' + tabTitle[index].title;
                if (tabTitle[index].value === 3 || tabTitle[index].value === 4) {
                    tabTitle.splice(index, 1);
                }
            } else {
                tabTitle[index].title = '专题' + tabTitle[index].title;
                if (special.is_light) {
                    if (tabTitle[index].value === 3) {
                        tabTitle.splice(index, 1);
                    }
                } else {
                    if (tabTitle[index].value === 4) {
                        tabTitle.splice(index, 1);
                    }
                }
            }
        }
        new Vue({
            el: '#app',
            data: {
                special: special,
                liveInfo: liveInfo,
                tabTitle: tabTitle,
                status: 1
            },
            computed: {
                type: function () {
                    switch (this.special.type) {
                        case 1: return '图文专题';
                        case 2: return '音频专题';
                        case 3: return '视频专题';
                        case 4: return '直播专题';
                        case 5: return '专栏专题';
                        case 6: return '轻专题';
                    }
                }
            },
            mounted: function () {
                this.$nextTick(function () {
                    var vm = this;
                    this.getSourceList();
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
                    element.on('tab(tab)', function (data) {
                        if (data.index === 2) {
                            if (vm.special.type === 1 || vm.special.type === 2 || vm.special.type === 3) {
                                table.resize('source');
                            }
                        } else {
                            if (vm.special.type === 6 && vm.special.light_type !== 1 && vm.player && vm.player.getStatus() === 'playing') {
                                vm.player.pause();
                            }
                        }
                    });
                    table.on('tool(source)', function (obj) {
                        if (obj.event === 'detail') {
                            layer.open({
                                type: 2,
                                title: obj.data.title,
                                maxmin: true,
                                area: ['90%', '90%'],
                                content: layList.U({
                                    a: 'getSources',
                                    q: {
                                        id: obj.data.source_id
                                    }
                                })
                            });
                        } else if (obj.event === 'image') {
                            vm.look(obj.data.image);
                        }
                    });
                    // 轻专题
                    if (this.special.is_light) {
                        if (this.special.light_type !== 1) {
                            if (this.special.singleProfile.videoId) {
                                // 点播
                                layList.basePost(layList.U({
                                    a: 'video_upload_address_voucher'
                                }), {
                                    FileName: '',
                                    type: 3,
                                    image: '',
                                    videoId: this.special.singleProfile.videoId
                                }, function (res) {
                                    $.getJSON(res.msg, function (data) {
                                        vm.createPlayer(data.PlayInfoList.PlayInfo[0].PlayURL);
                                    });
                                });
                            } else {
                                this.createPlayer(this.special.singleProfile.link);
                            }
                        }
                    }
                });
            },
            methods: {
                // 审核通过
                success: function () {
                    layList.baseGet(layList.U({
                        a: 'succ',
                        p: {
                            id: this.special.id
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
                            id: this.special.id
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
                },
                // 获得已关联的素材
                getSourceList: function () {
                    table.render({
                        elem: '#source',
                        url: layList.U({
                            a: 'get_source_sure_list'
                        }),
                        where: {
                            id: this.special.id,
                            order: this.special.sort_order
                        },
                        cols: [[
                            {field: 'id', title: 'ID', align: 'center'},
                            {field: 'title', title: '名称', align: 'center'},
                            {field: 'type', title: '类型', align: 'center', templet: '#type'},
                            {field: 'image', title: '封面', align: 'center', templet: '#image'},
                            {field: 'sort', title: '排序', align: 'center'},
                            {fixed: 'right', title: '操作', align: 'center', toolbar: '#toolbar'}
                        ]],
                    });
                },
                // 创建播放器
                createPlayer: function (source) {
                    this.player = new Aliplayer({
                        id: 'J_prismPlayer',
                        height: '500px',
                        autoplay: false,
                        source: source
                    }, function (player) {
                        console.log('The player is created.');
                    });
                }
            }
        });
    });
</script>
{/block}