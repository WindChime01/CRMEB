{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">讲师后台</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">讲师名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" class="layui-input" placeholder="请输入讲师名称">
                                </div>
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon">&#xe615;</i>搜索
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="mer_avatar">
                        <img style="cursor:pointer;" width="50" height="50" lay-event='open_image' src="{{d.mer_avatar}}">
                    </script>
                    <script type="text/html" id="status">
                        {{# if(d.status==1){ }}
                        <button lay-event='modify_error' class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button">[正常]</button>
                        {{# }else{ }}
                        <button lay-event='modify_success' class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button">[锁定]</button>
                        {{# } }}
                    </script>
                    <script type="text/html" id="estate">
                        {{# if(d.estate==1){ }}
                        <i class="fa fa-check text-navy"></i>
                        {{# }else{ }}
                        <i class="fa fa-close text-danger"></i>
                        {{# } }}
                    </script>
                    <script type="text/html" id="act">
                        <a class="layui-btn layui-btn-normal layui-btn-xs" target="_blank" href="{:url('login')}?id={{d.id}}">访问</a>
                        <button class="layui-btn layui-btn-primary layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('edit')}?id={{d.id}}',{h:700,w:800})">编辑</button>
                        <button class="layui-btn layui-btn-warm success layui-btn-xs" lay-event='reset_pwd'>重置密码</button>
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delstor'>
                            <i class="iconfont icon-shanchu"></i>删除讲师后台
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
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('lecturer_merchant_list')}",function (){
        return [
            {field: 'id', title: '编号', align: 'center'},
            {field: 'mer_name', title: '名称',align: 'center'},
            {field: 'mer_avatar', title: '头像',templet:'#mer_avatar',align:'center'},
            {field: 'mer_phone', title: '电话',align: 'center'},
            {field: 'mer_address', title: '地址',align: 'center'},
            {field: 'status', title: '后台状态',align: 'center',templet:'#status'},
            {field: 'estate', title: '讲师状态',align: 'center',templet:'#estate'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',minWidth:300}
        ];
    });
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
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
            case 'reset_pwd':
                var url=layList.U({a:'reset_pwd',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.post(url).then(function(res){
                        if(res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success', res.data.msg);
                        }else
                            $eb.$swal('error',res.data.msg||'操作失败!');
                    });
                },{'title':'您确定重置选择讲师后台的密码吗？','text':'重置后的密码为123456','confirm':'您确定重置密码吗？'});
                break;
            case 'modify_success':
                var url=layList.U({a:'modify',q:{id:data.id,status:1}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要修改讲师后台的状态吗？','text':'请谨慎操作！','confirm':'是的，我要修改'});
                break;
            case 'modify_error':
                var url=layList.U({a:'modify',q:{id:data.id,status:0}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要修改讲师后台的状态吗？','text':'请谨慎操作！','confirm':'是的，我要修改'});
                break;
            case 'open_image':
                $eb.openImage(data.merchant_head);
                break;
        }
    });
</script>
{/block}
