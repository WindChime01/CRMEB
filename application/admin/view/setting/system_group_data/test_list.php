{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-btn-container conrelTable">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delete'>
                            <i class="layui-icon">&#xe640;</i> 删除
                        </button>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    //加载列表
    layList.tableList('List',"{:Url('recemmend_web_content',['id'=>$id])}",function (){
        return [
            {field: 'type_name', title: '类型',align: 'center'},
            {field: 'title', title: '标题',align: 'center'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:100},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'},
        ];
    });
    //快速编辑
    layList.edit(function (obj) {
        var rid=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'sort':
                if(value < 0) return layList.msg('排序不能小于0');
                layList.baseGet(layList.Url({
                    a: 'upRecemmendSort',
                    q: {id:rid,value: value}
                }), function (res) {
                    layList.msg(res.msg);
                });
                break;
        }
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delRecemmend',q:{id:data.id}});
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
                })
                break;
        }
    })
</script>
{/block}
