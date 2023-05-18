{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">荣誉证书列表</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">证书搜索</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="证书标题">
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
                                        <label class="layui-form-label">获取方式</label>
                                        <div class="layui-input-inline">
                                            <select name="obtain" lay-search="">
                                                <option value="0">全部</option>
                                                <option value="1">课程</option>
                                                <option value="2">考试</option>
                                            </select>
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
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add" onclick="action.open_add('{:Url('add')}','添加荣誉证书')">
                                    <i class="layui-icon">&#xe608;</i>添加荣誉证书
                                </button>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
                            <script type="text/html" id="background">
                                <img style="cursor: pointer;" width="35" height="50" lay-event='open_image_background' src="{{d.background}}">
                            </script>
                            <script type="text/html" id="qr_code">
                                <img style="cursor: pointer;" width="50" height="50" lay-event='open_image_qr_code' src="{{d.qr_code}}">
                            </script>
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
                                  <i class="layui-icon">&#xe625;</i>操作
                                </button>
                                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                    <li>
                                        <a  href="javascript:void(0)" onclick="action.open_add('{:Url('add')}?id={{d.id}}','编辑证书')">
                                            <i class="iconfont icon-bianji"></i> 编辑证书
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.title}}-证书转增','{:Url('increase')}?id={{d.id}}',{h:600,w:500})">
                                            <i class="iconfont icon-yidongshouye"></i> 证书转增
                                        </a>
                                    </li>
                                    <li>
                                        <a lay-event='delect' href="javascript:void(0)">
                                            <i class="iconfont icon-shanchu"></i> 删除证书
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
    var $ = layui.jquery;
    var layer = layui.layer;
    //实例化form
    layList.form.render();
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('getCertificateList')}",function (){
        return [
            {field: 'id', title: '编号', width: '8%', align: 'center'},
            {field: 'mer_name', title: '讲师',align: 'center',width:'6%'},
            {field: 'title', title: '证书标题', align: 'center'},
            {field: 'background', title: '证书背景', templet: '#background',align: 'center', width: '10%'},
            {field: 'qr_code', title: '二维码', templet: '#qr_code',align: 'center', width: '10%'},
            {field: 'obtains', title: '获取方式', align: 'center', width: '10%'},
            {field: 'number', title: '获取人数', align: 'center', width: '10%'},
            {field: 'sort', title: '排序', sort: true, event: 'sort', edit: 'sort', align: 'center', width: '8%'},
            {field: 'right', title: '操作', align: 'center', toolbar: '#act', width: '8%'}
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
    }
    layList.switch('is_show',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_show',value,is_show_value);
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
        }
    };
    //查询
    layList.search('search',function(where){
        layList.reload({
            obtain: where.obtain,
            mer_id: where.mer_id,
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
            case 'open_image_background':
                $eb.openImage(data.background);
                break;
            case 'open_image_qr_code':
                $eb.openImage(data.qr_code);
                break;
        }
    })

</script>
{/block}

