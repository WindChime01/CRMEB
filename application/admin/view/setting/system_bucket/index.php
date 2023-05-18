{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">Bucket列表</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">Bucket名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" class="layui-input" placeholder="Bucket名称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">地域</label>
                                <div class="layui-input-inline">
                                    <select name="endpoint" lay-search="" id="endpoints">
                                        <option value="">区域</option>
                                        {volist name="$endpoint" id="vo" key="k"}
                                        <option value="{$vo}">{$key}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <div class="layui-btn-group">
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                            <i class="layui-icon">&#xe615;</i>搜索
                                        </button>
                                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm pull">
                                            拉取Bucket
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-group conrelTable">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add" onclick="action.open_add('{:Url('create')}','创建Bucket')">
                            <i class="layui-icon">&#xe608;</i>创建Bucket
                        </button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="is_use">
                        {{# if(d.is_use==1){ }}
                        使用中<br/>
                        {{# }else{ }}
                        <button lay-event='userUse' class="layui-btn layui-btn-normal layui-btn-xs use-btn" type="button"><i class="fa fa-check"></i> 使用</button>
                        {{# } }}
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delect'>
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
    var $ = layui.jquery;
    var layer = layui.layer;
    //实例化form
    layList.form.render();
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('bucket_list')}",function (){
        return [
            {field: 'id', title: '编号', width: '8%', align: 'center'},
            {field: 'bucket_name', title: '存储空间名称', align: 'center'},
            {field: 'endpoint', title: '区域',align: 'center', width: '20%'},
            {field: 'domain_name', title: '空间域名Domain',align: 'center', width: '25%'},
            {field: 'is_use', title: '是否使用',templet:'#is_use', align: 'center', width: '10%'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act', width: '12%'}
        ];
    });
    //自定义方法
    var action= {
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['60%', '60%']
                ,title: title
                ,shade: 0.6 //遮罩透明度
                ,maxmin: true //允许全屏最小化
                ,anim: 1 //0-6的动画形式，-1不开启
                ,content: url
                ,end:function() {
                    location.reload();
                }
            });
        }
    };
    //查询
    layList.search('search',function(where){
        layList.reload({
            obtain: where.obtain,
            title: where.title
        },true);
    });
    $('.pull').on('click',function(){
        var endpoint=$('#endpoints option:selected').val();
        if(endpoint=='') return $eb.$swal('error','请先选择区域');
        var url=layList.U({a:'pullBucket',q:{endpoint:endpoint}});
        $eb.$swal('delete',function(){
            $eb.axios.get(url).then(function(res){
                if(res.data.code == 200) {
                    window.location.reload();
                    $eb.$swal('success', res.data.msg);
                }else
                    $eb.$swal('error',res.data.msg||'操作失败!');
            });
        },{
            title:'确定拉取储存空间Bucket吗?',
            text:'使用后无法撤销，请谨慎操作！',
            confirm:'确认'
        });
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'delete',q:{id:data.id}});
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
            case 'userUse':
                var url=layList.U({a:'userUse',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.post(url).then(function(res){
                        if(res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success', res.data.msg);
                        }else
                            $eb.$swal('error',res.data.msg||'操作失败!');
                    });
                },{
                    title:'确定使用该储存空间吗?',
                    text:'使用后无法撤销，请谨慎操作！',
                    confirm:'确认'
                });
                break;
        }
    })

</script>
{/block}

