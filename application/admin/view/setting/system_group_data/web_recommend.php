{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">PC端首页推荐</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-btn-container">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" onclick="$eb.createModalFrame('添加推荐分组','{:Url('create_web_recemmend')}',{h:580})">
                            <i class="layui-icon">&#xe608;</i>添加推荐分组
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <table id="List" lay-filter="List"></table>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        {{# if(d.type!=5){ }}
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="dropdown(this)"><i class="layui-icon">&#xe625;</i>操作</button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                <li>
                                    <div onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('create_web_recemmend')}?id={{d.id}}',{h:480})">
                                        <i class="fa fa-paste"></i> 推荐编辑
                                    </div>
                                </li>
                                {{# if(d.type==0){ }}
                                <li>
                                    <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('special_list')}?id={{d.id}}',{w:1000,h:800})">
                                        <i class="fa fa-list-ul"></i> 内容管理
                                    </a>
                                </li>
                                {{# }else if(d.type==1){ }}
                                <li>
                                    <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('special_list')}?id={{d.id}}',{w:1000,h:800})">
                                        <i class="fa fa-list-ul"></i> 内容管理
                                    </a>
                                </li>
                                {{# }else if(d.type==2){ }}
                                <li>
                                    <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('lecturer_list')}?id={{d.id}}',{w:1000,h:800})">
                                        <i class="fa fa-list-ul"></i> 内容管理
                                    </a>
                                </li>
                                {{# }else if(d.type==3){ }}
                                <li>
                                    <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('data_download')}?id={{d.id}}',{w:1000,h:800})">
                                        <i class="fa fa-list-ul"></i> 内容管理
                                    </a>
                                </li>
                                {{# }else if(d.type==4){ }}
                                <li>
                                    <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('article_list')}?id={{d.id}}',{w:1000,h:800})">
                                        <i class="fa fa-list-ul"></i> 内容管理
                                    </a>
                                </li>
                                {{# }else if(d.type==7){ }}
                                <li>
                                    <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('test_list')}?id={{d.id}}',{w:1000,h:800})">
                                        <i class="fa fa-list-ul"></i> 内容管理
                                    </a>
                                </li>
                                {{# }else if(d.type==8){ }}
                                <li>
                                    <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('test_list')}?id={{d.id}}',{w:1000,h:800})">
                                        <i class="fa fa-list-ul"></i> 内容管理
                                    </a>
                                </li>
                                {{# } }}
                                {{# if(d.type!=4){ }}
                                <li>
                                    <div lay-event='delete'>
                                        <i class="fa fa-trash"></i> 推荐删除
                                    </div>
                                </li>
                                {{# } }}
                        </ul>
                        {{# }else{ }}
                            <button type="button" class="layui-btn layui-btn-xs layui-btn-normal">自动推荐</button>
                        {{# } }}
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
    layList.tableList('List',"{:Url('web_recommend_list',['is_fixed' => 0])}",function (){
        return [
            {field: 'title', title: '列表名称',edit:'title',align:'center'},
            {field: 'type_name', title: '内容类型',align:'center'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align:'center'},
            {field: 'show_count', title: '可显示数量',align:'center'},
            {field: 'number', title: '展示数量',align:'center'},
            {field: 'is_show', title: '状态',templet:'#is_show',align:'center'},
            {field: 'right', title: '操作',toolbar:'#act',align:'center'}
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value,recommend:'web'}
            }), function (res) {
                layList.msg(res.msg);
            },function (err) {
                layList.msg(err.msg,function () {
                    location.reload();
                });
            });
        },
    }
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:1,id:value,recommend:'web'}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:0,id:value,recommend:'web'}}),function (res) {
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
    //监听并执行排序
    layList.sort(['sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delete':
                var url=layList.U({a:'delete_web_recomm',q:{id:data.id}});
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
</script>
{/block}
