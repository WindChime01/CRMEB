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
    .edui-default .edui-for-image .edui-icon {background-position: -380px 0px;}
    .layui-form-item .special-label {width: 50px;float: left;height: 30px;line-height: 38px;margin-left: 10px;margin-top: 5px;border-radius: 5px;background-color: #0092DC;text-align: center;}
    .layui-form-item .special-label i {display: inline-block;width: 18px;height: 18px;font-size: 18px;color: #fff;}
    .layui-form-item .label-box {border: 1px solid;border-radius: 10px;position: relative;padding: 10px;height: 30px;color: #fff;background-color: #393D49;text-align: center;cursor: pointer;display: inline-block;line-height: 10px;}
    .layui-form-item .label-box p {line-height: inherit;}
</style>
<script src="{__PLUG_PATH}reg-verify.js"></script>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">{{id ? '编辑老师':'添加老师'}}</div>
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <label class="layui-form-label required">姓名：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="name" v-model.trim="formData.name" required autocomplete="off" maxlength="10" placeholder="请输入老师名称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label required">UID：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="uid" v-model.trim="formData.uid" required autocomplete="off" disabled class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label required">分类：</label>
                    <div class="layui-input-inline">
                        <select name="pid" v-model="formData.pid" lay-filter="pid" >
                            <option value="0">选择分类</option>
                            <option :value="item.id"   v-for="item in teacher_cate" :disabled="item.pid==0 ? true : false">{{item.html}}{{item.title}}</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label required">头像：（200*200）</label>
                    <div class="layui-input-block">
                        <div class="upload-image-box" v-if="formData.image" >
                            <img :src="formData.image" alt="">
                            <div class="mask"  style="display: block">
                                <p>
                                    <i class="fa fa-eye" @click="look(formData.image)"></i>
                                    <i class="fa fa-trash-o" @click="delect('image')"></i>
                                </p>
                            </div>
                        </div>
                        <div class="upload-image" v-show="!formData.image" @click="upload('image')">
                            <div class="fiexd"><i class="fa fa-plus"></i></div>
                            <p>选择图片</p>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item" style="margin-top: 10px;">
                    <label class="layui-form-label required">职位：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="position" v-model.trim="formData.position" required autocomplete="off" maxlength="10" placeholder="请输入老师职位" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item" style="margin-top: 10px;">
                    <label class="layui-form-label required">手机号：</label>
                    <div class="layui-input-inline">
                        <input type="text" name="phone" v-model.trim="formData.phone" required autocomplete="off" maxlength="11" placeholder="请输入手机号" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">排序：</label>
                    <div class="layui-input-inline">
                        <input type="number" name="sort" v-model="formData.sort" autocomplete="off" class="layui-input" min="0">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn layui-btn-normal" type="button" @click="save">{{id ? '立即修改':'立即提交'}}</button>
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
    var id={$id},uid={$uid},teacher=<?=isset($teacher) ? $teacher : []?>;
    require(['vue','helper','request','plupload','aliyun-oss','OssUpload'],function(Vue,$h) {
        new Vue({
            el: "#app",
            data: {
                formData:{
                    name:teacher.name || '',
                    uid:teacher.uid || (uid >0 ? uid : 0),
                    image: teacher.image || '',
                    pid: teacher.pid || 0,
                    position: teacher.position || '',
                    phone: teacher.phone || '',
                    sort:Number(teacher.sort) || 0
                },
                teacher_cate:[]
            },
            methods:{
                //查看图片
                look: function (pic) {
                    if (self.$eb) {
                        self.$eb.openImage(pic);
                    } else {
                        parent.$eb.openImage(pic);
                    }
                },
                //上传图片
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                clone_form: function () {
                    var that = this;
                    if (parseInt(id) == 0) {
                        parent.layer.closeAll();
                    }
                    var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                    parent.layer.close(index); //再执行关闭
                },
                //获取分类
                get_teacher_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'get_teacher_cate'}), function (res) {
                        that.$set(that, 'teacher_cate', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                save:function () {
                    var that=this;
                    that.$nextTick(function () {
                        if (that.formData.uid<=0) return layList.msg('请在后台用户管理中选择用户');
                        if (!that.formData.name) return layList.msg('请输入老师名称');
                        if (!that.formData.image) return layList.msg('请选择老师头像');
                        if (that.formData.pid<=0) return layList.msg('请选择老师分类');
                        if (!that.formData.position) return layList.msg('请编辑老师职位');
                        if (!that.formData.phone) return layList.msg('请编辑老师手机号');
                        if(!$reg.isPhone(that.formData.phone)){
                            return layList.msg('请输入正确的手机号');
                        }
                        layList.loadFFF();
                        layList.basePost(layList.U({a: 'save_add', q: {id: id}}), that.formData, function (res) {
                            layList.loadClear();
                            if (parseInt(id) == 0) {
                                layList.msg('添加成功', function () {
                                    parent.layer.closeAll();
                                    for (let i = parent.document.querySelectorAll('iframe').length; i--;) {
                                        if (parent.document.querySelectorAll('iframe')[i].dataset.id == '/admin/educational.teacher/index.html') {
                                            parent.document.querySelectorAll('iframe')[i].contentWindow.location.reload();
                                            break;
                                        }
                                    }
                                })
                            } else {
                                layList.msg('修改成功', function () {
                                    parent.layer.closeAll();
                                })
                            }
                        }, function (res) {
                            layList.msg(res.msg);
                            layList.loadClear();
                        });
                    })
                },
                delect:function(key){
                    var that=this;
                    that.formData[key]='';
                },
                changeIMG: function (key, value, multiple) {
                    if (multiple) {
                        var that = this;
                        value.map(function (v) {
                            that.formData[key].push({pic: v, is_show: false});
                        });
                        this.$set(this.formData, key, this.formData[key]);
                    } else {
                        this.$set(this.formData, key, value);
                    }
                },
            },
            mounted:function () {
                var that=this;
                window.changeIMG = that.changeIMG;
                that.get_teacher_list();
                layList.select('pid', function (obj) {
                    that.formData.pid = obj.value;
                });
            }
        })
    })
</script>
{/block}
