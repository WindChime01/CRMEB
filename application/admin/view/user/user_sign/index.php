{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">签到记录</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">昵称/ID</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" lay-verify="title" class="layui-input" placeholder="请输入微信昵称、uid">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <div class="layui-btn-group">
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon">&#xe615;</i>搜索</button>
                                    <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" data-type="export">
                                        <i class="layui-icon">&#xe67d;</i>导出
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                    <div class="layui-col-md12">
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

    layList.form.render();

    layList.tableList({o:'List'},layList.U({a:'getUserSignList'}),function (){
        return [
            {field: 'id', title: '编号', width:60,align:'center'},
            {field: 'uid', title: 'UID',align:'center',width:120},
            {field: 'nickname', title: '微信昵称',align:'center'},
            {field: 'title', title: '标题',align:'center'},
            {field: 'balance', title: '金币余量',align:'center'},
            {field: 'number', title: '明细数字',align:'center'},
            {field: 'add_time', title: '签到时间',align:'center'},
        ];
    });
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    $('.layui-btn').on('click', function () {
        var types = $(this).data('type');
        if (types == 'export') {
            var title = $.trim($('input[name="title"]').val());
            location.href = layList.U({a:'getUserSignList',q:{title:title,excel:1}});
        }
    });
    layList.tool(function (layEvent,data,obj) {
        switch (layEvent){
            case 'delete':
                var url=layList.U({a:'delete',q:{id:data.id}});
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
            case 'edit':
                $eb.createModalFrame('编辑',layList.Url({a:'edit',p:{id:data.id}}));
                break;
            case 'open_image':
                $eb.openImage(data.poster);
                break;
        }
    });
</script>
{/block}
