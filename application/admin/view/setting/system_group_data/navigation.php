{extend name="public/container"}
{block name="head"}
<style>
    .layui-nav {
        padding: 0 10px;
    }

    .layui-bg-blue {
        background-color: #0092DC !important;
    }

    .layui-nav-bar::after {
        background-color: #5FB878;
    }

    .layui-nav .layui-nav-item {
        line-height: 30px;
    }

    .layui-nav .layui-nav-item a {
        color: #fff;
    }

    .layui-nav-child {
        top: 35px;
    }

    .layui-nav .layui-nav-child a {
        line-height: 36px;
        color: #333;
    }

    .layui-nav .layui-nav-child dd.layui-this a {
        background-color: #0092DC;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">导航配置</div>
        <div class="layui-card-body">
            <ul class="layui-nav layui-inline layui-bg-blue" lay-filter="nav">
                <li class="layui-nav-item">
                    <a href="javascript:;">添加导航</a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" data-type="1">固定跳转</a></dd>
                        <dd><a href="javascript:;" data-type="2">自定义跳转</a></dd>
                    </dl>
                </li>
            </ul>
            <table id="List" lay-filter="List"></table>
            <script type="text/html" id="icon">
                {{# if(d.icon) { }}
                <img lay-event='open_image' src="{{d.icon}}" width="50" height="50">
                {{# } }}
            </script>
            <script type="text/html" id="is_show">
                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
            </script>
            <script type="text/html" id="act">
                <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="dropdown(this)"><i class="layui-icon">&#xe625;</i>操作</button>
                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                    {{# if(d.type == 3) { }}
                    <li>
                        <div onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('create_recemmend_custom')}?id={{d.id}}&is_fixed=1')">
                            <i class="fa fa-paste"></i> 编辑
                        </div>
                    </li>
                    {{# }else{ }}
                    <li>
                        <div onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('create_recemmend')}?id={{d.id}}',{h:480})">
                            <i class="fa fa-paste"></i> 编辑
                        </div>
                    </li>
                    {{# } }}
                    <li>
                        <div lay-event='delete'>
                            <i class="fa fa-trash"></i> 删除
                        </div>
                    </li>
                </ul>
            </script>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
  var element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块
  element.render();

  //监听导航点击
  element.on('nav(nav)', function(elem){
    var type = elem.data('type');
    if (type == 1) {
        $eb.createModalFrame('添加固定跳转导航','{:Url('create_recemmend')}',{w:800,h:500});
    } else if (type == 2) {
        $eb.createModalFrame('添加自定义跳转导航','{:Url('create_recemmend_custom')}?is_fixed=1');
    }
  });
    //实例化form
    layList.form.render();

    //加载列表
    layList.tableList('List',"{:Url('recommend_list',['is_fixed' => 1])}",function (){
        return [
            {field: 'title', title: '导航名称',edit:'title',align:'center'},
            {field: 'type_name', title: '导航类型',align:'center'},
            {field: 'icon', title: '图标',templet:'#icon',align:'center'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align:'center'},
            {field: 'grade_title', title: '一级分类',align:'center'},
            {field: 'is_show', title: '状态',templet:'#is_show',align:'center'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act'},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value,recommend:''}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:1,id:value,recommend:''}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:0,id:value,recommend:''}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'title':
                action.set_value('title',id,value);
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
                            action.set_value('sort', id, value.trim());
                        }
                    }
                } else {
                    layList.msg('排序不能为空');
                }
                break;
        }
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
    //监听并执行排序
    layList.sort(['sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delete_recomm',q:{id:data.id}});
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
                $eb.openImage(data.icon);
                break;
        }
    })

</script>
{/block}
