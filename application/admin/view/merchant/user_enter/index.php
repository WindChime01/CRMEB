{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">入驻申请</div>
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
                            </div>
                            <div class="layui-inline">
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
                    <script type="text/html" id="merchant_head">
                        <img style="cursor:pointer;" height="50" lay-event='open_image' src="{{d.merchant_head}}">
                    </script>
                    <script type="text/html" id="status">
                        {{# if(d.status==1){ }}
                        <button class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button"><i class="layui-icon">&#xe605;</i>通过</button>
                        <br>
                        通过时间：{{d.success_time}}
                        {{# }else if(d.status==-1){ }}
                        <button class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button"><i class="layui-icon">&#x1006;</i>不通过</button>
                        <br>
                        未通过原因：{{d.fail_message}}
                        <br>
                        未通过时间：{{d.fail_time}}
                        {{# }else if(d.status==2){ }}
                        <button class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button">已生成讲师后台</button>
                        {{# }else{ }}
                        <button lay-event='fail' class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button"><i class="layui-icon">&#x1006;</i>不通过</button>
                        <button lay-event='succ' class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button"><i class="layui-icon">&#xe605;</i>通过</button>
                        {{# } }}
                    </script>
                    <script type="text/html" id="act">
                        {{# if(d.status==1){ }}
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('生成讲师后台','{:Url('create')}?id={{d.id}}',{h:700,w:800})">
                            <i class="iconfont icon-bianji"></i>生成讲师后台
                        </button>
                        {{# } }}
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('查看申请','{:Url('see')}?id={{d.id}}',{h:700,w:800})">
                            <i class="iconfont icon-guanlianlianxi"></i>查看申请
                        </button>
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delstor'>
                            <i class="iconfont icon-shanchu"></i>删除申请
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
    layList.tableList({o:'List', done:function () {}},"{:Url('lecturer_enter_list')}",function (){
        return [
            {field: 'id', title: '编号', align: 'center',width:'5%'},
            {field: 'merchant_name', title: '名称',align: 'center',width:'10%'},
            {field: 'merchant_head', title: '头像',templet:'#merchant_head',align:'center',width:'10%'},
            {field: 'link_tel', title: '电话',align: 'center',width:'10%'},
            {field: 'address', title: '地址',align: 'center'},
            {field: 'status', title: '审核状态',align: 'center',templet:'#status',width:'20%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'20%'}
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
            case 'succ':
                var url=layList.U({a:'succ',q:{id:data.id}});
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
                }, {
                    title:'确定审核通过?',
                    text:'通过后无法撤销，请谨慎操作！',
                    confirm:'审核通过'
                });
                break;
            case 'fail':
                var url=layList.U({a:'fail',q:{id:data.id}});
                $eb.$alert('textarea',{
                    title:'请输入未通过愿意',
                    value:'输入信息不完整或有误!',
                },function(value){
                    $eb.axios.post(url,{message:value}).then(function(res){
                        if(res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success', res.data.msg);
                        }else
                            $eb.$swal('error',res.data.msg||'操作失败!');
                    });
                });
                break;
            case 'open_image':
                $eb.openImage(data.merchant_head);
                break;
        }
    });
</script>
{/block}
