{extend name="public/container"}
{block name="head_top"}
<!-- 全局js -->
<script src="{__PLUG_PATH}echarts/echarts.common.min.js"></script>
<script src="{__PLUG_PATH}echarts/theme/macarons.js"></script>
<script src="{__PLUG_PATH}echarts/theme/westeros.js"></script>
<style scoped>
    .box{width:0px;}
    .mask{  background-color: rgba(0,0,0,0.5);
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        z-index: 55;
    }
    .mask img{
        width: 70%;
        position: fixed;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }
    .mask span{
        position: fixed;
        top: 70%;
        left: 35%;
        color: #fff;
        font-size: 36px;
    }

    .layui-layer-page .layui-layer-content {
        padding: 15px;
        font-size: 14px;
        line-height: 1.6;
        color: #ed5565;
    }
    .normal{
        background-color: #0092DC;
        color: #fff;
    }
    .layui-btn-group .layui-btn-normal {
        border: 1px solid #0092DC;
        border-left: none;
    }
    .layui-btn-group .layui-btn-normal:first-child {
        border-left: 1px solid #0092DC;
    }
</style>
{/block}
{block name="content"}
<div id="app" v-cloak>
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">课程订单<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_num.data}</p>
                        <p>
                            今日课程订单
                            <span class="layuiadmin-span-color">
                          {$first_line.d_num.percent}%
                          {if condition='$first_line.d_num.is_plus egt 0'}<i class="fa {if condition='$first_line.d_num.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                          </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">课程收入<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_price.data}</p>
                        <p>
                            今日课程交易额
                            <span class="layuiadmin-span-color">
                          {$first_line.d_price.percent}%
                          {if condition='$first_line.d_price.is_plus egt 0'}<i class="fa {if condition='$first_line.d_price.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                          </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">商品订单<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_store_num.data}</p>
                        <p>
                            今日商品订单
                            <span class="layuiadmin-span-color">
                          {$first_line.d_store_num.percent}%
                          {if condition='$first_line.d_store_num.is_plus egt 0'}<i class="fa {if condition='$first_line.d_store_num.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                          </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">商品收入<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_store_price.data}</p>
                        <p>
                            今日商品交易额
                            <span class="layuiadmin-span-color">
                          {$first_line.d_store_price.percent}%
                          {if condition='$first_line.d_store_price.is_plus egt 0'}<i class="fa {if condition='$first_line.d_store_price.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                          </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">资料订单<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_data_num.data}</p>
                        <p>
                            今日资料订单
                            <span class="layuiadmin-span-color">
                          {$first_line.d_data_num.percent}%
                          {if condition='$first_line.d_data_num.is_plus egt 0'}<i class="fa {if condition='$first_line.d_data_num.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                          </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">资料收入<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_data_price.data}</p>
                        <p>
                            今日资料交易额
                            <span class="layuiadmin-span-color">
                          {$first_line.d_data_price.percent}%
                          {if condition='$first_line.d_data_price.is_plus egt 0'}<i class="fa {if condition='$first_line.d_data_price.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                          </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">活动订单<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_event_num.data}</p>
                        <p>
                            今日活动订单
                            <span class="layuiadmin-span-color">
                          {$first_line.d_event_num.percent}%
                          {if condition='$first_line.d_event_num.is_plus egt 0'}<i class="fa {if condition='$first_line.d_event_num.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                          </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">活动收入<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_event_price.data}</p>
                        <p>
                            今日活动交易额
                            <span class="layuiadmin-span-color">
                          {$first_line.d_event_price.percent}%
                          {if condition='$first_line.d_event_price.is_plus egt 0'}<i class="fa {if condition='$first_line.d_event_price.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                          </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">考试订单<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_test_num.data}</p>
                        <p>
                            今日考试订单
                            <span class="layuiadmin-span-color">
                        {$first_line.d_test_num.percent}%
                        {if condition='$first_line.d_test_num.is_plus egt 0'}<i class="fa {if condition='$first_line.d_test_num.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                        </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-header">考试收入<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font">{$first_line.d_test_price.data}</p>
                        <p>
                            今日考试交易额
                            <span class="layuiadmin-span-color">
                        {$first_line.d_test_price.percent}%
                        {if condition='$first_line.d_test_price.is_plus egt 0'}<i class="fa {if condition='$first_line.d_test_price.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                        </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="layui-row">
                            <div class="layui-col-md9">
                                <div class="layui-card">
                                    <div class="layui-card-header">
                                        <div class="layui-btn-group">
                                            <button type="button" :class="[type == '0' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('0')">课程</button>
                                            <button type="button" :class="[type == '2' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('2')">商城</button>
                                            <button type="button" :class="[type == '3' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('3')">资料</button>
                                            <button type="button" :class="[type == '4' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('4')">考试</button>
                                            <button type="button" :class="[type == '5' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('5')">报名</button>
                                        </div>
                                    </div>
                                    <div class="layui-card-body">
                                        <div class="flot-chart-content echarts" ref="order_echart"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-md3">
                                <div class="layui-card">
                                    <div class="layui-card-header">
                                        {{typename}}订单--{{cyclename}}
                                        <div class="layui-btn-group layuiadmin-btn-group">
                                            <button type="button" :class="[active == 'thirtyday' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-xs']" @click="getlist('thirtyday')">30天</button>
                                            <button type="button" :class="[active == 'week' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-xs']" @click="getlist('week')">周</button>
                                            <button type="button" :class="[active == 'month' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-xs']" @click="getlist('month')">月</button>
                                            <button type="button" :class="[active == 'year' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-xs']" @click="getlist('year')">年</button>
                                        </div>
                                    </div>
                                    <div class="layui-card-body">
                                        <ul class="stat-list" style="height: 280px;">
                                            <li>
                                                <h2 class="no-margins ">{{pre_cycleprice}}</h2>
                                                <small>{{precyclename}}销售额</small>
                                            </li>
                                            <li>
                                                <h2 class="no-margins ">{{cycleprice}}</h2>
                                                <small>{{cyclename}}销售额</small>
                                                <div class="stat-percent text-navy" v-if='cycleprice_is_plus ===1'>
                                                    {{cycleprice_percent}}%
                                                    <i  class="fa fa-level-up"></i>
                                                </div>
                                                <div class="stat-percent text-danger" v-else-if='cycleprice_is_plus === -1'>
                                                    {{cycleprice_percent}}%
                                                    <i class="fa fa-level-down"></i>
                                                </div>
                                                <div class="stat-percent" v-else>
                                                    {{cycleprice_percent}}%
                                                </div>
                                                <div class="progress progress-mini">
                                                    <div :style="{width:cycleprice_percent+'%'}" class="progress-bar box"></div>
                                                </div>
                                            </li>
                                            <li>
                                                <h2 class="no-margins ">{{pre_cyclecount}}</h2>
                                                <small>{{precyclename}}订单总数</small>
                                            </li>
                                            <li>
                                                <h2 class="no-margins">{{cyclecount}}</h2>
                                                <small>{{cyclename}}订单总数</small>
                                                <div class="stat-percent text-navy" v-if='cyclecount_is_plus ===1'>
                                                    {{cyclecount_percent}}%
                                                    <i class="fa fa-level-up"></i>
                                                </div>
                                                <div class="stat-percent text-danger" v-else-if='cyclecount_is_plus === -1'>
                                                    {{cyclecount_percent}}%
                                                    <i  class="fa fa-level-down"></i>
                                                </div>
                                                <div class="stat-percent " v-else>
                                                    {{cyclecount_percent}}%
                                                </div>
                                                <div class="progress progress-mini">
                                                    <div :style="{width:cyclecount_percent+'%'}" class="progress-bar box"></div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    require(['vue','axios','layer'],function(Vue,axios,layer){
        new Vue({
            el:"#app",
            data:{
                option:{},
                myChart:{},
                active:'thirtyday',
                cyclename:'最近30天',
                precyclename:'上个30天',
                cyclecount:0,
                cycleprice:0,
                cyclecount_percent:0,
                cycleprice_percent:0,
                cyclecount_is_plus:0,
                cycleprice_is_plus:0,
                pre_cyclecount:0,
                pre_cycleprice:0,
                type:0,//0：课程 1：会员 2：商城 3：资料 4：考试 5：报名 6：充值
                typename:'课程',
                cycle:'week',
                masks:false
            },
            methods:{
                getlisttype:function(type)
                {
                    var that=this;
                    that.type=type;
                    switch (type) {
                        case '0':
                            that.typename='课程';
                            break;
                        case '1':
                            that.typename='会员';
                            break;
                        case '2':
                            that.typename='商城';
                            break;
                        case '3':
                            that.typename='资料';
                            break;
                        case '4':
                            that.typename='考试';
                            break;
                        case '5':
                            that.typename='报名';
                            break;
                        case '6':
                            that.typename='充值';
                            break;
                    }
                    that.getlist(that.cycle);
                },
                getlist:function (e) {
                    var that=this;
                    var cycle = e!=null ? e :'week';
                    that.cycle=cycle;
                    axios.get("{:Url('orderchart')}?cycle="+cycle+'&type='+this.type).then(function(res){
                        that.myChart.order_echart.clear();
                        that.myChart.order_echart.setOption(that.orderchartsetoption(res.data.data));
                        that.active = cycle;
                        switch (cycle){
                            case 'thirtyday':
                                that.cyclename = '最近30天';
                                that.precyclename = '上个30天';
                                break;
                            case 'week':
                                that.cyclename = '本周';
                                that.precyclename = '上周';
                                break;
                            case 'month':
                                that.cyclename = '本月';
                                that.precyclename = '上月';
                                break;
                            case 'year':
                                that.cyclename = '今年';
                                that.precyclename = '去年';
                                break;
                            default:
                                break;
                        }
                        var data = res.data.data || {cycle:{count:{},price:{}},pre_cycle:{price:{},count:{}}};
                        if(!Array.isArray(data)){
                            that.cyclecount = data.cycle.count.data;
                            that.cyclecount_percent = data.cycle.count.percent;
                            that.cyclecount_is_plus = data.cycle.count.is_plus;
                            that.cycleprice = data.cycle.price.data;
                            that.cycleprice_percent = data.cycle.price.percent;
                            that.cycleprice_is_plus = data.cycle.price.is_plus;
                            that.pre_cyclecount = data.pre_cycle.count.data;
                            that.pre_cycleprice = data.pre_cycle.price.data;
                        }else{
                            that.cyclecount = 0;
                            that.cyclecount_percent = 0;
                            that.cyclecount_is_plus = 0;
                            that.cycleprice = 0;
                            that.cycleprice_percent = 0;
                            that.cycleprice_is_plus = 0;
                            that.pre_cyclecount = 0;
                            that.pre_cycleprice = 0;
                        }

                    });
                },
                orderchartsetoption:function(data){
                    if(data === undefined){
                        data = {} ;
                    }
                    this.option = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'cross',
                                crossStyle: {
                                    color: '#999'
                                }
                            }
                        },
                        toolbox: {
                            feature: {
                                dataView: {show: true, readOnly: false},
                                magicType: {show: true, type: ['line', 'bar']},
                                restore: {show: false},
                                saveAsImage: {show: true}
                            }
                        },
                        legend: {
                            data: data.legend !== undefined ? data.legend : []
                        },
                        grid: {
                            x: 70,
                            x2: 50,
                            y: 60,
                            y2: 50
                        },
                        xAxis: [
                            {
                                type: 'category',
                                data: data.xAxis,
                                axisPointer: {
                                    type: 'shadow'
                                },
                                axisLabel:{
                                    interval: 0,
                                    rotate:40
                                }
                            }
                        ],
                        yAxis:[{type : 'value',interval: 1000}],
                        series: data.series
                    };
                    return  this.option;
                },
                setChart:function(name,myChartname){
                    this.myChart[myChartname] = echarts.init(name,'macarons');//初始化echart
                }
            },
            mounted:function () {
                var self = this;
                this.setChart(self.$refs.order_echart,'order_echart');//订单图表
                this.getlist();
                window.onresize = function() {
                    self.myChart.order_echart.resize();
                };
            }
        });
    });
</script>
{/block}
