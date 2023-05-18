{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">提现申请</div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">搜索</label>
                        <div class="layui-input-inline">
                            <input type="text" name="nireid" class="layui-input" placeholder="微信昵称/姓名/支付宝账号/银行卡号/微信号">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">时间范围</label>
                        <div class="layui-input-inline" style="width: 260px;">
                            <input type="text" name="datetime" class="layui-input" id="datetime" placeholder="时间范围">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">提现状态</label>
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">全部</option>
                                <option value="0">未提现</option>
                                <option value="-1">未通过</option>
                                <option value="1">已通过</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">提现方式</label>
                        <div class="layui-input-inline">
                            <select name="extract_type">
                                <option value="">全部</option>
                                <option value="alipay">支付宝</option>
                                <option value="bank">银行卡</option>
                                <option value="weixin">微信</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" lay-submit="search" lay-filter="search"><i class="layui-icon">&#xe615;</i>搜索</button>
                        <button class="layui-btn layui-btn-sm layui-btn-normal" type="button" onclick="window.location.reload()"><i class="layui-icon">&#xe669;</i>刷新</button>
                    </div>
                </div>
            </form>
            <table id="userList" lay-filter="userList"></table>
            <script type="text/html" id="extract_type">
                {{# if(d.extract_type == 'bank'){ }}
                姓名：{{d.real_name}}<br>
                银行卡号：{{d.bank_code}}
                <br/>
                开户行地址：{{d.bank_address}}
                {{# }else if(d.extract_type == 'weixin'){ }}
                昵称：{{d.nickname}}<br>
                微信号：{{d.wechat}}
                {{# }else if(d.extract_type == 'alipay'){ }}
                姓名：{{d.real_name}}<br>
                支付宝账号：{{d.alipay_code}}
                {{# }else{ }}
                提现到余额
                {{# } }}
            </script>
            <script type="text/html" id="status">
                {{# if(d.status ==1){ }}
                提现通过<br/>
                {{# }else if(d.status == -1){ }}
                提现未通过<br/>
                未通过原因：{{d.fail_msg}}
                <br>
                未通过时间：{{d.fail_time}}
                {{# }else{ }}
                待审核<br/>
                <button lay-event='fail' class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button"><i class="layui-icon">&#x1006;</i>不通过</button>
                <button lay-event='succ' class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button"><i class="layui-icon">&#xe605;</i>通过</button>
                {{# } }}
            </script>
            <script type="text/html" id="act">
                <button lay-event='remarks' class="layui-btn layui-btn-normal layui-btn-xs" type="button">备注</button>
            </script>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    layList.form.render();
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    layList.tableList('userList',"{:Url('get_user_extract')}",function () {
        return [
            {field: 'id', title: '编号', align: 'center',width:'6%'},
            {field: 'name', title: '用户信息',align: 'center',width:'12%'},
            {field: 'extract_price', title: '提现金额',align: 'center',width:'8%'},
            {field: 'extract_type', title: '提现方式',templet:'#extract_type',align: 'left',width:'20%'},
            {field: 'add_time', title: '添加时间',align: 'center',width:'10%'},
            {field: 'mark', title: '备注',align: 'center',width:'15%'},
            {field: 'status', title: '审核状态',templet:'#status',align: 'center',width:'20%'},
            {title: '操作',align:'center',toolbar:'#act'}
        ];
    });

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
            nireid: where.nireid,
            status: where.status,
            extract_type: where.extract_type,
            start_time: start_time,
            end_time: end_time,
        },true);
    });
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'succ':
                var url=layList.U({a:'succ',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success',res.data.msg);
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                }, {
                    title:'确定审核通过?',
                    text:'通过后无法撤销，请谨慎操作！',
                    confirm:'审核通过'
                });
                break;
            case 'fail':
                var url=layList.U({a:'fail',q:{id:data.id}});
                $eb.$alert('textarea',{
                    title:'请输入未通过愿意',
                    value:'输入信息不完整或有误!',
                },function(value){
                    $eb.axios.post(url,{message:value}).then(function(res){
                        if(res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success', res.data.msg);
                        }else
                            $eb.$swal('error',res.data.msg||'操作失败!');
                    });
                });
                break;
            case 'remarks':
                var url=layList.U({a:'remarks',q:{id:data.id}});
                $eb.$alert('textarea',{
                    title:'请输入提现备注',
                    value:'',
                },function(value){
                    $eb.axios.post(url,{message:value}).then(function(res){
                        if(res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success', res.data.msg);
                        }else
                            $eb.$swal('error',res.data.msg||'操作失败!');
                    });
                });
                break;
            case 'open_image':
                $eb.openImage(data.merchant_head);
                break;
        }
    });
</script>
{/block}
