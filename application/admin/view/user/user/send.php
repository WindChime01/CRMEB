{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15"  id="app">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">搜索条件</div>
                <div class="layui-card-body">
                    <div class="layui-form layui-form-pane">
                        <div class="layui-form-item">
                            试卷名称：
                            <div class="layui-inline">
                                <input class="layui-input" name="title" id="demoReload" placeholder="请输入试卷名称">
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">试卷类型</label>
                                <div class="layui-input-block">
                                    <select name="type" id="test">
                                        <option value="">全部</option>
                                        <option value="1">练习</option>
                                        <option value="2">考试</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">讲师</label>
                                <div class="layui-input-inline">
                                    <select name="mer_id"  id="mertest">
                                        <option value="">全部</option>
                                        <option value="0">总平台</option>
                                        {volist name='mer_list' id='vc'}
                                        <option  value="{$vc.id}">{$vc.mer_name}</option>
                                        {/volist}
                                    </select>
                                </div>
                            </div>
                            <button class="layui-btn layui-btn-normal layui-btn-sm" lay-submit="search" lay-filter="search">搜索</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">试卷列表</div>
                <div class="layui-card-body">
                    <div class="layui-btn-container conrelTable">
                        <button class="layui-btn layui-btn-normal layui-btn-sm" type="button" data-type="add_test_paper">
                            确定
                        </button>
                        <button class="layui-btn layui-btn-normal layui-btn-sm" onclick="window.location.reload()"><i class="layui-icon layui-icon-refresh"></i> 刷新</button>
                    </div>
                    <table class="layui-hide" id="List" lay-filter="List"></table>
                    <script type="text/html" id="type">
                        {{# if(d.type==1){ }}
                        练习
                        {{# }else if(d.type==2){ }}
                        考试
                        {{# } }}
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
    var uid="{$uid}";
    //实例化form
    layList.form.render();
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('educational.student/getTestPaperList')}",function (){
        return [
            {type:'checkbox'},
            {field: 'id', title: '编号', width:'8%',align: 'center'},
            {field: 'type', title: '试卷类型', width:'12%', align: 'center',templet:'#type'},
            {field: 'cate', title: '试卷分类', width:'12%', align: 'center'},
            {field: 'mer_name', title: '讲师', width:80},
            {field: 'title', title: '试卷标题'},
        ];
    },20);
    //查询
    layList.search('search',function(where){
        layList.reload({
            mer_id:where.mer_id,
            type:where.type,
            title: where.title
        },true);
    });
    $('.conrelTable').find('button').each(function () {
        var type=$(this).data('type');
        $(this).on('click',function () {
            action[type] && action[type]();
        })
    });
    var action={
        add_test_paper:function () {
            var ids=layList.getCheckData().getIds('id');
            if(ids.length){
                $eb.$swal('delete',function(){
                    var str = ids.join(',');
                    layList.baseGet(layList.Url({
                        a: 'sendTestPaper',
                        q: {uid: uid, tid: str}
                    }), function (res) {
                        layList.msg(res.msg,function () {
                            parent.layer.closeAll();
                        });
                    });
                }, {
                    title:'确定赠送试卷吗?',
                    text:'通过后无法撤销，请谨慎操作！',
                    confirm:'确认'
                });
            }else{
                layList.msg('请选择需要发送的试卷');
            }
        },
        refresh:function () {
            layList.reload();
        }
    };
</script>
{/block}
