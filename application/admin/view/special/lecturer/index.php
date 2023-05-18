{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">讲师列表</div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">讲师名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="title" class="layui-input" placeholder="请输入讲师名称">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">显示状态</label>
                        <div class="layui-input-inline">
                            <select name="is_show">
                                <option value="">全部</option>
                                <option value="1">显示</option>
                                <option value="0">隐藏</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="search" lay-filter="search">
                                <i class="layui-icon layui-icon-search"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="layui-btn-container">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add">
                    <i class="layui-icon layui-icon-add-1"></i>添加讲师
                </button>
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh">
                    <i class="layui-icon layui-icon-refresh-1"></i>刷新
                </button>
            </div>
            <table id="List" lay-filter="List"></table>
            <script type="text/html" id="is_show">
                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
            </script>
            <script type="text/html" id="recommend">
                <div class="layui-btn-container">
                    {{#  layui.each(d.recommend, function(index, item){ }}
                    <button type="button" class="layui-btn layui-btn-yd layui-btn-normal layui-btn-xs" data-type="recommend" data-id="{{index}}" data-pid="{{d.id}}">yd-{{item}}</button>
                    {{#  }); }}
                </div>
                <div class="layui-btn-container">
                    {{#  layui.each(d.web_recommend, function(index, item){ }}
                    <button type="button" class="layui-btn layui-btn-pc layui-btn-normal layui-btn-xs" data-type="recommend" data-id="{{index}}" data-pid="{{d.id}}">pc-{{item}}</button>
                    {{#  }); }}
                </div>
            </script>
            <script type="text/html" id="lecturer_name">
                <p>{{d.lecturer_name}}/{{d.mer_id}}</p>
            </script>
            <script type="text/html" id="lecturer_head">
                <img width="50" height="50" lay-event='open_image' src="{{d.lecturer_head}}">
            </script>
            <script type="text/html" id="mer_id">
                <div class="layui-btn-group">
                    {{# if(d.mer_id==0){ }}
                    <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" data-type="mercreate" data-id="{{d.id}}">生成讲师后台</button>
                    {{# }else{ }}
                    <a class="layui-btn layui-btn-warm layui-btn-xs" target="_blank" href="{:url('merchant.merchant/login')}?id={{d.mer_id}}">访问</a>
                    <button class="layui-btn layui-btn-normal layui-btn-xs" onclick="$eb.createModalFrame('编辑讲师信息','{:Url('merchant.merchant/edit')}?id={{d.mer_id}}',{h:700,w:800})">编辑</button>
                    <button class="layui-btn success layui-btn-xs" lay-event='reset_pwd'>重置密码</button>
                    {{# } }}
                </div>
            </script>
            <script type="text/html" id="status">
                {{# if(d.status==1){ }}
                <button lay-event='modify_error' class="modify layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button">[正常]</button>
                {{# }else{ }}
                <button lay-event='modify_success' class="modify layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button">[锁定]</button>
                {{# } }}
            </script>
            <script type="text/html" id="is_source">
                {{# if(d.is_source==1){ }}
                入驻
                {{# }else{ }}
                平台
                {{# } }}
            </script>
            <script type="text/html" id="act">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
                    <i class="layui-icon">&#xe625;</i>操作
                </button>
                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                    <li>
                        <a href="javascript:void(0)" lay-event="edit">
                            <i class="iconfont icon-bianji"></i> 编辑讲师
                        </a>
                    </li>
                    {{# if(d.mer_id>0){ }}
                    <li>
                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.lecturer_name}}-推荐管理','{:Url('recommend')}?lecturer_id={{d.id}}',{h:300,w:400})">
                            <i class="iconfont icon-PCshouye"></i> 推至移动首页
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.lecturer_name}}-推荐管理','{:Url('web_recommend')}?lecturer_id={{d.id}}',{h:300,w:400})">
                            <i class="iconfont icon-PCshouye"></i> 推至PC首页
                        </a>
                    </li>
                    {{# } }}
                    <li>
                        <a lay-event='delstor' href="javascript:void(0)">
                            <i class="iconfont icon-shanchu"></i> 删除讲师
                        </a>
                    </li>
                </ul>
            </script>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var $ = layui.jquery;
    var layer = layui.layer;
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {
        $('.layui-btn').on('mouseover', function (event) {
            var target = event.target;
            var type = target.dataset.type;
            if ('recommend' === type) {
                layer.tips('点击即可取消此推荐', target, {
                    tips: [1, '#0093dd']
                });
            }
        });
        $('.modify').on('mouseover', function (event) {
            var target = event.target;
            layer.tips('点击即可切换后台状态', target, {
                tips: [1, '#0093dd']
            });
        });

        $('.layui-btn').on('mouseout', function (event) {
            var target = event.target;
            var type = target.dataset.type;
            if ('recommend' === type) {
                layer.closeAll();
            }
        });
            $('.layui-btn-yd').on('click', function (event) {
                var target = event.target;
                var type = target.dataset.type;
                if ('recommend' === type) {
                    var id = target.dataset.id;
                    var pid = target.dataset.pid;
                    var url = layList.U({ a: 'cancel_recommendation', q: { id: id, lecturer_id: pid } });
                    $eb.$swal(
                        'delete',
                        function () {
                            $eb.axios
                                .get(url)
                                .then(function (res) {
                                    if (res.data.code == 200) {
                                        $eb.$swal('success', res.data.msg);
                                        layList.reload()
                                    } else {
                                        return Promise.reject(res.data.msg || '取消失败');
                                    }
                                })
                                .catch(function (err) {
                                    $eb.$swal('error', err);
                                });
                        },
                        {
                            title: '确定取消此推荐？',
                            text: '取消后无法撤销，请谨慎操作！',
                            confirm: '确定取消'
                        }
                    );
                }
            });
            $('.layui-btn-pc').on('click', function (event) {
                var target = event.target;
                var type = target.dataset.type;
                if ('recommend' === type) {
                    var id = target.dataset.id;
                    var pid = target.dataset.pid;
                    var url = layList.U({ a: 'cancel_web_recommendation', q: { id: id, lecturer_id: pid } });
                    $eb.$swal(
                        'delete',
                        function () {
                            $eb.axios
                                .get(url)
                                .then(function (res) {
                                    if (res.data.code == 200) {
                                        $eb.$swal('success', res.data.msg);
                                        layList.reload()
                                    } else {
                                        return Promise.reject(res.data.msg || '取消失败');
                                    }
                                })
                                .catch(function (err) {
                                    $eb.$swal('error', err);
                                });
                        },
                        {
                            title: '确定取消此推荐？',
                            text: '取消后无法撤销，请谨慎操作！',
                            confirm: '确定取消'
                        }
                    );
                }
            });
    }},"{:Url('lecturer_list')}",function (){
        return [
            {field: 'id', title: '编号', align: 'center'},
            {field: 'lecturer_name', title: '名称/讲师编号',templet:'#lecturer_name',align: 'left'},
            {field: 'is_source', title: '来源',align: 'center',templet:'#is_source'},
            {field: 'lecturer_head', title: '头像',templet:'#lecturer_head',align:'center',minWidth:84},
            {field: 'recommend', title: '推荐[yd:移动端,pc:PC端]',templet:'#recommend',align: 'center',minWidth:100},
            {field: 'phone', title: '手机号',align:'center'},
            {field: 'curriculum', title: '课程数量',align:'center'},
            {field: 'status', title: '后台状态',align: 'center',templet:'#status'},
            {field: 'is_show', title: '讲师状态',templet:'#is_show',align: 'center',minWidth:92},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center'},
            {field: 'mer_id', title: '讲师后台',templet:'#mer_id',align:'center',minWidth:166},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',minWidth:81},
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
        set_value: function (field, id, value) {
            layList.baseGet(layList.Url({
                a: 'set_value',
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
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
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
                })
                break;
            case 'open_image':
                $eb.openImage(data.lecturer_head);
                break;
            case 'open_images':
                $eb.openImage(data.image);
                break;
            case 'edit':
                layer.open({
                    type: 2,
                    title: '编辑讲师',
                    content: '{:Url('create')}?id=' + data.id,
                    area: ['100%', '100%'],
                    maxmin: true
                });
                break;
            case 'reset_pwd':
                if(!data.mer_id) return layList.msg('请先生成讲师后台');
                var url=layList.U({c:'merchant.merchant',a:'reset_pwd',q:{id:data.mer_id}});
                $eb.$swal('delete',function(){
                    $eb.axios.post(url).then(function(res){
                        if(res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success', res.data.msg);
                        }else
                            $eb.$swal('error',res.data.msg||'操作失败!');
                    });
                },{'title':'您确定重置选择讲师后台的密码吗？','text':'重置后的密码为1234567','confirm':'您确定重置密码吗？'});
                break;
            case 'modify_success':
                if(!data.mer_id) return layList.msg('请先生成讲师后台');
                var url=layList.U({c:'merchant.merchant',a:'modify',q:{id:data.mer_id,status:1}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要修改讲师后台的状态吗？','text':'请谨慎操作！','confirm':'是的，我要修改'});
                break;
            case 'modify_error':
                if(!data.mer_id) return layList.msg('请先生成讲师后台');
                var url=layList.U({c:'merchant.merchant',a:'modify',q:{id:data.mer_id,status:0}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },{'title':'您确定要修改讲师后台的状态吗？','text':'请谨慎操作！','confirm':'是的，我要修改'});
                break;
        }
    })
    //是否显示快捷按钮操作
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_show',p:{is_show:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    $(document).on('click', '.layui-btn', function (event) {
        var type = $(this).data('type');
        var id = $(this).data('id');
        if (type === 'mercreate') {
            layer.open({
                type: 2,
                title: '生成讲师后台',
                content: "{:Url('mercreate')}?id=" + id,
                area: ['800px', '700px'],
                end: function () {
                    location.reload();
                }
            });
        } else if (type === 'add') {
            layer.open({
                type: 2,
                title: '添加讲师',
                content: "{:Url('create')}",
                area: ['100%', '100%'],
                maxmin: true,
                end: function () {
                    location.reload();
                }
            });
        } else if (type === 'refresh') {
            layList.reload();
        }
    });
</script>
{/block}
