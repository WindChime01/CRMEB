{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <div class="layui-tab layui-tab-brief" lay-filter="tab">
                                <ul class="layui-tab-title">
                                    <li lay-id="list" {eq name='activity_type' value='1'}class="layui-this" {/eq} >
                                    <a href="{eq name='activity_type' value='1'}javascript:;{else}{:Url('index',['activity_type'=>1])}{/eq}">活动列表</a>
                                    </li>
                                    <li lay-id="list" {eq name='activity_type' value='2'}class="layui-this" {/eq}>
                                    <a href="{eq name='activity_type' value='2'}javascript:;{else}{:Url('card_index',['activity_type'=>2])}{/eq}">兑换码列表</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">活动</label>
                                        <div class="layui-input-inline">
                                            <select name="card_batch_id">
                                                <option value="">全部</option>
                                                {foreach $batch_list as $vo}
                                                <option value="{$vo.id}" {if condition="$card_batch_id eq $vo['id']"} selected {/if}>{$vo.title}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">兑换码</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="exchange_code" class="layui-input" placeholder="请输入兑换码">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">电话</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="phone" class="layui-input" placeholder="请输入电话">

                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">兑换状态</label>
                                        <div class="layui-input-inline">
                                            <select name="is_use">
                                                <option value="">全部</option>
                                                <option value="1">已兑换</option>
                                                <option value="0">未兑换</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">状态</label>
                                        <div class="layui-input-inline">
                                            <select name="is_status">
                                                <option value="">全部</option>
                                                <option value="1">开启</option>
                                                <option value="0">结束</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">兑换时间</label>
                                        <div class="layui-input-inline" style="width: 260px;">
                                            <input type="text" name="datetime" class="layui-input" id="datetime" placeholder="时间范围">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <button class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                <i class="layui-icon">&#xe615;</i>搜索
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--产品列表-->
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="is_status">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_status' lay-text='开启|结束'  {{ d.status == 1 ? 'checked' : '' }}>
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
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('card_list',['card_batch_id'=>$card_batch_id])}",function (){
        return [
            {field: 'id', title: '编号', sort: true,event:'id',width:'5%',align: 'center'},
            {field: 'exchange_code', title: '兑换码',align: 'center'},
            {field: 'username', title: '兑换人',align: 'center'},
            {field: 'user_phone', title: '兑换人电话',align: 'center'},
            {field: 'use_time', title: '兑换时间',align: 'center'},
            {field: 'status', title: '状态', templet:'#is_status',align: 'center'}
        ];
    });
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
            start_time: start_time,
            end_time: end_time,
            card_batch_id: where.card_batch_id,
            exchange_code: where.exchange_code,
            phone: where.phone,
            is_use: where.is_use,
            is_status: where.is_status
        },true);
    });
    layList.switch('is_status',function (odj,value) {
        var is_status_value = 0
        if(odj.elem.checked==true){
            var is_status_value = 1
        }
        action.set_value('status',value,is_status_value,'special_exchange');
    });
    layList.sort(['id','sort'],true);
</script>
{/block}

