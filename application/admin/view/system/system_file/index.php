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
            <table class="layui-table">
                <thead>
                <tr>
                    <th>类型</th>
                    <th>文件地址</th>
                    <th>校验码</th>
                    <th>上次访问时间</th>
                    <th>上次修改时间</th>
                    <th>上次改变时间</th>
                </tr>
                </thead>
                <tbody class="">
                {volist name="cha" id="vo"}
                <tr>
                    <td>
                        <span style="color: #ff0000">[{$vo.type}]</span>
                    </td>
                    <td>
                        {$vo.filename}
                    </td>
                    <td>
                        {$vo.cthash}
                    </td>
                    <td>
                        {$vo.atime|date='Y-m-d H:i:s',###}
                    </td>
                    <td>
                        {$vo.mtime|date='Y-m-d H:i:s',###}
                    </td>
                    <td>
                        {$vo.ctime|date='Y-m-d H:i:s',###}
                    </td>
                </tr>
                {/volist}
                </tbody>
            </table>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    $('.btn-warning').on('click',function(){
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
