{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">专题列表</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container conrelTable">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="learning_records">
                            <i class="layui-icon layui-icon-set"></i> 学习记录
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
    layList.tableList({o:'List', done:function () {}},"{:Url('getUserSpecialList')}?uid="+uid,function (){
        return [
            {field: 'id', title: '编号', width:'6%', align: 'center'},
            {field: 'types', title: '类型' , width:'8%', align: 'center'},
            {field: 'title', title: '专题标题'},
            {field: 'image', title: '封面',templet:'#image', align: 'center',width:'10%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'20%'}
        ];
    },10);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'del_special_buy',q:{uid:uid,special_id:data.id}});
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
            case 'learning_records':
                layer.open({
                    type: 2,
                    title: '学习记录',
                    content: "{:Url('special.special_type/learningRecords')}?id="+data.id + "&uid="+uid,
                    area: ['95%', '95%'],
                    maxmin: true
                });
                break;
        }
    })
</script>
{/block}
