{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">活动名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="请输入活动名称">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">状态</label>
                                        <div class="layui-input-inline">
                                            <select name="is_show">
                                                <option value="">全部</option>
                                                <option value="1">显示</option>
                                                <option value="0">隐藏</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">审核状态</label>
                                        <div class="layui-input-inline">
                                            <select name="status">
                                                <option value="">全部</option>
                                                <option value="1">通过</option>
                                                <option value="0">未审核</option>
                                                <option value="-1">未通过</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                <i class="layui-icon">&#xe615;</i>搜索</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" onclick="action.open_add('{:Url('create')}','添加活动')"><i class="layui-icon">&#xe608;</i>添加活动</button>
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                            </div>
                            <div style="color: red;">
                                注：活动核销员设置已挪到活动列表->操作->核销员列表中，每个活动单独设置核销员，核销员不能跨活动核销
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="is_show">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
                            </script>
                            <script type="text/html" id="image">
                                <img style="cursor: pointer;" height="50" lay-event='open_image' src="{{d.image}}">
                            </script>
                            <script type="text/html" id="statu">
                                {{# if (d.statu === 0) { }}
                                报名尚未开始
                                {{# } else if (d.statu === 1) { }}
                                报名开始
                                {{# } else if (d.statu === 2) { }}
                                报名结束
                                {{# } else if (d.statu === 3) { }}
                                活动中
                                {{# } else { }}
                                活动结束
                                {{# } }}
                            </script>
                            <script type="text/html" id="status">
                                {{# if(d.status==1){ }}
                                <span class="layui-badge layui-bg-blue">通过</span>
                                {{# }else if(d.status==0){ }}
                                <span class="layui-badge layui-bg-blue">未审核</span>
                                {{# }else{ }}
                                <span class="layui-badge">未通过</span>
                                <span class="layui-badge layui-bg-blue" lay-event='fail'>查看原因</span>
                                {{# } }}
                            </script>
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-xs layui-btn-normal" onclick="dropdown(this)"><i class="layui-icon">&#xe625;</i>操作</button>
                                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                    <li>
                                        <a href="javascript:void(0)"  onclick="action.open_add('{:Url('create')}?id={{d.id}}','编辑')" >
                                            <i class="iconfont icon-bianji"></i> 编辑活动
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="action.open_write('{:Url('write_off_user')}?event_id={{d.id}}','核销员列表')">
                                            <i class="iconfont icon-yidongshouye"></i> 核销员列表
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)"  onclick="action.open_add('{:Url('viewStaff')}?id={{d.id}}','报名人员')" >
                                            <i class="iconfont icon-baoming"></i> 报名人员
                                        </a>
                                    </li>
                                    <li>
                                          <a lay-event='delect' href="javascript:void(0)">
                                              <i class="iconfont icon-shanchu"></i> 删除活动
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
    layList.tableList({o:'List',done:function () {}},"{:Url('event_registration_list',[])}",function (){
        return [
            {field: 'id', title: '编号', width:'5%',align: 'center'},
            {field: 'title', title: '活动名称',align: 'center'},
            {field: 'image', title: '图片', templet:'#image',align: 'center',width:'8%'},
            {field: 'address', title: '地址', align: 'center',width:'16%'},
            {field: 'number', title: '活动人数',align: 'center',width:'8%'},
            {field: 'status', title: '审核',templet:'#status',align: 'center',width:'8%'},
            {field: 'statu', title: '活动状态',templet:'#statu',align: 'center', width:'8%'},
            {field: 'is_show', title: '显示状态',templet:'#is_show',align: 'center', width:'8%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'}
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
        });
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
    };
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
        open_write: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['80%', '90%']
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
    };
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
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
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'title':
                action.set_value('title',id,value,'member_card_batch');
                break;
            case 'number':
                action.set_value('remark',id,value,'member_card_batch');
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
            case 'open_image':
                $eb.openImage(data.image);
                break;
            case 'fail':
                layer.open({
                    type: 1,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['420px', '240px'], //宽高
                    title: '审核未通过原因',
                    content: data.fail_message
                });
                break;
        }
    })
</script>
{/block}

