{extend name="public/container"}
{block name="head"}
<style>
    .layui-table-cell img{max-width: 100%;height: 50px;cursor: pointer;}
    .layui-table-cell .layui-btn-container{overflow: hidden;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">资料审核</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">资料搜索</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" class="layui-input" placeholder="资料名称、简介">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">资料分类</label>
                                <div class="layui-input-inline">
                                    <select name="cate_id" lay-search="">
                                        <option value="0">全部</option>
                                        {volist name='cate_list' id='vc'}
                                            <option {if $vc.pid==0}disabled{/if} value="{$vc.id}">{$vc.html}{$vc.title}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">讲师</label>
                                <div class="layui-input-inline">
                                    <select name="mer_id" lay-search="">
                                        <option value="">全部</option>
                                        {volist name='mer_list' id='vc'}
                                        <option  value="{$vc.id}">{$vc.mer_name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-inline">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="0">未审核</option>
                                        <option value="-1">未通过</option>
                                    </select>
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
                                    <div class="layui-btn-group">
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                            <i class="layui-icon layui-icon-search"></i>搜索
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table id="List" lay-filter="List"></table>
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
                        {{# } }}
                    </script>
                    <script type="text/html" id="image">
                        <img lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="act">
                        {{# if (d.status) { }}
                        <a class="layui-btn layui-btn-xs layui-btn-disabled">审核</a>
                        {{# } else { }}
                        <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="detail">审核</a>
                        {{# } }}
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
    layList.tableList({o:'List', done:function () {}},"{:Url('data_download_examine_list')}",function (){
        return [
            {field: 'id', title: '编号', width:'6%',align: 'center'},
            {field: 'title', title: '名称',align: 'left'},
            {field: 'cate_name', title: '分类',align: 'center',width:'8%'},
            {field: 'mer_name', title: '讲师',align: 'center',width:'6%'},
            {field: 'image', title: '封面',templet:'#image',align: 'center',width:'8%'},
            {field: 'money', title: '价格',align: 'center',width:'8%'},
            {field: 'member_money', title: '会员价',align: 'center',width:'8%'},
            {field: 'status', title: '状态',templet:'#status',align: 'center',width:'18%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'}
        ];
    });
    //自定义方法
    var action= {
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['100%', '100%']
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
        var arr_time = [];
        var start_time = '';
        var end_time = '';
        if (where.datetime) {
            arr_time = where.datetime.split('~');
            start_time = arr_time[0].trim();
            end_time = arr_time[1].trim();
        }
        layList.reload({
            cate_id: where.cate_id,
            status: where.status,
            mer_id: where.mer_id,
            start_time: start_time,
            end_time: end_time,
            title: where.title
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
                $eb.openImage(data.image);
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
            case 'detail':
                action.open_add('{:Url('examineDetails')}?id=' + data.id, '审核');
                break;
        }
    })

</script>
{/block}

