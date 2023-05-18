{extend name="public/container"}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">活动名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="store_name" class="layui-input" placeholder="请输入活动名称,关键字,编号">
                                </div>
                            </div>

                            <div class="layui-inline">
                                <label class="layui-form-label">是否显示</label>
                                <div class="layui-input-block">
                                    <select name="is_show">
                                        <option value="">全部</option>
                                        <option value="1">显示</option>
                                        <option value="0">隐藏</option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon layui-icon-search"></i>搜索</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--产品列表-->
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="alert alert-info" role="alert">
                        列表[排序]可进行快速修改,双击或者单击进入编辑模式,失去焦点可进行自动保存
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add" onclick="action.open_special_add('关联商品')">
                            <i class="layui-icon">&#xe608;</i>关联活动
                        </button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i>  刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <input type="hidden" id="check_event_tmp" name="check_event_tmp"/>
                    <script type="text/html" id="gis_show">
                        <input type='checkbox' name='live_goods_id' lay-skin='switch' value="{{d.live_goods_id}}" lay-filter='gis_show' lay-text='显示|隐藏'  {{ d.gis_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;width: 80px;height: 40px;" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event='delect'><i class="fa fa-trash"></i> 移除</button>
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
    //实例化form
    layList.form.render();
    layList.date({elem:'#start_time',type:'datetime'});
    layList.date({elem:'#end_time',type:'datetime'});
    var special_id="{$special_id}";
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('live_event_list',['live_id'=>$live_id])}",function (){
        return [
            {field: 'live_goods_id', title: '编号', sort: true,event:'live_goods_id',width:'8%',align: 'center'},
            {field: 'title', title: '活动名称',align: 'center'},
            {field: 'image', title: '封面图',templet:'#image',align: 'center'},
            {field: 'gsort', title: '排序',sort: true,event:'gsort',edit:'gsort',align: 'center'},
            {field: 'gis_show', title: '是否显示',templet:'#gis_show',align: 'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
        ];
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    })
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
                c:'special.special_type',
                a: 'set_value',
                q: {field: field, id: id, value: value, model_type:model_type}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
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
        },
        //打开新添加页面
        open_special_add: function (title) {
            var url = "{:Url('special.special_type/event_task')}?special_id=" + special_id;
            layer.open({
                type: 2 //Page层类型
                ,area: ['80%', '90%']
                ,title: '关联活动'
                ,shade: 0.6 //遮罩透明度
                ,maxmin: true //允许全屏最小化
                ,anim: 1 //0-6的动画形式，-1不开启
                ,content: url,
                btn: '确定',
                btnAlign: 'c', //按钮居中
                closeBtn:1,
                yes: function(){
                    layer.closeAll();
                    var source_tmp = $("#check_event_tmp").val();
                    var source_tmp_list = JSON.parse(source_tmp);
                    var arr=[];
                    for(var i=0;i<source_tmp_list.length;i++){
                        arr.push(source_tmp_list[i].id);
                    }
                    var ids=arr.join(',');
                    layList.baseGet(layList.Url({
                        a: 'add_live_special',
                        q: {special_id: special_id, ids: ids, type: 2}
                    }), function (res) {
                        layList.msg(res.msg, function () {
                            location.reload();
                        });
                    });
                }
            });
        }
    };
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    layList.switch('gis_show',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_show',value,is_show_value,'live_goods');
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.live_goods_id,value=obj.value;
        switch (obj.field) {
            case 'gsort':
                if(value < 0) return layList.msg('排序不能小于0');
                action.set_value('sort',id,value,'live_goods');
                break;
            case 'gfake_sales':
                action.set_value('fake_sales',id,value,'live_goods');
                break;
        }
    });
    //监听并执行排序
    layList.sort(['live_goods_id','gsort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({c:'special.special_type',a:'set_value',q:{id:data.live_goods_id, field:'is_delete',value:1,model_type:'live_goods'}});
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
                })
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })

</script>
{/block}

