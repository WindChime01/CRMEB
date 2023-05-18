{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label {
        width: 50px;
        float: left;
        height: 30px;
        line-height: 38px;
        margin-left: 10px;
        margin-top: 5px;
        border-radius: 5px;
        background-color: #0092DC;
        text-align: center;
    }

    .layui-form-item .special-label i {
        display: inline-block;
        width: 18px;
        height: 18px;
        font-size: 18px;
        color: #fff;
    }

    .layui-form-item .label-box {
        border: 1px solid;
        border-radius: 10px;
        position: relative;
        padding: 10px;
        height: 30px;
        color: #fff;
        background-color: #393D49;
        text-align: center;
        cursor: pointer;
        display: inline-block;
        line-height: 10px;
    }

    .layui-form-item .label-box p {
        line-height: inherit;
    }

    .edui-default .edui-for-image .edui-icon {
        background-position: -380px 0px;
    }
    .file {
        position: relative;
        background: #0092DC;
        border: 1px solid #99D3F5;
        border-radius: 4px;
        padding: 7px 12px;
        overflow: hidden;
        color: #fff;
        text-decoration: none;
        text-indent: 0;
        line-height: 20px;
        width: 120px;
    }
    .file input {
        width: 100%;
        position: absolute;
        font-size: 5px;
        right: 0;
        top: 0;
        opacity: 0;
    }
    .file:hover {
        background: #AADFFD;
        border-color: #78C3F3;
        color: #004974;
        text-decoration: none;
    }
    .layui-progress {
        margin-top: 12px;
    }
</style>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="">
                <div class="layui-tab layui-tab-brief" lay-filter="tab">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="0">基本设置</li>
                        <li lay-id="1">上传内容</li>
                        <li lay-id="2">价格设置</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item">
                                <label class="layui-form-label required">资料名称：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" required v-model.trim="formData.title" autocomplete="off" placeholder="请输入资料名称" maxlength="50" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">资料分类：</label>
                                <div class="layui-input-block">
                                    <select name="subject_id" v-model="formData.cate_id" lay-search="" lay-filter="cate_id" lay-verify="required">
                                        <option value="0">请选分类</option>
                                        <option  v-for="item in cate_list" :value="item.id" :disabled="item.pid==0 ? true : false">{{item.html}}{{item.title}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">资料排序：</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="sort" v-model="formData.sort" min="0" max="9999" class="layui-input" v-sort>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">资料封面：（710*400）</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.image">
                                        <img :src="formData.image" alt="">
                                        <div class="mask">
                                            <p>
                                                <i class="fa fa-eye" @click="look(formData.image)"></i>
                                                <i class="fa fa-trash-o" @click="delect('image')"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="!formData.image" @click="upload('image')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">推广海报：（600*740）</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.poster_image">
                                        <img :src="formData.poster_image" alt="">
                                        <div class="mask">
                                            <p><i class="fa fa-eye" @click="look(formData.poster_image)"></i>
                                                <i class="fa fa-trash-o" @click="delect('poster_image')"></i></p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="!formData.poster_image" @click="upload('poster_image')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">资料详情：</label>
                                <div class="layui-input-block">
                                    <textarea id="editor">{{formData.abstract}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: 150px;">上传方式：</label>
                                <div class="layui-input-block" style="margin-left: 150px;">
                                    <input type="radio" name="type" lay-filter="type" v-model="formData.type" value="0" title="全部">
                                    <input type="radio" name="type" lay-filter="type" v-model="formData.type" value="1" title="OSS上传">
                                    <input type="radio" name="type" lay-filter="type" v-model="formData.type" value="2" title="百度网盘">
                                </div>
                            </div>
                            <div v-show="formData.type != 2" class="layui-form-item">
                                <label class="layui-form-label required" style="width: 150px;">资料链接：</label>
                                <div class="layui-input-block" style="margin-left: 150px;overflow: hidden;">
                                    <div class="layui-row layui-col-space15">
                                        <div class="layui-col-md8">
                                            <input v-model.trim="link" :disabled="!!formData.link" type="text" name="title" placeholder="请输入资料链接" autocomplete="off" class="layui-input">
                                            <div>{{ filename }}</div>
                                        </div>
                                        <div class="layui-col-md4">
                                            <button v-show="formData.link" type="button" class="layui-btn layui-btn-danger layui-btn-sm" @click="deleteFile">删除</button>
                                            <button v-show="!formData.link" type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="uploadVideo">确认资料</button>
                                            <button v-show="!formData.link" type="button" class="layui-btn layui-btn-normal layui-btn-sm" id="upload">上传资料</button>
                                        </div>
                                    </div>
                                    <div class="layui-row layui-col-space15">
                                        <div class="layui-col-md12">
                                            <div class="layui-form-mid layui-word-aux">仅支持zip、rar格式的文件，请在OSS上传大于1000M的文件，然后在此处添加OSS链接</div>
                                        </div>
                                    </div>
                                    <div v-show="is_video" class="layui-row layui-col-space15">
                                        <div class="layui-col-md8">
                                            <div class="layui-progress" lay-showPercent="true" lay-filter="progress">
                                                <div class="layui-progress-bar layui-bg-blue" lay-percent="0%"></div>
                                            </div>
                                        </div>
                                        <div class="layui-col-md4">
                                            <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" @click="cancelUpload">取消上传</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-show="formData.type != 1" class="layui-form-item">
                                <label class="layui-form-label required" style="width: 150px;">百度网盘链接：</label>
                                <div class="layui-input-block" style="margin-left: 150px;">
                                    <input type="text" name="network_disk_link" required v-model.trim="formData.network_disk_link" autocomplete="off" placeholder="请输入百度网盘链接，由于微信iOS端网页不支持下载文件，请通过百度网盘上传、下载文件"  class="layui-input">
                                </div>
                            </div>
                            <div v-if="formData.type != 1" class="layui-form-item">
                                <label class="layui-form-label required" style="width: 150px;">百度网盘提取码：</label>
                                <div class="layui-input-block" style="margin-left: 150px;">
                                    <input type="text" name="network_disk_pwd" required v-model.trim="formData.network_disk_pwd" autocomplete="off" placeholder="请输入百度网盘提取码" maxlength="4" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">付费方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="pay_type" lay-filter="pay_type" v-model="formData.pay_type" value="1" title="付费">
                                    <input type="radio" name="pay_type" lay-filter="pay_type" v-model="formData.pay_type" value="0" title="免费">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.pay_type == 1">
                                <label class="layui-form-label">购买金额：</label>
                                <div class="layui-input-block">
                                    <input style="width: 300px" type="number" name="money" lay-verify="number" v-model="formData.money" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="padding: 9px 0;">会员付费方式：</label>
                                    <div class="layui-input-block">
                                        <input type="radio" name="member_pay_type" lay-filter="member_pay_type" v-model="formData.member_pay_type" value="1" title="付费">
                                        <input type="radio" name="member_pay_type" lay-filter="member_pay_type" v-model="formData.member_pay_type" value="0" title="免费">
                                    </div>
                                </div>
                                <div class="layui-form-item" v-show="formData.member_pay_type == 1">
                                    <label class="layui-form-label" style="padding: 9px 0;">会员购买金额：</label>
                                    <div class="layui-input-block">
                                        <input style="width: 300px" type="number" name="member_money" lay-verify="number" v-model="formData.member_money" autocomplete="off" class="layui-input" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-primary" @click="clone_form">取消</button>
                        <button v-show="tabIndex" type="button" class="layui-btn layui-btn-primary" @click="tabChange(-1)">上一步</button>
                        <button v-show="tabIndex != 2" type="button" class="layui-btn layui-btn-normal" @click="tabChange(1)">下一步</button>
                        <button v-show="tabIndex == 2" type="button" class="layui-btn layui-btn-normal" @click="save">{$id ?'确认修改':'立即提交'}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var id = {$id}, download =<?=isset($download) ? $download : "{}"?>;
    require(['vue','helper','zh-cn','request','plupload','aliyun-oss','OssUpload'], function (Vue,$h) {
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
                cate_list: [],
                formData: {
                    title: download.title || '',
                    cate_id: download.cate_id || 0,
                    image: download.image || '',
                    poster_image: download.poster_image || '',
                    abstract:download.abstract || '',
                    sort: download.sort || 0,
                    sales: download.sales || 0,
                    pay_type: download.pay_type == 1 ? 1 : 0,
                    money: download.money || 0.00,
                    member_pay_type: download.member_pay_type == 1 ? 1 : 0,
                    member_money: download.member_money || 0.00,
                    link:download.link || '',
                    network_disk_link:download.network_disk_link || '',
                    network_disk_pwd:download.network_disk_pwd || '',
                    type: download.type || 0
                },
                link: download.link || '',
                host: ossUpload.host + '/',
                mask: {
                    poster_image: false,
                    image: false,
                    service_code: false,
                },
                ue: null,
                is_video: false,
                is_upload:false,
                is_suspend:false,
                //上传类型
                mime_types: {
                    Image: "jpg,gif,png,JPG,GIF,PNG",
                    Video: "mp4,MP4",
                    Audio: "mp3,MP3",
                },
                videoWidth: 0,
                uploader: null,
                uploadInst: null,
                tabIndex: 0
            },
            computed: {
                filename: function () {
                    var arr = this.formData.link.split('/');
                    return arr[arr.length - 1];
                }
            },
            methods: {
                //取消
                cancelUpload: function () {
                    this.uploader.stop();
                    this.is_video = false;
                    this.videoWidth = 0;
                    this.is_upload = false;
                },
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
                    parent.$eb.openImage(pic);
                },
                //鼠标移入事件
                enter: function (item) {
                    if (item) {
                        item.is_show = true;
                    } else {
                        this.mask = true;
                    }
                },
                //鼠标移出事件
                leave: function (item) {
                    if (item) {
                        item.is_show = false;
                    } else {
                        this.mask = false;
                    }
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
                confirmAdd:function(){
                    var that = this;
                    if(that.link.substr(0,7).toLowerCase() == "http://" || that.link.substr(0,8).toLowerCase() == "https://"){
                        that.is_upload=true;
                        that.uploadVideo();
                    }else{
                        layList.msg('请输入正确的资料链接');
                    }
                },
                uploadVideo: function () {
                    if (this.link) {
                        if (/^https?:\/\/(.+\/)+.+(\.(zip|rar))$/i.test(this.link)) {
                            this.formData.link = this.link;
                        } else {
                            layui.layer.msg('请输入正确的资料链接');
                        }
                    }
                },
                // 删除资料
                deleteFile: function () {
                    this.formData.link = this.link = '';
                },
                //上传图片
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                //获取分类
                get_subject_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'get_cate_list'}), function (res) {
                        that.$set(that, 'cate_list', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                save: function () {
                    var that = this;
                    that.formData.abstract = that.ue.getContent();
                    that.$nextTick(function () {
                    if (!that.formData.title) return layList.msg('请输入资料名称');
                    if (!that.formData.cate_id) return layList.msg('请选择分类');
                    if (!that.formData.image) return layList.msg('请上传资料封面');
                    if (!that.formData.poster_image) return layList.msg('请上传推广海报');
                    if (!that.formData.abstract) return layList.msg('请输入资料详情');
                    if (that.formData.type == 1) {
                        if (!that.formData.link) {
                            return layList.msg('请上传资料文件');
                        }
                    } else if (that.formData.type == 2) {
                        if (!that.formData.network_disk_link) {
                            return layList.msg('请输入百度网盘链接');
                        }
                        if (!that.formData.network_disk_pwd) {
                            return layList.msg('请输入百度网盘提取码');
                        }
                    } else {
                        if (!that.formData.link) {
                            return layList.msg('请上传资料文件');
                        }
                        if (!that.formData.network_disk_link) {
                            return layList.msg('请输入百度网盘链接');
                        }
                        if (!that.formData.network_disk_pwd) {
                            return layList.msg('请输入百度网盘提取码');
                        }
                    }
                    if (that.formData.pay_type == 1) {
                        if (!that.formData.money || that.formData.money == 0.00) return layList.msg('请填写购买金额');
                    }
                    if (that.formData.member_pay_type == 1) {
                        if (!that.formData.member_money || that.formData.member_money == 0.00) return layList.msg('请填写会员购买金额');
                    }
                    layList.loadFFF();
                    layList.basePost(layList.U({
                        a: 'save_data',
                        q: {id: id}
                    }), that.formData, function (res) {
                        layList.loadClear();
                        if (parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加资料吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function (index) {
                                layList.layer.close(index);
                            }, function () {
                                parent.layer.closeAll();
                            });
                        } else {
                            layList.msg('修改成功', function () {
                                parent.layer.closeAll();
                            })
                        }
                    }, function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                    })
                },
                clone_form: function () {
                    if (parseInt(id) == 0) {
                        var that = this;
                        if (that.formData.image) return layList.msg('请先删除上传的图片在尝试取消');
                        if (that.formData.poster_image) return layList.msg('请先删除上传的图片在尝试取消');
                        parent.layer.closeAll();
                    }
                    parent.layer.closeAll();
                },
                // 上一步、下一步
                tabChange: function (value) {
                    layui.element.tabChange('tab', this.tabIndex + value);
                }
            },
            mounted: function () {
                var that = this;
                window.changeIMG = that.changeIMG;
                //选择图片
                function changeIMG(index, pic) {
                    $(".image_img").css('background-image', "url(" + pic + ")");
                    $(".active").css('background-image', "url(" + pic + ")");
                    $('#image_input').val(pic);
                }

                //选择图片插入到编辑器中
                window.insertEditor = function(list,fodder){
                    that.ue.execCommand('insertimage', list);
                };
                this.$nextTick(function () {
                    layList.form.render();
                    //实例化编辑器
                    UE.registerUI('选择图片', function (editor, uiName) {
                        var btn = new UE.ui.Button({
                            name: uiName,
                            title: uiName,
                            cssRules: 'background-position: -380px 0;',
                            onclick: function() {
                                ossUpload.createFrame(uiName, { fodder: editor.key }, { w: 800, h: 550 });
                            }
                        });
                        return btn;
                    });
                    that.ue = UE.getEditor('editor');

                    layui.element.on('tab(tab)', function (data) {
                        that.tabIndex = data.index;
                    });
                });
                //获取科目
                that.get_subject_list();

                layList.form.on('radio(pay_type)', function (data) {
                    that.formData.pay_type = parseInt(data.value);
                    if (that.formData.pay_type != 1) {
                        that.formData.is_pink = 0;
                        that.formData.member_pay_type = 0;
                        that.formData.member_money = 0;
                    };
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.form.on('radio(type)', function (data) {
                    that.formData.type = parseInt(data.value);
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.form.on('radio(member_pay_type)', function (data) {
                    that.formData.member_pay_type = parseInt(data.value);
                    if (that.formData.member_pay_type != 1) {
                        that.formData.member_money = 0;
                    };
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.select('cate_id', function (obj) {
                    that.formData.cate_id = obj.value;
                });

                layui.element.render('progress');

                that.uploader = ossUpload.upload({
                    id: 'upload',
                    mime_types: [
                        {title: "Zip files", extensions: "zip,rar"}
                    ],
                    uploadIng: function (file) {
                        layui.element.progress('progress', file.percent + '%');
                    },
                    FilesAddedSuccess: function (files) {
                        that.is_video = true;
                    },
                    success: function (res) {
                        layList.msg('上传成功');
                        that.is_video = false;
                        that.link = res.url;
                        that.uploadVideo();
                    },
                    fail: function (err) {
                        that.is_video = false;
                        that.is_upload = false;
                        layList.msg(err);
                    }
                });
            }
        })
    })
</script>
{/block}
