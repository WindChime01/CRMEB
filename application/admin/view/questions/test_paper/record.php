{extend name="public/container"}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">{$type==1 ? '练习记录':'考试记录'}</div>
                <div class="layui-card-body">
                    <div class="layui-row layui-col-space15">
                        <div class="layui-col-md12">
                            <form class="layui-form layui-form-pane" action="">
                                <div class="layui-form-item">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">用户搜索</label>
                                        <div class="layui-input-inline">
                                            <input type="text" name="title" class="layui-input" placeholder="用户UID">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <label class="layui-form-label">{$type==1 ? '练习标题':'考试标题'}</label>
                                        <div class="layui-input-inline">
                                            <select name="test_id" id="test_id">
                                                <option value="0">全部</option>
                                                {volist name='testPaper' id='vc'}
                                                <option value="{$vc.id}" {eq name="test_id" value="$vc.id"}selected="selected"{/eq}>{$vc.title}</option>
                                                {/volist}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <div class="layui-btn-group">
                                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" lay-submit="search" lay-filter="search">
                                                    <i class="layui-icon">&#xe615;</i>搜索
                                                </button>
                                                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm export" data-type="export" lay-filter="export">
                                                    <i class="layui-icon">&#xe67d;</i>导出
                                                </button>
                                            </div>
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

                            </div>
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                            <script type="text/html" id="act">
                                <button type="button" class="layui-btn layui-btn-normal layui-btn-xs" lay-event='answers'>
                                    查看答题
                                </button>
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
    var type=<?=$type?>;
    var test_id=<?=$test_id?>;
    var uid=<?=$uid?>;
    //加载列表
    layList.tableList({o:'List', done:function () {}},"{:Url('getExaminationRecords',['type'=>$type,'testId'=>$test_id,'uid'=>$uid])}",function (){
        var join=new Array();
        switch (parseInt(type)) {
            case 1:
                join = [
                    {field: 'id', title: '答题编号', width: '8%', align: 'center'},
                    {field: 'uid', title: 'UID', align: 'center', width: '10%'},
                    {field: 'nickname', title: '昵称', align: 'center', width: '10%'},
                    {field: 'title', title: '练习标题', align: 'center'},
                    {field: 'accuracy', title: '正确率%', align: 'center', width: '10%'},
                    {field: 'add_time', title: '提交时间', align: 'center', width: '14%'},
                    {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'}
                ];
                break;
            case 2:
                join = [
                    {field: 'id', title: '答题编号', width: '8%', align: 'center'},
                    {field: 'uid', title: 'UID', align: 'center', width: '8%'},
                    {field: 'nickname', title: '昵称', align: 'center', width: '10%'},
                    {field: 'title', title: '考试标题', align: 'center'},
                    {field: 'score', title: '分数', align: 'center', width: '10%'},
                    {field: 'accuracy', title: '正确率%', align: 'center', width: '10%'},
                    {field: 'grade', title: '分数等级', align: 'center', width: '10%'},
                    {field: 'add_time', title: '提交时间', align: 'center', width: '14%'},
                    {field: 'right', title: '操作',align:'center',toolbar:'#act',width:'10%'}
                ];
                break;
        }
        return join;
    });
    //查询
    layList.search('search',function(where){
        layList.reload({
            test_id: where.test_id,
            title: where.title,
            excel:0
        },true);
    });
    //点击事件绑定
    layList.tool(function (event,data,obj) {
        switch (event) {
            case 'answers':
                layer.open({
                    type: 2,
                    title: '答题情况',
                    content: "{:Url('answers')}?record_id="+data.id + "&test_id="+data.test_id+"&type="+data.type+"&uid="+data.uid,
                    area: ['80%', '90%'],
                    maxmin: true
                });
                break;
        }
    });
   $('.layui-btn').on('click', function () {
       var types = $(this).data('type');
       if (types == 'export') {
           var title = $.trim($('input[name="title"]').val());
          if(!test_id) test_id = $('#test_id').val();
          location.href= layList.U({a:'getExaminationRecords',q:{test_id:test_id,title:title,type:type,uid:uid,excel:1}});
       }
   });
</script>
{/block}

