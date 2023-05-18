{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">直播审核</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">直播名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="store_name" class="layui-input" placeholder="直播名称、直播间号">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                <i class="layui-icon">&#xe615;</i>搜索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--产品列表-->
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="_id">
                                <p>{{d.id}}</p>
                            </script>
                            <!--图片-->
                            <script type="text/html" id="image">
                                <img style="cursor: pointer;" height="50" lay-event="open_image" src="{{d.live_image}}">
                            </script>
                            <script type="text/html" id="status">
                                {{# if(d.status==0){ }}
                                <button lay-event='fail' class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button"><i class="layui-icon">&#x1006;</i>不通过</button>
                                <button lay-event='succ' class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button"><i class="layui-icon">&#xe605;</i>通过</button>
                                {{# }else if(d.status==-1){ }}
                                <button class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button"><i class="layui-icon">&#x1006;</i>不通过</button>
                                <br>
                                原因：{{d.fail_message}}
                                <br>
                                时间：{{d.fail_time}}
                                {{# }else if(d.status==1){ }}
                                <button class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button"><i class="layui-icon">&#xe605;</i>通过</button>
                                {{# } }}
                            </script>
                            <!--操作-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List', "{:Url('get_live_audit_list')}", function () {
        return [
            {field: 'id', title: 'ID', width: '6%', templet: '#_id', align: 'center'},
            {field: 'live_title', title: '直播间标题', align: 'center'},
            {field: 'stream_name', title: '直播间号', align: 'center', width: '8%'},
            {field: 'live_image', title: '图片', templet: '#image', align: 'center', width: '8%'},
            {field: 'live_strar_time', title: '直播开始时间',align: 'center', width: '12%'},
            {field: 'live_end_time', title: '直播结束时间',  align: 'center', width: '12%'},
            {field: 'status', title: '状态', templet: "#status", align: 'center', width: '20%'},
        ];
    });
    //查询
    layList.search('search', function (where) {
        layList.reload(where, true);
    });
    //点击事件绑定
    layList.tool(function (event, data, obj) {
        switch (event) {
            case 'open_image':
                $eb.openImage(data.live_image);
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
                },{
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
        }
    });

</script>
{/block}
