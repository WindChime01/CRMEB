{extend name="public/container"}
{block name="head"}
<style>
    .layui-table-cell img{max-width: 100%;height: 50px;cursor: pointer;}
    .layui-table-cell .layui-btn-container{overflow: hidden;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">专题审核</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">专题搜索</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="store_name" class="layui-input" placeholder="专题名称、简介、编号">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">专题分类</label>
                                <div class="layui-input-inline">
                                    <select name="subject_id" lay-search="">
                                        <option value="0">全部</option>
                                        {volist name='subject_list' id='vc'}
                                            <option {if $vc.grade_id==0}disabled{/if} value="{$vc.id}">{$vc.html}{$vc.name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-inline">
                                    <select name="status">
                                        <option value="">全部</option>
                                        <option value="0">未审核</option>
                                        <option value="-1">未通过</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">专题类别</label>
                                <div class="layui-input-inline">
                                    <select name="type">
                                        <option value="">全部</option>
                                        <option value="1">图文专题</option>
                                        <option value="2">音频专题</option>
                                        <option value="3">视频专题</option>
                                        <option value="4">直播专题</option>
                                        <option value="5">专栏专题</option>
                                        <option value="6">轻专题</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">时间范围</label>
                                <div class="layui-input-inline" style="width: 260px;">
                                    <input type="text" name="datetime" class="layui-input" id="datetime" placeholder="时间范围">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <div class="layui-btn-group">
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                            <i class="layui-icon">&#xe615;</i>搜索
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="image">
                        <img lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="quantity">
                        <a  href="javascript:void(0)" class="layui-badge layui-bg-blue" onclick="$eb.createModalFrame('查看素材','{:Url('source_material')}?id='+{{d.id}}+'&special_type='+{{d.type}},{w:1200,h:800})">
                            查看素材
                        </a>
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
                    <script type="text/html" id="type">
                        {{# if(d.type==1){ }}
                            图文专题
                        {{# }else if(d.type==2){ }}
                            音频专题
                        {{# }else if(d.type==3){ }}
                            视频专题
                        {{# }else if(d.type==4){ }}
                            直播专题
                        {{# }else if(d.type==5){ }}
                            专栏专题
                        {{# }else if(d.type==6){ }}
                            轻专题
                        {{# } }}
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
                            <i class="layui-icon">&#xe625;</i>操作
                        </button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a  href="javascript:void(0)" onclick="action.open_add('{:Url('add')}?id={{d.id}}&special_type={{d.type}}','编辑专题')">
                                    <i class="iconfont icon-bianji"></i> 编辑专题
                                </a>
                            </li>
                            <li>
                                <a lay-event='delect' href="javascript:void(0)">
                                    <i class="iconfont icon-shanchu"></i> 删除专题
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
    layList.tableList({o:'List', done:function () {}},"{:Url('specialExamineList')}",function (){
        return [
            {field: 'id', title: '编号', width:'6%',align: 'center'},
            {field: 'title', title: '名称'},
            {field: 'subject_name', title: '分类',align: 'center',width:'6%'},
            {field: 'type', title: '专题类别',align: 'center',templet:'#type',width:'6%'},
            {field: 'image', title: '封面',templet:'#image',align: 'center',width:'6%'},
            {field: 'money', title: '价格',align: 'center',width:'6%'},
            {field: 'member_money', title: '会员价',align: 'center',width:'6%'},
            {field: 'pink_money', title: '拼团价',align: 'center',width:'6%'},
            {field: 'quantity', title: '已选素材',align: 'center',width:'7%',templet:'#quantity'},
            {field: 'sum', title: '素材总数',align: 'center',width:'6%'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:'6%'},
            {field: 'status', title: '状态',templet:'#status',align: 'center',width:'16%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'},
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
        }
    };
    //查询
    layList.search('search',function(where){
        var arr_time = [];
        var start_time = '';
        var end_time = '';
        if (where.datetime) {
            arr_time = where.datetime.split('~');
            start_time = arr_time[0].trim();
            end_time = arr_time[1].trim();
        }
        layList.reload({
            subject_id: where.subject_id,
            status: where.status,
            type: where.type,
            start_time: start_time,
            end_time: end_time,
            store_name: where.store_name
        },true);
    });
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'title':
                action.set_value('title',id,value,'special');
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
                            action.set_value('sort', id, value.trim(), 'special');
                        }
                    }
                } else {
                    layList.msg('排序不能为空');
                }
                break;
            case 'fake_sales':
                if(value < 0) return layList.msg('虚拟不能小于0');
                action.set_value('fake_sales',id,value,'special');
                break;
        }
    });

    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delect':
                var url=layList.U({a:'delete',q:{id:data.id, model_type:'special'}});
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

