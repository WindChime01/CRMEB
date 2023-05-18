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

    .layui-tab-title .layui-this:after {
        border-bottom-color: #fff !important;
    }
</style>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card" id="app" v-cloak>
        <div class="layui-card-header">添加消息</div>
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <div class="layui-form-item submit">
                        <label class="layui-form-label">消息名称：</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" style="width: 600px" v-model.trim="formData.name" autocomplete="off" placeholder="请输入消息名称" maxlength="20" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item submit">
                        <label class="layui-form-label">模版常数：</label>
                        <div class="layui-input-block">
                            <input type="text" name="template_const" style="width: 600px" v-model.trim="formData.template_const" autocomplete="off" placeholder="请输入模版常数" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item submit">
                        <label class="layui-form-label">模板编号：</label>
                        <div class="layui-input-block">
                            <input type="text" name="tempkey" style="width: 600px" v-model.trim="formData.tempkey" autocomplete="off" placeholder="请输入模板编号" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item submit">
                        <label class="layui-form-label">短信模板ID：</label>
                        <div class="layui-input-block">
                            <input type="text" name="temp_id" style="width: 600px" v-model.trim="formData.temp_id" autocomplete="off" placeholder="请输入短信模板ID" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-top: 10px;">
                        <label class="layui-form-label">短信内容：</label>
                        <div class="layui-input-block">
                            <textarea placeholder="请输入短信内容" v-model="formData.sms_content" style="width: 600px" class="layui-textarea"></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">公众号模板：</label>
                        <div class="layui-input-block">
                            <input type="radio" name="is_wechat" value="1" title="显示" v-model="formData.is_wechat" lay-filter="is_wechat" >
                            <input type="radio" name="is_wechat" value="0" title="隐藏" v-model="formData.is_wechat" lay-filter="is_wechat">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">发送短信：</label>
                        <div class="layui-input-block">
                            <input type="radio" name="is_sms" value="1" title="显示" v-model="formData.is_sms" lay-filter="is_sms" >
                            <input type="radio" name="is_sms" value="0" title="隐藏" v-model="formData.is_sms" lay-filter="is_sms">
                        </div>
                    </div>
                    <div class="layui-form-item submit">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" type="button" @click="save">立即提交</button>
                            <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消</button>
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
    require(['vue','helper','zh-cn','request','plupload'],function(Vue,$h) {
        new Vue({
            el: "#app",
            data: {
                formData:{
                    name:'',
                    template_const:'',
                    tempkey: '',
                    temp_id: '',
                    sms_content: '',
                    is_wechat: 1,
                    is_sms: 1
                }
            },
            methods:{
                clone_form: function () {
                    var that = this;
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                },
                save:function () {
                    var that=this;
                    if (!that.formData.name) return layList.msg('请输入消息名称');
                    if (!that.formData.template_const) return layList.msg('请输入模版常数');
                    if (!that.formData.tempkey) return layList.msg('请输入模板编号');
                    layList.loadFFF();
                    layList.basePost(layList.U({a: 'save_message'}), that.formData, function (res) {
                        layList.loadClear();
                            layList.layer.confirm('添加成功,您要继续消息管理吗?', {
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
                }
            },
            mounted:function () {
                var that=this;
                that.$nextTick(function () {
                    layList.form.render();
                    layList.form.on('radio(is_wechat)',function (data) {
                        that.formData.is_wechat=data.value;
                    });
                    layList.form.on('radio(is_sms)',function (data) {
                        that.formData.is_sms=data.value;
                    });
                });
            }
        })
    })
</script>
{/block}
