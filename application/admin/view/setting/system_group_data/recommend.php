{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">热门推荐</div>
        <div class="layui-card-body">
            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame('添加推荐分组','{:Url('create_recemmend_v1')}',{h:580})">
                    <i class="layui-icon">&#xe608;</i>添加推荐分组
                </button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                    <i class="layui-icon">&#xe669;</i>刷新
                </button>
            </div>
            <table class="layui-hide" id="List" lay-filter="List"></table>
            <script type="text/html" id="image">
                {{# if(d.image) { }}
                <img style="cursor: pointer" lay-event='open_image' src="{{d.image}}">
                {{# } }}
            </script>
            <script type="text/html" id="is_show">
                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
            </script>
            <script type="text/html" id="act">
                <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="dropdown(this)"><i class="layui-icon">&#xe625;</i>操作</button>
                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                    <li>
                        <div onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('create_recemmend_v1')}?id={{d.id}}',{h:480})">
                            <i class="fa fa-paste"></i> 推荐编辑
                        </div>
                    </li>
                    {{# if(d.typesetting!=5){ }}
                        {{# if(d.type==1 || d.type==10){ }}
                        <li>
                            <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('recemmend_article_content')}?id={{d.id}}',{w:1000,h:800})">
                                <i class="fa fa-list-ul"></i> 内容管理
                            </a>
                        </li>
                        {{# }else if(d.type==11 || d.type==12 || d.type==14 || d.type==6){ }}
                        <li>
                            <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('recemmend_test_content')}?id={{d.id}}',{w:1000,h:800})">
                                <i class="fa fa-list-ul"></i> 内容管理
                            </a>
                        </li>
                        {{# }else if(d.type==4){ }}
                        <li>
                            <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('recemmend_store_content')}?id={{d.id}}',{w:1000,h:800})">
                                <i class="fa fa-list-ul"></i> 内容管理
                            </a>
                        </li>
                        {{# }else{ }}
                        <li>
                            <a href="javascript:;" onclick="$eb.createModalFrame('{{d.title}}-'+this.innerText,'{:Url('recemmend_content')}?id={{d.id}}',{w:1000,h:800})">
                                <i class="fa fa-list-ul"></i> 内容管理
                            </a>
                        </li>
                        {{# } }}
                        {{# if(d.type !=11 && d.type !=12 && d.type !=14 && d.type !=6 && d.type !=4){ }}
                        <li>
                            <div onclick="$eb.createModalFrame(this.innerText,'{:Url('recemmend_banner')}?id={{d.id}}',{w:900})">
                                <i class="fa fa-file-image-o"></i>  轮播图
                            </div>
                        </li>
                        {{# } }}
                        <li>
                            <div lay-event='delete'>
                                <i class="fa fa-trash"></i> 推荐删除
                            </div>
                        </li>
                    {{# } }}
                </ul>
            </script>
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
    layList.tableList('List',"{:Url('recommend_list')}",function (){
        return [
            {field: 'title', title: '列表名称',edit:'title',align:'center'},
            {field: 'type_ting', title: '列表模式',align:'center'},
            {field: 'type_name', title: '内容类型',align:'center'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align:'center'},
            {field: 'grade_title', title: '关联分类',align:'center'},
            {field: 'show_count', title: '展示数量',align:'center'},
            {field: 'number', title: '已推数量',align:'center'},
            {field: 'is_show', title: '状态',templet:'#is_show',align:'center'},
            {field: 'right', title: '操作',toolbar:'#act',align:'center'},
        ];
    });
    //自定义方法
    var action= {
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
                q: {field: field, id: id, value: value,recommend:'wap'}
            }), function (res) {
                layList.msg(res.msg);
            });
        },
    }
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:1,id:value,recommend:'wap'}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:0,id:value,recommend:'wap'}}),function (res) {
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
