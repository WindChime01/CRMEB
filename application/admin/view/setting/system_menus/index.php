{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">权限规则</div>
        <div class="layui-card-body">
            <form action="" class="layui-form layui-form-pane">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <select name="is_show">
                            <option value="">是否显示</option>
                            <option value="1" {eq name="params.is_show" value="1"}selected="selected"{/eq}>显示</option>
                            <option value="0" {eq name="params.is_show" value="0"}selected="selected"{/eq}>不显示</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="keyword" value="{$params.keyword}" placeholder="请输入关键词/规则ID/父级ID" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon layui-icon-search"></i>搜索</button>
                    </div>
                </div>
            </form>
            <div class="layui-btn-container">
                <a class="layui-btn layui-btn-normal layui-btn-sm" href="{:Url('index')}"><i class="layui-icon layui-icon-home"></i>规则首页</a>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加规则','{:Url('create',array('cid'=>0))}')"><i class="layui-icon layui-icon-add-1"></i>添加规则</button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                    <i class="layui-icon">&#xe669;</i>刷新
                </button>
            </div>
            <table class="layui-table">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>按钮名</th>
                    <th>父级</th>
                    <th>模块名</th>
                    <th>控制器名</th>
                    <th>方法名</th>
                    <th>是否菜单</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {volist name="list" id="vo"}
                <tr>
                    <td>
                        {$vo.id}
                    </td>
                    <td>
                        <a href="{:Url('index',array('pid'=>$vo['id']))}">{$vo.menu_name}</a>
                    </td>
                    <td>
                        {$vo.pid}
                    </td>
                    <td>
                        {$vo.module}
                    </td>
                    <td>
                        {$vo.controller}
                    </td>
                    <td>
                        {$vo.action}
                    </td>
                    <td>
                        <i class="layui-icon {eq name='vo.is_show' value='1'}layui-icon-ok text-navy{else/}layui-icon-close text-danger{/eq}" style="font-size: 20px;"></i>
                    </td>
                    <td>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('添加子菜单','{:Url('create',array('cid'=>$vo['id']))}')"><i class="layui-icon layui-icon-add-1"></i>添加子菜单</button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}')"><i class="layui-icon layui-icon-edit"></i>编辑</button>
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}"><i class="layui-icon layui-icon-delete"></i>删除</button>
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
    layui.form.render();
    $('.layui-btn-danger').on('click',function(){
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
