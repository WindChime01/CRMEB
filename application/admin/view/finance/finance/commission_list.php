{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">分销用户佣金统计</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">昵称/ID</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="nickname" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">佣金范围</label>
                                <div class="layui-input-inline" style="width: 180px;">
                                    <input type="number" name="price_min" placeholder="￥" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid">-</div>
                                <div class="layui-input-inline" style="width: 180px;">
                                    <input type="number" name="price_max" placeholder="￥" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">佣金排序</label>
                                <div class="layui-input-inline">
                                    <select name="order">
                                        <option value="1" selected="">升序</option>
                                        <option value="0">降序</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <div class="layui-btn-group">
                                        <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search"><i class="layui-icon">&#xe615;</i>搜索</button>
                                        <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <table id="List" lay-filter="List"></table>
                    <script type="text/html" id="barDemo">
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" lay-event="see" onclick="$eb.createModalFrame('{{d.nickname}}-详情','{:Url('content_info')}?uid={{d.uid}}')"><i class="layui-icon">&#xe60a;</i>详情</button>
                    </script>
                </div>
            <div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    layList.form.render();
    layList.tableList('List',"{:Url('get_commission_list')}",function () {
        return [
            {field: 'nickname', title: '昵称/姓名',align: 'center'},
            {field: 'brokerage_price', title: '总佣金金额',align: 'center'},
            {field: 'ex_price', title: '提现佣金',align: 'center'},
            {field: 'extract_price', title: '提现到账佣金',align: 'center'},
            {title: '操作',align:'center',toolbar:'#barDemo'},
        ];
    });
    layList.search('search',function(where){
        if(where.price_max < where.price_min) layList.msg('请输入正确的佣金范围');
        layList.reload(where,true);
    });
</script>
{/block}
