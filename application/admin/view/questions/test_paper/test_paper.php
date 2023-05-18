{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delect'>
                            <i class="layui-icon">&#xe640;</i>删除
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
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('getTestPaperList',['id'=>$id,'type'=>$type])}",function (){
        return [
            {field: 'types', title: '题型',align: 'center',width:100},
            {field: 'title', title: '题干'},
            {field: 'score', title: '分数',align: 'center',width:100},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:100},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:120},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value,test:1}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    };
    //查询
    layList.search('search',function(where){
        layList.reload({
            pid: where.pid,
            title: where.title
        },true);
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'sort':
                if(value < 0) return layList.msg('排序不能小于0');
                action.set_value('sort',id,value);
                break;
        }
    });

    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'TestPaperDelete',q:{id:data.id}});
                parent.$eb.$swal('delete',function(){
                    parent.$eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            parent.$eb.$swal('success',res.data.msg);
                            obj.del();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        parent.$eb.$swal('error',err);
                    });
                });
                break;
        }
    })

</script>
{/block}

