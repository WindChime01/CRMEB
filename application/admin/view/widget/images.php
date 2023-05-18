<!DOCTYPE html>
<!--suppress JSAnnotator -->
<html lang="zh-CN">
<head>
    <link rel="stylesheet" href="{__FRAME_PATH}font-awesome/css/font-awesome.min.css">
    <link href="{__PLUG_PATH}layui/css/layui.css" rel="stylesheet">
    <link rel="stylesheet" href="{__ADMIN_PATH}css/images.css?v=100">
    <script src="{__PLUG_PATH}jquery-1.10.2.min.js"></script>
    <script src="{__PLUG_PATH}layui/layui.all.js"></script>
    <script src="{__PLUG_PATH}vue/dist/vue.min.js"></script>
</head>
<style>

</style>
<body>
    <div v-cloak id="app" class="layui-fluid">
        <div class="layui-fluid-side">
            <div class="layui-tree" lay-filter="LAY-tree-8">
                <div data-id="2" class="layui-tree-set layui-tree-setHide">
                    <div class="layui-tree-entry">
                        <div class="layui-tree-main" @click="OpenTree({ child: [], id: 0 })">
                            <span style="visibility: hidden;" class="layui-tree-iconClick">
                                <i class="layui-tree-iconArrow"></i>
                            </span>
                            <span :class="{ on: pid == 0 }" class="layui-tree-txt">全部图片</span>
                        </div>
                    </div>
                </div>
                <div v-for="(item, index) in categoryList" :key="item.id" :class="{ 'layui-tree-spread': item.isOpen }" data-id="2" class="layui-tree-set layui-tree-setHide">
                    <div class="layui-tree-entry">
                        <div class="layui-tree-main" @click="OpenTree(item, index)">
                            <span :style="{ visibility: item.child.length ? 'visible' : 'hidden' }" class="layui-tree-iconClick">
                                <i class="layui-tree-iconArrow"></i>
                            </span>
                            <span :class="{ on: pid == item.id }" class="layui-tree-txt">{{ item.name }}</span>
                        </div>
                        <div class="layui-layer layui-layer-tips">
                            <div id="" class="layui-layer-content">
                                <div class="layui-btn-group layui-tree-btnGroup">
                                    <i class="layui-icon layui-icon-add-1" data-type="add" @click.stop="addCategory(item)"></i>
                                    <i class="layui-icon layui-icon-edit" data-type="update" @click.stop="updateCategory(item)"></i>
                                    <i v-if="!item.child.length" class="layui-icon layui-icon-delete" data-type="del" @click.stop="delCategory(item)"></i>
                                </div>
                                <i class="layui-layer-TipsG layui-layer-TipsT"></i>
                            </div>
                            <span class="layui-layer-setwin"></span>
                        </div>
                    </div>
                    <div :style="{ display: item.isOpen ? 'block' : 'none' }" class="layui-tree-pack layui-tree-lineExtend layui-tree-showLine">
                        <div v-for="cell in item.child" :key="cell.id" data-id="2000" class="layui-tree-set">
                            <div class="layui-tree-entry">
                                <div class="layui-tree-main" @click="OpenTree(cell, index)">
                                    <span class="layui-tree-iconClick" style="visibility: hidden;">
                                        <i class="layui-tree-iconArrow" style="display: none;"></i>
                                    </span>
                                    <span :class="{ on: pid == cell.id }" class="layui-tree-txt">{{ cell.name }}</span>
                                </div>
                                <div class="layui-layer layui-layer-tips">
                                    <div id="" class="layui-layer-content">
                                        <div class="layui-btn-group layui-tree-btnGroup">
                                            <i class="layui-icon layui-icon-edit" data-type="update" @click.stop="updateCategory(cell)"></i>
                                            <i class="layui-icon layui-icon-delete" data-type="del" @click.stop="delCategory(cell)"></i>
                                        </div>
                                        <i class="layui-layer-TipsG layui-layer-TipsT"></i>
                                    </div>
                                    <span class="layui-layer-setwin"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-fluid-main">
            <div class="main-header">
                <div class="layui-btn-group">
                    <button :class="{ 'layui-btn-normal': selectedImage.length, 'layui-btn-disabled': !selectedImage.length }" type="button" class="layui-btn layui-btn-sm" @click="useImage">使用选中的图片</button>
                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="addCategory">添加分类</button>
                    <button ref="upload" type="button" class="layui-btn layui-btn-normal layui-btn-sm">上传图片</button>
                    <button :class="{ 'layui-btn-normal': selectedImage.length, 'layui-btn-disabled': !selectedImage.length }" type="button" class="layui-btn layui-btn-sm" @click="moveCategory">图片移至</button>
                    <button :class="{ 'layui-btn-danger': selectedImage.length, 'layui-btn-disabled': !selectedImage.length }" type="button" class="layui-btn layui-btn-sm" @click="deleteImage">删除图片</button>
                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="searchImage"><i class="layui-icon layui-icon-search"></i></button>
                </div>
            </div>
            <div class="main-content">
                <div v-for="item in imageList" :key="item.att_id" class="image-item">
                    <div :class="{ on: item.selected }" class="image-wrap" @click="selectImage(item)">
                        <img :src="item.att_dir">
                        <span v-show="item.number" class="layui-badge layui-bg-cyan">{{ item.number }}</span>
                        <div class="layui-btn-group">
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click.stop="lookImage(item.att_dir)">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click.stop="handleEdit(item)">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    </div>
                    <input :ref="item.att_id" v-model.trim="item.title" type="text" :disabled="item.disabled" @blur="handleBlur(item)">
                </div>
                <img v-if="!imageList.length && !loading" class="empty" src="{__ADMIN_PATH}images/empty.jpg">
            </div>
            <div ref="main-footer" class="main-footer"></div>
        </div>
    </div>
