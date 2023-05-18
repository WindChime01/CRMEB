{extend name="public/container"}
{block name='head_top'}
<style>
    .go-live{width: 100%;background-color: #eeeeee;display: none;}
    .go-live .live-box{padding: 10px 0 10px 30px;background-color: #ffffff;border-radius: 5px;margin-top: 30px;}
    .go-live .live-box .live-text{font-size: 15px;color: #0092DC;margin: 5px;padding: 10px 0;}
    .go-live .live-box .live-title{text-align: center;}
    .go-live .live-box .live-text p{color:#333333;padding:10px;display: inline-block;width: 80%;background-color: #eeeeee;border-radius: 10px;}
    .go-live .live-box .live-text label{width: 8%;}
    .layui-table-cell img{max-width: 100%;height: 50px;cursor: pointer;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15"  id="app">
                        <div class="layui-col-md12">
                            <div class="layui-tab layui-tab-brief" lay-filter="tab">
                                <ul class="layui-tab-title">
                                    <li lay-id="list" {eq name='type' value='1'}class="layui-this" {/eq} >
                                        <a href="{eq name='type' value='1'}javascript:;{else}{:Url('special_live',['special_type'=>4,'type'=>1])}{/eq}">直播列表</a>
                                    </li>
                                    <li lay-id="list" {eq name='type' value='2'}class="layui-this" {/eq}>
                                        <a href="{eq name='type' value='2'}javascript:;{else}{:Url('index',['special_type'=>4,'type'=>2])}{/eq}">直播间管理</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">直播搜索</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="store_name" class="layui-input" placeholder="直播标题、简介、编号">
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
                                        <label class="layui-form-label">审核状态</label>
                                        <div class="layui-input-inline">
                                            <select name="status">
                                                <option value="">全部</option>
                                                <option value="1">通过</option>
                                                <option value="0">未审核</option>
                                                <option value="-1">未通过</option>
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
                                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
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
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="action.open_add('{:Url('special.special_type/add',['special_type' =>$special_type])}','添加{$special_title}')">
                                    <i class="layui-icon">&#xe608;</i>添加{$special_title}
                                </button>
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()">
                                    <i class="layui-icon">&#xe669;</i>刷新
                                </button>
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="is_pink">
                                {{# if(d.is_pink){ }}
                                <span class="layui-badge layui-bg-blue">开启</span>
                                {{# }else{ }}
                                <span class="layui-badge">关闭</span>
                                {{# } }}
                            </script>
                            <script type="text/html" id="status">
                                {{# if(d.status==1){ }}
                                <span class="layui-badge layui-bg-blue">通过</span>
                                {{# }else if(d.status==0){ }}
                                <span class="layui-badge layui-bg-blue">未审核</span>
                                {{# }else{ }}
                                <span class="layui-badge">未通过</span>
                                <span class="layui-badge layui-bg-blue" lay-event='fail'>查看原因</span>
                                {{# } }}
                            </script>
                            <script type="text/html" id="is_show">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='上架|下架'  {{ d.is_show == 1 ? 'checked' : '' }}>
                            </script>
                            <script type="text/html" id="is_mer_visible">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_mer_visible' lay-text='是|否'  {{ d.is_mer_visible == 1 ? 'checked' : '' }}>
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
                                        <a href="javascript:void(0)" onclick="action.open_add('{:Url('special.special_type/add')}?id={{d.id}}&special_type={$special_type}','编辑直播')" >
                                            <i class="iconfont icon-bianji"></i> 编辑直播
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" lay-event='go_live'>
                                            <i class="iconfont icon-quzhibo"></i> 去直播
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.title}}-拼团管理','{:Url('special.special_type/pink')}?special_id={{d.id}}',{h:500})">
                                            <i class="iconfont icon-pintuanshezhi"></i> 拼团设置
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{:Url('ump.store_combination/combina_list')}?cid={{d.id}}&special_type={$special_type}" >
                                            <i class="iconfont icon-chakanpintuan"></i> 查看拼团
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" onclick="$eb.createModalFrame('{{d.title}}-直播回放','{:Url('download_list')}?special_id={{d.id}}',{w:1200})">
                                            <i class="iconfont icon-huifang"></i> 直播回放
                                        </a>
                                    </li>

                                    <li>
                                        <a lay-event='delect' href="javascript:void(0)">
                                            <i class="iconfont icon-shanchu"></i> 删除直播
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

<div class="go-live">
    <div class="live-box">
        <div class="live-title">
            <h2>直播推流地址</h2>
        </div>
        <div class="live-text">
            <label>服务器</label>
            <p id="live-href"></p>
            <button type="button" class="layui-btn layui-btn-normal copy" data-id="live-href">复制</button>
        </div>
        <div class="live-text">
            <label>串流密钥</label>
            <p id="live-code"></p>
            <button type="button" class="layui-btn layui-btn-normal copy" data-id="live-code">复制</button>
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
    layList.tableList({o:'List', done:function () {}},"{:Url('special_list',['special_type'=>$special_type])}",function (){
        return [
            {field: 'id', title: '编号', width:'6%',align: 'center'},
            {field: 'title', title: '直播标题',align: 'left'},
            {field: 'subject_name', title: '分类',align: 'center',width:'8%'},
            {field: 'image', title: '封面',templet:'#image',align: 'center',width:'7%'},
            {field: 'stream_name', title: '直播间ID',align: 'center',width:'7%'},
            {field: 'money', title: '价格',align: 'center',width:'6%'},
            {field: 'member_money', title: '会员价',align: 'center',width:'6%'},
            {field: 'pink_money', title: '拼团价',align: 'center',width:'6%'},
            {field: 'is_pink', title: '拼团状态',templet:'#is_pink',align: 'center',width:'6%'},
            {field: 'sort', title: '排序',sort: true,event:'sort',edit:'sort',align: 'center',width:'5%'},
            {field: 'status', title: '审核',templet:'#status',align: 'center',width:'7%'},
            {field: 'is_show', title: '状态',templet:'#is_show',align: 'center',width:'7%'},
            {field: 'is_mer_visible', title: '仅会员可见',templet:'#is_mer_visible',align: 'center',width:'7%'},
            {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'7%'},
        ];
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
    //自定义方法
    var action= {
        set_value: function (field, id, value, model_type) {
            layList.baseGet(layList.Url({
                c: 'special.special_type' ,
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
        },
        //直播间转移
        move_live_admin: function(data){
                    layer.open({
                    type: 1
                    ,scrollbar: true
                    ,area: ['500px', '70%']
                    ,content: $('.move_live_admin')
                    ,btn: '转让'
                    ,btnAlign: 'c' //按钮居中
                    ,shade: 0 //不显示遮罩
                    ,yes: function(){
                       var admin_id = $("#admin_id").val();
                       if (!admin_id) {
                           layer.msg("请选择转让账号", {
                               icon: 2,
                               time: 2000
                           });
                           return false;
                       }
                        layList.baseGet(layList.U({a:'move_live_admin',q:{special_id:data.id, admin_id:admin_id}}),function(res){
                            if (res.code == 200) {
                                layer.closeAll();
                                layer.msg("转让成功", {
                                    icon: 1,
                                    time: 2000
                                });
                            }else{
                                layer.closeAll();
                                layer.msg("转让失败", {
                                    icon: 2,
                                    time: 2000
                                });
                            }
                        }, function (res) {
                            layer.msg(res.msg, {
                                icon: 2,
                                time: 2000
                            });
                        })
                    }
                    ,cancel:function () {
                        $('.live_remind').hide();
                    }
                });
        }
    }
    $('.copy').each(function () {
        $(this).on('click',function () {
            copy($(this).data('id'),function () {
                layList.msg('复制成功');
            });
        });
    })
    function copy (id, attr,errorFn)
    {
        var target = null,successFn=null;
        if(typeof attr=='function'){
            successFn=attr;
            attr='';
        }
        if (attr && typeof attr=='string') {
            target = document.createElement('div');
            target.id = 'tempTarget';
            target.style.opacity = '0';
            if (id) {
                var curNode = document.querySelector('#' + id);
                target.innerText = curNode[attr];
            } else {
                target.innerText = attr;
            }
            document.body.appendChild(target);
        } else {
            target = document.querySelector('#' + id);
        }

        try {
            var range = document.createRange();
            range.selectNode(target);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            successFn && successFn();
        } catch (e) {
            errorFn && errorFn();
        }

        if (attr) {
            target.parentElement.removeChild(target);
        }
    }
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
            is_show: where.is_show,
            status: where.status,
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
    layList.switch('is_mer_visible',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_mer_visible',value,is_show_value,'special');
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
                if(value < 0) return layList.msg('虚拟不能小于0');
                action.set_value('fake_sales',id,value,'special');
                break;
            case 'online_num':
                layList.baseGet(layList.Url({
                    c: 'live.aliyun_live' ,
                    a: 'set_value',
                    q: {stream_name: obj.data.stream_name,field:'online_num',value: value}
                }), function (res) {
                    layList.msg(res.msg);
                });
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
                            $eb.$swal('success','删除成功！');
                            obj.del();
                        }else
                            return Promise.reject(res.data.msg || '删除失败')
                    }).catch(function(err){
                        $eb.$swal('error',err);
                    });
                });
                break;
                case 'recommend':
                var url=layList.U({a:'recommend',q:{id:data.id}});
                    $eb.$swal('delete',function(){
                        $eb.axios.get(url).then(function(res){
                            if(res.status == 200 && res.data.code == 200) {
                                $eb.$swal('success',res.data.msg);
                                obj.update({is_publish:1});
                            }else
                                return Promise.reject(res.data.msg || '删除失败')
                        }).catch(function(err){
                            $eb.$swal('error',err);
                        });
                    },{title:"确认要把该直播专题推荐到首页弹窗吗?",text:'确认后无法修改',confirm:'确认'});
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
            case 'go_live':
                layList.baseGet(layList.U({a:'is_audit',q:{id:data.id}}),function(res){
                     if(res.data.status==1){
                         layList.baseGet(layList.U({a:'go_live',q:{id:data.id}}),function(res){
                             $('#live-href').text(res.data.href);
                             $('#live-code').text(res.data.code);
                             layList.layer.open({
                                 type: 1,
                                 area: ['800px', 'auto'], //宽高
                                 content:$('.go-live'),
                                 title:false,
                                 cancel:function () {
                                     $('.go-live').hide();
                                 }
                             });
                         },function (res) {
                             layList.msg(res.msg);
                         });
                     }else if(res.data.status==0){
                         layList.msg('请等待总平台审核');
                     }else if(res.data.status==-1){
                         layList.msg('审核未通过');
                     }else{
                         $eb.createModalFrame(data.title+'--直播审核',layList.U({a:'audit',q:{id:data.id}}),{w:800,h:600});
                     }
                });
                break;
            case 'move_live_admin':
                action.move_live_admin(data);
                break;
            case 'fail':
                layer.open({
                    type: 1,
                    skin: 'layui-layer-rim', //加上边框
                    area: ['420px', '240px'], //宽高
                    title: '审核未通过原因',
                    content: data.fail_message
                });
                break;
        }
    })
</script>
{/block}
