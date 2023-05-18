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
            <div class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label required">证书标题：</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" v-model.trim="formData.title" autocomplete="off" placeholder="请输入证书标题" maxlength="20" class="layui-input" disabled>
                    </div>
                </div>
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md6">
                        <div class="layui-form-item">
                            <label class="layui-form-label required">背景图：（600*850）</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.background">
                                    <img :src="formData.background" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">二维码：（200*200）</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.qr_code">
                                    <img :src="formData.qr_code" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序：</label>
                            <div class="layui-input-inline">
                                <input type="number" name="sort" v-model="formData.sort" autocomplete="off" class="layui-input" disabled>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">说明：</label>
                            <div class="layui-input-block">
                                <textarea name="explain" v-model.trim="formData.explain" autocomplete="off" maxlength="30" placeholder="最多30字" class="layui-textarea" disabled></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">获取方式：</label>
                            <div class="layui-input-block">
                                <input type="radio" name="obtain" lay-filter="obtain" v-model="formData.obtain" value="1" title="课程" disabled>
                                <input type="radio" name="obtain" lay-filter="obtain" v-model="formData.obtain" value="2" title="考试" disabled>
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
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var form = layui.form,
        layer = layui.layer,
        parentLayer = parent.layui.layer;
    var certificate=<?=isset($certificate) ? $certificate : []?>;
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
                status: 1
            },
            methods:{
                // 审核通过
                success: function () {
                    layList.baseGet(layList.U({
                        a: 'succ',
                        p: {
                            id: certificate.id
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
                            id: certificate.id
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
            },
            mounted:function () {
                layList.form.render();
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
            }
        })
    })
</script>
{/block}
