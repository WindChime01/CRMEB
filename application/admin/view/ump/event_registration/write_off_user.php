{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" onclick="action.open_add('{:Url('user')}?event_id='+{$event_id},'添加核销用户')">
                    添加核销用户
                </button>
                <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
            </div>
            <table class="layui-hide" id="List" lay-filter="List"></table>
            <script type="text/html" id="act">
                <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delstor'>
                    <i class="layui-icon">&#xe640;</i> 删除
                </button>
            </script>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var event_id = "{$event_id}";
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o: 'List', done: function () { }}, "{:Url('get_write_off_user_list')}?event_id=" + event_id, function () {
        return [
            {field: 'id', title: '编号', width: '10%', align: 'center'},
            {field: 'uid', title: 'UID', width: '16%', align: 'center'},
            {field: 'nickname', title: '用户昵称', width: '30%', align: 'center'},
            {field: 'avatar', title: '用户头像', width: '20%', event: 'open_image', align: 'center', templet: '<div><img class="avatar open_image" style="cursor: pointer" height="50" data-image="{{d.avatar}}" src="{{d.avatar}}" alt="{{d.nickname}}"></div>'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act'}
        ];
    }, 10);

    //点击事件绑定
    layList.tool(function (event, data, obj) {
        switch (event) {
            case 'delstor':
                var url = layList.U({a: 'del_write_off_user', q: {event_id: event_id, uid: data.uid}});
                parent.$eb.$swal('delete', function () {
                    parent.$eb.axios.get(url).then(function (res) {
                        if (res.status == 200 && res.data.code == 200) {
                            parent.$eb.$swal('success', res.data.msg);
                            location.reload();
                        } else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function (err) {
                        parent.$eb.$swal('error', err);
                    });
                });
                break;
        }
    })
    //监听并执行排序
    layList.sort(['id', 'sort'], true);
    var action = {
        open_add: function (url, title) {
            layer.open({
                type: 2 //Page层类型
                , area: ['80%', '90%']
                , title: title
                , shade: 0.6 //遮罩透明度
                , maxmin: true //允许全屏最小化
                , anim: 1 //0-6的动画形式，-1不开启
                , content: url,
                btnAlign: 'c', //按钮居中
                closeBtn: 1,
                yes: function () {
                    layer.closeAll();
                },
                end: function () {
                    location.reload();
                }
            });
        },
        refresh: function () {
            layList.reload();
        }
    };
</script>
{/block}