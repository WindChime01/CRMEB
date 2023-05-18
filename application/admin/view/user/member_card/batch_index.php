{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <div class="layui-tab layui-tab-brief" lay-filter="tab">
                                <ul class="layui-tab-title">
                                    <li lay-id="list" {eq name='activity_type' value='1'}class="layui-this" {/eq} >
                                    <a href="{eq name='activity_type' value='1'}javascript:;{else}{:Url('index',['activity_type'=>1])}{/eq}">批次列表</a>
                                    </li>
                                    <li lay-id="list" {eq name='activity_type' value='2'}class="layui-this" {/eq}>
                                    <a href="{eq name='activity_type' value='2'}javascript:;{else}{:Url('card_index',['activity_type'=>2])}{/eq}">会员卡列表</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">批次名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="请输入批次名称">
                                            <input type="hidden" name="activity_type" value="{$activity_type}">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                <i class="layui-icon">&#xe615;</i>搜索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="action.open_add('{:Url('add_batch',['id'=>''])}','添加批次')">
                                    <i class="layui-icon">&#xe608;</i>添加批次
                                </button>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="is_status">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_status' lay-text='激活|冻结'  {{ d.status == 1 ? 'checked' : '' }}>
                            </script>
                            <script type="text/html" id="qrcode">
                                <img style="cursor: pointer;" height="50" lay-event='qrcode_image' src="{{d.qrcode}}">
                            </script>
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)" style="margin:5px 0;">
                                    <i class="layui-icon">&#xe625;</i>操作
                                </button>
                                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                    <li>
                                        <a href="javascript:void(0)" lay-event='export'>
                                            <i class="iconfont icon-daochu"></i> 导出会员卡
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{:Url('card_index')}?card_batch_id={{d.id}}&activity_type=2" >
                                            <i class="iconfont icon-kaliebiao"></i> 查看卡列表
                                        </a>
                                    </li>
                                    <li>
                                        <a lay-event='delect' href="javascript:void(0)">
                                            <i class="iconfont icon-shanchu"></i> 删除
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
{/block}
{block name="script"}
<script>
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('batch_list',[])}",function (){
        return [
            {field: 'id', title: '编号', width:'6%',align: 'center'},
            {field: 'title', title: '批次名称',align: 'center'},
            {field: 'use_day', title: '体验天数', edit:'use_day',align: 'center'},
            {field: 'total_num', title: '发卡总数量',align: 'center'},
            {field: 'use_num', title: '使用数量',align: 'center'},
            {field: 'create_time', title: '制卡时间',align: 'center'},
            {field: 'qrcode', title: '二维码', templet:'#qrcode',align: 'center'},
            {field: 'status', title: '状态', templet:'#is_status',align: 'center'},
            {field: 'remark', title: '备注', edit:"remark",align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'},
        ];
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    });
    function dropdown(that){
        var oEvent = arguments.callee.caller.arguments[0] || event;
        oEvent.stopPropagation();
        var offset = $(that).offset();
        var top=offset.top-$(window).scrollTop();
        var index = $(that).parents('tr').data('index');
        $('.layui-nav-child').each(function (key) {
            if (key != index) {
                $(this).hide();
            }
        })
        if($(document).height() < top+$(that).next('ul').height()){
            $(that).next('ul').css({
                'padding': 10,
                'top': - ($(that).parent('td').height() / 2 + $(that).height() + $(that).next('ul').height()/2),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }else{
            $(that).next('ul').css({
                'padding': 10,
                'top':$(that).parent('td').height() / 2 + $(that).height(),
                'min-width': 'inherit',
                'position': 'absolute'
            }).toggle();
        }
    }
    //自定义方法
    var action= {
        set_value: function (field, id, value, model_type) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value, model_type:model_type}
            }), function (res) {
                layList.msg(res.msg);
            }, function (err) {
                layList.msg(err.msg);
            });
        },
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['80%', '80%']
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
    }
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('is_status',function (odj,value) {
        var is_status_value = 0
        if(odj.elem.checked==true){
            var is_status_value = 1
        }
        action.set_value('status',value,is_status_value,'member_card_batch');
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'title':
                action.set_value('title',id,value,'member_card_batch');
                break;
            case 'remark':
                action.set_value('remark',id,value,'member_card_batch');
                break;
            case 'use_day':
                action.set_value('use_day',id,value,'member_card_batch');
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
             case 'qrcode_image':
                $eb.openImage(data.qrcode);
                break;
            case 'export':
                location.href=layList.U({c:'user.member_card',a:'card_list',q:{card_batch_id:data.id,excel:1}});
                break;
        }
    })

</script>
{/block}

