{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header"></div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">昵称/ID</label>
                        <div class="layui-input-inline">
                            <input type="text" name="nickname" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">时间范围</label>
                        <div class="layui-input-inline" style="width: 260px;">
                            <input type="text" name="datetime" class="layui-input" id="datetime" placeholder="时间范围">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">筛选类型</label>
                        <div class="layui-input-inline">
                            <select name="type">
                                <option value="">全部</option>
                                {volist name='selectList' id='val'}
                                    {if $val['type'] eq 'brokerage' }
                                    <option value="{$val.type}">推广返佣</option>
                                    {else /}
                                    <option value="{$val.type}">{$val.title}</option>
                                    {/if}
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
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    parent.$('.J_menuTab').each(function () {
        if ($(this).hasClass('active')) {
            $('.layui-card-header').text($(this).text())
            return false;
        }
    });
    var category="{$category}";
    layList.form.render();
    layList.date({
        elem: '#datetime',
        type: 'datetime',
        range: '~'
    });
    layList.tableList('userList',"{:Url('billlist')}?category={$category}",function () {
        return [
            {field: 'uid', title: '用户ID', align: 'center'},
            {field: 'nickname', title: '昵称',align: 'center'},
            {field: 'name', title: '用户姓名',align: 'center'},
            {field: 'category', title: '类型',align: 'center'},
            {field: 'number', title: '{$gold_name}',templet:'#number',align: 'center'},
            {field: 'title', title: '账单标题',align: 'center'},
            {field: 'mark', title: '备注',align: 'center'},
            {field: 'add_time', title: '创建时间',align: 'center'}
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
            type: where.type,
            category: where.category,
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
        location.href=layList.U({a:'save_bell_export',q:{type:where.type,start_time:start_time,end_time:end_time,nickname:where.nickname,category:category}});
    });
</script>
{/block}
