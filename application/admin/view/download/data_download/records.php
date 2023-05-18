{extend name="public/container"}
{block name="head"}
<style>
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
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">下载记录</div>
        <div class="layui-card-body">
            <div v-cloak id="app" class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label">创建时间：</label>
                    <div class="layui-input-inline" data-type="data" style="width: auto;">
                        <div class="layui-btn-group">
                            <button v-for="item in dataList" :class="[where.data == item.value ? 'layui-btn-normal' : 'layui-btn-primary', 'layui-btn layui-btn-sm']" type="button" @click="setData(item)">{{item.name}}</button>
                        </div>
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="date" placeholder="自定义" id="date" autocomplete="off" class="layui-input" style="height: 30px;">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <div class="layui-btn-container">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="search"><i class="layui-icon layui-icon-search"></i>搜索</button>
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" @click="excel"><i class="layui-icon layui-icon-export"></i>导出</button>
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" @click="refresh"><i class="layui-icon layui-icon-refresh-1"></i>刷新</button>
                        </div>
                    </div>
                </div>
            </div>
            <table id="userList" lay-filter="userList"></table>
            <script type="text/html" id="level">
                {{#  if(d.level==1){ }}
                会员
                {{#  }else{ }}
                非会员
                {{# }; }}
            </script>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    var id={$id};
    layList.tableList('userList',"{:Url('get_download_records_list')}?id={$id}",function () {
        return [
            {field: 'id', title: '编号', width:'8%',align: 'center'},
            {field: 'uid', title: 'UID', width:'8%',align: 'center'},
            {field: 'nickname', title: '昵称',align: 'center'},
            {field: 'phone', title: '电话',width:'10%',align: 'center'},
            {field: 'level', title: '身份',width:'10%',align: 'center',templet:'#level'},
            {field: 'title', title: '资料名称',width:'21%',align: 'center'},
            {field: 'number', title: '下载次数',width:'10%',align: 'center'},
            {field: 'price', title: '价格',width:'10%',align: 'center'},
            {field: 'last_study_time', title: '最后下载时间',width:'14%',align: 'center'}
        ];
    });
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                badge: [],
                dataList: [
                    {name: '全部', value: ''},
                    {name: '昨天', value: 'yesterday'},
                    {name: '今天', value: 'today'},
                    {name: '本周', value: 'week'},
                    {name: '本月', value: 'month'},
                    {name: '本季度', value: 'quarter'},
                    {name: '本年', value: 'year'},
                ],
                where:{
                    id:id,
                    excel:0,
                    data:''
                },
                showtime: false,
            },
            methods: {
                setData:function(item){
                    console.log(item);
                    var that=this;
                    if(item.is_zd==true){
                        that.showtime=true;
                        this.where.data=this.$refs.date_time.innerText;
                    }else{
                        this.showtime=false;
                        this.where.data=item.value;
                    }
                },
                search:function () {
                    this.where.excel=0;
                    this.where.id=id;
                    layList.reload(this.where,true);
                },
                excel:function () {
                    this.where.id=id;
                    this.where.excel=1;
                    location.href=layList.U({a:'get_download_records_list',q:this.where});
                },
                refresh:function () {
                    window.location.reload();
                }
            },
            mounted:function () {
                var that=this;
                layList.laydate.render({
                    elem:'#date',
                    range:true,
                    change:function (value){
                        that.where.data=value;
                    }
                });
            }
        })
    });
</script>
{/block}
