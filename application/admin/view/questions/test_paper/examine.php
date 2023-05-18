{extend name="public/container"}
{block name="head_top"}
<style>
    .layui-table-cell img{max-width: 100%;height: 50px;cursor: pointer;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">{$type==1 ? '练习列表':'考试列表'}</div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">{$type==1 ? '练习搜索':'考试搜索'}</label>
                        <div class="layui-input-inline">
                            <input type="text" name="title" class="layui-input" placeholder="{$type==1 ? '练习':'考试'}标题">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">讲师</label>
                        <div class="layui-input-inline">
                            <select name="mer_id" lay-search="">
                                <option value="">全部</option>
                                {volist name='mer_list' id='vc'}
                                <option  value="{$vc.id}">{$vc.mer_name}</option>
                                {/volist}
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">{$type==1 ? '练习分类':'考试分类'}</label>
                        <div class="layui-input-inline">
                            <select name="pid" lay-search="">
                                <option value="0">全部</option>
                                {volist name='category' id='vc'}
                                    <option {if $vc.pid==0}disabled{/if} value="{$vc.id}">{$vc.html}{$vc.title}</option>
                                {/volist}
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="search" lay-filter="search">
                                <i class="layui-icon layui-icon-search"></i>搜索
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="layui-btn-container">
                <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" data-type="refresh" onclick="window.location.reload()">
                    <i class="layui-icon layui-icon-refresh-1"></i>刷新
                </button>
            </div>
            <table id="List" lay-filter="List"></table>
            <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
            <script type="text/html" id="status">
                {{# if(d.status==0){ }}
                <button lay-event='fail' class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button"><i class="layui-icon">&#x1006;</i>不通过</button>
                <button lay-event='succ' class="layui-btn layui-btn-normal layui-btn-xs zsff-success" type="button"><i class="layui-icon">&#xe605;</i>通过</button>
                {{# }else if(d.status==-1){ }}
                <button class="layui-btn layui-btn-danger layui-btn-xs zsff-fail" type="button"><i class="layui-icon">&#x1006;</i>不通过</button>
                <br>
                原因：{{d.fail_message}}
                <br>
                时间：{{d.fail_time}}
                {{# } }}
            </script>
            <script type="text/html" id="is_show">
                <input type='checkbox' name='id' lay-skin='switch' value="{{d.id}}" lay-filter='is_show' lay-text='显示|隐藏'  {{ d.is_show == 1 ? 'checked' : '' }}>
            </script>
            <script type="text/html" id="image">
                <img width="89" height="50" lay-event='open_image' src="{{d.image}}">
            </script>
            <script type="text/html" id="recommend">
                {{#  layui.each(d.recommend, function(index, item){ }}
                <span class="layui-badge layui-bg-blue recom-item" data-id="{{index}}" data-pid="{{d.id}}" style="margin-bottom: 5px;">{{item}}</span>
                {{#  }); }}
            </script>
            <script type="text/html" id="act">
                {{# if (d.status) { }}
                <a class="layui-btn layui-btn-xs layui-btn-disabled">审核</a>
                {{# } else { }}
                <a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="detail">审核</a>
                {{# } }}
            </script>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    var $ = layui.jquery;
    var layer = layui.layer;
    var type=<?=$type?>;
    var types=type==1 ? '添加练习' : '添加考试';
    //实例化form
    layList.form.render();
    layList.date({elem: '#datetime', type: 'datetime', range: '~'});
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('get_test_paper_examine_list',['type'=>$type])}",function (){
        var join=new Array();
        switch (parseInt(type)) {
            case 1:
                join = [
                    {field: 'id', title: '编号', width: '4%', align: 'center'},
                    {field: 'cate', title: '分类', width: '6%'},
                    {field: 'mer_name', title: '讲师',align: 'center',width:'6%'},
                    {field: 'title', title: '练习标题'},
                    {field: 'item_number', title: '题目数量', align: 'center', width: '7%'},
                    {field: 'single_number', title: '单选题数量', align: 'center', width: '7%'},
                    {field: 'many_number', title: '多选题数量', align: 'center', width: '7%'},
                    {field: 'judge_number', title: '判断题数量', align: 'center', width: '7%'},
                    {field: 'answer', title: '答题人数', align: 'center', width: '6%'},
                    {field: 'status', title: '状态',templet:'#status',align: 'center',width:'18%'},
                    {title: '操作', align: 'center', toolbar: '#act', width: '7%'}
                ];
                break;
            case 2:
                join = [
                    {field: 'id', title: '编号', width: '4%', align: 'center'},
                    {field: 'cate', title: '分类', width: '5%'},
                    {field: 'mer_name', title: '讲师',align: 'center',width:'6%'},
                    {field: 'title', title: '考试标题'},
                    {field: 'image', title: '封面图', templet: '#image',align: 'center', width: '7%'},
                    {field: 'item_number', title: '题目数量', align: 'center', width: '5%'},
                    {field: 'single_number', title: '单选题数量', align: 'center', width: '5%'},
                    {field: 'many_number', title: '多选题数量', align: 'center', width: '5%'},
                    {field: 'judge_number', title: '判断题数量', align: 'center', width: '5%'},
                    {field: 'answer', title: '答题人数', align: 'center', width: '5%'},
                    {field: 'txamination_time', title: '考试时长/分', align: 'center', width: '5%'},
                    {field: 'money', title: '价格', align: 'center', width: '5%'},
                    {field: 'member_money', title: '会员价', align: 'center', width: '5%'},
                    {field: 'status', title: '状态',templet:'#status',align: 'center',width:'18%'},
                    {title: '操作', align: 'center', toolbar: '#act', width: '7%'}
                ];
                break;
        }
            return join;
    });
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
            var url = layList.U({ a: 'cancel_recommendation', q: { id: that.dataset.id, test_id: that.dataset.pid } });
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
            pid: where.pid,
            mer_id: where.mer_id,
            title: where.title
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
            case 'open_image':
                $eb.openImage(data.image);
                break;
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
                },{
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
            case 'detail':
                action.open_add('{:Url('examineDetails')}?id=' + data.id + '&type=' + data.type, '审核');
                break;
        }
    })

</script>
{/block}

