{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">证书记录</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">用户搜索</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="用户昵称、UID">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">证书列表</label>
                                        <div class="layui-input-inline">
                                            <select name="cid" id="cid">
                                                <option value="0">全部</option>
                                                {volist name='certificate' id='vc'}
                                                <option value="{$vc.id}">{$vc.title}</option>
                                                {/volist}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <div class="layui-btn-group">
                                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                    <i class="layui-icon">&#xe615;</i>搜索
                                                </button>
                                                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm export" data-type="export" lay-filter="export">
                                                    <i class="layui-icon">&#xe67d;</i>导出
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="act">
                                {{# if(d.status==1){}}
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event='revoke'>
                                    <i class="fa fa-paste"></i>撤销
                                </button>
                                {{# }}}
                                <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delect'>
                                    <i class="layui-icon">&#xe640;</i>删除
                                </button>
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    //实例化form
    layList.form.render();
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('getCertificateRecord')}",function (){
        return [
            {field: 'id', title: '编号', width: '8%', align: 'center'},
            {field: 'title', title: '证书标题', align: 'center'},
            {field: 'source', title: '来源', align: 'center', width: '10%'},
            {field: 'uids', title: '昵称/UID', align: 'center', width: '10%'},
            {field: 'obtains', title: '获取方式', align: 'center', width: '10%'},
            {field: 'statu', title: '状态', align: 'center', width: '10%'},
            {field: 'addTime', title: '获得时间', align: 'center', width: '12%'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act', width: '12%'}
        ];
    });
    //查询
    layList.search('search',function(where){
        layList.reload({
            cid: where.cid,
            title: where.title
        },true);
    });

    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'deleteRecord',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            obj.del();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                });
                break;
            case 'revoke':
                var url=layList.U({a:'revokeRecord',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            location.reload();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{
                    title:'确定要撤销证书记录吗?',
                    text:'确认后无法更改，请谨慎操作！',
                    confirm:'确认'
                });
                break;
        }
    })
    $('.layui-btn').on('click', function () {
        var types = $(this).data('type');
        if (types == 'export') {
            var title = $.trim($('input[name="title"]').val());
            var cid = $('#cid').val();
            location.href= layList.U({a:'getCertificateRecord',q:{title:title,cid:cid,excel:1}});
        }
    });
</script>
{/block}

