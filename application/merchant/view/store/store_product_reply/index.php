{extend name="public/container"}
{block name="head_top"}
<style>
.layui-table-cell p{height:auto;}
.layui-table-cell img{width: 40px;height: 40px;cursor: pointer;}
.layui-layer-imgsee {display: none;}
.layui-layer-shade{opacity: 0.6 !important;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">商品评论</div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form class="layui-form layui-form-pane" action="">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label class="layui-form-label">是否回复</label>
                                <div class="layui-input-inline">
                                    <select name="is_reply">
                                        <option value="">全部</option>
                                        <option value="1">已回复</option>
                                        <option value="0">未回复</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">商品信息</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="store_name" class="layui-input" placeholder="商品名称、编号">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">用户名称</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" class="layui-input" placeholder="UID、昵称">
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">内容搜索</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="comment" class="layui-input" placeholder="评论内容">
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
                <div class="layui-col-md12">
                    <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                            <i class="layui-icon">&#xe669;</i>刷新
                        </button>
                        {if condition="$product_id gt 0"}
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.history.back()">
                            <i class="layui-icon layui-icon-return"></i>返回
                        </button>
                        {/if}
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="comment">
                        <p>{{d.comment}}</p>
                        {{#  layui.each(d.pics, function(index, item){ }}
                        <img src="{{item}}" onclick="openImage(this)">
                        {{#  }); }}
                    </script>
                    <script type="text/html" id="is_selected">
                        <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-text='加精|未加精' lay-filter='is_selected'  {{ d.is_selected == 1 ? 'checked' : '' }}>
                    </script>
                    <script type="text/html" id="act">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event='reply'>
                            <i class="layui-icon">&#xe642;</i>回复
                        </button>
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var product_id={$product_id};
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList('List',"{:Url('productReplyList')}?product_id="+product_id,function (){
        return [
                {field: 'id', title: '编号', width:'5%',align:'center'},
                {field: 'nickname', title: '昵称', width:'7%',align:'center'},
                {field: 'store_name', title: '商品名称', width:'12%',align:'center'},
                {field: 'product_score', title: '商品评分', width:'6%',align:'center'},
                {field: 'service_score', title: '服务评分', width:'6%',align:'center'},
                {field: 'delivery_score', title: '物流评分', width:'6%',align:'center'},
                {field: 'comment', title: '评论内容', toolbar:'#comment'},
                {field: 'merchant_reply_content', title: '回复内容', align:'center'},
                {field: 'is_selected', title: '加精', align:'center', width:'7%',toolbar:'#is_selected'},
                {field: 'add_time', title: '评论时间',width:'12%',align:'center'},
                {field: 'right', title: '操作',align:'center',width:'6%',toolbar:'#act'}
            ];
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
                ,area: ['70%', '92%']
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
        layList.reload(where,true);
    });
    layList.switch('is_selected',function (odj,value) {
        var is_show_value = 0
        if(odj.elem.checked==true){
            var is_show_value = 1
        }
        action.set_value('is_selected',value,is_show_value);
    });
    //监听并执行排序
    layList.sort(['id','sort'],true);
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'delstor':
                var url=layList.U({c:'store.store_product_reply',a:'delete',q:{id:data.id}});
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
            case 'reply':
                var url =layList.U({c:'store.store_product_reply',a:'set_reply'}),
                    id=data.id,
                    make=data.merchant_reply_content;
                $eb.$alert('textarea',{title:'请添加回复内容',value:make},function (result) {
                    if(result){
                        $.ajax({
                            url:url,
                            data:'id='+id+'&content='+result,
                            type:'post',
                            dataType:'json',
                            success:function (res) {
                                layList.msg(res.msg,function () {
                                    location.reload();
                                });
                            }
                        })
                    }else{
                        $eb.$swal('error','请输入回复内容');
                    }
                });
                break;
            case 'refining':
                var url=layList.U({c:'store.store_product_reply',a:'refining_reply',q:{id:data.id}});
                $eb.$swal('delete',function(){
                    $eb.axios.post(url).then(function(res){
                        if(res.data.code == 200) {
                            window.location.reload();
                            $eb.$swal('success', res.data.msg);
                        }else
                            $eb.$swal('error',res.data.msg||'操作失败!');
                    });
                },{
                    title:'确定给这个评论加精吗?',
                    text:'加精后无法撤销，请谨慎操作！',
                    confirm:'加精'
                });
                break;
        }
    })
    function openImage(img) {
        layui.layer.photos({
            photos: {
                data: [
                    {
                        src: img.src
                    }
                ]
            },
            anim: 5
        });
    }
</script>
{/block}
