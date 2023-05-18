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
                                    <li lay-id="list" {eq name='type' value='7'}class="layui-this" {/eq} >
                                        <a href="{eq name='type' value='7'}javascript:;{else}{:Url('index',['type'=>7])}{/eq}">全部商品({$all})</a>
                                    </li>
                                    <li lay-id="list" {eq name='type' value='1'}class="layui-this" {/eq} >
                                        <a href="{eq name='type' value='1'}javascript:;{else}{:Url('index',['type'=>1])}{/eq}">出售中商品({$onsale})</a>
                                    </li>
                                    <li lay-id="list" {eq name='type' value='2'}class="layui-this" {/eq}>
                                        <a href="{eq name='type' value='2'}javascript:;{else}{:Url('index',['type'=>2])}{/eq}">待上架商品({$forsale})</a>
                                    </li>
                                    <li lay-id="list" {eq name='type' value='3'}class="layui-this" {/eq}>
                                        <a href="{eq name='type' value='3'}javascript:;{else}{:Url('index',['type'=>3])}{/eq}">仓库中商品({$warehouse})</a>
                                    </li>
                                    <li lay-id="list" {eq name='type' value='4'}class="layui-this" {/eq}>
                                        <a href="{eq name='type' value='4'}javascript:;{else}{:Url('index',['type'=>4])}{/eq}">已经售馨商品({$outofstock})</a>
                                    </li>
                                    <li lay-id="list" {eq name='type' value='5'}class="layui-this" {/eq}>
                                        <a href="{eq name='type' value='5'}javascript:;{else}{:Url('index',['type'=>5])}{/eq}">警戒库存({$policeforce})</a>
                                    </li>
                                    <li lay-id="list" {eq name='type' value='6'}class="layui-this" {/eq}>
                                        <a href="{eq name='type' value='6'}javascript:;{else}{:Url('index',['type'=>6])}{/eq}">商品回收站({$recycle})</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">商品名称</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="store_name" class="layui-input" placeholder="商品名称、关键字、编号">
                                            <input type="hidden" name="type" value="{$type}">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">商品分类</label>
                                        <div class="layui-input-inline">
                                            <select name="cate_id" lay-search="">
                                                <option value="0">全部</option>
                                                    {volist name='cate' id='vc'}
                                                    <option   value="{$vc.id}">|--{$vc.cate_name}</option>
                                                {/volist}
                                            </select>
                                        </div>
                                    </div>
                                    {switch name='type'}
                                    {case value="7"}
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
                                    {/case}
                                    {/switch}
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                <i class="layui-icon">&#xe615;</i>搜索
                                            </button>
                                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm export"  lay-submit="search" lay-filter="export">
                                                <i class="layui-icon">&#xe67d;</i>导出
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!--产品列表-->
                        <div class="layui-col-md12">
                            <div class="layui-btn-group">
                                {switch name='type'}
                                    {case value="1"}
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="action.open_add('{:Url('create')}','添加商品')">
                                        <i class="layui-icon">&#xe608;</i>添加商品
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();">
                                        <i class="layui-icon">&#xe669;</i>刷新
                                    </button>
                                    {/case}
                                    {case value="2"}
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="show">
                                        <i class="layui-icon">&#xe608;</i>批量上架
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();">
                                        <i class="layui-icon">&#xe669;</i>刷新
                                    </button>
                                    {/case}
                                    {case value="3"}
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();">
                                        <i class="layui-icon">&#xe669;</i>刷新
                                    </button>
                                    {/case}
                                    {case value="4"}
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();">
                                        <i class="layui-icon">&#xe669;</i>刷新
                                    </button>
                                    {/case}
                                    {case value="5"}
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();">
                                        <i class="layui-icon">&#xe669;</i>刷新
                                    </button>
                                    {/case}
                                    {case value="6"}
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();">
                                        <i class="layui-icon">&#xe669;</i>刷新
                                    </button>
                                    {/case}
                                    {case value="7"}
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="action.open_add('{:Url('create')}','添加商品')">
                                        <i class="layui-icon">&#xe608;</i>添加商品
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload();">
                                        <i class="layui-icon">&#xe669;</i>刷新
                                    </button>
                                    {/case}
                                {/switch}
                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="_id">
                                <p>{{d.id}}</p>
                            </script>
                            <!--图片-->
                            <script type="text/html" id="image">
                                <img style="cursor: pointer;" height="50" lay-event="open_image" src="{{d.image}}">
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
                            <!--上架|下架-->
                            <script type="text/html" id="checkboxstatus">
                                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='上架|下架'  {{ d.is_show == 1 ? 'checked' : '' }}>
                            </script>
                            <!--收藏-->
                            <script type="text/html" id="like">
                                <span><i class="layui-icon layui-icon-praise"></i> {{d.like}}</span>
                            </script>
                            <!--点赞-->
                            <script type="text/html" id="collect">
                                <span><i class="layui-icon layui-icon-star"></i> {{d.collect}}</span>
                            </script>
                            <!--产品名称-->
                            <script type="text/html" id="store_name">
                                <div>{{d.store_name}}</div>
                                {{# if(d.cate_name!=''){ }}
                                <div><span style="font-weight: bold;">分类</span>：{{d.cate_name}}</div>
                                {{# } }}
                            </script>
                            <!--操作-->
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" onclick="dropdown(this)" style="margin:5px 0;">
                                  <i class="layui-icon">&#xe625;</i>操作
                                </button>
                                <ul class="layui-nav-child layui-anim layui-anim-upbit">
                                    <li>
                                        <a href="javascript:void(0)" onclick="action.open_add('{:Url('create')}?id={{d.id}}','编辑')">
                                            <i class="iconfont icon-bianji"></i> 编辑商品
                                        </a>
                                    </li>
                                    <li>
                                        <a  href="javascript:void(0)" onclick="action.open_add('{:Url('knowledge')}?id={{d.id}}','关联专题')">
                                            <i class="layui-icon layui-icon-set"></i> 关联专题
                                        </a>
                                    </li>
                                    {{# if(d.is_del){ }}
                                    <li>
                                        <a href="javascript:void(0);" lay-event='delstor'>
                                            <i class="iconfont icon-huifu"></i> 恢复商品
                                        </a>
                                    </li>
                                    {{# }else{ }}
                                    <li>
                                        <a href="javascript:void(0);" lay-event='delstor'>
                                            <i class="iconfont icon-shanchu"></i> 移到回收站
                                        </a>
                                    </li>
                                    {{# } }}
                                    <li>
                                        <a href="javascript:void(0);" lay-event='comments'>
                                            <i class="iconfont icon-pinglunchakan"></i> 查看评论
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
<script>
    var type=<?=$type?>;
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('product_ist',['type'=>$type])}",function (){
        var join=new Array();
        switch (parseInt(type)){
            case 7:
                join=[
                    {field: 'id', title: 'ID', width:'5%',templet:'#_id',align: 'center'},
                    {field: 'store_name', title: '名称',templet:'#store_name'},
                    {field: 'image', title: '图片',templet:'#image',align: 'center',width:'8%'},
                    {field: 'stock', title: '库存',edit:'stock',align: 'center',width:'6%'},
                    {field: 'sort', title: '排序',sort: true,edit:'sort',align: 'center',width:'6%'},
                    {field: 'sales', title: '销量',sort: true,event:'sales',align: 'center',width:'6%'},
                    {field: 'price', title: '价格',align: 'center',width:'6%'},
                    {field: 'vip_price', title: '会员价',align: 'center',width:'6%'},
                    {field: 'status', title: '审核',templet:'#status',align: 'center',width:'7%'},
                    {field: 'is_show', title: '状态',templet:"#checkboxstatus",align: 'center',width:'7%'},
                    {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'}
                ];
                break;
            case 1:case 3:case 4:case 5:
                join=[
                    {field: 'id', title: 'ID', width:'5%',templet:'#_id',align: 'center'},
                    {field: 'store_name', title: '名称',templet:'#store_name'},
                    {field: 'image', title: '图片',templet:'#image',align: 'center',width:'8%'},
                    {field: 'stock', title: '库存',edit:'stock',align: 'center',width:'6%'},
                    {field: 'sort', title: '排序',sort: true,edit:'sort',align: 'center',width:'6%'},
                    {field: 'sales', title: '销量',sort: true,event:'sales',align: 'center',width:'6%'},
                    {field: 'price', title: '价格',align: 'center',width:'6%'},
                    {field: 'vip_price', title: '会员价',align: 'center',width:'6%'},
                    {field: 'is_show', title: '状态',templet:"#checkboxstatus",align: 'center',width:'7%'},
                    {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'8%'}
                ];
                break;
            case 2:
                join=[
                    {type:'checkbox'},
                    {field: 'id', title: 'ID', width:'5%',align: 'center'},
                    {field: 'store_name', title: '名称',templet:'#store_name'},
                    {field: 'image', title: '图片',templet:'#image',align: 'center',width:'8%'},
                    {field: 'stock', title: '库存',edit:'stock',align: 'center',width:'6%'},
                    {field: 'sort', title: '排序',sort: true,edit:'sort',align: 'center',width:'6%'},
                    {field: 'sales', title: '销量',sort: true,event:'sales',align: 'center',width:'6%'},
                    {field: 'price', title: '价格',align: 'center',width:'6%'},
                    {field: 'vip_price', title: '会员价',align: 'center',width:'6%'},
                    {field: 'is_show', title: '状态',templet:"#checkboxstatus",align: 'center',width:'7%'},
                    {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'}
                ];
                break;
            case 6:
                join=[
                    {field: 'id', title: '产品ID', width:'5%',align: 'center'},
                    {field: 'store_name', title: '名称',templet:'#store_name'},
                    {field: 'image', title: '图片',templet:'#image',align: 'center',width:'8%'},
                    {field: 'stock', title: '库存',edit:'stock',align: 'center',width:'6%'},
                    {field: 'sort', title: '排序',sort: true,edit:'sort',align: 'center',width:'6%'},
                    {field: 'sales', title: '销量',sort: true,event:'sales',align: 'center',width:'6%'},
                    {field: 'price', title: '价格',align: 'center',width:'6%'},
                    {field: 'vip_price', title: '会员价',align: 'center',width:'6%'},
                    {field: 'is_show', title: '状态',templet:"#checkboxstatus",align: 'center',width:'7%'},
                    {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'}
                ];
                break;
        }
        return join;
    });
    //excel下载
    layList.search('export',function(where){
        location.href=layList.U({c:'store.store_product',a:'product_ist',q:{
                store_name:where.store_name,
                type:where.type,
                excel:1
            }});
    })
    //查询
    layList.search('search',function(where){
        layList.reload(where,true);
    });
    //下拉框
    $(document).click(function (e) {
        $('.layui-nav-child').hide();
    })
    $(function () {
        $(document).on('mouseover', '.recom-item', function () {
            var that = this;
            layui.use('layer', function () {
                var layer = layui.layer;
                layer.tips('点击即可取消此推荐', that, {
                    tips: [1, '#0093dd']
                });
            });
        });
        $(document).on('mouseout', '.recom-item', function () {
            var that = this;
            layui.use('layer', function () {
                var layer = layui.layer;
                layer.closeAll();
            });
        });
        $(document).on('click', '.recom-item', function () {
            var that = this;
            var url = layList.U({ a: 'cancel_recommendation', q: { id: that.dataset.id, product_id: that.dataset.pid } });
            $eb.$swal(
                'delete',
                function () {
                    $eb.axios
                        .get(url)
                        .then(function (res) {
                            if (res.data.code == 200) {
                                $eb.$swal('success', res.data.msg);
                                window.location.reload();
                            } else {
                                return Promise.reject(res.data.msg || '删除失败');
                            }
                        })
                        .catch(function (err) {
                            $eb.$swal('error', err);
                        });
                },
                {
                    title: '确定取消此推荐？',
                    text: '取消后无法撤销，请谨慎操作！',
                    confirm: '确定取消'
                }
            );
        });
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
    //快速编辑
    layList.edit(function (obj) {
        var id=obj.data.id,value=obj.value;
        switch (obj.field) {
            case 'price':
                action.set_product('price',id,value);
                break;
            case 'stock':
                if(value < 0) return layList.msg('库存不能小于0');
                action.set_product('stock',id,value);
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
                            action.set_product('sort', id, value.trim());
                        }
                    }
                } else {
                    layList.msg('排序不能为空');
                }
                break;
            case 'ficti':
                action.set_product('ficti',id,value);
                break;
        }
    });
    //上下加产品
    layList.switch('is_show',function (odj,value) {
        if(odj.elem.checked==true){
            layList.baseGet(layList.Url({c:'store.store_product',a:'set_show',p:{is_show:1,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }else{
            layList.baseGet(layList.Url({c:'store.store_product',a:'set_show',p:{is_show:0,id:value}}),function (res) {
                layList.msg(res.msg);
            });
        }
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
                var url=layList.U({c:'store.store_product',a:'delete',q:{id:data.id}});
                if(data.is_del) var code = {title:"操作提示",text:"确定恢复产品操作吗？",type:'info',confirm:'是的，恢复该产品'};
                else var code = {title:"操作提示",text:"确定将该产品移入回收站吗？",type:'info',confirm:'是的，移入回收站'};
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
                },code)
                break;
            case 'open_image':
                $eb.openImage(data.image);
                break;
            case 'comments':
                location.href=layList.U({c:'store.store_product_reply',a:'index',q:{
                        product_id:data.id
                    }});
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
    //排序
    layList.sort(function (obj) {
        var type = obj.type;
        switch (obj.field){
            case 'id':
                layList.reload({order: layList.order(type,'p.id')},true,null,obj);
                break;
            case 'sales':
                layList.reload({order: layList.order(type,'p.sales')},true,null,obj);
                break;
        }
    });

    //自定义方法
    var action={
        set_product:function(field,id,value){
            layList.baseGet(layList.Url({c:'store.store_product',a:'set_product',q:{field:field,id:id,value:value}}),function (res) {
                layList.msg(res.msg);
            });
        },
        show:function(){
            var ids=layList.getCheckData().getIds('id');
            if(ids.length){
                layList.basePost(layList.Url({c:'store.store_product',a:'product_show'}),{ids:ids},function (res) {
                    layList.msg(res.msg);
                    layList.reload();
                });
            }else{
                layList.msg('请选择要上架的产品');
            }
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
    //多选事件绑定
    $('.layui-btn-group').find('button').each(function () {
        var type=$(this).data('type');
        $(this).on('click',function(){
            action[type] && action[type]();
        })
    });
</script>
{/block}
