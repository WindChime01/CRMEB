{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  >
        <div class="layui-col-md12">
            <div class="layui-card">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm " onclick="window.location.reload()">
                    <i class="layui-icon">&#xe669;</i>刷新
                </button>
                <div class="layui-card-body">
                    <table class="layui-hide" id="userList" lay-filter="userList"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    layList.tableList('userList',"{:Url('percentageData')}?special_id={$special_id}"+"&uid={$uid}"+"&type={$type}"+"&is_light={$is_light}",function () {
        return [
            {field: 'title', title: '素材名称',align: 'left'},
            {field: 'percentage', title: '学习进度%',align: 'center',width:100}
        ];
    });
</script>
{/block}
