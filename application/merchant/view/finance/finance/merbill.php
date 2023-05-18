{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">时间范围</label>
                                        <div class="layui-input-inline" style="width: 260px;">
                                            <input type="text" name="datetime" class="layui-input" id="datetime" placeholder="时间范围">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" lay-submit="search" lay-filter="search"><i class="layui-icon">&#xe615;</i>搜索</button>
                                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" lay-submit="export" lay-filter="export"><i class="layui-icon">&#xe67d;</i>导出</button>
                                        <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <table class="layui-hide" id="userList" lay-filter="userList"></table>
                            <script type="text/html" id="number">
                                {{#  if(d.pm ==0){ }}
                                <span style="color:#FF5722">-{{d.number}}</span>
                                {{# }else{ }}
                                <span style="color:#009688">{{d.number}}</span>
                                {{# } }}
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    layList.form.render();
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    layList.tableList('userList',"{:Url('merbilllist')}",function () {
        return [
            {field: 'id', title: '编号', align: 'center',width:'6%'},
            {field: 'title', title: '标题',align: 'center',width:'10%'},
            {field: 'category', title: '类型',align: 'center',width:'7%'},
            {field: 'number', title: '金额',templet:'#number',align: 'center',width:'6%'},
            {field: 'balance', title: '剩余',align: 'center',width:'6%'},
            {field: 'mark', title: '备注',align: 'center'},
            {field: 'add_time', title: '创建时间',align: 'center',width:'12%'}
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
        location.href=layList.U({a:'save_mer_bell_export',q:{mer_id:where.mer_id,start_time:start_time,end_time:end_time,nickname:where.nickname}});
    });
</script>
{/block}
