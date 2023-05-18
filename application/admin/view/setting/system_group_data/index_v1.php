{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">首页活动</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">是否展示</label>
                                <div class="layui-input-inline">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="1">显示</option>
                                        <option value="0">不显示</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search"
                                            lay-filter="search">
                                        <i class="layui-icon">&#xe615;</i>搜索
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-container">
                        <form class="layui-form" lay-filter="formswitch" action="">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label" style="font-size: 14px;width: 170px;">在前台首页显示状态：</label>
                                    <div class="layui-input-inline">
                                        <input type='checkbox' name='state' class="test" lay-skin='switch'  lay-filter='is_show_or_hide' lay-text='显示|隐藏'>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="pic">
                        <img width="50" height="50" lay-event='open_image' src="{{d.pic}}">
                    </script>
                    <script type="text/html" id="status">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='status'
                                lay-text='显示|隐藏' {{ d.status== 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit">
                            <i class="layui-icon">&#xe642;</i>编辑
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
    var is_show_or_hide={$is_show_or_hide};
    //实例化form
    layList.form.render();
    layList.form.val('formswitch',{
        state: is_show_or_hide == 1 ? true : false
    })
    //加载列表
    layList.tableList('List', "{:Url('get_group_data_list',['gid'=>$gid])}", function () {
        return [
            {field: 'id', title: '编号', width: 60,align: 'center'},
            {field: 'title', title: '标题', edit: 'title'},
            {field: 'info', title: '简介', edit: 'info'},
            {field: 'pic', title: '图标', templet: '#pic',align:'center'},
            {field: 'sort', title: '排序', sort: true, event: 'sort', edit: 'sort',align:'center'},
            {field: 'status', title: '状态', templet: '#status',align:'center'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act'},
        ];
    });
    //自定义方法
    var action = {
        set_group_data: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_group_data',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    };
    //查询
    layList.search('search', function (where) {
        layList.reload(where, true);
    });
    layList.form.on('switch(is_show_or_hide)',function (data) {
        layList.baseGet(layList.Url({
            a: 'is_show_or_hide',
            q: {value: data.elem.checked==true ? 1 : 2}
        }), function (res) {
            layList.msg(res.msg);
        });
    });

    layList.switch('status', function (odj, value) {
        action.set_group_data('status', value, odj.elem.checked == true ? 1 : 0);
    });
    //快速编辑
    layList.edit(function (obj) {
        var id = obj.data.id, value = obj.value;
        switch (obj.field) {
            case 'title':
                action.set_group_data('title', id, value);
                break;
            case 'info':
                action.set_group_data('info', id, value);
                break;
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
                            action.set_group_data('sort', id, value.trim());
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
            case 'delstor':
                var url = layList.U({ a: 'delete', q: {id: data.id}});
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
                $eb.openImage(data.pic);
                break;
            case 'edit':
                layui.layer.open({
                    type: 2,
                    title: '编辑',
                    content: "{:Url('create_v1')}?id=" + data.id,
                    area: ['800px', '500px'],
                    end: function () {
                        window.location.reload();
                    }
                });
                break;
        }
    })
</script>
{/block}
