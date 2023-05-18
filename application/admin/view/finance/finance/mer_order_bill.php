{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">分成记录</div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">讲师名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="nickname" class="layui-input" placeholder="讲师名称">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">时间范围</label>
                        <div class="layui-input-inline" style="width: 260px;">
                            <input type="text" name="datetime" class="layui-input" id="datetime" placeholder="时间范围">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">讲师选择</label>
                        <div class="layui-input-inline">
                            <select name="mer_id">
                                <option value="">全部</option>
                                {volist name='selectList' id='val'}
                                    <option value="{$val.id}">{$val.mer_name}</option>
                                {/volist}
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" lay-submit="search" lay-filter="search"><i class="layui-icon">&#xe615;</i>搜索</button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" lay-submit="export" lay-filter="export"><i class="layui-icon">&#xe67d;</i>导出</button>
                        <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                    </div>
                </div>
            </form>
            <table id="userList" lay-filter="userList"></table>
            <script type="text/html" id="status">
                {{#  if(d.status ==0){ }}
                <span style="color:#FF5722">退还</span>
                {{# }else{ }}
                <span style="color:#009688">增加</span>
                {{# } }}
            </script>
            <script type="text/html" id="barDemo">
                {{#  if(d.type ==0 || d.type ==1 || d.type ==2){ }}
                <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="$eb.createModalFrame('订单详情','{:Url('order.store_order/order_info')}?oid={{d.oid}}')"><i class="layui-icon">&#xe60a;</i>订单详情</button>
                {{# }else if(d.type ==3){ }}
                <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="$eb.createModalFrame('订单详情','{:Url('order.data_download_order/order_info')}?oid={{d.oid}}')"><i class="layui-icon">&#xe60a;</i>订单详情</button>
                {{# }else if(d.type ==4){ }}
                <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="$eb.createModalFrame('订单详情','{:Url('ump.event_registration/order_info')}?oid={{d.oid}}')"><i class="layui-icon">&#xe60a;</i>订单详情</button>
                {{# }else if(d.type ==5){ }}
                <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="$eb.createModalFrame('订单详情','{:Url('order.test_paper_order/order_info')}?oid={{d.oid}}')"><i class="layui-icon">&#xe60a;</i>订单详情</button>
                {{# } }}
            </script>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    layList.form.render();
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    layList.tableList('userList',"{:Url('merOrderBillList')}",function () {
        return [
            {field: 'mer_id', title: '讲师ID', align: 'center',width:'6%'},
            {field: 'mer_name', title: '讲师名称',align: 'center',width:'12%'},
            {field: 'title', title: '订单类型',align: 'center',width:'8%'},
            {field: 'total_price', title: '订单总价',align: 'center',width:'11%'},
            {field: 'pay_price', title: '实际金额',align: 'center',width:'11%'},
            {field: 'refund_price', title: '退款金额',align: 'center',width:'11%'},
            {field: 'price', title: '分成/退还',align: 'center',width:'10%'},
            {field: 'status', title: '状态',align: 'center',templet:'#status',width:'8%'},
            {field: 'add_time', title: '创建时间',align: 'center',width:'13%'},
            {title: '操作',align:'center',toolbar:'#barDemo'}
        ];
    });

    layList.search('search',function(where){
        var arr_time = [];
        var start_time = '';
        var end_time = '';
        if (where.datetime) {
            arr_time = where.datetime.split('~');
            start_time = arr_time[0].trim();
            end_time = arr_time[1].trim();
        }
        layList.reload({
            mer_id: where.mer_id,
            start_time: start_time,
            end_time: end_time,
            nickname: where.nickname
        },true);
    });
    layList.search('export',function(where){
        var arr_time = [];
        var start_time = '';
        var end_time = '';
        if (where.datetime) {
            arr_time = where.datetime.split('~');
            start_time = arr_time[0].trim();
            end_time = arr_time[1].trim();
        }
        location.href=layList.U({a:'save_mer_order_bell_export',q:{mer_id:where.mer_id,start_time:start_time,end_time:end_time,nickname:where.nickname}});
    });
</script>
{/block}
