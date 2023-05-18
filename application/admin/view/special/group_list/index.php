{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">拼团专题</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">专题搜索</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="store_name" class="layui-input" placeholder="专栏名称、简介、编号">
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
                                <label class="layui-form-label">讲师</label>
                                <div class="layui-input-inline">
                                    <select name="mer_id" lay-search="">
                                        <option value="">全部</option>
                                        <option value="0">总平台</option>
                                        {volist name='mer_list' id='vc'}
                                        <option  value="{$vc.id}">{$vc.mer_name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-inline">
                                    <select name="is_show">
                                        <option value="">全部</option>
                                        <option value="1">上架</option>
                                        <option value="0">下架</option>
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
                                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                        <i class="layui-icon">&#xe615;</i>搜索
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                                        <i class="layui-icon">&#xe669;</i>刷新
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="recommend">
                        <div class="layui-btn-container">
                        {{#  layui.each(d.recommend, function(index, item){ }}
                        <button type="button" class="layui-btn  layui-btn-normal layui-btn-xs" data-type="recommend" data-id="{{index}}" data-pid="{{d.id}}">{{item}}</button>
                        {{#  }); }}
                        </div>
                    </script>
                    <script type="text/html" id="is_pink">
                        {{# if(d.is_pink){ }}
                        <span class="layui-badge layui-bg-blue">开启</span>
                        {{# }else{ }}
                        <span class="layui-badge">关闭</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="is_mer_visible">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_mer_visible' lay-text='是|否'  {{ d.is_mer_visible == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='上架|下架'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;" height="50" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="quantity">
                        <p>{{d.quantity}}/{{d.sum}}</p>
                        <a  href="javascript:void(0)" class="layui-badge layui-bg-blue" onclick="$eb.createModalFrame('查看素材','{:Url('source_material')}?id='+{{d.id}}+'&special_type='+{{d.type}},{w:1200,h:800})">
                            查看素材
                        </a>
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
                            <i class="layui-icon">&#xe625;</i>操作
                        </button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a href="{:Url('ump.store_combination/combina_list')}?cid={{d.id}}&special_type=0">
                                    <i class="fa fa-street-view"></i> 查看拼团
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.title}}-推荐管理','{:Url('recommend')}?special_id={{d.id}}',{h:300,w:400})">
                                    <i class="fa fa-check-circle"></i> 推至移动首页
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.title}}-拼团管理','{:Url('pink')}?special_id={{d.id}}',{h:500})">
                                    <i class="fa fa-users"></i> 拼团设置
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
    //实例化form
    layList.form.render();
    layList.date({
        elem: '#datetime',
        type: 'datetime',
        range: '~'
    });
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

        $('.layui-btn').on('mouseout', function (event) {
            var target = event.target;
            var type = target.dataset.type;
            if ('recommend' === type) {
                layer.closeAll();
            }
        });

        $('.layui-btn').on('click', function (event) {
            var target = event.target;
            var type = target.dataset.type;
            if ('recommend' === type) {
                var id = target.dataset.id;
                var pid = target.dataset.pid;
                var url = layList.U({ a: 'cancel_recommendation', q: { id: id, special_id: pid } });
                $eb.$swal(
                    'delete',
                    function () {
                        $eb.axios
                            .get(url)
                            .then(function (res) {
                                if (res.data.code == 200) {
                                    $eb.$swal('success', res.data.msg);
                                    layList.reload();
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
    }},"{:Url('pink_list')}",function (){
        return [
            {field: 'id', title: '编号',width:'5%',align: 'center'},
            {field: 'title', title: '名称',align:'left'},
            {field: 'subject_name', title: '分类',align: 'center',width:'6%'},
            {field: 'mer_name', title: '讲师',align: 'center',width:'6%'},
            {field: 'image', title: '封面',templet:'#image',align: 'center',width:'8%'},
            {field: 'recommend', title: '移动端推荐',templet:'#recommend',align: 'center',width:'12%'},
            {field: 'money', title: '价格',align: 'center',width:'5%'},
            {field: 'member_money', title: '会员价',align: 'center',width:'5%'},
            {field: 'pink_money', title: '拼团价',align: 'center',width:'5%'},
            {field: 'quantity', title: '(已选/总数) 查看素材',align: 'center',width:'7%',templet:'#quantity'},
            {field: 'is_pink', title: '拼团状态',templet:'#is_pink',align: 'center',width:'6%'},
            {field: 'is_show', title: '状态',templet:'#is_show',align: 'center',width:'7%'},
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
            mer_id: where.mer_id,
            is_show: where.is_show,
            start_time: start_time,
            end_time: end_time,
            store_name: where.store_name
        },true);
    });
    layList.switch('is_show',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_show',value,is_show_value,'special');
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
                })
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })

</script>
{/block}

