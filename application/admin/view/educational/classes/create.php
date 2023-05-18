{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-select dl dd.layui-this {
        background-color: #fff;
    }

    .layui-form-checkbox[lay-skin=primary]:hover i {
        border-color: #0093de;
    }

    label {
        margin-bottom: 0;
        font-weight: normal;
    }

    .layui-laydate-footer span:hover {
        color: #0092dc;
    }
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header"></div>
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-xs8 layui-col-sm6 layui-col-md4">
                    <form action="" class="layui-form" lay-filter="form">
                        <div class="layui-form-item">
                            <label class="layui-form-label required">班级名称：</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" required lay-verify="required" placeholder="请输入班级名称" maxlength="10" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">配班教师：</label>
                            <div id="xmSelect" class="layui-input-block"></div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">班级名额：</label>
                            <div class="layui-input-block">
                                <input type="text" name="upper_limit" lay-verify="required|number" placeholder="请输入班级名额" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">教学时限：</label>
                            <div class="layui-input-block">
                                <input id="datetime" type="text" required lay-verify="required" placeholder="请选择教学时限" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">排序：</label>
                            <div class="layui-input-block">
                                <input type="text" name="sort" lay-verify="required|number" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">状态：</label>
                            <div class="layui-input-block">
                                <input type="radio" name="status" value="1" title="开班" checked>
                                <input type="radio" name="status" value="2" title="结班">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" type="submit" lay-submit lay-filter="*">提交</button>
                                <button class="layui-btn layui-btn-primary" type="button" id="reset">重置</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    (function () {
        var id={$id},classes=<?=isset($classes) ? $classes : []?>;
        var $ = layui.jquery;
        var form = layui.form;
        var laydate = layui.laydate;
        var layer = layui.layer;
        var start_time = '';
        var end_time = '';
        var initValue = '';
        var XMSelect = xmSelect.render({
            el: '#xmSelect',
            data: [],
            theme: {
                color: '#0092DC'
            },
            autoRow: true,
            name: 'teacher_id',
            layVerify: 'required',
            prop: {
                value: 'id'
            }
        });

        function dateFormat(time) {
            var date = new Date(time * 1000),
                year = date.getFullYear(),
                month = date.getMonth() + 1,
                day = date.getDate();

            return year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);
        }

        $('.layui-card-header').text(id ? '编辑班级' : '添加班级');

        if (!Array.isArray(classes)) {
            form.val('form', classes);
            initValue = dateFormat(classes.start_time) + ' - ' + dateFormat(classes.end_time);
        }

        form.render();

        form.verify({
            integer: []
        });

        laydate.render({
            elem: '#datetime',
            range: true,
            value: initValue,
            done: function (value) {
                var arr = value.split(' - ');
                start_time = arr[0];
                end_time = arr[1];
            }
        });

        layList.baseGet(layList.U({
            a: 'getTeacherList'
        }), function (res) {
            XMSelect.update({
                data: res.data
            });
            if (!Array.isArray(classes)) {
                XMSelect.setValue(classes.teacher_id.split(','));
            }
        });

        form.on('submit(*)', function (data) {
            var field = data.field;

            field.start_time = start_time;
            field.end_time = end_time;
            field.title =data.field.title.trim();
            var index = layer.load(1);

            layList.basePost(layList.U({
                a: 'save_add',
                q: {
                    id: id
                }
            }), field, function (res) {
                layer.close(index);

                if (id) {
                    layer.msg('修改成功', function () {
                        parent.layer.closeAll();
                    })
                } else {
                    layer.confirm('添加成功，是否继续添加班级？', {
                        btn: ['继续添加', '立即提交']
                    }, function (index) {
                        layList.layer.close(index);
                        location.reload();
                    }, function () {
                        parent.layer.closeAll();
                    });
                }
            }, function (err) {
                layer.msg(err.msg,function () {
                    layList.layer.close(index);
                    location.reload();
                });
            });
            return false;
        });
        $('#reset').on('click', function () {
            form.val('form', {
                title: '',
                upper_limit: '',
                sort: 0,
            });
            $('#datetime').val('');
            $('#xmSelect').val('');
            XMSelect.setValue([]);
        });
        // $('input[name="sort"]').on('change', function () {
        //     if (!this.value || this.value < 0) {
        //         this.value = 0;
        //     } else if (this.value > 99999) {
        //         this.value = 99999;
        //     } else {
        //         this.value = parseInt(this.value);
        //     }
        // });
    })();
</script>
{/block}
