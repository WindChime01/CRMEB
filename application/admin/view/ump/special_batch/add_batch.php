{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label{
        width: 50px;
        float: left;
        height: 30px;
        line-height: 38px;
        margin-left: 10px;
        margin-top: 5px;
        border-radius: 5px;
        background-color: #0092DC;
        text-align: center;
    }
    .layui-form-item .special-label i{
        display: inline-block;
        width: 18px;
        height: 18px;
        font-size: 18px;
        color: #fff;
    }
    .layui-form-item .label-box{
        border: 1px solid;
        border-radius: 10px;
        position: relative;
        padding: 10px;
        height: 30px;
        color: #fff;
        background-color: #393D49;
        text-align: center;
        cursor: pointer;
        display: inline-block;
        line-height: 10px;
    }
    .layui-form-item .label-box p{
        line-height: inherit;
    }
    .layui-form-mid{
        margin-left: 18px;
    }
    .m-t-5{
        margin-top:5px;
    }
    .edui-default .edui-for-image .edui-icon{
        background-position: -380px 0px;
    }
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label required" >活动名称：</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" v-model.trim="formData.title" autocomplete="off" placeholder="请输入活动名称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label required">课程专题：</label>
                    <div class="layui-input-block">
                        <select name="special_id" v-model="formData.special_id" lay-filter="special_id" lay-verify="required">
                            <option value="0">请选择专题</option>
                            <option  v-for="item in special_list" :value="item.id">{{item.title}}</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label required">制卡数量：</label>
                    <div class="layui-input-inline">
                        <input type="number" min="1" name="total_num" v-model="formData.total_num" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否激活：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="status" lay-filter="status" v-model="formData.status" value="0" title="冻结">
                        <input type="radio" name="status" lay-filter="status" v-model="formData.status" value="1" title="激活">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">备注：</label>
                    <div class="layui-input-block">
                        <textarea placeholder="请输入备注" v-model="formData.remark" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" type="button" @click="save">{$id ? '确认修改':'立即提交'}</button>
                        <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    require(['vue'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                special_list:[],
                formData:{
                    title:'',
                    special_id:0,
                    total_num:0,
                    status:0,
                    remark:''
                }
            },
            methods:{
                save:function () {
                    var that=this;
                    if(!that.formData.title) return layList.msg('请输入批次标题');
                    if(!that.formData.special_id) return layList.msg('请选择专题');
                    if(!that.formData.total_num) return layList.msg('请输入制卡数量');
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save_batch'}),that.formData,function (res) {
                        layList.loadClear();
                        layList.layer.confirm('添加成功,您要继续添加活动吗?', {
                            btn: ['继续添加', '立即提交'] //按钮
                        }, function (index) {
                            layList.layer.close(index);
                        }, function () {
                            parent.layer.closeAll();
                        });
                    },function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                },
                //获取分类
                get_special_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'specialList'}), function (res) {
                        that.$set(that, 'special_list', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                clone_form:function (id) {
                    var that = this;
                    //有关闭扩展事件直接写在这里
                    if(parseInt(id) == 0){
                        parent.layer.closeAll();
                    }
                    parent.location.href = layList.U({a:'index',p:{}});
                }
            },
            mounted:function () {
                var that=this;
                that.get_special_list();
                this.$nextTick(function () {
                    layList.form.render();
                });
                layList.select('special_id', function (obj) {
                    that.formData.special_id = obj.value;
                });
                //操作dom时触发
                layList.form.on('radio(status)', function(data){
                    that.formData.status = parseInt(data.value);
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
            }
        })
    })

</script>
{/block}
