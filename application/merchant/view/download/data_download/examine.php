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
        <div class="layui-card-header">资料列表</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">资料搜索</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" class="layui-input" placeholder="资料名称、简介">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">资料分类</label>
                                <div class="layui-input-inline">
                                    <select name="cate_id" lay-search="">
                                        <option value="0">全部</option>
                                        {volist name='cate_list' id='vc'}
                                            <option {if $vc.pid==0}disabled{/if} value="{$vc.id}">{$vc.html}{$vc.title}</option>
                                        {/volist}
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
                    <table id="List" lay-filter="List"></table>
                    <script type="text/html" id="status">
                        {{# if(d.status==0){ }}
                        <button lay-event='fail' class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button"><i class="layui-icon">&#x1006;</i>不通过</button>
                        <button lay-event='succ' class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button"><i class="layui-icon">&#xe605;</i>通过</button>
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
                    <script type="text/html" id="image">
                        <img lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
                            <i class="layui-icon">&#xe625;</i>操作
                        </button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a  href="javascript:void(0)" onclick="action.open_add('{:Url('add')}?id={{d.id}}','编辑资料')">
                                    <i class="iconfont icon-bianji"></i> 编辑资料
                                </a>
                            </li>
                            <li>
                                <a lay-event='delect' href="javascript:void(0)">
                                    <i class="iconfont icon-shanchu"></i> 删除资料
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
    layList.tableList({o:'List', done:function () {}},"{:Url('data_download_examine_list')}",function (){
        return [
            {field: 'id', title: '编号', width:'6%',align: 'center'},
            {field: 'cate_name', title: '分类',align: 'center',width:'8%'},
            {field: 'title', title: '名称',align: 'left'},
            {field: 'image', title: '封面',templet:'#image',align: 'center',width:'8%'},
            {field: 'money', title: '价格',align: 'center',width:'8%'},
            {field: 'member_money', title: '会员价',align: 'center',width:'8%'},
            {field: 'ficti', title: '虚拟下载',event:'ficti',edit:'ficti',align: 'center',width:'8%'},
            {field: 'status', title: '状态',templet:'#status',align: 'center',width:'18%'},
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
            cate_id: where.cate_id,
            is_show: where.is_show,
            start_time: start_time,
            end_time: end_time,
            title: where.title
        },true);
    });
    layList.switch('is_show',function (odj,value) {
        var is_show_value = 0;
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_show',value,is_show_value);
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
            case 'ficti':
                if(value < 0) return layList.msg('虚拟下载不能小于0');
                action.set_value('ficti',id,value);
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
            case 'records':
                layer.open({
                    type: 2,
                    title: data.title + '—下载记录',
                    content: '{:Url('records')}?id=' + data.id,
                    area: ['100%', '100%'],
                    maxmin: true
                });
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    })

</script>
{/block}

