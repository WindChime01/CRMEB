{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">
            <div class="layui-btn-group">
                <a class="layui-btn layui-btn-normal layui-btn-sm" href="{:Url('index')}"><i class="layui-icon">&#xe68e;</i>规则首页</a>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加规则','{:Url('create',array('cid'=>0))}')"><i class="layui-icon">&#xe608;</i>添加规则</button>
            </div>
        </div>
        <div class="layui-card-body">
            <div class="ibox-content">
                <div class="row">
                    <div class="m-b m-l">
                        <form action="" class="form-inline">

                            <select name="is_show" aria-controls="editable" class="form-control input-sm">
                                <option value="">是否显示</option>
                                <option value="1" {eq name="params.is_show" value="1"}selected="selected"{/eq}>显示</option>
                                <option value="0" {eq name="params.is_show" value="0"}selected="selected"{/eq}>不显示</option>
                            </select>
                        <div class="input-group">
                            <input type="text" name="keyword" value="{$params.keyword}" placeholder="请输入关键词/规则ID/父级ID" class="input-sm form-control"> <span class="input-group-btn">
                                    <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon">&#xe615;</i>搜索</button> </span>
                        </div>
                        </form>
                    </div>

                </div>
                <div class="table-responsive">
                    <table class="table table-striped  table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center" style="width:60px;">编号</th>
                            <th class="text-center">按钮名</th>
                            <th class="text-center">父级</th>
                            <th class="text-center">模块名</th>
                            <th class="text-center">控制器名</th>
                            <th class="text-center">方法名</th>
                            <th class="text-center">是否菜单</th>
                            <th class="text-center">操作</th>
                        </tr>
                        </thead>
                        <tbody class="">
                        {volist name="list" id="vo"}
                        <tr>
                            <td class="text-center">
                                {$vo.id}
                            </td>
                            <td class="text-center">
                                <a href="{:Url('index',array('pid'=>$vo['id']))}">{$vo.menu_name}</a>
                            </td>
                            <td class="text-center">
                                {$vo.pid}
                            </td>
                            <td class="text-center">
                                {$vo.module}
                            </td>
                            <td class="text-center">
                                {$vo.controller}
                            </td>
                            <td class="text-center">
                                {$vo.action}
                            </td>
                            <td class="text-center">
                                <i class="fa {eq name='vo.is_show' value='1'}fa-check text-navy{else/}fa-close text-danger{/eq}"></i>
                            </td>
                            <td class="text-center">
                                <button class="layui-btn layui-btn-normal layui-btn-xs" type="button"  onclick="$eb.createModalFrame('添加子菜单','{:Url('create',array('cid'=>$vo['id']))}')"><i class="layui-icon">&#xe608;</i>添加子菜单</button>
                                <button class="layui-btn layui-btn-normal layui-btn-xs" type="button"  onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}')"><i class="layui-icon">&#xe642;</i>编辑</button>
                                <button class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}" type="button"><i class="layui-icon">&#xe640;</i>删除
                                </button>
                            </td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                </div>
                {include file="public/inner_page"}
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
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
