{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">

                 <div class="layui-card-body">
                     <div class="layui-btn-container">
                         <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
                     </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var uid="{$uid}";
    var $ = layui.jquery;
    var layer = layui.layer;
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('get_member_record')}?uid="+uid,function () {
        return [
            {field: 'id', title: '编号', width:60,align: 'center'},
            {field: 'title', title: '类别',align: 'center', width:90},
            {field: 'validity', title: '有期期',align: 'center'},
            {field: 'price', title: '优惠价',align:'center'},
            {field: 'code', title: '卡号',align: 'center'},
            {field: 'add_time', title: '购买时间',align: 'center'}
        ];
    });
</script>
{/block}
