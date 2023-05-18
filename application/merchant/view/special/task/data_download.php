{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">资料列表</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container conrelTable">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" onclick="action.open_add('{:Url('download')}?id='+{$id}+'&relationship='+relationship,'选择资料')">
                            选择资料
                        </button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delstor'>
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
    var id="{$id}",relationship="{$relationship}";
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('getRelationDataDownloadList')}?id="+id+"&relationship="+relationship,function (){
        return [
            {field: 'id', title: '编号', width:'8%',align: 'center'},
            {field: 'title', title: '资料标题'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:100},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'}
        ];
    },10);
    //快速编辑
    layList.edit(function (obj) {
        var test_id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'sort':
                if(value < 0) return layList.msg('排序不能小于0');
                layList.baseGet(layList.Url({
                    a: 'upRelationSort',
                    q: {id:id,data_id:test_id, value: value,relationship:relationship}
                }), function (res) {
                    layList.msg(res.msg);
                });
                break;
        }
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
                var url=layList.U({a:'delRelation',q:{id:id,data_id:data.id,relationship:relationship}});
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
                });
                break;
        }
    })
    //监听并执行排序
    layList.sort(['id','sort'],true);
    var action={
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['80%', '90%']
                ,title: '关联资料'
                ,shade: 0.6 //遮罩透明度
                ,maxmin: true //允许全屏最小化
                ,anim: 1 //0-6的动画形式，-1不开启
                ,content: url,
                btn: '确定',
                btnAlign: 'c', //按钮居中
                closeBtn:1,
                yes: function(){
                    layer.closeAll();
                    var source_tmp = $("#check_source_tmp").val();
                    var source_tmp_list = JSON.parse(source_tmp);
                    var arr=[];
                    for(var i=0;i<source_tmp_list.length;i++){
                        arr.push(source_tmp_list[i].id);
                    }
                    var ids=arr.join(',');
                    layList.baseGet(layList.Url({
                        a: 'addRelation',
                        q: {id: id, ids: ids,relationship:relationship}
                    }), function (res) {
                        layList.msg(res.msg,function () {
                            location.reload();
                        });
                    });
                }
            });
        },
        refresh:function () {
            layList.reload();
        }
    };
</script>
{/block}
