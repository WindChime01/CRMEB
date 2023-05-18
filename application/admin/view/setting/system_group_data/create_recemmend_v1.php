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
                                <input type="hidden" name="is_fixed" value="{if isset($recemmend)}{$recemmend.is_fixed}{else}{$is_fixed}{/if}">
                                <input type="hidden" name="is_show" value="{if isset($recemmend)}{$recemmend.is_show}{else}1{/if}">
                                <input type="text" maxlength="10" name="title" lay-verify="title" value="{if isset($recemmend)}{$recemmend.title}{/if}" autocomplete="off" placeholder="最多10个字" class="layui-input">
                                <input type="hidden" name="typesetting"  value="{if isset($recemmend)}{$recemmend.typesetting}{/if}">
                            </div>
                        </div>
                        {if !isset($recemmend) || $recemmend.typesetting != 5}
                        <div class="layui-form-item">
                            <label class="layui-form-label required">类型：</label>
                            <div class="layui-input-block">
                                <select name="type" id="groupid" lay-filter="select">
                                    <option value="">请选择类型</option>
                                    <option value="0" {if isset($recemmend) && $recemmend.type==0}selected{/if}>专题</option>
                                    <option value="1" {if isset($recemmend) && $recemmend.type==1}selected{/if}>新闻</option>
                                    <option value="4" {if isset($recemmend) && $recemmend.type==4}selected{/if}>商品</option>
                                    <option value="6" {if isset($recemmend) && $recemmend.type==6}selected{/if}>讲师</option>
                                    <option value="8" {if isset($recemmend) && $recemmend.type==8}selected{/if}>拼团</option>
                                    <option value="10" {if isset($recemmend) && $recemmend.type==10}selected{/if}>素材</option>
                                    <option value="11" {if isset($recemmend) && $recemmend.type==11}selected{/if}>练习</option>
                                    <option value="12" {if isset($recemmend) && $recemmend.type==12}selected{/if}>考试</option>
                                    <option value="14" {if isset($recemmend) && $recemmend.type==14}selected{/if}>资料</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item" id="typesettings" {if isset($recemmend) && $recemmend.type!=0 && $recemmend.type!=4 && $recemmend.type!=8 && $recemmend.type!=10 && $recemmend.type!=12 && $recemmend.type!=14}style="display: none;"{/if}>
                            <label class="layui-form-label">排版：</label>
                            <div class="layui-input-block">
                                <select name="typesetting" lay-verify="typesetting">
                                    <option value="">请选择排版类型</option>
                                    <option value="1" {if isset($recemmend) && $recemmend.typesetting==1}selected{/if}>大图</option>
                                    <option value="2" {if isset($recemmend) && $recemmend.typesetting==2}selected{/if}>宫图</option>
                                    <option value="3" {if isset($recemmend) && $recemmend.typesetting==3}selected{/if}>小图</option>
                                    <option value="4" {if isset($recemmend) && $recemmend.typesetting==4}selected{/if}>左右切换</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item" id="category" {if isset($recemmend) && $recemmend.type!=0}style="display: none;"{/if}>
                            <label class="layui-form-label">一级分类：</label>
                            <div class="layui-input-block">
                                <select name="grade_id" lay-verify="grade_id">
                                    <option value="0" {if isset($recemmend) && $recemmend.grade_id==0}selected{/if}>全部</option>
                                    {volist name='grade_list' id='item'}
                                    <option value="{$item.id}" {if isset($recemmend) && $recemmend.grade_id==$item.id}selected{/if}>{$item.name}</option>
                                    {/volist}
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">展示数量：</label>
                            <div class="layui-input-inline">
                                <input type="number" name="show_count" lay-verify="show_count" value="{if isset($recemmend)}{$recemmend.show_count}{/if}" autocomplete="off" placeholder="超过最大数量不展示" class="layui-input">
                            </div>
                        </div>
                        {/if}
                        <div class="layui-form-item">
                            <label class="layui-form-label">排序：</label>
                            <div class="layui-input-inline">
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
    layList.form.on('select(select)',function (data) {
        switch (data.value) {
            case '0':
                $('#category').show();
                $('#typesettings').show();
                break;
            case '1':
            case '11':
                $('#category').hide();
                $('#typesettings').hide();
                break;
            case '4':
                $('#category').hide();
                $('#typesettings').show();
                break;
            case '8':
                $('#category').hide();
                $('#typesettings').show();
                break;
            case '10':
                $('#category').hide();
                $('#typesettings').show();
                break;
            case '12':
                $('#category').hide();
                $('#typesettings').show();
                break;
            case '14':
                $('#category').hide();
                $('#typesettings').show();
                break;
            case '6':
                $('#category').hide();
                $('#typesettings').hide();
                break;
        }
    });
    $('input[name="file_image"]').change(function () {
        if(this.files.length > 1) return layList.msg('您上传的图片不能大与1张');
        var file=this.files[0];
        if(file){
            var extension = file.name.split('.').pop(),timestamp = new Date().getTime(),key='image/' + timestamp+'.'+extension;
            if(mime_types.indexOf(extension)===-1) return layList.msg('您上传的图片格式不正确');
            Ks3.putObject({
                Key: key,
                File: file,
                ACL: 'public-read',
            }, function (err) {
                if(!err){
                    file_image.parents('.layui-input-block').prepend(JSY.getImgBoxHtml(key));
                    file_image.hide();
                    JSY.LoadEvent();
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
                }else{
                    layList.msg(JSON.stringify(err));
                }
            });
        }
    });
    //提交
    layList.search('save',function(data){
        delete data.file_image;
        if(!data.title) return layList.msg('请输入标题');
        if (data.typesetting !=5) {
            if(!data.type) return layList.msg('请选择类型');
            if(data.show_count<=0) return layList.msg('请填写展示几个内容板块');
            if(data.type==1 || data.type==11|| data.type==6){
                data.typesetting=0;
            }else if(data.type==14){
                if(data.typesetting==1 || data.typesetting==4) return layList.msg('资料不能选择该排版');
            }else{
                if(!data.typesetting) return layList.msg('请选择排版');
            }
            if(data.type==10 && data.typesetting!=2) return layList.msg('素材只能选择宫图');
        }
        layList.basePost(layList.U({a:'save_recemmend',q:{id:id}}),data,function (res) {
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
