{extend name="public/container"}
{block name="head_top"}
<script src="{__PLUG_PATH}city.js"></script>
<style>
    .layui-btn-xs{margin-left: 0px !important;}
    legend{
        width: auto;
        border: none;
        font-weight: 700 !important;
    }
    .site-demo-button{
        padding-bottom: 20px;
        padding-left: 10px;
    }
    .layui-form-label{
        width: auto;
    }
    .layui-input-block input{
        width: 50%;
        height: 34px;
    }
    .layui-form-item{
        margin-bottom: 0;
    }
    .layui-input-block .time-w{
        width: 200px;
    }
    .layui-btn-group button i{
        line-height: 30px;
        margin-right: 3px;
        vertical-align: bottom;
    }
    .back-f8{
        background-color: #F8F8F8;
    }
    .layui-input-block button{
        border: 1px solid #e5e5e5;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">用户管理</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">用户搜索：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="nickname" lay-verify="nickname" style="width: 100%" autocomplete="off" placeholder="请输入姓名、编号、手机号" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">状态：</label>
                                <div class="layui-input-inline">
                                    <select name="status" lay-verify="status">
                                        <option value="">全部</option>
                                        <option value="1">正常</option>
                                        <option value="0">锁定</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                    <label class="layui-form-label">身份：</label>
                                    <div class="layui-input-inline">
                                        <select name="is_promoter" lay-verify="is_promoter">
                                            <option value="">全部</option>
                                            <option value="1">推广员</option>
                                            <option value="2">会员</option>
                                            <option value="3">讲师</option>
                                        </select>
                                    </div>
                                </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">消费情况：</label>
                                <div class="layui-input-inline">
                                    <select name="pay_count" lay-verify="pay_count">
                                        <option value="">全部</option>
                                        <option value="-1">0</option>
                                        <option value="0">1+</option>
                                        <option value="1">2+</option>
                                        <option value="2">3+</option>
                                        <option value="3">4+</option>
                                        <option value="4">5+</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline" id="province-div">
                                <label class="layui-form-label">省份：</label>
                                <div class="layui-input-inline">
                                    <select name="province" lay-verify="province" lay-filter='province' id="province">
                                        <option value="" id="province-top">请选择省</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline" id="city-div">
                                <label class="layui-form-label">市区：</label>
                                <div class="layui-input-inline">
                                    <select name="city" lay-verify="city"  lay-filter='city' id="city">
                                        <option value="" id="city-top">请选择市</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">访问情况：</label>
                                <div class="layui-input-inline">
                                    <select name="user_time_type" lay-verify="user_time_type">
                                        <option value="">全部</option>
                                        <option value="visitno">时间段未访问</option>
                                        <option value="visit">时间段访问过</option>
                                        <option value="add_time">首次访问</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">选择时间：</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input time-w" name="user_time" lay-verify="user_time"  id="user_time" placeholder=" - ">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <div class="layui-input-inline">
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="" lay-filter="search" >
                                        <i class="layui-icon">&#xe615;</i>搜索
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-btn-group conrelTable">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" data-type="set_custom">
                            <i class="layui-icon">&#xe609;</i>发送客服图文消息
                        </button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" data-type="add_superior">
                            <i class="iconfont icon-bianji"></i>设置用户上级推广人
                        </button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" data-type="refresh">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                    </div>
                    <table class="layui-hide" id="userList" lay-filter="userList"></table>
                    <script type="text/html" id="nickname">
                        <a href="javascript:;" onclick="$eb.createModalFrame('用户资料','{:Url('user_data')}?uid='+{{d.uid}},{w:1000})" style="color:#0092DC">{{d.nickname ? d.nickname :'暂无昵称'}}</a>
                    </script>
                    <script type="text/html" id="avatar">
                        <div><img class="avatar open_image" style="cursor: pointer" height="50" data-image="{{d.avatar}}" src="{{d.avatar}}" alt="{{d.nickname}}"></div>
                    </script>
                    <script type="text/html" id="spread">
                        <p>{{d.spread_uid_nickname}}</p>
                        {{# if(d.valid_time && d.spread_uid){ }}
                        <p>{{d._valid_time}}</p>
                        {{# } }}
                    </script>
                    <script type="text/html" id="_time">
                        <p>首次：{{d.add_time}}</p>
                        <p>最近：{{d.last_time}}</p>
                    </script>
                    <script type="text/html" id="member">
                        {{# if(d.level==1){ }}
                        <a onclick="$eb.createModalFrame('会员记录','{:Url('member_record')}?uid='+{{d.uid}},{w:1000})" class="layui-btn layui-btn-normal layui-btn-xs">会员记录</a>
                        {{# }else{ }}
                        <span class="layui-badge">非会员</span>
                        {{# } }}
                    </script>
                    <script type="text/html" id="checkboxstatus">
                        <input type='checkbox' name='status' lay-skin='switch' value="{{d.uid}}" lay-filter='status' lay-text='正常|禁止'  {{ d.status == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="barDemo">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit" style="margin: 2px;">
                            <i class="iconfont icon-bianji"></i>编辑
                        </button>
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)" style="margin: 2px;">
                            <i class="layui-icon">&#xe625;</i>操作
                        </button>
                        <ul class="layui-nav-child layui-anim layui-anim-upbit">
                            <li>
                                <a lay-event='see' href="javascript:void(0);" >
                                    <i class="layui-icon layui-icon-edit"></i>查看详情
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('获得数据{{d.nickname}}','{:Url('access')}?uid='+{{d.uid}},{w:1200})">
                                    <i class="iconfont icon-dingdanxiangqing"></i>获得数据
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('赠送专题给{{d.nickname}}','{:Url('relation')}?uid='+{{d.uid}},{w:1200})">
                                    <i class="fa fa-paste"></i>赠送专题
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('赠送会员给{{d.nickname}}','{:Url('members')}?uid='+{{d.uid}},{w:500,h:400})">
                                    <i class="fa fa-paste"></i>赠送会员
                                </a>
                            </li>
                            {{# if(d.level==1){ }}
                            <li>
                                <a href="javascript:void(0);" lay-event='del_vip'>
                                    <i class="fa fa-trash"></i>清除会员
                                </a>
                            </li>
                            {{# } }}
                            <li>
                                <a href="javascript:void(0);" onclick="$eb.createModalFrame('赠送试卷','{:Url('send')}?uid='+{{d.uid}},{w:1000})">
                                    <i class="fa fa-paste"></i>赠送试卷
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
<script src="{__FRAME_PATH}js/content.min.js?v=1.0.0"></script>
{/block}
{block name="script"}
<script>
    $('#province-div').hide();
    $('#city-div').hide();
    layList.select('country',function (odj,value,name) {
        var html = '';
        $.each(city,function (index,item) {
            html += '<option value="'+item.label+'">'+item.label+'</option>';
        })
        if(odj.value == 'domestic'){
            $('#province-div').show();
            $('#city-div').show();
            $('#province-top').siblings().remove();
            $('#province-top').after(html);
            $('#province').val('');
            layList.form.render('select');
        }else{
            $('#province-div').hide();
            $('#city-div').hide();
        }
        $('#province').val('');
        $('#city').val('');
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    })
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
    layList.select('province',function (odj,value,name) {
        var html = '';
        $.each(city,function (index,item) {
            if(item.label == odj.value){
                $.each(item.children,function (indexe,iteme) {
                    html += '<option value="'+iteme.label+'">'+iteme.label+'</option>';
                })
                $('#city').val('');
                $('#city-top').siblings().remove();
                $('#city-top').after(html);
                layList.form.render('select');
            }
        })
    });
    layList.form.render();
    layList.tableList('userList',"{:Url('get_user_list')}",function () {
        return [
            {type:'checkbox'},
            {field: 'uid', title: 'UID', align: 'center',width:70},
            {field: 'avatar', title: '头像', event:'open_image', align: 'center', templet: '#avatar',width:100},
            {field: 'nickname', title: '姓名',templet:"#nickname",align: 'center'},
            {field: 'business', title: '讲师',align: 'center',width:'6%'},
            {field: 'user_type', title: '用户类型',width:'6%',align:'center'},
            {field: 'phone', title: '手机号',align: 'center'},
            {field: 'spread', title: '推荐人',templet:'#spread',align: 'center'},
            {field: 'now_money', title: '余额',align: 'center'},
            {field: 'gold_num', title: "{$gold_name}",align: 'center'},
            {field: 'levelType', title: '会员',align: 'center',width:'6%'},
            {field: 'member', title: '会员记录',templet:'#member',align: 'center',width:'7%'},
            {field: 'action', title: '操作', align: 'center', toolbar: '#barDemo'}
        ];
    });
    layList.date('last_time');
    layList.date('add_time');
    layList.date('user_time');
    layList.date('time');
    //监听并执行 uid 的排序
    layList.sort(function (obj) {
        var layEvent = obj.field;
        var type = obj.type;
        switch (layEvent){
            case 'uid':
                layList.reload({order: layList.order(type,'u.uid')},true,null,obj);
                break;
            case 'now_money':
                layList.reload({order: layList.order(type,'u.now_money')},true,null,obj);
                break;
            case 'integral':
                layList.reload({order: layList.order(type,'u.integral')},true,null,obj);
                break;
        }
    });
    //监听并执行 uid 的排序
    layList.tool(function (event,data) {
        var layEvent = event;
        switch (layEvent){
            case 'edit':
                $eb.createModalFrame('编辑',layList.Url({a:'edit',p:{uid:data.uid}}));
                break;
            case 'see':
                $eb.createModalFrame(data.nickname+'-用户详情',layList.Url({a:'see',p:{uid:data.uid}}),{w:800});
                break;
            case 'del_vip':
                var url=layList.U({c:'user.user',a:'del_vip',q:{uid:data.uid}});
                var code = {title:"操作提示",text:"确定将该用户的会员清除吗？",type:'info',confirm:'确定'};
                $eb.$swal('delete',function(){
                    $eb.axios.get(url).then(function(res){
                        if(res.status == 200 && res.data.code == 200) {
                            $eb.$swal('success',res.data.msg);
                            location.reload();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                },code);
                break;
        }
    });
    //监听 checkbox 的状态
    layList.switch('status',function (odj,value,name) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({a:'set_status',p:{status:1,uid:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({a:'set_status',p:{status:0,uid:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    layList.search('search',function(where){
        if(where['user_time_type'] != '' && where['user_time'] == '') return layList.msg('请选择选择时间');
        if(where['user_time_type'] == '' && where['user_time'] != '') return layList.msg('请选择访问情况');
        layList.reload(where,true);
    });

    var action={
        set_status_f:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length){
                layList.basePost(layList.Url({a:'set_status',p:{is_echo:1,status:0}}),{uids:ids},function (res) {
                    layList.msg(res.msg);
                    layList.reload();
                });
            }else{
                layList.msg('请选择要封禁的会员');
            }
        },
        set_status_j:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length){
                layList.basePost(layList.Url({a:'set_status',p:{is_echo:1,status:1}}),{uids:ids},function (res) {
                    layList.msg(res.msg);
                    layList.reload();
                });
            }else{
                layList.msg('请选择要解封的会员');
            }
        },
        set_grant:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length){
                var str = ids.join(',');
                $eb.createModalFrame('发送优惠券',layList.Url({c:'ump.store_coupon',a:'grant',p:{id:str}}),{'w':800});
            }else{
                layList.msg('请选择要发送优惠券的会员');
            }
        },
        set_template:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length){
                var str = ids.join(',');
            }else{
                layList.msg('请选择要发送模板消息的会员');
            }
        },
        set_info:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length){
                var str = ids.join(',');
                $eb.createModalFrame('发送站内信息',layList.Url({c:'user.user_notice',a:'notice',p:{id:str}}),{'w':1200});
            }else{
                layList.msg('请选择要发送站内信息的会员');
            }
        },
        set_custom:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length){
                var str = ids.join(',');
                $eb.createModalFrame('发送客服图文消息',layList.Url({c:'wechat.wechat_news_category',a:'send_news',p:{id:str}}),{'w':1200});
            }else{
                layList.msg('请选择要发送客服图文消息的用户');
            }
        },
        add_superior:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length>0){
                var str = ids.join(',');
                $eb.createModalFrame('设置用户的推广人',layList.Url({c:'user.user',a:'add_superior',p:{uid:str}}),{'w':600,'h':200});
            }else{
                layList.msg('请正确选择要设置的用户');
            }
        },
        add_teacher:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length==1){
                var str = ids.join(',');
                $eb.createModalFrame('添加老师',layList.Url({c:'educational.teacher',a:'create',p:{uid:str}}),{'w':1200});
            }else{
                layList.msg('请正确选择要成为老师的用户');
            }
        },
        add_student:function () {
            var ids=layList.getCheckData().getIds('uid');
            if(ids.length==1){
                var str = ids.join(',');
                $eb.createModalFrame('添加学员',layList.Url({c:'educational.student',a:'create',p:{uid:str}}),{'w':1200});
            }else{
                layList.msg('请正确选择要成为学员的用户');
            }
        },
        refresh:function () {
            layList.reload();
        }
    };
    $('.conrelTable').find('button').each(function () {
        var type=$(this).data('type');
        $(this).on('click',function () {
            action[type] && action[type]();
        })
    });
    $(document).on('click',".open_image",function (e) {
        var image = $(this).data('image');
        $eb.openImage(image);
    })
</script>
{/block}
