{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">试卷列表</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container conrelTable">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="answers">
                            <i class="layui-icon layui-icon-set"></i> 查看答题
                        </button>
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delect'>
                            <i class="layui-icon">&#xe640;</i> 移除
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
    var uid="{$uid}";
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('getUserTestPaperList')}?uid="+uid,function (){
        return [
            {field: 'id', title: '编号', width:'8%',align: 'center'},
            {field: 'types', title: '类型', width:'12%', align: 'center'},
            {field: 'title', title: '试卷标题'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'20%'}
        ];
    },10);
    //查询
    layList.search('search',function(where){
        layList.reload({
            type:where.type,
            title: where.title
        },true);
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'removeTestPaper',q:{uid:uid,test_id:data.id}});
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
            case 'answers':
                layer.open({
                    type: 2,
                    title: '查看答题',
                    content: "{:Url('questions.test_paper/answerNotes')}?type="+data.type + "&test_id="+data.id + "&uid="+uid,
                    area: ['95%', '95%'],
                    maxmin: true
                });
                break;
        }
    })
</script>
{/block}
