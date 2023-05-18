{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
        <div class="layui-card">
            <div class="layui-card-header"></div>
            <div class="layui-card-body">
                <form action="" class="layui-form layui-form-pane">
                    <div class="layui-form-item">
                        <label class="layui-form-label">回复类型</label>
                        <div class="layui-input-inline">
                            <select name="type">
                                <option value="">请选择</option>
                                <option value="text" {eq name="$where.type" value="text"}selected="selected"{/eq}>文字消息</option>
                                <option value="image" {eq name="$where.type" value="image"}selected="selected"{/eq}>图片消息</option>
                                <option value="news" {eq name="$where.type" value="news"}selected="selected"{/eq}>图文消息</option>
                                <option value="voice" {eq name="$where.type" value="voice"}selected="selected"{/eq}>声音消息</option>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="key" value="{$where.key}" placeholder="请输入关键词" class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon">&#xe615;</i>搜索</button>
                        </div>
                    </div>
                </form>
                <div class="layui-btn-group">
                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.href='{:Url('add_keyword')}'"><i class="layui-icon">&#xe608;</i>添加关键字</button>
                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                </div>
                <table class="layui-table">
                    <thead>
                        <tr>
                            <th>关键字</th>
                            <th>回复类型</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    {volist name="list" id="vo"}
                    <tr>
                        <td>{$vo.key}</td>
                        <td>
                            {switch name="$vo['type']" }
                            {case value="text" break="1"}文字消息{/case}
                            {case value="voice" break="1"}声音消息{/case}
                            {case value="image" break="1"}图片消息{/case}
                            {case value="news" break="1"}图文消息{/case}
                            {/switch}
                        </td>
                        <td>
                            {switch name="$vo['status']" }
                            {case value="1" break="1"}<i class="fa fa-check text-navy"></i>{/case}
                            {case value="0" break="1"}<i class="fa fa-close text-danger"></i>{/case}
                            {/switch}
                        </td>
                        <td>
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="window.location.href='{:Url('info_keyword',array('key'=>$vo['key']))}'" ><i class="iconfont icon-bianji"></i>编辑</button>
                            <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}"><i class="iconfont icon-shanchu"></i>删除</button>
                        </td>
                    </tr>
                    {/volist}
                    </tbody>
                </table>
                {include file="public/inner_page"}
            </div>
        </div>
</div>
{/block}
{block name="script"}
<script>
    parent.$('.J_menuTab').each(function () {
        if ($(this).hasClass('active')) {
            $('.layui-card-header').text($(this).text());
            return false;
        }
    });
    layui.form.render();
    $('.layui-btn-danger').on('click',function(){
        window.t = $(this);
        var _this = $(this),url =_this.data('url');
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.status == 200 && res.data.code == 200) {
                    $eb.$swal('success',res.data.msg);
                    _this.parents('tr').remove();
                }else
                    return Promise.reject(res.data.msg || '删除失败')
            }).catch(function(err){
                $eb.$swal('error',err);
            });
        })
    });
</script>
{/block}