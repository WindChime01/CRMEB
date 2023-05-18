{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
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
                                <button class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button">未审核</button>
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
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)" style="margin:5px 0;">
                                    <i class="layui-icon">&#xe625;</i>操作
                                </button>
                                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                    <li>
                                        <a href="javascript:void(0)" lay-event='upbit'>
                                            <i class="iconfont icon-bianji"></i> 编辑审核
                                        </a>
                                    </li>
                                </ul>
                            </script>
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
            {field: 'right', title: '操作', align: 'center', toolbar: '#act', width: '8%'}
        ];
    });
    //查询
    layList.search('search', function (where) {
        layList.reload(where, true);
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    });

    function dropdown(that) {
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var top = offset.top - $(window).scrollTop();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        })
        if ($(document).height() < top + $(that).next('ul').height()) {
            $(that).next('ul').css({
                'padding': 10,
                'top': -($(that).parent('td').height() / 2 + $(that).height() + $(that).next('ul').height() / 2),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        } else {
            $(that).next('ul').css({
                'padding': 10,
                'top': $(that).parent('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }
    }
    //点击事件绑定
    layList.tool(function (event, data, obj) {
        switch (event) {
            case 'open_image':
                $eb.openImage(data.live_image);
                break;
            case 'upbit':
                $eb.createModalFrame(data.live_title+'--直播审核',layList.U({a:'edit_audit',q:{id:data.id}}),{w:800,h:600});
                break;
        }
    });

</script>
{/block}
