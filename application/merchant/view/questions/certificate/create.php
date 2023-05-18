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
    .layui-form-label{width:150px;}
    .layui-input-block{margin-left:150px;}
    .shili {
        width: 100%;
    }

    .download-link{
        padding-top: 20px;
        text-align: center;
    }

    .download-link a {
        font-size: 14px;
        color: #0092dc
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid" id="app" v-cloak>
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label required">证书标题：</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" v-model.trim="formData.title" autocomplete="off" placeholder="请输入证书标题" maxlength="20" class="layui-input">
                    </div>
                </div>
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md6">
                        <div class="layui-form-item">
                            <label class="layui-form-label required">背景图：（600*850）</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.background">
                                    <img :src="formData.background" alt="">
                                    <div class="mask">
                                        <p><i class="fa fa-eye" @click="look(formData.background)"></i><i class="fa fa-trash-o" @click="delect('background')"></i></p>
                                    </div>
                                </div>
                                <div class="upload-image"  v-show="!formData.background" @click="upload('background')">
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>选择图片</p>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">二维码：（200*200）</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.qr_code">
                                    <img :src="formData.qr_code" alt="">
                                    <div class="mask">
                                        <p><i class="fa fa-eye" @click="look(formData.qr_code)"></i><i class="fa fa-trash-o" @click="delect('qr_code')"></i></p>
                                    </div>
                                </div>
                                <div class="upload-image"  v-show="!formData.qr_code" @click="upload('qr_code')">
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>选择图片</p>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序：</label>
                            <div class="layui-input-inline">
                                <input type="number" name="sort" v-model="formData.sort" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">说明：</label>
                            <div class="layui-input-block">
                                <textarea name="explain" v-model.trim="formData.explain" autocomplete="off" maxlength="30" placeholder="最多30字" class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">获取方式：</label>
                            <div class="layui-input-block">
                                <input type="radio" name="obtain" lay-filter="obtain" v-model="formData.obtain" value="1" title="课程">
                                <input type="radio" name="obtain" lay-filter="obtain" v-model="formData.obtain" value="2" title="考试">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <div class="layui-btn-container">
                                    <button class="layui-btn layui-btn-normal" type="button" @click="save">{{id ? '立即修改':'立即提交'}}</button>
                                    <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <img class="shili" src="{__PUBLIC_PATH}wap/first/zsff/images/certificate_shili.png">
                        <div class="download-link">
                            <a href="http://dev.zhishifufei.crmeb.net/system/certificates/certificate1.xd" download="http://dev.zhishifufei.crmeb.net/system/certificates/certificate1.xd">下载证书模板（仅供参考）</a>
                        </div>
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
    var id={$id},certificate=<?=isset($certificate) ? $certificate : []?>;
    require(['vue','request','OssUpload'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                formData:{
                    title:certificate.title || '',
                    background: certificate.background || '',
                    qr_code: certificate.qr_code || '',
                    obtain:Number(certificate.obtain) || 1,
                    explain:certificate.explain || '',
                    sort:Number(certificate.sort) || 0
                },
                mask:{
                    background:false,
                    qr_code:false
                }
            },
            methods:{
                clone_form: function () {
                    var that = this;
                    if (parseInt(id) == 0) {
                        parent.layer.closeAll();
                    }
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                },
                //上传图片
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                save:function () {
                    var that=this;
                    if(!that.formData.title) return layList.msg('请输入证书标题');
                    if(that.formData.background=='') return layList.msg('请上传背景图');
                    if(that.formData.qr_code=='') return layList.msg('请上传二维码');
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save_add',q:{id:id}}),that.formData,function (res) {
                        layList.loadClear();
                        if(parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加证书吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function (index) {
                                layList.layer.close(index);
                            }, function () {
                                parent.layer.closeAll();
                            });
                        }else{
                            layList.msg('修改成功',function () {
                                parent.layer.closeAll();
                            })
                        }
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                },
                delect:function(key){
                    var that=this;
                    that.formData[key]='';
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
                look: function (src) {
                    layui.layer.photos({
                        photos: {
                            data: [
                                {
                                    src: src
                                }
                            ]
                        },
                        anim: 5
                    });
                }
            },
            mounted:function () {
                var that=this;
                window.changeIMG = that.changeIMG;
                this.$nextTick(function () {
                    layList.form.render();
                    layList.form.on('radio(obtain)', function (data) {
                        that.formData.obtain = parseInt(data.value);
                    });
                });
            }
        })
    })
</script>
{/block}
