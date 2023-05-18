{extend name="public/container" /}
{block name="head"}
<style>
    .section1 {
        height: 248px;
        padding-top: 78px;
        background: url("{__FRAME_PATH}img/auth-bg.jpg") left top/100% 248px no-repeat;
        text-align: center;
        font-weight: 500;
        font-size: 16px;
        line-height: 22px;
        color: #FFFFFF;
    }

    .section1 .title {
        margin-bottom: 27px;
        font-weight: 600;
        font-size: 30px;
        line-height: 42px;
    }

    .section2 {
        height: 288px;
        padding-top: 100px;
        background: url("{__FRAME_PATH}img/auth-icon1.png") center 20px/36px 55px no-repeat;
        text-align: center;
    }

    .section2 .list {
        display: inline-block;
        width: 280px;
    }

    .section2 .item {
        text-align: left;
        font-size: 16px;
        line-height: 30px;
        color: #333333;
    }

    .section2 .name {
        display: inline-block;
        width: 5em;
        color: #999999;
    }

    .section2 .layui-btn-container {
        margin-top: 30px;
        margin-bottom: -10px;
    }

    .section2 .layui-btn-container .layui-btn {
        margin-right: 30px;
    }

    .section2 .layui-btn-container .layui-btn:last-child {
        margin-right: 0;
    }

    .section2 .layui-btn {
        width: 92px;
    }

    .section3 {
        height: 288px;
        padding-top: 100px;
        background: url("{__FRAME_PATH}img/auth-icon2.png") center 20px/36px 43px no-repeat;
    }

    .section3 .layui-form-item:first-child,
    .section3 .layui-form-item:nth-child(2) {
        padding-right: 46px;
        padding-left: 49px;
    }

    .section3 .layui-form-item:last-child {
        margin-top: 26px;
        text-align: center;
    }

    .section3 .layui-btn {
        width: 92px;
    }

    .upload-img {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 60px;
        border: 1px solid #D9D9D9;
        border-radius: 3px;
    }

    .upload-img img {
        width: 100%;
        height: 100%;
    }

    .upload-img .upload-action {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        text-align: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .upload-img .upload-action::after {
        content: "";
        display: inline-block;
        height: 100%;
        vertical-align: middle;
    }

    .upload-img:hover .upload-action {
        opacity: 1;
    }

    .upload-img .layui-icon {
        font-size: 16px;
        color: #FFFFFF;
        cursor: pointer;
    }

    .upload {
        display: inline-block;
        width: 60px;
        height: 60px;
        border: 1px solid #D9D9D9;
        border-radius: 3px;
        vertical-align: bottom;
        text-align: center;
        line-height: 58px;
        cursor: pointer;
    }

    .upload:hover {
        border-color: #D2D2D2;
    }

    .upload .layui-icon {
        font-size: 20px;
        color: #BBBBBB;
    }
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="section1">
                <div class="title">商业使用授权证书，保护您的合法权益</div>
                <div>您的支持是我们不断进步的动力，商业授权更多是一个保障和附加的增值服务，让您优先享受新版本的强大功能和安全保障</div>
            </div>
        </div>
        <div class="layui-col-md6">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="section2">
                        <ul class="list">
                            <li class="item"><span class="name">授权状态：</span>{{ msg || '——' }}</li>
                            <li class="item"><span class="name">授权期限：</span>{{ authCode ?  '永久' : (day + '天') }}</li>
                            <li class="item"><span class="name">授权码：</span>{{ authCode || '——' }}</li>
                        </ul>
                        <div class="layui-btn-container">
                            <a href="http://www.crmeb.com/web/auth/query.html" target="_blank" rel="noopener noreferrer" class="layui-btn layui-btn-normal layui-btn-sm">查询授权</a>
                            <button v-if="!authCode" type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="goAuth">获取授权</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="hasCopyright" class="layui-col-md6">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="section3">
                        <form class="layui-form" action="">
                            <div class="layui-form-item">
                                <label class="layui-form-label">修改授权信息：</label>
                                <div class="layui-input-block">
                                    <input v-model.trim="copyrightContent" type="text" name="copyrightContent" required lay-verify="required" autocomplete="off"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">上传授权图片：</label>
                                <div class="layui-input-block">
                                    <div v-if="copyrightLogo" class="upload-img">
                                        <img :src="copyrightLogo" alt="">
                                        <div class="upload-action">
                                            <i class="layui-icon layui-icon-delete" @click="deleteIMG"></i>
                                        </div>
                                    </div>
                                    <div v-else class="upload" @click="upload('copyrightLogo')">
                                        <i class="layui-icon layui-icon-addition"></i>
                                    </div>
                                    <div style="color: #999;font-size: 10px;">建议尺寸：宽130px*高44px</div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="save_copyright">保存</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="copyrightLoaded" class="layui-col-md6">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="section2">
                        <ul class="list">
                            <li class="item"><span class="name">服务类型：</span>去版权服务</li>
                            <li class="item"><span class="name">版权信息：</span>购买之后可以设置</li>
                        </ul>
                        <div class="layui-btn-container">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="goBuy">去版权</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    window.addEventListener('message', function (e) {
        if (e.data.event === 'onCancel') {
            layui.layer.closeAll();
            layui.layer.load(1);
            $.getJSON("{:url('get_auth_data')}", function () {
                window.location.reload();
            });
        }
    });
    require(['vue', 'request', 'OssUpload'], function (Vue) {
        var shopZsffUrl = 'https://shop.crmeb.net/html/index.html?product=zsff&label=4&venrsion={$curent_version.version}&url=' + window.location.hostname;
        var shopCopyrightUrl = 'https://shop.crmeb.net/html/index.html?product=copyright&label=4&venrsion={$curent_version.version}&url=' + window.location.hostname;
        var upload = layui.upload;
        window.vm = new Vue({
            el: "#app",
            data: {
                msg: '',
                day: 0,
                authCode: '',
                hasCopyright: false,
                product: {
                    price: 0
                },
                copyrightContent: '',
                copyrightLoaded: false,
                copyrightLogo: ''
            },
            created: function () {
                window.changeIMG = this.changeIMG;
                this.check_auth_data();
            },
            mounted: function () {
                this.$nextTick(function () {
                    upload.render({
                        elem: '#upload'
                    });
                });
            },
            methods: {
                deleteIMG: function () {
                    this.copyrightLogo = '';
                },
                changeIMG: function (key, value) {
                    this[key] = value;
                },
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count}, {w: 800, h: 550});
                },
                check_auth_data: function () {
                    var self = this;
                    layui.layer.load(1);
                    $.when($.getJSON("{:url('check_auth')}"), $.getJSON("{:url('auth_data')}")).done(function (res1, res2) {
                        layui.layer.closeAll();
                        self.copyrightLoaded = true;
                        if (res2[0].code === 200) {
                            self.msg = res2[0].data.msg;
                            self.day = res2[0].data.day;
                            self.authCode = res2[0].data.authCode;
                            self.hasCopyright = res2[0].data.copyright;
                            if (self.hasCopyright) {
                                self.get_copyright();
                            }
                        }
                    });
                },
                // 获取授权
                goAuth: function () {
                    layui.layer.open({
                        type: 2,
                        title: ' ',
                        area: ['800px', '600px'],
                        content: shopZsffUrl
                    });
                },
                // 立即购买
                goBuy: function () {
                    if (!this.authCode) {
                        return layui.layer.msg('请先去申请授权', {icon: 5});;
                    }
                    layui.layer.open({
                        type: 2,
                        title: ' ',
                        area: ['800px', '600px'],
                        content: shopCopyrightUrl
                    });
                },
                // 保存版权信息
                save_copyright: function () {
                    var self = this;
                    if (!this.copyrightContent && !this.copyrightLoaded) {
                        return layui.layer.msg('请添加版权信息', {icon: 5});
                    }
                    layui.layer.load(1);
                    $.post("{:url('save_copyright')}", {
                        copyrightContent: self.copyrightContent,
                        copyrightLogo: self.copyrightLogo,
                    }, function (res) {
                        layui.layer.closeAll();
                        if (res.code === 200) {
                            layui.layer.msg('保存成功', {icon: 1});
                        } else {
                            layui.layer.msg(res.msg, {icon: 5});
                        }
                    }, 'json');
                },
                // 获取版权信息
                get_copyright: function () {
                    var self = this;
                    layui.layer.load(1);
                    $.getJSON("{:url('login/get_copyright')}", function (res) {
                        layui.layer.closeAll();
                        if (res.code === 200) {
                            if (typeof res.data.nncnL_crmeb_copyright === 'string') {
                                self.copyrightContent = res.data.nncnL_crmeb_copyright;
                                self.copyrightLogo = res.data.nncnL_crmeb_copyright_logo;
                            }
                        } else {
                            layui.layer.msg(res.msg, {icon: 5});
                        }
                    });
                }
            }
        });
    });
</script>
{/block}
