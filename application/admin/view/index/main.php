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

    .layui-form-label {
        width: 100px;
        padding: 5px 15px;
    }
    .layui-input-block {
        margin-left: 100px;
    }
    .layui-btn-group .layui-btn-normal {
        border: 1px solid #0092DC;
        border-left: none;
    }
    .layui-btn-group .layui-btn-normal:first-child {
        border-left: 1px solid #0092DC;
    }
    .check-auth-layer .layui-layer-content {
        padding: 15px;
        color: #FF5722;
    }
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">新增学员<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{$first_line.day.data}</p>
                    <p>
                        今日新增学员
                        <span class="layuiadmin-span-color">
                        {$first_line.day.percent}%
                        {if condition='$first_line.day.is_plus egt 0'}<i class="fa {if condition='$first_line.day.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">学习次数<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{$first_line.records.data}</p>
                    <p>
                        今日学习次数
                        <span class="layuiadmin-span-color">
                        {$first_line.records.percent}%
                        {if condition='$first_line.records.is_plus egt 0'}<i class="fa {if condition='$first_line.records.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">新增会员<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{$first_line.d_vip_num.data}</p>
                    <p>
                        今日新增会员
                        <span class="layuiadmin-span-color">
                        {$first_line.d_vip_num.percent}%
                        {if condition='$first_line.d_vip_num.is_plus egt 0'}<i class="fa {if condition='$first_line.d_vip_num.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">会员充值<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{$first_line.d_vip_price.data}</p>
                    <p>
                        今日会员充值金额
                        <span class="layuiadmin-span-color">
                        {$first_line.d_vip_price.percent}%
                        {if condition='$first_line.d_vip_price.is_plus egt 0'}<i class="fa {if condition='$first_line.d_vip_price.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                        </span>
                    </p>
                </div>
            </div>
        </div>
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
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">充值订单<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{$first_line.d_recharge_num.data}</p>
                    <p>
                        今日充值订单
                        <span class="layuiadmin-span-color">
                        {$first_line.d_recharge_num.percent}%
                        {if condition='$first_line.d_recharge_num.is_plus egt 0'}<i class="fa {if condition='$first_line.d_recharge_num.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">充值收入<span class="layui-badge layui-bg-blue layuiadmin-badge">今</span></div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">{$first_line.d_recharge_price.data}</p>
                    <p>
                        今日充值交易额
                        <span class="layuiadmin-span-color">
                        {$first_line.d_recharge_price.percent}%
                        {if condition='$first_line.d_recharge_price.is_plus egt 0'}<i class="fa {if condition='$first_line.d_recharge_price.is_plus eq 1'}fa-level-up{else /}fa-level-down{/if}"></i>{/if}
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
                                        <button type="button" :class="[type === '0' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('0')">课程</button>
                                        <button type="button" :class="[type === '1' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('1')">会员</button>
                                        <button type="button" :class="[type === '2' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('2')">商城</button>
                                        <button type="button" :class="[type === '3' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('3')">资料</button>
                                        <button type="button" :class="[type === '4' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('4')">考试</button>
                                        <button type="button" :class="[type === '5' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('5')">报名</button>
                                        <button type="button" :class="[type === '6' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" @click="getlisttype('6')">充值</button>
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
                                    {{typename}}订单-{{cyclename}}
                                    <div class="layui-btn-group layuiadmin-btn-group">
                                        <button type="button" :class="[active === 'thirtyday' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-xs']" @click="getlist('thirtyday')">30天</button>
                                        <button type="button" :class="[active === 'week' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-xs']" @click="getlist('week')">周</button>
                                        <button type="button" :class="[active === 'month' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-xs']" @click="getlist('month')">月</button>
                                        <button type="button" :class="[active === 'year' ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-xs']" @click="getlist('year')">年</button>
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
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    用户
                </div>
                <div class="layui-card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="flot-chart">
                                <div class="flot-chart-content" ref="user_echart" id="flot-dashboard-chart2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12" >
            <div class="mask" v-show="masks" @click="masks = false">
                <img src="{__ADMIN_PATH}images/qrcode.jpg"/>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    var ip="{$ip}";
     require(['vue','axios'],function(Vue,axios){
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
                ip:ip,
                type:'0',//0：课程 1：会员 2：商城 3：资料 4：考试 5：报名 6：充值
                typename:'课程',
                cycle:'week',
                masks:false
            },
            methods:{
                info:function () {
                    var that=this;
                    axios.get("{:Url('userchart')}").then(function (res) {
                        that.myChart.user_echart.setOption(that.userchartsetoption(res.data.data));
                    });
                },
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
                                    that.cyclename = '近30天';
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
                            },
                            right: '5%'
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
                userchartsetoption:function(data){
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
                                dataView: {show: false, readOnly: false},
                                magicType: {show: true, type: ['line', 'bar']},
                                restore: {show: false},
                                saveAsImage: {show: false}
                            },
                            right: '5%'
                        },
                        legend: {
                            data:data.legend
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
                                }
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value',
                                name: '人数',
                                min: 0,
                                max: data.yAxis ? data.yAxis.maxnum : 0,
                                interval: 500,
                                axisLabel: {
                                    formatter: '{value} 人'
                                }
                            }
                        ],
                        series : [ {
                            name : '人数',
                            type : 'bar',
                            barWidth : '50%',
                            itemStyle: {
                                normal: {
                                    label: {
                                        show: true, //开启显示
                                        position: 'top', //在上方显示
                                        textStyle: { //数值样式
                                            color: '#666',
                                            fontSize: 12
                                        }
                                    }
                                }
                            },
                            data : data.series
                        } ]
                    };
                    return  this.option;
                },
                setChart:function(name,myChartname){
                    this.myChart[myChartname] = echarts.init(name,'macarons');//初始化echart
                },
                checkAuth:function(){
                    layui.$.getJSON("{:url('check_auth')}", function (data) {
                        var content = '';
                        if (typeof data === 'string') {
                            data = layui.$.parseJSON(data);
                        }
                        if (data.code === 200) {
                            if (data.data.status === 1) {
                                content = data.data.msg;
                            }
                        } else {
                            content = data.msg;
                        }
                        if (!content) {
                            return;
                        }
                        layui.layer.open({
                            type: 1,
                            offset: 'rt',
                            content: content,
                            btn: '关闭',
                            btnAlign: 'c',
                            shade: 0,
                            skin: 'check-auth-layer',
                            yes: function (index) {
                                layui.layer.close(index);
                            }
                        });
                    });
                }
            },
            created: function () {
                this.checkAuth();
            },
            mounted:function () {
                var self = this;
                this.setChart(self.$refs.order_echart,'order_echart');//订单图表
                this.setChart(self.$refs.user_echart,'user_echart');//用户图表
                this.info();
                this.getlist();
                if(this.ip=='172.31.152.14'){
                    this.masks=true;
                }
                window.onresize = function() {
                    self.myChart.order_echart.resize();
                    self.myChart.user_echart.resize();
                };
            }
        });
    });
</script>
{/block}
