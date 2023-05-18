{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">个人中心题库菜单</div>
        <div class="layui-card-body">
            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                    <i class="layui-icon">&#xe669;</i>刷新
                </button>
            </div>
            <table id="Lists" lay-filter="Lists"></table>
            <script type="text/html" id="icons">
                {{# if(d.icon) { }}
                <img lay-event='open_images' src="{{d.icon}}" height="50">
                {{# } }}
            </script>
            <script type="text/html" id="status">
                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='status' lay-text='显示|隐藏' {{ d.status== 1 ? 'checked' : '' }}>
            </script>
            <script type="text/html" id="acts">
                <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="$eb.createModalFrame('编辑','{:Url('edit')}?gid={$gid}&id={{d.id}}')"><i class="fa fa-paste"></i>编辑</button>
            </script>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    //实例化form
    layList.form.render();
    var gid="{$gid}";
    //加载列表
    layList.tableList('Lists', "{:Url('get_group_data_list')}?gid="+gid, function () {
        return [
            {field: 'title', title: '标题',align:'center'},
            {field: 'explain', title: '说明',align:'center'},
            {field: 'icon', title: '图标', templet: '#icons',align:'center'},
            {field: 'sort', title: '排序',align:'center'},
            {field: 'status', title: '状态', templet: '#status',align:'center'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#acts'},
        ];
    });
    //自定义方法
    var action= {
        set_group_data: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_group_data',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        }
    };
    layList.switch('status', function (odj, value) {
        action.set_group_data('status', value, odj.elem.checked == true ? 1 : 0);
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'open_images':
                $eb.openImage(data.icon);
                break;
        }
    })

</script>
{/block}
