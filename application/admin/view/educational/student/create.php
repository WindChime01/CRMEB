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
{/block}
{block name="content"}
<div class="layui-fluid">
    <div class="layui-card" id="app" v-cloak>
        <div class="layui-card-header">{{id ? '编辑学员':'添加学员'}}</div>
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item submit">
                    <label class="layui-form-label required">学员姓名：</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" v-model.trim="formData.name" autocomplete="off" placeholder="请输入学员名称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item submit">
                    <label class="layui-form-label">UID：</label>
                    <div class="layui-input-block">
                        <input type="text" name="uid" v-model="formData.uid" autocomplete="off" disabled class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item submit">
                    <label class="layui-form-label required">所在班级：</label>
                    <div class="layui-input-block">
                        <select name="classes_id" v-model="formData.classes_id" lay-filter="classes_id" >
                            <option value="0">请选择班级</option>
                            <option  :value="item.id"   v-for="item in classes_list">{{item.title}}</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item submit">
                    <label class="layui-form-label required">学员头像：（200*200）</label>
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
                <div class="layui-form-item submit">
                    <label class="layui-form-label">学员性别：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="sex" lay-filter="is_sex" v-model="formData.sex" value="1" title="男">
                        <input type="radio" name="sex" lay-filter="is_sex" v-model="formData.sex" value="2" title="女">
                    </div>
                </div>
                <div v-for="(item, index) in options" :key="index"  class="layui-form-item">
                    <label v-if="!index" class="layui-form-label required">联系方式：</label>
                    <div class="layui-input-block">
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input v-model.trim="item.title" type="text" required  lay-verify="required" maxlength="60" placeholder="关系" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <div class="layui-input-inline">
                                <input v-model="item.phone" type="text" required  lay-verify="required" maxlength="60" placeholder="请输入手机号" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div v-if="index && index === options.length - 1" class="layui-inline">
                            <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" @click="onDel">删除选项</button>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="onAdd">添加选项</button>
                    </div>
                </div>
                <div class="layui-form-item" style="margin-top: 10px;">
                    <label class="layui-form-label required">学员地址：</label>
                    <div class="layui-input-block">
                        <div class="layui-form">
                            <div id="area-picker">
                                <div class="layui-inline">
                                    <select name="province" v-model="formData.province" class="province-selector" :data-value="formData.province" lay-filter="province-1" >
                                        <option value="">请选择省</option>
                                    </select>
                                </div>
                                <div class="layui-inline">
                                    <select name="city" v-model="formData.city"  class="city-selector" :data-value="formData.city" lay-filter="city-1">
                                        <option value="">请选择市</option>
                                    </select>
                                </div>
                                <div class="layui-inline">
                                    <select name="county" v-model="formData.district" class="county-selector" :data-value="formData.district" lay-filter="county-1">
                                        <option value="">请选择区</option>
                                    </select>
                                </div>
                                <div class="layui-inline">
                                    <input id="address" class="layui-input col-md-4" v-model.trim="formData.detail" placeholder="详细地址" style="height:38px;resize:none;line-height:20px;color:#333;width:auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item submit">
                    <label class="layui-form-label">排序：</label>
                    <div class="layui-input-block">
                        <input type="number" name="sort" style="width: 100%" v-model="formData.sort" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item submit">
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
    var id={$id},uid={$uid},student=<?=isset($student) ? $student : []?>,phone=<?=isset($phone) ? $phone : []?>;
    require(['vue','helper','reg-verify','request','plupload','aliyun-oss','OssUpload'],function(Vue,$h,$reg) {
        new Vue({
            el: "#app",
            data: {
                formData:{
                    name:student.name || '',
                    uid:student.uid || (uid >0 ? uid : 0),
                    image: student.image || '',
                    sex:student.sex || 1,
                    classes_id: student.classes_id || 0,
                    province: student.province || '北京市',
                    city: student.city || '北京市',
                    district: student.district || '东城区',
                    detail: student.detail || '',
                    sort:Number(student.sort) || 0
                },
                options: [
                    {
                        title:'',
                        phone: ''
                    }
                ],
                classes_list:[]
            },
            methods:{
                onAdd: function () {
                    this.options.push({
                        title: '',
                        phone: ''
                    });
                },
                onDel: function () {
                    this.options.pop();

                },
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
                get_classes_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'classesList'}), function (res) {
                        that.$set(that, 'classes_list', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                save:function () {
                    var that=this;
                    that.$nextTick(function () {
                        if (that.formData.uid<=0) return layList.msg('请在后台用户管理中选择用户');
                        if (!that.formData.name) return layList.msg('请输入学员名称');
                        if (that.formData.classes_id<=0) return layList.msg('请选择所在班级');
                        if (!that.formData.image) return layList.msg('请选择学员头像');
                        var options=that.options;
                        for(var i=0;i<options.length;i++){
                            if(options[i].title=='' || options[i].phone=='') return layList.msg('联系方式没填完整');
                            var v=i+1;
                            if(!$reg.isPhone(options[i].phone)) return layList.msg('第'+v+'行手机号不正确');
                        }
                        
                        if (that.formData.province=='' || that.formData.city=='' || that.formData.district=='' ||that.formData.detail=='') return layList.msg('请输入地址信息');
                        that.formData.contact=JSON.stringify(options);
                        layList.loadFFF();
                        layList.basePost(layList.U({a: 'save_add', q: {id: id}}), that.formData, function (res) {
                            layList.loadClear();
                            if (parseInt(id) == 0) {
                                layList.msg('添加成功', function () {
                                    parent.layer.closeAll();
                                    for (let i = 0; i < parent.$('.J_iframe').length; i++) {
                                        if (parent.$('.J_iframe')[i].dataset.id == '/admin/educational.student/index.html') {
                                            parent.$('.J_iframe')[i].contentWindow.location.reload();
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
                that.get_classes_list();
                that.$nextTick(function () {
                    layList.form.render();
                    layui.config({
                        base: '{__ADMIN_PATH}mods/'
                        , version: '1.0'
                    }).extend({
                        layarea:'layarea'
                    });
                    layui.use(['layarea'], function () {
                        var layarea = layui.layarea;
                        layarea.render({
                            elem: '#area-picker',
                            change: function (res) {
                                //选择结果
                                that.formData.province= res.province;
                                that.formData.city= res.city;
                                that.formData.district= res.county;
                            }
                        });
                    });
                    layList.select('classes_id', function (obj) {
                        that.formData.classes_id = obj.value;
                    });
                    layList.form.on('radio(is_sex)', function (data) {
                        that.formData.sex = parseInt(data.value);
                    });
                    if(phone && phone.length){
                        that.options=phone;
                    }
                });
            }
        })
    })
</script>
{/block}
