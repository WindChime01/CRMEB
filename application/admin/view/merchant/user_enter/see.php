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
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app" v-cloak>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form action="" class="layui-form">
                        <div class="layui-form-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">名称：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="lecturer_name" v-model.trim="lecturer.merchant_name" autocomplete="off" disabled maxlength="8" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">手机：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="link_tel" v-model.trim="lecturer.link_tel" autocomplete="off" disabled maxlength="12" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">头像：</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box">
                                        <img :src="lecturer.merchant_head" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">领域：</label>
                                <div v-if="lecturer.label.length">
                                    <div class="layui-input-block">
                                        <button v-for="(item,index) in lecturer.label" :key="index" type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="delLabel(index)">{{item}}</button>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-if="lecturer.charter.length">
                                <label class="layui-form-label">证书：</label>
                                <div class="layui-input-block">
                                    <div v-for="(img,index) in lecturer.charter" class="upload-image-box">
                                        <img :src="img" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" style="margin-top: 10px;">
                                <label class="layui-form-label">地址：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="address" style="width: 100%" v-model="lecturer.address" autocomplete="off" maxlength="20" disabled class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item" style="margin-top: 10px;">
                                <label class="layui-form-label">简介：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="explain" style="width: 100%" v-model="lecturer.explain" autocomplete="off" maxlength="20" disabled class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item" style="margin-top: 10px;">
                                <label class="layui-form-label">介绍：</label>
                                <div class="layui-input-block">
                                    <textarea id="editor" rows="5" style="width: 100%;" disabled>{{lecturer.introduction}}</textarea>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var id={$id};
    require(['vue','helper','zh-cn','request','plupload'],function(Vue,$h) {
        new Vue({
            el: "#app",
            data: {
                lecturer:[],
                label: ''
            },
            methods:{
                //查看图片
                look: function (pic) {
                   parent.$eb.openImage(pic);
                },
                //获取讲师
                get_lecturer_enter: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'getUserEnter',p:{id:id}}), function (res) {
                        that.lecturer=res.data;
                    });
                }
            },
            mounted:function () {
                var that=this;
                that.get_lecturer_enter();
            }
        })
    })
</script>
{/block}
