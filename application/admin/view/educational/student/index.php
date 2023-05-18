{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">学员列表</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">学员搜索</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" class="layui-input" placeholder="学员名称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">班级名称</label>
                                <div class="layui-input-inline">
                                    <select name="cid" lay-search="" >
                                        <option value="0">全部</option>
                                        {volist name='classes' id='vc'}
                                            <option {if $vc['id'] eq $cid}selected {/if} value="{$vc.id}">{$vc.title}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <div class="layui-btn-group">
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                            <i class="layui-icon layui-icon-search"></i>搜索
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-group conrelTable">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="$eb.createModalFrame(this.innerText,'{:Url('user')}',{'w':1200})"><i class="layui-icon layui-icon-add-1"></i> 添加学员</button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" data-type="send_test_paper">
                            <i class="layui-icon">&#xe609;</i>发送试卷
                        </button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" data-type="send_special">
                            <i class="layui-icon">&#xe609;</i>发送课程
                        </button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;" height="50" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
                            <i class="layui-icon">&#xe625;</i>操作
                        </button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a  href="javascript:void(0)" onclick="action.open_add('{:Url('create')}?id={{d.id}}','编辑学员')">
                                    <i class="fa fa-paste"></i> 编辑学员
                                </a>
                            </li>
                            <li>
                                <a  href="javascript:void(0)" onclick="action.open_add('{:Url('test_paper')}?uid={{d.uid}}','已发试卷')">
                                    <i class="fa fa-paste"></i> 已发试卷
                                </a>
                            </li>
                            <li>
                                <a  href="javascript:void(0)" onclick="action.open_add('{:Url('special_list')}?uid={{d.uid}}','已发课程')">
                                    <i class="fa fa-paste"></i> 已发课程
                                </a>
                            </li>
                            <li>
                                <a lay-event='delect' href="javascript:void(0)">
                                    <i class="fa fa-trash"></i> 删除学员
                                </a>
                            </li>
                        </ul>
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
    var $ = layui.jquery;
    var layer = layui.layer;
    //实例化form
    layList.form.render();
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('getStudentList',['c_id'=>$cid])}",function (){
        return [
            {type:'checkbox'},
            {field: 'uid', title: 'UID', width:'6%',align: 'center'},
            {field: 'nickname', title: '昵称',align: 'center',width:'10%'},
            {field: 'image', title: '头像',templet:'#image', width:'9.8%',align: 'center'},
            {field: 'sex', title: '性别',align: 'center',width:'7%'},
            {field: 'name', title: '名称', width:'10%',align: 'center'},
            {field: 'title', title: '班级', width:'20%',align: 'center'},
            {field: 'address', title: '地址',align: 'center',width:'18%'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:'6%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'}
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
    $('.conrelTable').find('button').each(function () {
        var type=$(this).data('type');
        $(this).on('click',function () {
            action[type] && action[type]();
        })
    });
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
        //打开新添加页面
        open_add: function (url,title) {
            layer.open({
                type: 2 //Page层类型
                ,area: ['90%', '90%']
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
        send_test_paper:function () {
            var arr = layList.getCheckData();
            var ids=layList.getCheckData().getIds('id');
            if(ids.length){
                var uid = ids.join(',');
                layer.open({
                    type: 2 //Page层类型
                    ,area: ['80%', '90%']
                    ,title: '发送试卷'
                    ,shade: 0.6 //遮罩透明度
                    ,maxmin: true //允许全屏最小化
                    ,anim: 1 //0-6的动画形式，-1不开启
                    ,content: layList.Url({c:'educational.student',a:'send'}),
                    btn: '确定',
                    btnAlign: 'c', //按钮居中
                    closeBtn:1,
                    yes: function(){
                        layer.closeAll();
                        var source_tmp = $("#check_source_tmp").val();
                        var source_tmp_list = JSON.parse(source_tmp);
                        var arr=[];
                        for(var i=0;i<source_tmp_list.length;i++){
                            arr.push(source_tmp_list[i].id);
                        }
                        var str=arr.join(',');
                        layList.baseGet(layList.Url({
                            a: 'sendTestPaper',
                            q: {sid: uid, tid: str}
                        }), function (res) {
                            layList.msg(res.msg,function () {
                                location.reload();
                            });
                        });
                    }
                });
            }else{
                layList.msg('请选择要发送试卷的学员');
            }
        },
        send_special:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length){
                var uid = ids.join(',');
                layer.open({
                    type: 2 //Page层类型
                    ,area: ['80%', '90%']
                    ,title: '发送课程'
                    ,shade: 0.6 //遮罩透明度
                    ,maxmin: true //允许全屏最小化
                    ,anim: 1 //0-6的动画形式，-1不开启
                    ,content: layList.Url({c:'educational.student',a:'special'}),
                    btn: '确定',
                    btnAlign: 'c', //按钮居中
                    closeBtn:1,
                    yes: function(){
                        layer.closeAll();
                        var source_tmp = $("#check_source_tmp").val();
                        var source_tmp_list = JSON.parse(source_tmp);
                        var arr=[];
                        for(var i=0;i<source_tmp_list.length;i++){
                            arr.push(source_tmp_list[i].id);
                        }
                        var str=arr.join(',');
                        layList.baseGet(layList.Url({
                            a: 'sendSpecial',
                            q: {uid: uid, tid: str}
                        }), function (res) {
                            layList.msg(res.msg,function () {
                                location.reload();
                            });
                        });
                    }
                });
            }else{
                layList.msg('请选择要发送课程的学员');
            }
        }
    };
    //查询
    layList.search('search',function(where){
        layList.reload({
            cid:where.cid,
            title: where.title
        },true);
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
        }
    })

</script>
{/block}

