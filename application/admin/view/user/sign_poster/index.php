{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">签到海报</div>
        <div class="layui-card-body">
                <div class="layui-btn-group">
                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加海报','{:Url('create')}',{w:800,h:600})" ><i class="layui-icon">&#xe608;</i>添加海报</button>
                    <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                </div>
            <table class="layui-hide" id="List" lay-filter="List"></table>
            <script type="text/html" id="act">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon">&#xe642;</i>编辑</button>
                <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete"><i class="layui-icon">&#xe640;</i>删除</button>
            </script>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    layList.form.render();

    layList.tableList({o:'List'},layList.U({a:'getSignPosterList'}),function (){
        return [
            {field: 'id', title: '编号', width:60,align:'center'},
            {field: 'sign_time', title: '签到时间',align:'center'},
            {field: 'poster', title: '海报', event:'open_image', align: 'center', templet: '<div><img class="avatar" style="cursor: pointer;" height="50" class="open_image" data-image="{{d.poster}}" src="{{d.poster}}" ></div>'},
            {field: 'sign_talk', title: '语录',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'},
        ];
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
