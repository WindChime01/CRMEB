{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">荣誉证书列表</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">证书搜索</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="证书标题">
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
                                        <label class="layui-form-label">获取方式</label>
                                        <div class="layui-input-inline">
                                            <select name="obtain" lay-search="">
                                                <option value="0">全部</option>
                                                <option value="1">课程</option>
                                                <option value="2">考试</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <div class="layui-btn-group">
                                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                    <i class="layui-icon">&#xe615;</i>搜索
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
                            <table class="layui-hide" id="List" lay-filter="List"></table>
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
                            <script type="text/html" id="background">
                                <img style="cursor: pointer;" width="35" height="50" lay-event='open_image_background' src="{{d.background}}">
                            </script>
                            <script type="text/html" id="qr_code">
                                <img style="cursor: pointer;" width="50" height="50" lay-event='open_image_qr_code' src="{{d.qr_code}}">
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
    layList.tableList({o:'List', done:function () {}},"{:Url('get_certificate_examine_list')}",function (){
        return [
            {field: 'id', title: '编号', width: '8%', align: 'center'},
            {field: 'mer_name', title: '讲师',align: 'center',width:'6%'},
            {field: 'title', title: '证书标题', align: 'center'},
            {field: 'background', title: '证书背景', templet: '#background',align: 'center', width: '10%'},
            {field: 'qr_code', title: '二维码', templet: '#qr_code',align: 'center', width: '10%'},
            {field: 'obtains', title: '获取方式', align: 'center', width: '10%'},
            {field: 'status', title: '状态',templet:'#status',align: 'center',width:'18%'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act', width: '12%'}
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['90%', '90%']
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
            mer_id: where.mer_id,
            title: where.title
        },true);
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'sort':
                if (value.trim()) {
                    if (isNaN(value.trim())) {
                        layList.msg('请输入正确的数字');
                    } else {
                        if (value.trim() < 0) {
                            layList.msg('排序不能小于0');
                        } else if (value.trim() > 9999) {
                            layList.msg('排序不能大于9999');
                        } else if (parseInt(value.trim()) != value.trim()) {
                            layList.msg('排序不能为小数');
                        } else {
                            action.set_value('sort', id, value.trim());
                        }
                    }
                } else {
                    layList.msg('排序不能为空');
                }
                break;
        }
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
            case 'open_image_background':
                $eb.openImage(data.background);
                break;
            case 'open_image_qr_code':
                $eb.openImage(data.qr_code);
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

