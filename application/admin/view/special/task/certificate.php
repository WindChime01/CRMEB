{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label i{display: inline-block;width: 18px;height: 18px;font-size: 18px;color: #fff;}
    .layui-form-item .label-box p{line-height: inherit;}
    .m-t-5{margin-top:5px;}
    #app .layui-barrage-box{margin-bottom: 10px;margin-top: 10px;margin-left: 10px;border: 1px solid #0092DC;border-radius: 5px;cursor: pointer;position: relative;}
    #app .layui-barrage-box.border-color{border-color: #0bb20c;}
    #app .layui-barrage-box .del-text{position: absolute;top: 0;left: 0;background-color: rgba(0,0,0,0.5);color: #ffffff;width: 92%;text-align: center;}
    #app .layui-barrage-box p{padding:5px 5px; }
    #app .layui-empty-text{text-align: center;font-size: 18px;}
    #app .layui-empty-text p{padding: 10px 10px;}
    .layui-form-label{width:150px;}
    .layui-input-block{margin-left:150px;}
</style>
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-row layui-col-space15" id="app" v-cloak>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form action="" class="layui-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">选择证书：</label>
                            <div class="layui-input-block">
                                <select name="cid" v-model="formData.cid" lay-search="" lay-filter="cid">
                                        <option v-for="item in certificateList" :value="item.id">{{item.title}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">获得条件/百分比：</label>
                            <div class="layui-input-block">
                                <input type="number" name="condition" v-model="formData.condition" autocomplete="off" placeholder="素材观看百分比大于输入值获得(80=80%)" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item submit">
                            <label class="layui-form-label">关联状态：</label>
                            <div class="layui-input-block">
                                <input type="radio" name="is_show" lay-filter="is_show" v-model="formData.is_show" value="1" title="显示">
                                <input type="radio" name="is_show" lay-filter="is_show" v-model="formData.is_show" value="0" title="隐藏">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <div class="layui-btn-container">
                                    <button class="layui-btn layui-btn-normal" type="button" @click="save">{{id ? '立即修改':'立即提交'}}</button>
                                    <button class="layui-btn layui-btn-primary clone" type="button" @click="clone_form">取消</button>
                                </div>
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
    var related_id={$related_id},id={$id},certificate=<?=isset($certificate) ? $certificate : []?>;
    require(['vue','request','OssUpload'],function(Vue) {
        new Vue({
            el: "#app",
            data: {
                formData:{
                    cid: certificate.cid || 0,
                    condition: certificate.condition || '',
                    related: related_id,
                    is_show: certificate.is_show || 1,
                },
                certificateList: []
            },
            methods:{
                clone_form: function () {
                    var that = this;
                    if (parseInt(id) == 0) {
                        parent.layer.closeAll();
                    }
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                },
                get_certificate_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'certificateLists',p:{obtain:1}}), function (res) {
                        that.$set(that, 'certificateList', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                save:function () {
                    var that=this;
                    if(that.formData.cid<=0) return layList.msg('请选择关联证书');
                    if(Number(that.formData.condition)<=0 || Number(that.formData.condition)>100) return layList.msg('请编辑获得证书的条件');
                    that.formData.condition=Number(that.formData.condition);
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'certificateRecord',q:{id:id,obtain:1}}),that.formData,function (res) {
                        layList.loadClear();
                        layList.msg(res.msg,function () {
                            parent.layer.closeAll();
                        });
                    },function (res) {
                        layList.msg(res.msg,function () {
                            parent.layer.closeAll();
                        });
                    });
                }
            },
            mounted:function () {
                var that=this;
                that.get_certificate_list();
                this.$nextTick(function () {
                    layList.form.render();
                    layList.select('cid', function (obj) {
                        that.formData.cid = obj.value;
                    });
                    layList.form.on('radio(is_show)', function (data) {
                        that.formData.is_show = parseInt(data.value);
                    });
                });
            }
        })
    })
</script>
{/block}
