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
                                        <label class="layui-form-label">商品名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="store_name" class="layui-input" placeholder="商品名称、关键字、编号">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">商品分类</label>
                                        <div class="layui-input-inline">
                                            <select name="cate_id" lay-search="">
                                                <option value="0">全部</option>
                                                {volist name='cate' id='vc'}
                                                <option value="{$vc.id}">|--{$vc.cate_name}</option>
                                                {/volist}
                                            </select>
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
                                <img style="cursor: pointer;" height="50" lay-event="open_image" src="{{d.image}}">
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
                            <!--产品名称-->
                            <script type="text/html" id="store_name">
                                <div>{{d.store_name}}</div>
                                {{# if(d.cate_name!=''){ }}
                                <div><span style="font-weight: bold;">分类</span>：{{d.cate_name}}</div>
                                {{# } }}
                            </script>
                            <!--操作-->
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)" style="margin:5px 0;">
                                    <i class="layui-icon">&#xe625;</i>操作
                                </button>
                                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                    <li>
                                        <a href="javascript:void(0)" onclick="action.open_add('{:Url('create')}?id={{d.id}}','编辑')">
                                            <i class="iconfont icon-bianji"></i> 编辑商品
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" lay-event='delstor'>
                                            <i class="iconfont icon-shanchu"></i> 移到回收站
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
    layList.tableList('List', "{:Url('product_examine_ist')}", function () {
        return [
            {field: 'id', title: 'ID', width: '6%', templet: '#_id', align: 'center'},
            {field: 'store_name', title: '名称', templet: '#store_name'},
            {field: 'image', title: '图片', templet: '#image', align: 'center', width: '8%'},
            {field: 'stock', title: '库存', edit: 'stock', align: 'center', width: '8%'},
            {field: 'price', title: '价格', align: 'center', width: '8%'},
            {field: 'vip_price', title: '会员价', align: 'center', width: '8%'},
            {field: 'status', title: '状态', templet: "#status", align: 'center', width: '16%'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act', width: '10%'}
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
            case 'delstor':
                var url = layList.U({c: 'store.store_product', a: 'delete', q: {id: data.id}});
                if (data.is_del) var code = {title: "操作提示", text: "确定恢复产品操作吗？", type: 'info', confirm: '是的，恢复该产品'};
                else var code = {title: "操作提示", text: "确定将该产品移入回收站吗？", type: 'info', confirm: '是的，移入回收站'};
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
                }, code);
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    });

    //自定义方法
    var action = {
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
    //多选事件绑定
    $('.layui-btn-group').find('button').each(function () {
        var type = $(this).data('type');
        $(this).on('click', function () {
            action[type] && action[type]();
        })
    });
</script>
{/block}