</body>
</html>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script>
    var form = layui.form;
    var pid = {$pid}, small = {$Request.param.small ? : 0} , parentinputname = '{$fodder}', maxLength = {$maxLength};//当前图片分类ID

    new Vue({
        el: "#app",
        data: {
            categoryList: [],
            searchTitle: '',
            pid: pid,
            imageList: [],
            page: 1,
            limit: 20,
            loading: false,
            small: small,
            selectedImage: [],
            selectedImageIDS: [],
            uploadInst: null,
            searchContent: '',
            files: {}
        },
        watch: {
            page: function () {
                this.getImageList();
            },
        },
        methods: {
            searchImage: function () {
                var vm = this;
                layer.prompt({
                    title: '输入图片名称，并确认',
                    formType: 0
                }, function (text, index) {
                    if (!text.trim().length) {
                        return;
                    }
                    this.pid = 0;
                    this.searchContent = text.trim();
                    this.page = 1;
                    layer.close(index);
                    var load = layer.load(1);
                    layList.baseGet(this.U({
                        a: 'get_image_list',
                        q: {pid: this.pid, page: this.page, limit: this.limit, title: this.searchContent}
                    }), function (res) {
                        layer.close(load);
                        var list = res.data.list;
                        list.forEach(function (item) {
                            item.disabled = true;
                            item.hover = false;
                            if (!item.title) {
                                item.title = item.name.slice(0, item.name.lastIndexOf('.'));
                            }
                        });
                        this.$set(this, 'imageList', list);
                        if (this.page == 1) {
                            layList.laypage.render({
                                elem: this.$refs['main-footer']
                                , count: res.data.count
                                , limit: this.limit
                                , theme: '#1E9FFF',
                                groups: 3,
                                jump: function (obj) {
                                    vm.page = obj.curr;
                                }
                            });
                        }
                    }.bind(this), function (res) {
                        layer.close(load);
                        layList.msg(res.msg);
                    });
                }.bind(this));
            },
            handleEdit: function (item) {
                item.disabled = false;
                this.oldTitle = item.title;
                this.$nextTick(function () {
                    this.$refs[item.att_id][0].focus();
                });
            },
            handleBlur: function (item) {
                item.disabled = true;
                if (this.oldTitle !== item.title) {
                    this.updateTitle(item);
                }
            },
            lookImage: function (image) {
                layui.layer.photos({
                    photos: {
                        data: [
                            {
                                src: image
                            }
                        ]
                    },
                    anim: 5
                });
            },
            // 修改名称
            updateTitle: function (item) {
                layList.basePost(this.U({
                    a: 'updateImageTitle'
                }), {
                    att_id: item.att_id,
                    title: item.title
                }, function (res) {
                    layList.msg(res.msg);
                }, function (res) {
                    layList.msg(res.msg);
                });
            },
            //删除图片
            deleteImage: function () {
                var that = this;
                if (!this.selectedImage.length) return;
                layList.layer.confirm('是否要删除选中图片？', {
                    btn: ['是的我要删除', '我想想吧'] //按钮
                }, function () {
                    layList.basePost(that.U({a: 'delete'}), {imageid: that.selectedImageIDS}, function (res) {
                        layList.msg(res.msg);
                        that.getImageList();
                        window.location.reload()
                    }, function (res) {
                        layList.msg(res.msg);
                    })
                })
            },
            //移动图片分类
            moveCategory: function () {
                var that = this;
                window.formSuccessBack = function () {
                    that.getImageList();
                    that.selectedImage = [];
                    that.selectedImageIDS = [];
                };
                this.getOpenWindow('移动图片', this.U({a: 'moveimg'}) + '?imgaes=' + this.selectedImageIDS, {
                    end: function () {
                        window.formSuccessBack = null;
                    }
                });
            },
            //使用选中图片
            useImage: function () {
                if (!this.selectedImage.length) return;
                //判断表单限制图片个数
                if (typeof parent.$f != 'undefined') {
                    //已有图片个数
                    var nowpics = parent.$f.getValue(parentinputname).length,
                        props = parent.$f.model()[parentinputname].props || {},
                        maxlength = props.maxLength || 0;
                    //已选图片个数
                    var selectlength = this.selectedImage.length;
                    //还可以选择多少张
                    var surplus = maxlength - nowpics;
                    if (nowpics + selectlength > maxlength) {
                        return layList.msg('最多只能选择 ' + surplus + ' 张');
                    }
                }
                //编辑器中
                if (parentinputname.includes('editor')) {
                    var list = this.selectedImage.map(function (image) {
                        return {
                            _src: image,
                            src: image
                        };
                    });
                    parent.insertEditor(list, parentinputname);
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index)
                } else {
                    //form表单中
                    if (parent.$f) {
                        var value = parent.$f.getValue(parentinputname);//父级input 值
                        var list = value || [];
                        for (var i = 0; i < this.selectedImage.length; i++) {
                            if (value.indexOf(this.selectedImage[i]) == -1) list.push(this.selectedImage[i]);
                        }
                        parent.$f.changeField(parentinputname, list);
                        parent.$f.closeModal(parentinputname);
                    } else {
                        //独立图片选择页面
                        if(maxLength > 0 ){
                            if(this.selectedImage.length > maxLength){
                                return layList.msg('最多能选择' + maxLength + '张');
                            }
                            parent.changeIMG(parentinputname, this.selectedImage,1);
                        }else{
                            if(this.selectedImage.length > 1){
                                return layList.msg('只能选择一张图片');
                            }
                            parent.changeIMG(parentinputname, this.selectedImage[0]);
                        }
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                    }
                }

            },
            //图片选中和取消
            selectImage: function (item, index) {
                if (!item.selected) {
                    if (maxLength) {
                        if (this.selectedImage.length === maxLength) {
                            return layList.msg('最多选择' + maxLength + '张图片');
                        }
                    } else {
                        if (parentinputname.indexOf('editor') === -1) {
                            if (this.selectedImage.length) {
                                return layList.msg('只能选择1张图片');
                            }
                        }
                    }
                }

                this.$set(item, 'selected', item.selected == undefined ? true : !item.selected);
                var val = small == 1 ? item['satt_dir'] : item['att_dir'];
                if (item.selected === true) {
                    this.selectedImage[this.selectedImage.length] = val;
                    this.selectedImageIDS[this.selectedImage.length] = item['att_id'];
                    item.number = this.selectedImage.length;
                } else {
                    this.selectedImage.splice(this.selectedImage.indexOf(val), 1);
                    this.selectedImageIDS.splice(this.selectedImage.indexOf(item['att_id']), 1);
                    for (var i = 0; i < this.imageList.length; i++) {
                        if (this.imageList[i].number > item.number) {
                            this.imageList[i].number = (this.imageList[i].number - 1) >= 0 ? this.imageList[i].number - 1 : 0;
                        }
                    }
                    item.number = 0;
                }
                this.$set(this, 'selectedImage', this.selectedImage);
                this.$set(this, 'selectedImageIDS', this.selectedImageIDS);
            },
            //获取图片列表
            getImageList: function () {
                var that = this;
                if (that.loading) return;
                that.loading = true;
                var index = layList.layer.load(1, {shade: [0.1, '#fff']});
                layList.baseGet(this.U({
                    a: 'get_image_list',
                    q: {pid: this.pid, page: this.page, limit: this.limit, title: that.searchContent }
                }), function (res) {
                    that.loading = false;

                    var list = res.data.list;
                    for (var i = list.length; i--;) {
                        list[i].disabled = true;
                        list[i].hover = false;
                        if (!list[i].title) {
                            list[i].title = list[i].name.slice(0, list[i].name.lastIndexOf('.'));
                        }
                    }
                    that.$set(that, 'imageList', res.data.list);
                    layList.layer.close(index);
                    if (that.page == 1) {
                        layList.laypage.render({
                            elem: that.$refs['main-footer']
                            , count: res.data.count
                            , limit: that.limit
                            , theme: '#1E9FFF',
                            jump: function (obj) {
                                that.page = obj.curr;
                            }
                        });
                    }
                }, function () {
                    that.loading = false;
                    layList.layer.close(index);
                });
            },
            //查询分类
            search: function () {
                this.getCategoryList();
            },
            //打开和关闭树形
            OpenTree: function (item, index) {
                this.searchContent = '';
                this.pid = item.id;
                if (item.child.length) {
                    item.isOpen == undefined ? false : item.isOpen;
                    this.$set(this.categoryList[index], 'isOpen', !item.isOpen);
                } else {
                    this.page = 1;
                    this.$set(this, 'selectedImage', []);
                    this.$set(this, 'selectedImageIDS', []);
                    this.getImageList();
                }
                this.uploadInst.reload({
                    url: this.U({a: 'upload'}) + '?pid=' + this.pid
                });
            },
            //组装URL
            U: function (opt) {
                opt = typeof opt == 'object' ? opt : {};
                return layList.U({m: 'admin', c: "widget.images", a: opt.a || '', q: opt.q || {}, p: opt.q || {}});
            },
            //获取分类
            getCategoryList: function () {
                var that = this;
                layList.baseGet(that.U({a: 'get_image_cate', q: {name: this.searchTitle}}), function (res) {
                    that.$set(that, 'categoryList', res.data);
                });
            },
            //鼠标移入显示图标
            changeActive: function (item, indexK, index) {
                if (index)
                    this.$set(this.categoryList[indexK]['child'], 'isShow', true);
                else
                    this.$set(this.categoryList[indexK], 'isShow', true);
            },
            //鼠标移出隐藏
            removeActive: function (item, indexK, index) {
                if (index)
                    this.$set(this.categoryList[indexK]['child'], 'isShow', false);
                else
                    this.$set(this.categoryList[indexK], 'isShow', false);
            },
            //添加分类
            addCategory: function (item, pid) {
                item = item == undefined ? {} : item;
                var id = item.id == undefined ? 0 : item.id,
                    pid = pid == undefined ? 0 : pid;
                return this.getOpenWindow(item.name ? item.name + '编辑' : '新增分类', this.U({
                    a: 'addcate',
                    q: {id: pid == 0 ? id : pid}
                }));
            },
            //修改分类
            updateCategory: function (item, pid) {
                item = item == undefined ? {} : item;
                pid = pid == undefined ? 0 : pid;
                return this.getOpenWindow(item.name + '编辑', this.U({a: 'editcate', q: {id: item.id}}));
            },
            //删除分类
            delCategory: function (item, pid) {
                var that = this;
                if (item.child.length) return layList.msg('请先删除子分类再尝试删除此分类！');
                layList.layer.confirm('是否要删除[' + item.name + ']分类？', {
                    btn: ['是的我要删除', '我想想吧'] //按钮
                }, function () {
                    layList.baseGet(that.U({a: 'deletecate', q: {id: item.id}}), function (res) {
                        layList.msg(res.msg, function () {
                            that.getCategoryList();
                        });
                    }, function (err) {
                        layList.msg(err.msg);
                    });
                });
            },
            //打开一个窗口
            getOpenWindow: function (title, url, opt) {
                opt = opt || {};
                return layList.layer.open({
                    type: 2,
                    title: title,
                    shade: [0],
                    area: [opt.w || 340 + "px", opt.h || 265 + 'px'],
                    anim: 2,
                    content: [url, 'no'],
                    end: opt.end || null
                });
            },
            //回调
            SuccessCateg: function () {
                this.getCategoryList();
            },
            renderUpload: function () {
                var vm = this;
                this.uploadInst = layui.upload.render({
                    elem: this.$refs.upload,
                    url: this.U({ a: 'upload' }),
                    acceptMime: 'image/*',
                    size: 2097152,
                    before: function (obj) {
                        layui.layer.load(1);
                        vm.files = obj.pushFile();
                        var file;
                        for (var key in vm.files) {
                            if (Object.hasOwnProperty.call(vm.files, key)) {
                                file = vm.files[key];
                            }
                        }
                        this.data = {
                            pid: vm.pid,
                            title: file.name.slice(0, file.name.lastIndexOf('.'))
                        };
                    },
                    done: function (res, index, upload) {
                        for (var key in vm.files) {
                            if (Object.hasOwnProperty.call(vm.files, key)) {
                                delete vm.files[key];
                            }
                        }
                        layui.layer.closeAll('loading');
                        layui.layer.msg(res.msg === 'ok' ? '上传成功': res.msg);
                        vm.page = 1;
                        vm.getImageList();
                    },
                    error: function (index, upload) {
                        layui.layer.closeAll('loading');
                    }
                });
            }
        },
        mounted: function () {
            this.getCategoryList();
            this.getImageList();
            window.SuccessCateg = this.SuccessCateg;
            this.renderUpload();
        }
    });
</script>
