{extend name="public/container"}
{block name="head"}
<style>
    .layui-table-cell img {
        max-width: 100%;
        height: 50px;
        cursor: pointer;
    }

    .layui-table-cell .layui-btn-container {
        overflow: hidden;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">客服列表</div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">客服搜索</label>
                        <div class="layui-input-inline">
                            <input type="text" name="title" class="layui-input" placeholder="客服昵称">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">讲师</label>
                        <div class="layui-input-inline">
                            <select name="mer_id" lay-search="">
                                <option value="">全部</option>
                                <option value="0">总平台</option>
                                {volist name='mer_list' id='vc'}
                                <option value="{$vc.id}">{$vc.mer_name}</option>
                                {/volist}
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">全部</option>
                                <option value="1">显示</option>
                                <option value="0">隐藏</option>
                            </select>
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
            <div class="layui-btn-container">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="service"><i class="layui-icon">&#xe608;</i>添加客服</button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                    <i class="layui-icon">&#xe669;</i>刷新
                </button>
            </div>
            <table id="List" lay-filter="List"></table>
            <script type="text/html" id="status">
                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='status' lay-text='显示|隐藏'  {{ d.status == 1 ? 'checked' : '' }}>
            </script>
            <script type="text/html" id="avatar">
                <img lay-event='open_image' src="{{d.avatar}}" width="89" height="50">
            </script>
            <script type="text/html" id="act">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" href="javascript:void(0)" lay-event='chat_user'><i class="layui-icon">&#xe60a;</i>聊天记录</button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" href="javascript:void(0)" lay-event='edit'><i class="layui-icon">&#xe642;</i>编辑</button>
                <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" href="javascript:void(0)" lay-event='delect'><i class="layui-icon">&#xe640;</i>删除</button>
            </script>
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
    layList.tableList({o: 'List', done: function () { }}, "{:Url('get_store_service_list')}", function () {
        return [
            {field: 'id', title: '编号', width: '8%', align: 'center'},
            {field: 'uid', title: 'UID', align: 'center', width: '8%'},
            {field: 'mer_name', title: '讲师', align: 'center', width: '10%'},
            {field: 'nickname', title: '客服昵称', align: 'center', width: '16%'},
            {field: 'kefu_id', title: '客服ID', align: 'center', width: '10%'},
            {field: 'avatar', title: '封面', templet: '#avatar', align: 'center', minWidth: 119, width: '10%'},
            {field: 'sort', title: '排序', sort: true, event: 'sort', edit: 'sort', align: 'center', width: '9%'},
            {field: 'status', title: '状态', templet: '#status', align: 'center', minWidth: 92, width: '9%'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act', minWidth: 81, width: '20%'}
        ];
    });
    //自定义方法
    var action = {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
        //打开新添加页面
        open_add: function (url, title) {
            layer.open({
                type: 2 //Page层类型
                , area: ['100%', '100%']
                , title: title
                , shade: 0.6 //遮罩透明度
                , maxmin: true //允许全屏最小化
                , anim: 1 //0-6的动画形式，-1不开启
                , content: url
                , end: function () {
                    location.reload();
                }
            });
        }
    };

    //查询
    layList.search('search', function (where) {
        var arr_time = [];
        var start_time = '';
        var end_time = '';
        if (where.datetime) {
            arr_time = where.datetime.split('~');
            start_time = arr_time[0].trim();
            end_time = arr_time[1].trim();
        }
        layList.reload({
            mer_id: where.mer_id,
            status: where.status,
            title: where.title
        }, true);
    });
    layList.switch('status', function (odj, value) {
        var is_status_value = 0;
        if (odj.elem.checked == true) {
            var is_status_value = 1
        }
        action.set_value('status', value, is_status_value);
    });
    //快速编辑
    layList.edit(function (obj) {
        var id = obj.data.id, value = obj.value;
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
    layList.sort(['id', 'sort'], true);
    //点击事件绑定
    layList.tool(function (event, data, obj) {
        switch (event) {
            case 'delect':
                var url = layList.U({a: 'delete', q: {id: data.id}});
                $eb.$swal('delete', function () {
                    $eb.axios.get(url).then(function (res) {
                        if (res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success', res.data.msg);
                            obj.del();
                        } else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function (err) {
                        $eb.$swal('error', err);
                    });
                });
                break;
            case 'open_image':
                $eb.openImage(data.avatar);
                break;
            case 'chat_user':
                $eb.createModalFrame('聊天记录', layList.Url({a: 'chat_user', p: {id: data.id}}));
                break;
            case 'edit':
                $eb.createModalFrame('编辑', layList.Url({a: 'edit', p: {id: data.id}}));
                break;
        }
    })
    $(function () {
        $('.layui-btn').on('click', function () {
            if ($(this).data('type') === 'service') {
                layer.open({
                    type: 2,
                    title: '添加客服',
                    content: "{:Url('create')}",
                    area: ['800px', '560px'],
                });
            }
        });
    })
</script>
{/block}