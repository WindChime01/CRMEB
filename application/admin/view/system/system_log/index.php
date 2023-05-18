{extend name="public/container"}
{block name="head_top"}
<script src="{__PLUG_PATH}moment.js"></script>
<link rel="stylesheet" href="{__PLUG_PATH}daterangepicker/daterangepicker.css">
<script src="{__PLUG_PATH}daterangepicker/daterangepicker.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <form action="" class="layui-form layui-form-pane" id="form" method="get">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <select name="admin_id">
                                    <option value="">管理员名称</option>
                                    {volist name="$admin" id="vo"}
                                    <option value="{$vo.id}" {eq name="where.admin_id" value="$vo.id"}selected="selected"{/eq}>{$vo.real_name}</option>
                                    {/volist}
                                </select>
                            </div>
                            <div class="layui-inline">
                                <input type="text" id="data" class="layui-input" name="data" value="{$where.data}" placeholder="请选择日期" >
                            </div>
                            <div class="layui-inline">
                                <input type="text" name="pages" value="{$where.pages}" placeholder="请输入行为" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-inline">
                                <button type="submit" class="layui-btn layui-btn-normal layui-btn-sm">
                                    <i class="layui-icon">&#xe615;</i>搜索
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="layui-col-md12">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>ID/名称</th>
                            <th>行为</th>
                            <th>链接</th>
                            <th>操作ip</th>
                            <th>类型</th>
                            <th>操作时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        {volist name="list" id="vo"}
                        <tr>
                            <td>
                                {$vo.id}
                            </td>
                            <td>
                                {$vo.admin_id} / {$vo.admin_name}
                            </td>
                            <td>
                                {$vo.page}
                            </td>
                            <td>
                                {$vo.path}({$vo.method})
                            </td>
                            <td>
                                {$vo.ip}
                            </td>
                            <td>
                                {$vo.type}
                            </td>
                            <td>
                                {$vo.add_time|date="Y-m-d H:i:s",###}
                            </td>
                        </tr>
                        {/volist}
                        </tbody>
                    </table>
                    {include file="public/inner_page"}
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    var form = layui.form;
    form.render();
    var dateInput =$('#data');
    dateInput.daterangepicker({
        autoUpdateInput: false,
        "opens": "center",
        "drops": "down",
        "ranges": {
            '今天': [moment(), moment().add(1, 'days')],
            '昨天': [moment().subtract(1, 'days'), moment()],
            '上周': [moment().subtract(6, 'days'), moment()],
            '前30天': [moment().subtract(29, 'days'), moment()],
            '本月': [moment().startOf('month'), moment().endOf('month')],
            '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "locale" : {
            applyLabel : '确定',
            cancelLabel : '清空',
            fromLabel : '起始时间',
            toLabel : '结束时间',
            format : 'YYYY/MM/DD',
            customRangeLabel : '自定义',
            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',
                '七月', '八月', '九月', '十月', '十一月', '十二月' ],
            firstDay : 1
        }
    });
    dateInput.on('cancel.daterangepicker', function(ev, picker) {
        $("#data").val('');
    });
    dateInput.on('apply.daterangepicker', function(ev, picker) {
        $("#data").val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    });
</script>
{/block}

