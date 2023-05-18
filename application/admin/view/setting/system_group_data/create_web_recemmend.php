{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form" action="">
                        <div class="layui-form-item">
                            <label class="layui-form-label required">名称：</label>
                            <div class="layui-input-block">
                                <input type="hidden" name="is_show" value="{if isset($recemmend)}{$recemmend.is_show}{else}1{/if}">
                                <input type="text" maxlength="10" name="title" lay-verify="title" value="{if isset($recemmend)}{$recemmend.title}{/if}" autocomplete="off" placeholder="最多10个字" class="layui-input">
                            </div>
                        </div>
                        {if isset($recemmend) && $recemmend.type!=4 || !isset($recemmend)}
                        <div class="layui-form-item">
                            <label class="layui-form-label required">类型：</label>
                            <div class="layui-input-block">
                                <select name="type" id="groupid" lay-filter="select">
                                    <option value="">请选择类型</option>
                                    <option value="0" {if isset($recemmend) && $recemmend.type==0}selected{/if}>专题</option>
                                    <option value="1" {if isset($recemmend) && $recemmend.type==1}selected{/if}>直播</option>
                                    <option value="2" {if isset($recemmend) && $recemmend.type==2}selected{/if}>讲师</option>
                                    <option value="3" {if isset($recemmend) && $recemmend.type==3}selected{/if}>资料</option>
                                    <option value="7" {if isset($recemmend) && $recemmend.type==7}selected{/if}>练习</option>
                                    <option value="8" {if isset($recemmend) && $recemmend.type==8}selected{/if}>考试</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label required">展示数量：</label>
                            <div class="layui-input-block">
                                <input type="number" name="show_count" lay-verify="show_count" value="{if isset($recemmend)}{$recemmend.show_count}{/if}" autocomplete="off" placeholder="超过最大数量不展示" class="layui-input">
                            </div>
                        </div>
                        {/if}
                        <div class="layui-form-item">
                            <label class="layui-form-label">说明：</label>
                            <div class="layui-input-block">
                                <input type="text" maxlength="30" name="explain" lay-verify="explain" value="{if isset($recemmend)}{$recemmend.explain}{/if}" autocomplete="off" placeholder="最多30个字" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">排序：</label>
                            <div class="layui-input-block">
                                <input type="number" name="sort" lay-verify="sort" value="{if isset($recemmend)}{$recemmend.sort}{/if}" autocomplete="off" placeholder="0" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <div class="layui-input-block">
                                {if isset($recemmend)}
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">立即修改</button>
                                {else}
                                <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="save">立即提交</button>
                                <button class="layui-btn layui-btn-primary clone">取消</button>
                                {/if}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
<script type="text/javascript" src="{__PC_KS3}src/plupload.full.min.js"></script>
<script type="text/javascript" src="{__PC_KS3}src/ks3jssdk.js"></script>
<script type="text/javascript" src="{__PC_KS3}ks3.js"></script>
{/block}
{block name="script"}
<script>
    var id={$id};
    var mime_types='jpg,gif,png,JPG,GIF,PNG';
    //实例化form
    layList.form.render();
    //初始化
    JSY.Config();
    var file_image=$('#file_image'),windowindex =parent.layer.getFrameIndex(window.name);
    $('.clone').click(function () {
        parent.layer.close(windowindex);
    });
    file_image.on('click',function () {
        $('input[name="file_image"]').click();
    });
    //提交
    layList.search('save',function(data){
        delete data.file_image;
        if(!data.title) return layList.msg('请输入标题');
        if(!data.type) return layList.msg('请选择类型');
        if(data.show_count<=0) return layList.msg('请填写展示几个内容板块');
        if(data.type==1 && data.show_count>3) return layList.msg('直播板块只能显示3个');
        if(data.type==4 && data.show_count>2) return layList.msg('新闻板块只能显示2个');
        layList.basePost(layList.U({a:'save_web_recemmend',q:{id:id}}),data,function (res) {
            layList.msg(res.msg,function () {
                parent.layer.close(windowindex);
                parent.$(".J_iframe:visible")[0].contentWindow.location.reload();
            })
        },function (res) {
            layList.msg(res.msg);
        });
    });
    $('#image .delete_image').on('click',function () {
        var that=this;
        Ks3.delObject({Key: $(this).data('url')},function () {
            $(that).parents('.upload-image-box').remove();
            file_image.show();
        },function () {
            $(that).parents('.upload-image-box').remove();
            file_image.show();
        });
    })
</script>
{/block}
