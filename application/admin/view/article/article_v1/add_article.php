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
    .cate{
        margin-top:10px;
    }
    .edui-default .edui-for-image .edui-icon{
        background-position: -380px 0px;
    }
</style>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
{/block}
{block name="content"}
<div class="layui-fluid" style="background: #fff">
    <div class="layui-row layui-col-space15" id="app" v-cloak>
        <form action="" class="layui-form">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-body">
                        <div class="layui-form-item">
                            <label class="layui-form-label required">新闻标题：</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" v-model.trim="formData.title" autocomplete="off" placeholder="请输入新闻标题" maxlength="20" class="layui-input">
                            </div>
                        </div>
                       <div class="layui-form-item cate">
                            <label class="layui-form-label required">新闻分类：</label>
                            <div class="layui-input-block">
                               <select class="chosen-select" v-model="formData.cid" style="width:100%;" lay-filter="getSelect">
                                   <option value="0" >选择分类</option>
                                    <option v-for="(item,idx) in all"  :value="idx" :key="idx">{{item}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">新闻简介：</label>
                            <div class="layui-input-block">
                                <textarea placeholder="请输入新闻简介" v-model="formData.synopsis" class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">新闻封面：（500*334）</label>
                            <div class="layui-input-block">
                                <div class="upload-image-box" v-if="formData.image_input">
                                    <img :src="formData.image_input" alt="">
                                    <div class="mask">
                                        <p><i class="fa fa-eye" @click="look(formData.image_input)"></i><i class="fa fa-trash-o" @click="formData.image_input = ''"></i></p>
                                    </div>
                                </div>
                                <div class="upload-image"  v-show="!formData.image_input" @click="upload('image_input')">
                                    <div class="fiexd"><i class="fa fa-plus"></i></div>
                                    <p>上传图片</p>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item cate">
                            <label class="layui-form-label required">新闻标签：</label>
                            <div class="layui-input-inline">
                                <input type="text" v-model="label" name="price_min" placeholder="最多4个字" autocomplete="off" maxlength="4" class="layui-input">
                            </div>
                            <div class="layui-input-inline" style="width: auto;">
                                <button type="button" class="layui-btn layui-btn-normal" @click="addLabrl" >
                                    <i class="layui-icon">&#xe654;</i>
                                </button>
                            </div>
                            <div class="layui-form-mid layui-word-aux">输入标签名称后点击”+“号按钮添加；最多写入4个字；点击标签即可删除</div>
                        </div>
                        <div v-if="formData.label.length" class="layui-form-item">
                                <div class="layui-input-block">
                                    <button v-for="(item,index) in formData.label" :key="index" type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="delLabel(index)">{{item}}</button>
                                </div>
                            </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">新闻排序：</label>
                            <div class="layui-input-inline">
                                <input type="number" name="sort" v-model="formData.sort" min="0" max="9999" class="layui-input" v-sort>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">插入视频：</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" v-model="link" style="width:50%;display:inline-block;margin-right: 10px;" autocomplete="off" placeholder="请输入视频链接" class="layui-input">
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="uploadVideo">确认添加</button>
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="ossupload">上传视频</button>
                            </div>
                            <div class="layui-input-block" style="width: 50%;margin-top: 20px" v-show="is_video">
                                <div class="layui-progress" style="margin-bottom: 10px">
                                    <div class="layui-progress-bar layui-bg-blue" :style="'width:'+videoWidth+'%'"></div>
                                </div>
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" @click="cancelUpload">取消</button>
                            </div>
                            <div class="layui-form-mid layui-word-aux">输入链接将视为添加视频直接添加,请确保视频链接的正确性</div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label required">新闻内容：</label>
                            <div class="layui-input-block">
                                <textarea id="editor">{{formData.content}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item submit" style="margin-bottom: 10px">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" type="button" @click="save">{$id ? '确认修改':'立即提交'}</button>
                                <button class="layui-btn layui-btn-primary clone" @click="clone_form">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript" src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name='script'}
<script>
    var id={$id};
    var article=<?=isset($article) ? $article : "{}"?>;
    var all={$all};
    require(['vue','zh-cn','request','aliyun-oss','plupload','OssUpload'],function(Vue) {
        new Vue({
            el: "#app",
            directives: {
                sort: {
                    bind: function (el, binding, vnode) {
                        var vm = vnode.context;
                        el.addEventListener('change', function () {
                            if (!this.value || this.value < 0) {
                                vm.formData.sort = 0;
                            } else if (this.value > 9999) {
                                vm.formData.sort = 9999;
                            } else {
                                vm.formData.sort = parseInt(this.value);
                            }
                        });
                    }
                }
            },
            data: {
                formData: {
                    title:article.title || '',
                    cid:article.cid || 0,
                    synopsis:article.synopsis || '',
                    sort:article.sort || 0,
                    image_input:article.image_input || '',
                    label:article.label || [],
                    content:article.profile ? (article.profile.content || '') : '',
                },
                all:all,
                but_title:'上传视频',
                link:'',
                label:'',
                mask:false,
                ue:null,
                uploader:null,
                is_video:false,
                videoWidth:0,
            },
            methods:{
                uploadVideo:function(){
                    var link = this.link.trim();
                    if (link) {
                        if (link.indexOf('http') || link.indexOf('mp4') !== link.length - 3) {
                            layui.layer.msg('请输入正确的视频链接', {icon: 5});
                        } else {
                            this.setContent(this.link);
                        }
                    }
                },
                setContent:function(link){
                    this.ue.setContent('<div><video style="width: 100%" src="'+link+'" class="video-ue" controls="controls">\n' +
                        'your browser does not support the video tag\n' +
                        '</video></div><br>',true);
                },
                save:function(){
                    var that=this;
                    that.formData.content = that.ue.getContent();
                    if(!that.formData.title) return layList.msg('请输入新闻标题');
                    if(that.formData.cid<=0) return layList.msg('请选择新闻分类');
                    if(!that.formData.synopsis) return layList.msg('请输入新闻简介');
                    if(!that.formData.image_input) return layList.msg('请上传新闻封面');
                    if (!that.formData.label.length) return layList.msg('请添加新闻标签');
                    if (!that.formData.content) {
                        return layList.msg('请编辑新闻内容');
                    }
                    layList.loadFFF();
                    layList.basePost(layList.U({a:'save_article',q:{id:id}}),that.formData,function (res) {
                        layList.loadClear();
                        if(parseInt(id)==0) {
                            layList.layer.confirm('添加成功,您要继续添加新闻吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function (index) {
                                layList.layer.close(index);
                            }, function () {
                                parent.layer.closeAll();
                                window.location.href = layList.U({a: 'index', p: {type: 1}});
                            });
                        }else{
                            layList.msg('修改成功',function () {
                                parent.layer.closeAll();
                                window.location.href = layList.U({a: 'index', p: {type: 1}});
                            })
                        }
                    },function (res) {
                        layList.loadClear();
                        layList.msg(res.msg);
                    });
                },
                clone_form:function(){
                    if (parseInt(id) == 0) {
                        var that = this;
                        if(that.formData.image_input) return layList.msg('请先删除上传的图片在尝试取消');
                        parent.layer.closeAll();
                    }
                    parent.layer.closeAll();
                },
                //取消
                cancelUpload:function(){
                    this.uploader.stop();
                    this.is_video = false;
                    this.videoWidth = 0;
                },
                //上传图片
                upload:function(key,count){
                    ossUpload.createFrame('请选择图片',{fodder:key,max_count:count === undefined ? 0 : count},{w:800,h:550});
                },
                //删除图片
                delect:function(act,key,index){
                    var that=this;
                    switch (act){
                        case 1:
                            Ks3.delObject({Key: key},function () {
                                that.formData.image_input={};
                            },function () {
                                that.formData.image_input={};
                            });
                            break;
                    }
                },
                delLabel:function (index) {
                    this.formData.label.splice(index,1);
                    this.$set(this.formData,'label',this.formData.label);
                },
                addLabrl:function () {
                    if(this.label){
                        if(this.label.length > 4) return layList.msg('您输入的标签字数太长');
                        var length=this.formData.label.length;
                        if(length >= 2) return layList.msg('标签最多添加2个');
                        for(var i=0;i<length;i++){
                            if(this.formData.label[i]==this.label) return layList.msg('请勿重复添加');
                        }
                        this.formData.label.push(this.label);
                        this.$set(this.formData,'label',this.formData.label);
                        this.label='';
                    }
                },
                //查看图片
                look:function(pic){
                    parent.$eb.openImage(pic);
                },
                //鼠标移入事件
                enter:function(item){
                    if(item){
                        item.is_show=true;
                    }else{
                        this.mask=true;
                    }
                },
                //鼠标移出事件
                leave:function(item){
                    if(item){
                        item.is_show=false;
                    }else{
                        this.mask=false;
                    }
                },
                changeIMG:function(key,value,multiple){
                    if(multiple){
                        var that = this;
                        value.map(function (v) {
                            that.formData[key].push({pic:v,is_show:false});
                        });
                        this.$set(this.formData,key,this.formData[key]);
                    }else{
                        this.$set(this.formData,key,value);
                    }
                }
            },
            mounted:function () {
                var that = this;
                this.$nextTick(function () {
                    layList.form.render();
                    layList.form.on('select(getSelect)',function (data) {
                        that.formData.cid = data.value;
                    });
                    //实例化编辑器
                    UE.registerUI('选择图片', function (editor, uiName) {
                        var btn = new UE.ui.Button({
                            name: uiName,
                            title: uiName,
                            cssRules: 'background-position: -380px 0;',
                            onclick: function() {
                                ossUpload.createFrame(uiName, { fodder: editor.key }, { w: 800, h: 550 });
                            }
                        });
                        return btn;
                    });
                    that.ue = UE.getEditor('editor');
                    that.uploader = ossUpload.upload({
                        id:'ossupload',
                        mime_types: [
                            {title: "Mp4 files", extensions: "mp4"}
                        ],
                        FilesAddedSuccess:function(){
                            that.is_video = true;
                        },
                        uploadIng:function (file) {
                            that.videoWidth = file.percent;
                        },
                        success:function (res) {
                            layList.msg('上传成功');
                            that.videoWidth = 0;
                            that.is_video = false;
                            that.setContent(res.url);
                        },
                        fail:function (err) {
                            that.videoWidth = 0;
                            that.is_video = false;
                            layList.msg(err);
                        }
                    })
                }.bind(this));
                window.changeIMG = that.changeIMG;
                //选择图片插入到编辑器中
                window.insertEditor = function(list){
                    that.ue.execCommand('insertimage', list);
                }
            },
            updated:function(){
                layList.form.render();
            }
        })
    })
</script>
{/block}
