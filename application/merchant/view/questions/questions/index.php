{extend name="public/container"}
{block name="head_top"}
<!-- 全局js -->
<script src="{__PLUG_PATH}echarts/echarts.common.min.js"></script>
<script src="{__PLUG_PATH}echarts/theme/macarons.js"></script>
<script src="{__PLUG_PATH}echarts/theme/westeros.js"></script>
<style>
    .go-live{width: 100%;background-color: #eeeeee;display: none;}
    .go-live .live-box{padding: 10px 0 10px 30px;background-color: #ffffff;border-radius: 5px;margin-top: 30px;}
    .go-live .live-box .live-text{font-size: 15px;color: #0092DC;margin: 5px;padding: 10px 0;}
    .go-live .live-box .live-title{text-align: center;}
    .go-live .live-box .live-text p{color:#333333;padding:10px;display: inline-block;width: 80%;background-color: #eeeeee;border-radius: 10px;}
    .go-live .live-box .live-text label{width: 8%;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">题库列表</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">试题搜索</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" class="layui-input" placeholder="题干">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">试题题型</label>
                                <div class="layui-input-inline">
                                    <select name="type" id="type">
                                        <option value="">全部</option>
                                        <option value="3">判断题</option>
                                        <option value="2">多选题</option>
                                        <option value="1">单选题</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">题库分类</label>
                                <div class="layui-input-inline">
                                    <select name="pid" id="pid">
                                        <option value="0">全部</option>
                                        {volist name='category' id='vc'}
                                            <option value="{$vc.id}">{$vc.html}{$vc.title}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <div class="layui-btn-group">
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                            <i class="layui-icon">&#xe615;</i>搜索
                                        </button>
                                        <button type="button" class="layui-btn layui-btn-primary layui-btn-sm export" data-type="export" lay-filter="export">
                                            <i class="layui-icon">&#xe67d;</i>导出
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="add" onclick="action.open_add('{:Url('add')}','添加试题')">
                            <i class="layui-icon">&#xe608;</i>添加试题
                        </button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm download" data-type="download" lay-filter="download" >
                            <i class="layui-icon">&#xe67d;</i>下载题库模版
                        </button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm import" onclick="$eb.createModalFrame('导入试题','{:Url('imports')}',{w:800,h:200})">
                            <i class="layui-icon">&#xe624;</i>导入试题
                        </button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
                    <script type="text/html" id="is_show">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='上架|下架'  {{ d.is_show == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="image">
                        <img style="cursor: pointer;" height="50" lay-event='open_image' src="{{d.image}}">
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)">
                            <i class="layui-icon">&#xe625;</i>操作
                        </button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a  href="javascript:void(0)" onclick="action.open_add('{:Url('add')}?id={{d.id}}','编辑试题')">
                                    <i class="fa fa-paste"></i> 编辑试题
                                </a>
                            </li>
                            <li>
                                <a  href="javascript:void(0)" onclick="action.open_add('{:Url('knowledge')}?id={{d.id}}','关联知识点')">
                                    <i class="layui-icon layui-icon-set"></i> 关联知识点
                                </a>
                            </li>
                            <li>
                                <a lay-event='answer' href="javascript:void(0)">
                                    <i class="fa fa-file-text"></i> 答题情况
                                </a>
                            </li>
                            <li>
                                <a lay-event='delect' href="javascript:void(0)">
                                    <i class="fa fa-trash"></i> 删除试题
                                </a>
                            </li>
                        </ul>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="chart" style="display: none;width: 600px;height: 400px;"></div>
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
    layList.tableList({o:'List', done:function () {}},"{:Url('getQuestionsList')}",function (){
        return [
            {field: 'id', title: '编号', width:'8%',align: 'center'},
            {field: 'cate', title: '分类', width:'10%',align: 'center'},
            {field: 'stem', title: '题干'},
            {field: 'types', title: '题型',align: 'center',width:'10%'},
            {field: 'difficulty', title: '难度',align: 'center',width:'6%'},
            // {field: 'number', title: '答题人数',align: 'center',width:100},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:'8%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'},
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
        layList.reload({
            type: where.type,
            pid: where.pid,
            title: where.title,
            excel:0
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
    $('.layui-btn').on('click', function () {
        var types = $(this).data('type');
        if (types == 'export') {
            var title = $.trim($('input[name="title"]').val());
            var type = $('#type').val();
            var pid = $('#pid').val();
            location.href= layList.U({a:'getQuestionsList',q:{type:type,pid:pid,title:title,excel:1}});
        }else if(types == 'download'){
            window.open(window.location.origin + '/template.xlsx');
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
            case 'answer':
                layList.baseGet(layList.U({a:'getQuestionsAnswer',q:{id:data.id}}),function(res){
                    $('#live-href').text(res.data.href);
                    $('#live-code').text(res.data.code);
                    var subtext = [];
                    var start = 0;
                    for (var i = 1, len = data.stem.length; i <= len; i++) {
                        if (i % 30) {
                            continue;
                        }
                        subtext.push(data.stem.slice(start, i));
                        start = i;
                    }
                    if (start !== len) {
                        subtext.push(data.stem.slice(start));
                    }
                    if (subtext.length > 2) {
                        subtext.length = 2;
                        subtext[1] = subtext[1].slice(0, -2) + '……';
                    }
                    var chartEl = document.getElementById('chart');
                    var chart = echarts.init(chartEl);
                    var option = {
                        title: {
                            text: subtext.join('\n'),
                            textStyle: {
                                lineHeight: 28,
                                rich: {}
                            },
                            left: 'center'
                        },
                        color: ['#ee6666', '#91cc75'],
                        tooltip: {
                            trigger: 'item'
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left',
                            top: 'bottom'
                        },
                        series: [
                            {
                                type: 'pie',
                                radius: '50%',
                                data: [
                                    { value: res.data.wrong, name: '错误' },
                                    { value: res.data.yes, name: '正确' },
                                ],
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };
                    chart.setOption(option);
                    layui.layer.open({
                        type: 1,
                        title: '答题情况',
                        content: $('#chart'),
                        area: ['600px', '442px'],
                        end: function () {
                            $('#chart').hide();
                        }
                    });
                },function (res) {
                    layList.msg(res.msg);
                });
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
        }
    });
    require(['vue','axios','layer'],function(Vue,axios,layer){
        new Vue({
            el:"#app",
            data:{
                option:{},
                myChart:{},
                masks:false
            },
            methods:{
                info:function () {
                    var that=this;
                    axios.get("{:Url('ExaminationTestRecord')}").then(function (res) {
                        that.myChart.user_echart.setOption(that.userchartsetoption(res.data.data));
                    });
                },
                userchartsetoption:function(data){
                    this.option = {
                        title: {
                            text: 'Referer of a Website',
                            subtext: 'Fake Data',
                            left: 'center'
                        },
                        tooltip: {
                            trigger: 'item'
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left'
                        },
                        series: [
                            {
                                name: 'Access From',
                                type: 'pie',
                                radius: '50%',
                                data: [
                                    { value: 1048, name: 'Search Engine' },
                                    { value: 735, name: 'Direct' },
                                    { value: 580, name: 'Email' },
                                    { value: 484, name: 'Union Ads' },
                                    { value: 300, name: 'Video Ads' }
                                ],
                                emphasis: {
                                    itemStyle: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };
                    return  this.option;
                },
                setChart:function(name,myChartname){
                    this.myChart[myChartname] = echarts.init(name,'macarons');//初始化echart
                }
            },
            mounted:function () {
                var self = this;
            }
        });
    });
</script>
{/block}

