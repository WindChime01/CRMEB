{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">礼物设置</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加礼物','{:Url('create')}',{h:500,w:600})">
                            <i class="layui-icon">&#xe608;</i>添加礼物
                        </button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='is_show' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;" lay-event='open_image' src="{{d.live_gift_show_img}}" height="50">
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('编辑','{:Url('create')}?id={{d.id}}',{h:500,w:600})">
                            <i class="iconfont icon-bianji"></i>编辑
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
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('live_gift_list')}",function (){
        return [
            {field: 'id', title: '编号',align:"center",width:80},
            {field: 'live_gift_name', title: '名称',align:"center"},
            {field: 'live_gift_show_img', title: '图片',align:'center',templet:'#image',width:'8%'},
            {field: 'live_gift_price', title: '价格（虚拟货币）',align:'center',width:'10%'},
            {field: 'live_gift_num', title: '赠送数量列表',align:'center'},
            {field: 'is_show', title: '状态',align:'center',templet:'#is_show',width:'8%'},
            {field: 'sort', title: '排序',sort: true,edit:'sort',align:'center',width:'8%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_live_gift_value',
                q: {field: field, id: id, value: value}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
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
    //是否显示快捷按钮操作
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_gift_show',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_gift_show',p:{is_show:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'open_image':
                $eb.openImage(data.live_gift_show_img);
                break;
        }
    })
</script>
{/block}
