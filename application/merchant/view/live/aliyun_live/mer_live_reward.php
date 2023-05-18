{extend name="public/container"}
{block name="head_top"}
<style>
    .layui-table-cell img{max-width: 100%;height: 50px;cursor: pointer;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">直播间号</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="stream_name" class="layui-input" placeholder="直播标题、关键字、编号">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">时间范围</label>
                                        <div class="layui-input-inline" style="width: 260px;">
                                            <input type="text" name="datetime" class="layui-input" id="datetime" placeholder="时间范围">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                <i class="layui-icon">&#xe615;</i>搜索
                                            </button>
                                            <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                                                <i class="layui-icon">&#xe669;</i>刷新
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="image">
                                <img lay-event='open_image' src="{{d.live_image}}">
                            </script>
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="rawal">
                                    提现
                                </button>
                            </script>
                        </div>
                    </div>
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
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('mer_live_reward_list')}",function (){
        return [
            {field: 'stream_name', title: '直播间ID',align: 'center',width:'8%'},
            {field: 'live_title', title: '直播标题',edit:'live_title',align: 'center'},
            {field: 'live_image', title: '封面',templet:'#image',align: 'center',width:'8%'},
            {field: 'total_price', title: '{$gold_name}总额', align: 'center'},
            {field: 'rawal', title: '可提现', align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'},
        ];
    });
    //查询
    layList.search('search',function(where){
        var arr_time = [];
        var start_time = '';
        var end_time = '';
        if (where.datetime) {
            arr_time = where.datetime.split('~');
            start_time = arr_time[0].trim();
            end_time = arr_time[1].trim();
        }
        layList.reload({
            stream_name: where.stream_name,
            start_time: start_time,
            end_time: end_time
        },true);
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
                            obj.del();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                });
                break;
            case 'open_image':
                $eb.openImage(data.live_image);
                break;
            case 'rawal':
                var url=layList.U({a:'rawal',q:{live_id:data.live_id}});
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
                    title:'确定提现{$gold_name}吗?',
                    text:'提现后无法撤销，请谨慎操作！',
                    confirm:'提现'
                });
                break;
        }
    })
</script>
{/block}

