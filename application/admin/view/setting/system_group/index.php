{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
	<div class="layui-card">
		<div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加数据组','{:Url('create')}')">
                            <i class="layui-icon">&#xe608;</i>添加数据组
                        </button>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <table class="layui-table">
						<thead>
						<tr>
							<th>编号</th>
							<th>KEY</th>
							<th>数据组名称</th>
							<th>简介</th>
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
								{$vo.config_name}
							</td>
							<td>
								{$vo.name}
							</td>
							<td>
								{$vo.info}
							</td>
							<td>
								<a class="layui-btn layui-btn-normal layui-btn-xs" href="{:Url('setting.systemGroupData/index',array('gid'=>$vo['id']))}"><i class="layui-icon">&#xe60a;</i>数据列表</a>
								<button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}')" >
                                    <i class="layui-icon">&#xe642;</i>编辑
                                </button>
								<button type="button" class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}">
                                    <i class="layui-icon">&#xe640;</i>删除
                                </button>
							</td>
						</tr>
						{/volist}
						</tbody>
					</table>
                    {include file="public/inner_page"}
                </div>
            </div>
		</div>
	</div>
</div>
{/block}
{block name="script"}
<script>
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
