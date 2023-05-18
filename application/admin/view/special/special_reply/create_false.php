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
    .edui-default .edui-for-image .edui-icon {background-position: -380px 0px;}
    .layui-form-item .special-label {width: 50px;float: left;height: 30px;line-height: 38px;margin-left: 10px;margin-top: 5px;border-radius: 5px;background-color: #0092DC;text-align: center;}
    .layui-form-item .special-label i {display: inline-block;width: 18px;height: 18px;font-size: 18px;color: #fff;}
    .layui-form-item .label-box {border: 1px solid;border-radius: 10px;position: relative;padding: 10px;height: 30px;color: #fff;background-color: #393D49;text-align: center;cursor: pointer;display: inline-block;line-height: 10px;}
    .layui-form-item .label-box p {line-height: inherit;}
</style>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
{/block}
{block name="content"}
<div id="app" v-cloak class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <div class="layui-form-item submit">
                        <label class="layui-form-label required">用户昵称：</label>
                        <div class="layui-input-block">
                            <input type="text" name="nickname" v-model="formData.nickname" autocomplete="off" placeholder="请输入用户昵称" maxlength="10" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item submit">
                        <label class="layui-form-label required">头像：（200*200）</label>
                        <div class="layui-input-block">
                            <div class="upload-image-box" v-if="formData.avatar" >
                                <img :src="formData.avatar" alt="">
                                <div class="mask"  style="display: block">
                                    <p>
                                        <i class="fa fa-eye" @click="look(formData.avatar)"></i>
                                        <i class="fa fa-trash-o" @click="delect('avatar')"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="upload-image" v-show="!formData.avatar" @click="upload('avatar')">
                                <div class="fiexd"><i class="fa fa-plus"></i></div>
                                <p>选择图片</p>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label required">课程专题:</label>
                        <div class="layui-input-block">
                            <select name="special_id" v-model="formData.special_id" lay-search="" lay-filter="special_id" lay-verify="required">
                                <option value="0">请选专题</option>
                                <option  v-for="item in special_list" :value="item.id">{{item.title}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label required">满意度：</label>
                        <div class="layui-input-block">
                            <div id="rate"></div>
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-top: 10px;">
                        <label class="layui-form-label required">评论内容：</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入评论内容" v-model="formData.comment" maxlength="100" class="layui-textarea"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">评论图片：（200*200）</label>
                        <div class="layui-input-block">
                            <div class="upload-image-box" v-if="formData.pics.length" v-for="(item,index) in formData.pics">
                                <img :src="item.pic" alt="">
                                <div class="mask">
                                    <p>
                                        <i class="fa fa-eye" @click="look(item.pic)"></i>
                                        <i class="fa fa-trash-o" @click="delect('pics',index)"></i>
                                    </p>
                                </div>
                            </div>
                            <div class="upload-image" v-show="formData.pics.length < 5" @click="upload('pics',5 - formData.pics.length)">
                                <div class="fiexd"><i class="fa fa-plus"></i></div>
                                <p>选择图片</p>
                            </div>
                        </div>
                    </div>

                    <div class="layui-form-item submit">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" type="button" @click="save">立即提交</button>
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
    var special_id={$special_id};
    require(['vue','helper','zh-cn','request','plupload','aliyun-oss','OssUpload'],function(Vue,$h) {
        new Vue({
            el: "#app",
            data: {
                special_list:[],
                formData:{
                    nickname:'',
                    avatar: '',
                    special_id: special_id,
                    comment: '',
                    pics: [],
                    satisfied_score:0
                },
                mask: {
                    avatar: false
                },
                //上传类型
                mime_types: {
                    Image: "jpg,gif,png,JPG,GIF,PNG",
                    Video: "mp4,MP4",
                    Audio: "mp3,MP3",
                },
            },
            methods:{
                //查看图片
                look: function (pic) {
                    parent.$eb.openImage(pic);
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
                //获取专题
                get_special_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'specialList'}), function (res) {
                        that.$set(that, 'special_list', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                save:function () {
                    var that=this;
                    that.$nextTick(function () {
                        if (!that.formData.nickname) return layList.msg('请输入昵称');
                        if (!that.formData.avatar) return layList.msg('请上传头像');
                        if (that.formData.special_id<=0) return layList.msg('请选择专题');
                        if (that.formData.satisfied_score<=0 || that.formData.satisfied_score>5) return layList.msg('满意度不正确');
                        if (!that.formData.comment) return layList.msg('请编辑评论内容');
                        layList.loadFFF();
                        layList.basePost(layList.U({a: 'save_false'}), that.formData, function (res) {
                            layList.loadClear();
                                layList.layer.confirm('添加成功,您要继续添加吗?', {
                                    btn: ['继续添加', '立即提交'] //按钮
                                }, function (index) {
                                    layList.layer.close(index);
                                }, function () {
                                    parent.layer.closeAll();
                                });
                        }, function (res) {
                            layList.msg(res.msg);
                            layList.loadClear();
                        });
                    })
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
                }
            },
            mounted:function () {
                var that=this;
                window.changeIMG = that.changeIMG;
                that.get_special_list();
                layList.select('special_id', function (obj) {
                    that.formData.special_id = obj.value;
                });
                this.$nextTick(function () {
                    layui.rate.render({
                        elem: '#rate',
                        value: this.formData.satisfied_score,
                        choose: function (value) {
                            that.formData.satisfied_score = value;
                        }
                    });
                });
            }
        })
    })
</script>
{/block}
