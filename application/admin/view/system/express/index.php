{extend name="public/container"}
{block name="head"}
<style>
    .layui-table th, .layui-table td {
        text-align: center;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form action="" class="layui-form layui-form-pane">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <input type="text" name="keyword" value="{$params.keyword}" placeholder="请输入物流公司名称或编码" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <button type="submit" class="layui-btn layui-btn-sm layui-btn-normal"><i class="fa fa-search" ></i>搜索</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('create')}')">添加物流公司</button>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <table class="layui-table">
                        <thead>
                            <tr>
                                <th>编号</th>
                                <th>物流公司名称</th>
                                <th>编码</th>
                                <th>排序</th>
                                <th>状态</th>
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
                                {$vo.name}
                            </td>
                            <td>
                                {$vo.code}
                            </td>
                            <td>
                                {$vo.sort}
                            </td>
                            <td>
                                <i class="fa {eq name='vo.is_show' value='1'}fa-check text-navy{else/}fa-close text-danger{/eq}"></i>
                            </td>
                            <td>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit',array('id'=>$vo['id']))}')">
                                    <i class="iconfont icon-bianji"></i> 编辑
                                </button>
                                <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" data-url="{:Url('delete',array('id'=>$vo['id']))}">
                                    <i class="iconfont icon-shanchu"></i> 删除
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
