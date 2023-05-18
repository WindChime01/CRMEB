{extend name="public/container"}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-form-item">
                <div class="layui-form-item">
                    <label class="layui-form-label">上传文件：</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input :value="link" type="text" name="title" disabled class="layui-input">
                    </div>
                    <div class="layui-input-inline" style="width: auto;">
                        <button type="button" class="layui-btn layui-btn-normal" id="upload">上传文件</button>
                    </div>
                    <div class="layui-form-mid layui-word-aux">文件格式：xlsx、xls、csv</div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-normal" @click="save">导入</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    require(['vue','request'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                link:''
            },
            methods:{
                save:function () {
                    var that=this;
                    if (!this.link) {
                        return layui.layer.msg('请上传需要导入的文件', {icon: 5});
                    }
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'importTestQuestions'}),{link:that.link},function (res) {
                        layList.loadClear();
                        layer.msg('导入成功',{icon:1},function () {
                            parent.layer.closeAll();
                        });
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                }
            },
            mounted:function () {
                var that = this;
                this.$nextTick(function () {
                    layui.upload.render({
                        elem: '#upload',
                        url: "{:Url('file_import_upload')}",
                        accept: 'file',
                        exts: 'xlsx|xls|csv',
                        done: function (res) {
                            if (res.code === 200) {
                                that.link = res.data.filePath;
                            }
                            layui.layer.msg(res.msg, {icon: res.code === 200 ? 1 : 5});
                        }
                    });
                });
            }
        })
    })
</script>
{/block}
