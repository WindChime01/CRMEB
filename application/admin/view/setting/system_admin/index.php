{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">管理员列表</div>
        <div class="layui-card-body">
            <form action="" class="layui-form layui-form-pane">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <select name="roles">
                            <option value="">身份</option>
                            {volist name="$role" id="vo" key="k"}
                                <option value="{$key}" {eq name="where.roles" value="$key"}selected="selected"{/eq}>{$vo}</option>
                            {/volist}
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="name" value="{$where.name}" placeholder="请输入姓名或者账号" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm"><i class="layui-icon layui-icon-search"></i>搜索</button>
                    </div>
                </div>
            </form>
            <div class="layui-btn-container">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加管理员','{:Url('create')}')"><i class="layui-icon layui-icon-add-1"></i>添加管理员</button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh-1"></i>刷新</button>
            </div>
            <table class="layui-table">
                <thead>
                <tr>
                    <th>姓名</th>
                    <th>账号</th>
                    <th>身份</th>
                    <th>最后一次登陆时间</th>
                    <th>最后一次登陆ip</th>
                    <th>开启</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {volist name="list" id="vo"}
                <tr>
                    <td>
                        {$vo.real_name}
                    </td>
                    <td>
                        {$vo.account}
                    </td>
                    <td>
                        {$vo.roles}
                    </td>
                    <td>
                        {$vo.last_time? date('Y/m/d H:i',$vo.last_time) : ''}
                    </td>
                    <td>
                        {$vo.last_ip}
                    </td>
                    <td>
                        <i class="layui-icon {eq name='vo.status' value='1'}layui-icon-ok text-navy{else/}layui-icon-close text-danger{/eq}" style="font-size: 20px;"></i>
                    </td>

                    <td>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}')"><i class="layui-icon layui-icon-edit"></i>编辑</button>
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}"><i class="layui-icon layui-icon-delete"></i>删除</button>
                    </td>
                </tr>
                {/volist}
                </tbody>
            </table>
            {include file="public/inner_page"}
                    <?php /*  <form action="" class="form-inline">
                            <i class="fa fa-search" style="margin-right: 10px;"></i>
                            <select name="is_show" aria-controls="editable" class="form-control input-sm">
                                <option value="">是否显示</option>
                                <option value="1" {eq name="params.is_show" value="1"}selected="selected"{/eq}>显示</option>
                                <option value="0" {eq name="params.is_show" value="0"}selected="selected"{/eq}>不显示</option>
                            </select>
                            <select name="access" aria-controls="editable" class="form-control input-sm">
                                <option value="">子管理员是否可用</option>
                                <option value="1" {eq name="params.access" value="1"}selected="selected"{/eq}>可用</option>
                                <option value="0" {eq name="params.access" value="0"}selected="selected"{/eq}>不可用</option>
                            </select>
                        <div class="input-group">
                            <input type="text" name="keyword" value="{$params.keyword}" placeholder="请输入关键词" class="input-sm form-control"> <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                        </div>
                        </form>  */ ?>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
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
