{extend name="public/container" /}
{block name="title"}{/block}
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
    .layui-col-content:after {
        content: '\20';
        clear: both;
        *zoom: 1;
        display: block;
        height: 0;
    }
    .layui-col-label {
        float: left;
        width: 5em;
    }
    .layui-col-block {
        margin-left: 5em;
    }
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-tab layui-tab-brief" lay-filter="tab">
                <ul class="layui-tab-title">
                    <li v-for="(item, index) in tabTitle" :key="item.value" :class="{ 'layui-this': !index }">{{ item.title }}</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <div class="layui-row layui-col-space10">
                            <div class="layui-col-md4">素材名称：{{ special.title }}</div>
                        </div>
                        <div class="layui-row layui-col-space10">
                            <div class="layui-col-md4">素材类型：{{ special.type }}</div>
                        </div>
                        <div class="layui-row layui-col-space10">
                            <div class="layui-col-md4">素材排序：{{ special.sort }}</div>
                        </div>
                        <div class="layui-row layui-col-space10">
                            <div class="layui-col-md4">素材封面：<img width="60" height="60" :src="special.image.pic" @click="look(special.image.pic)"></div>
                        </div>
                        <div class="layui-row layui-col-space10">
                            <div class="layui-col-md12">试听状态：{{ special.is_try ? '开启' : '关闭' }}</div>
                        </div>
                        <div class="layui-row layui-col-space10">
                            <div class="layui-col-md12">试听时长：{{ special.try_time }}分钟</div>
                        </div>
                        <div class="layui-row layui-col-space10">
                            <div class="layui-col-md12">
                                <div class="layui-col-content">
                                    <div class="layui-col-label">素材简介：</div>
                                    <div class="layui-col-block" v-html="special.detail"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-tab-item">
                        <div v-if="special.type === 1" class="layui-row layui-col-space10">
                            <div v-if="special.is_try" class="layui-col-md12">
                                <div class="layui-col-content">
                                    <div class="layui-col-label">试看内容：</div>
                                    <div class="layui-col-block" v-html="special.try_content"></div>
                                </div>
                            </div>
                            <div class="layui-col-md12">
                                <div class="layui-col-label">素材内容：</div>
                                <div class="layui-col-block" v-html="special.content"></div>
                            </div>
                        </div>
                        <div v-else class="layui-row layui-col-space10">
                            <div class="layui-col-md12">
                                <div id="J_prismPlayer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="foot"}
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    require(['vue'], function (Vue) {
        var special = {$special},
            element = layui.element;
        new Vue({
            el: '#app',
            data: {
                special: special,
                tabTitle: [
                    {
                        title: '素材信息',
                        value: 1
                    },
                    {
                        title: '素材内容',
                        value: 2
                    }
                ]
            },
            mounted: function () {
                this.$nextTick(function () {
                    var vm = this;
                    if (this.special.type !== 1) {
                        if (this.special.videoId) {
                            layList.basePost(layList.U({
                                a: 'video_upload_address_voucher'
                            }), {
                                FileName: '',
                                type: 3,
                                image: '',
                                videoId: this.special.videoId
                            }, function (res) {
                                $.getJSON(res.msg, function (data) {
                                    vm.createPlayer(data.PlayInfoList.PlayInfo[0].PlayURL);
                                });
                            });
                        } else {
                            this.createPlayer();
                        }
                    }
                    element.on('tab(tab)', function (data) {
                        if (!data.index) {
                            if (vm.player && vm.player.getStatus() === 'playing') {
                                vm.player.pause();
                            }
                        }
                    });
                });
            },
            methods: {
                createPlayer: function (source) {
                    if (this.player) {
                        this.player.dispose();
                    }
                    this.player = new Aliplayer({
                        id: 'J_prismPlayer',
                        height: '500px',
                        autoplay: false,
                        source: source || this.special.link
                    }, function (player) {
                        console.log('The player is created.');
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