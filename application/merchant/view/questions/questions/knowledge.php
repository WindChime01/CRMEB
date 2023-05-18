{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">关联专题</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add" onclick="action.open_add('{:Url('relation')}?id='+{$id},'关联知识点')">
                            <i class="layui-icon">&#xe608;</i>关联知识点
                        </button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;" height="50" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="is_light">
                        {{#  if(d.is_light==1){ }}
                        轻专题
                        {{# }else{ }}
                        普通专题
                        {{#  }; }}
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delect'>
                            <i class="layui-icon">&#xe640;</i>移除
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
    var $ = layui.jquery;
    var layer = layui.layer;
    var id="{$id}";
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('knowledge_points')}?id="+id,function (){
        return [
            {field: 'id', title: '编号', width:80,align: 'center'},
            {field: 'types', title: '类型',align: 'center'},
            {field: 'is_light', title: '专题类别',templet:'#is_light',align:'center'},
            {field: 'title', title: '专题标题',align: 'center'},
            {field: 'image', title: '封面',templet:'#image',align: 'center'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:100},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:120},
        ];
    });
    //自定义方法
    var action= {
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['80%', '90%']
                ,title: '关联知识点'
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
                        a: 'add_knowledge_points',
                        q: {id: id, special_ids: ids}
                    }), function (res) {
                        layList.msg(res.msg,function () {
                            location.reload();
                        });
                    });
                }
            });
        }
    };
    //快速编辑
    layList.edit(function (obj) {
        var special_id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'sort':
                if(value < 0) return layList.msg('排序不能小于0');
                layList.baseGet(layList.Url({
                    a: 'up_knowledge_points_sort',
                    q: {id:id,special_id:special_id, value: value}
                }), function (res) {
                    layList.msg(res.msg);
                });
                break;
        }
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'delete_knowledge_points',q:{id:id,special_id:data.id}});
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
            case 'open_image':
                parent.$eb.openImage(data.image);
                break;
        }
    })

</script>
{/block}

