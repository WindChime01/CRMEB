{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-form-item .special-label {
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

    .layui-form-item .special-label i {
        display: inline-block;
        width: 18px;
        height: 18px;
        font-size: 18px;
        color: #fff;
    }

    .layui-form-item .label-box {
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

    .layui-form-item .label-box p {
        line-height: inherit;
    }

    .edui-default .edui-for-image .edui-icon {
        background-position: -380px 0px;
    }
</style>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="">
                <div class="layui-tab layui-tab-brief" lay-filter="tab">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="0">基本设置</li>
                        <li lay-id="1">专题选择</li>
                        <li lay-id="2">专题配置</li>
                        <li lay-id="3">价格设置</li>
                    </ul>
                    <div class="layui-tab-content">
                        <!-- 基本设置 -->
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item required">
                                <label class="layui-form-label">专栏名称：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" v-model.trim="formData.title" autocomplete="off" placeholder="请输入专栏名称" maxlength="30" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">课程分类：</label>
                                <div class="layui-input-block">
                                    <select name="subject_id" v-model="formData.subject_id" lay-search="" lay-filter="subject_id">
                                        <option value="0">请选分类</option>
                                        <option  v-for="item in subject_list" :value="item.id" :disabled="item.grade_id==0 ? true : false">{{item.html}}{{item.name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">专栏简介：</label>
                                <div class="layui-input-block">
                                    <textarea placeholder="请输入专栏简介" v-model="formData.abstract" class="layui-textarea"></textarea>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">专栏标签：</label>
                                <div class="layui-input-inline">
                                    <input type="text" v-model="label" name="price_min" placeholder="最多6个字" autocomplete="off" maxlength="6" class="layui-input">
                                </div>
                                <div class="layui-input-inline" style="width: auto;">
                                    <button type="button" class="layui-btn layui-btn-normal" @click="addLabrl" >
                                        <i class="layui-icon">&#xe654;</i>
                                    </button>
                                </div>
                                <div class="layui-form-mid layui-word-aux">输入标签名称后点击”+“号按钮添加；最多写入6个字；点击标签即可删除</div>
                            </div>
                            <div v-if="formData.label.length" class="layui-form-item">
                                <div class="layui-input-block">
                                <button v-for="(item,index) in formData.label" :key="index" type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="delLabel(index)">{{item}}</button>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">专栏封面：（250*140）</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.image">
                                        <img :src="formData.image" alt="">
                                        <div class="mask">
                                            <p><i class="fa fa-eye" @click="look(formData.image)"></i>
                                                <i class="fa fa-trash-o" @click="delect('image')"></i></p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="!formData.image" @click="upload('image')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">专栏Banner：（750*420）</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.banner.length" v-for="(item,index) in formData.banner">
                                        <img :src="item.pic" alt="">
                                        <div class="mask">
                                            <p><i class="fa fa-eye" @click="look(item.pic)"></i>
                                                <i class="fa fa-trash-o" @click="delect('banner',index)"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="formData.banner.length < 5" @click="upload('banner',5 - formData.banner.length)">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">推广海报：（600*740）</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.poster_image">
                                        <img :src="formData.poster_image" alt="">
                                        <div class="mask">
                                            <p><i class="fa fa-eye" @click="look(formData.poster_image)"></i>
                                                <i class="fa fa-trash-o" @click="delect('poster_image')"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="!formData.poster_image" @click="upload('poster_image')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">客服二维码：（200*200）</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.service_code">
                                        <img :src="formData.service_code" alt="">
                                        <div class="mask">
                                            <p>
                                                <i class="fa fa-eye" @click="look(formData.service_code)"></i>
                                                <i class="fa fa-trash-o" @click="delect('service_code')"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="upload-image" v-show="!formData.service_code" @click="upload('service_code')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">插入视频：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" v-model="link" style="width:50%;display:inline-block;margin-right: 10px;" autocomplete="off" placeholder="请输入视频链接" class="layui-input">
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="uploadVideo">确认添加
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="ossupload">上传视频
                                    </button>
                                </div>
                                <input type="file" name="video" v-show="" ref="video">
                                <div class="layui-input-block" style="width: 50%;margin-top: 20px" v-show="is_video">
                                    <div class="layui-progress" style="margin-bottom: 10px">
                                        <div class="layui-progress-bar layui-bg-blue" :style="'width:'+videoWidth+'%'"></div>
                                    </div>
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" @click="cancelUpload">取消</button>
                                </div>
                                <div class="layui-form-mid layui-word-aux">输入链接将视为添加视频直接添加,请确保视频链接的正确性</div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">专栏详情：</label>
                                <div class="layui-input-block">
                                   <textarea id="editor">{{formData.content}}</textarea>
                                </div>
                            </div>
                        </div>
                        <!-- 素材选择 -->
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">有效期(天)：</label>
                                <div class="layui-input-block">
                                    <input style="width: 300px" type="number" name="validity" lay-verify="number" v-model="formData.validity" autocomplete="off" class="layui-input" min="0" max="99999">
                                </div>
                                <p style="color: red;">有效期为用户购买后可以观看时间，0为时间不限；【注：专栏设置有效期后，下列选择的专题的有效期会和专栏保持一致，请谨慎设置】</p>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">仅会员可见：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_mer_visible" lay-filter="is_mer_visible" v-model="formData.is_mer_visible" value="1" title="是">
                                    <input type="radio" name="is_mer_visible" lay-filter="is_mer_visible" v-model="formData.is_mer_visible" value="0" title="否">
                                </div>
                                <p style="color: red;">注："所有人"都可观看的专栏下不能加"仅会员"可见的专题。</p>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题选择：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_source_tmp" name="check_source_tmp"/>
                                    <button type="button" class="layui-btn layui-btn-normal" @click='search_task'>
                                        选择专题
                                    </button>
                                    <table class="layui-hide"  id="List" lay-filter="List"></table>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">添加专题：</label>
                                <div class="layui-input-block">
                                    <button type="button" class="layui-btn layui-btn-normal" @click="add_source('添加视频专题',3)">
                                        添加视频专题
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-normal" @click="add_source('添加音频专题',2)">
                                        添加音频专题
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-normal" @click="add_source('添加图文专题',1)">
                                        添加图文专题
                                    </button>
                                    <button type="button" class="layui-btn layui-btn-normal" @click='add_single_source'>
                                        添加轻专题
                                    </button>
                                    <div class="layui-form-mid layui-word-aux" style="float: none;display: inline-block;">如专题列表中没有，可点击此添加</div>
                                    <br/>
                                    <p style="color: red;">注：选择的专题付费方式需要和专栏专题的付费方式保持一致,如：专栏是会员免费，则选择的专题也需要设置成会员免费；若未设置则会被强制修改。</p>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题展示：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_source_sure" name="check_source_sure"/>
                                    <table class="layui-hide" id="showSourceList" lay-filter="showSourceList"></table>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
<!--                            <div class="layui-form-item">-->
<!--                                <label class="layui-form-label">专题章节：</label>-->
<!--                                <div class="layui-input-inline">-->
<!--                                    <input type="number" name="sum" lay-verify="sum" v-model="formData.sum"-->
<!--                                           autocomplete="off" class="layui-input" min="0" max="99999">-->
<!--                                </div>-->
<!--                                <div class="layui-form-mid">节</div>-->
<!--                            </div>-->
                            <div class="layui-form-item">
                                <label class="layui-form-label">专题排序：</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="sort" v-model="formData.sort" autocomplete="off" min="0"
                                           class="layui-input" v-sort>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">虚拟学习人数：</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="fake_sales" v-model="formData.fake_sales"
                                           autocomplete="off" min="0" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <!-- 价格设置 -->
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">付费方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="pay_type" lay-filter="pay_type" v-model="formData.pay_type" value="1" title="付费">
                                    <input type="radio" name="pay_type" lay-filter="pay_type" v-model="formData.pay_type" value="0" title="免费">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.pay_type == 1">
                                <label class="layui-form-label">购买金额：</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="money" lay-verify="number" v-model="formData.money" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.pay_type == 1">
                                <label class="layui-form-label">会员付费方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="member_pay_type" lay-filter="member_pay_type" v-model="formData.member_pay_type" value="1" title="付费">
                                    <input type="radio" name="member_pay_type" lay-filter="member_pay_type" v-model="formData.member_pay_type" value="0" title="免费">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.member_pay_type == 1">
                                <label class="layui-form-label">会员购买金额：</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="member_money" lay-verify="number" v-model="formData.member_money" autocomplete="off" class="layui-input" min="0">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.pay_type == 1">
                                <label class="layui-form-label">单独分销：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_alone" lay-filter="is_alone" v-model="formData.is_alone" :disabled="formData.pay_type == 0" value="1" title="开启">
                                    <input type="radio" name="is_alone" lay-filter="is_alone" v-model="formData.is_alone" :disabled="formData.pay_type == 0" value="0" title="关闭">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_alone == 1">
                                <label class="layui-form-label">一级返佣比例[5%=5]：</label>
                                <div class="layui-input-block">
                                    <input style="width: 300px" type="number" name="brokerage_ratio" lay-verify="number" v-model="formData.brokerage_ratio" autocomplete="off" class="layui-input" min="0">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_alone == 1">
                                <label class="layui-form-label">二级返佣比例[5%=5]：</label>
                                <div class="layui-input-block">
                                    <input style="width: 300px" type="number" name="brokerage_two" lay-verify="number" v-model="formData.brokerage_two" autocomplete="off" class="layui-input" min="0">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.pay_type == 1">
                                <label class="layui-form-label">拼团状态：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_pink" lay-filter="is_pink" v-model="formData.is_pink" value="1" title="开启">
                                    <input type="radio" name="is_pink" lay-filter="is_pink" v-model="formData.is_pink" value="0" title="关闭" checked="">
                                </div>
                            </div>

                            <div class="layui-form-item" v-show="formData.is_pink">
                                <div class="layui-inline">
                                    <label class="layui-form-label">拼团金额：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="pink_money" v-model="formData.pink_money" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <label class="layui-form-label">拼团人数：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="pink_number" v-model="formData.pink_number" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_pink">
                                <div class="layui-inline">
                                    <label class="layui-form-label">开始时间：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="pink_strar_time" v-model="formData.pink_strar_time" id="start_time" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <label class="layui-form-label">结束时间：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="pink_end_time" v-model="formData.pink_end_time" id="end_time" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_pink">
                                <label class="layui-form-label">拼团时间：</label>
                                <div class="layui-input-inline">
                                    <input type="number" v-model="formData.pink_time" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid">小时</div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_pink">
                                <label class="layui-form-label">模拟成团：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_fake_pink" lay-filter="is_fake_pink" v-model="formData.is_fake_pink" value="1" title="开启" checked="">
                                    <input type="radio" name="is_fake_pink" lay-filter="is_fake_pink" v-model="formData.is_fake_pink" value="0" title="关闭">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_fake_pink && formData.is_pink">
                                <label class="layui-form-label">补齐比例：</label>
                                <div class="layui-input-inline">
                                    <input type="number" v-model="formData.fake_pink_number" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid" style="color: red;">注：可设置成团的补齐比例，拼团结束前实际拼团人数达不到拼团要求时，可根据补齐比例自动添加人数，达到拼团成功的目的</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-primary" @click="clone_form">取消</button>
                        <button v-show="tabIndex" type="button" class="layui-btn layui-btn-primary" @click="tabChange(-1)">上一步</button>
                        <button v-show="tabIndex != 3" type="button" class="layui-btn layui-btn-normal" @click="tabChange(1)">下一步</button>
                        <button v-show="tabIndex == 3" type="button" class="layui-btn layui-btn-normal" @click="save">{$id ?'确认修改':'立即提交'}</button>
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
    var id = {$id},special =<?=isset($special) ? $special : "{}"?>;
    require(['vue','zh-cn','request','plupload','aliyun-oss','OssUpload'], function (Vue) {
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
                subject_list: [],
                source_tmp_list: [],//用于子页父业选中素材传值的临时变量
                source_list: [],
                lecturer_list: [],
                formData: {
                    subjectIds:'',
                    phrase: special.phrase || '',
                    label: special.label || [],
                    abstract: special.abstract || '',
                    title: special.title || '',
                    subject_id: special.subject_id || 0,
                    lecturer_id: special.lecturer_id || 0,
                    task_id: 0,
                    sum: special.sum || 0,
                    image: special.image || '',
                    banner: special.banner || [],
                    poster_image: special.poster_image || '',
                    service_code: special.service_code || '',
                    money: special.money || 0.00,
                    pink_money: special.pink_money || 0.00,
                    pink_number: special.pink_number || 0,
                    pink_strar_time: special.pink_strar_time || '',
                    pink_end_time: special.pink_end_time || '',
                    fake_pink_number: special.fake_pink_number || 0,
                    sort: special.sort || 0,
                    is_mer_visible: special.is_mer_visible || 0,
                    is_pink: special.is_pink || 0,
                    is_fake_pink: special.is_fake_pink || 1,
                    fake_sales: special.fake_sales || 0,
                    validity: special.validity || 0,
                    browse_count: special.browse_count || 0,
                    pink_time: special.pink_time || 0,
                    content: special.profile ? (special.profile.content || '') : '',
                    pay_type: special.pay_type == 1 ? 1 : 0,
                    member_pay_type: special.member_pay_type == 1 ? 1 : 0,
                    member_money: special.member_money || 0.00,
                    link:special.link || '',
                    check_source_sure: [],
                    check_store_sure: [],
                    sort_order:special.sort_order || 0,
                    is_alone:special.pay_type == 1 ? (special.is_alone == 1 ? 1 : 0) : 0,
                    brokerage_ratio:special.pay_type == 1 ? (special.brokerage_ratio || 0) : 0,
                    brokerage_two:special.pay_type == 1 ? (special.brokerage_two || 0) : 0
                },
                but_title: '上传视频',
                link: '',
                label: '',
                host: ossUpload.host + '/',
                mask: {
                    poster_image: false,
                    image: false,
                    service_code: false,
                },
                ue: null,
                is_video: false,
                //上传类型
                mime_types: {
                    Image: "jpg,gif,png,JPG,GIF,PNG",
                    Video: "mp4,MP4",
                },
                videoWidth: 0,
                uploader: null,
                tabIndex: 0
            },
            watch:{
                'formData.validity':function (v) {
                    if (v.indexOf('.')!=-1) {
                        return layList.msg('不能输入小数');
                    }
                    if(v<0) return layList.msg('不能小于0');
                    if(v>99999) return layList.msg('不能大于99999');
                }
            },
            methods: {
                //取消
                cancelUpload: function () {
                    this.uploader.stop();
                    this.is_video = false;
                    this.videoWidth = 0;
                },
                //删除图片
                delect: function (key, index) {
                    var that = this;
                    if (index != undefined) {
                        that.formData[key].splice(index, 1);
                        that.$set(that.formData, key, that.formData[key]);
                    } else {
                        that.$set(that.formData, key, '');
                    }
                },
                //查看图片
                look: function (pic) {
                    parent.$eb.openImage(pic);
                },
                //鼠标移入事件
                enter: function (item) {
                    if (item) {
                        item.is_show = true;
                    } else {
                        this.mask = true;
                    }
                },
                //鼠标移出事件
                leave: function (item) {
                    if (item) {
                        item.is_show = false;
                    } else {
                        this.mask = false;
                    }
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
                uploadVideo: function () {
                    var link = this.link.trim();
                    if (link) {
                        if (/^https?:\/\/(.+\/)+.+(\.(mp4))$/i.test(link)) {
                            this.setContent(this.link);
                        } else {
                            layui.layer.msg('请输入正确的视频链接', {icon: 5});
                        }
                    }
                },
                setContent: function (link) {
                    this.formData.link = link;
                    this.ue.setContent('<div><video style="width: 100%" src="'+link+'" class="video-ue" controls="controls"><source src="'+link+'"></source></video></div><span style="color:white">.</span>',true);
                },
                //上传图片
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                //获取分类
                get_subject_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'get_subject_list'}), function (res) {
                        that.$set(that, 'subject_list', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                delLabel: function (index) {
                    this.formData.label.splice(index, 1);
                    this.$set(this.formData, 'label', this.formData.label);
                },
                addLabrl: function () {
                    if (this.label) {
                        if (this.label.length > 6) return layList.msg('您输入的标签字数太长');
                        var length = this.formData.label.length;
                        if (length >= 2) return layList.msg('标签最多添加2个');
                        for (var i = 0; i < length; i++) {
                            if (this.formData.label[i] == this.label) return layList.msg('请勿重复添加');
                        }
                        this.formData.label.push(this.label);
                        this.$set(this.formData, 'label', this.formData.label);
                        this.label = '';
                    }
                },
                save: function () {
                    var that = this, banner = new Array();
                    that.formData.content = that.ue.getContent();
                    if (!that.formData.title) return layList.msg('请输入专栏标题');
                    if (!that.formData.subject_id) return layList.msg('请选择分类');
                    if (!that.formData.abstract) return layList.msg('请输入专栏简介');
                    if (!that.formData.label.length) return layList.msg('请输入标签');
                    if (!that.formData.image) return layList.msg('请上传专栏封面');
                    if (!that.formData.banner.length) return layList.msg('请上传banner图,最少1张');
                    if (!that.formData.poster_image) return layList.msg('请上传推广海报');
                    if (!that.formData.content) return layList.msg('请编辑内容在进行保存');
                    if (that.formData.validity < 0) return layList.msg('专题有效期不能小于0');
                    if (that.formData.validity > 99999) return layList.msg('专题有效期不能大于99999');
                    if ((that.formData.validity+'').indexOf('.')!=-1) return layList.msg('专题有效期不能为小数');
                    if (that.formData.sum < 0) return layList.msg('专题章节不能小于0');
                    if (that.formData.sum > 99999) return layList.msg('专题章节不能大于99999');
                    if ((that.formData.sum + '').indexOf('.') != -1) return layList.msg('专题章节不能为小数');
                    if (that.formData.is_pink) {
                        if (!that.formData.pink_money) return layList.msg('请填写拼团金额');
                        if (!that.formData.pink_number) return layList.msg('请填写拼团人数');
                        if (!that.formData.pink_strar_time) return layList.msg('请选择拼团开始时间');
                        if (!that.formData.pink_end_time) return layList.msg('请选择拼团结束时间');
                        if (!that.formData.pink_time) return layList.msg('请填写拼团时间');
                        if (that.formData.is_fake_pink && !that.formData.fake_pink_number) return layList.msg('请填写补齐比例');
                    }
                    that.formData.subjectIds=JSON.stringify(that.formData.check_source_sure);
                    if (that.formData.pay_type == 1 && (!that.formData.money || that.formData.money == 0.00)) return layList.msg('请填写购买金额');
                    if (that.formData.member_pay_type == 1 && (!that.formData.member_money || that.formData.member_money == 0.00)) return layList.msg('请填写会员购买金额');
                    if (that.formData.is_alone == 1) {
                        if (that.formData.brokerage_ratio<0 || !that.formData.brokerage_two<0) return layList.msg('推广人返佣比例不能小于0');
                        if (!that.formData.brokerage_ratio || !that.formData.brokerage_two) return layList.msg('请填写推广人返佣比例');
                    }
                    var data={};
                    for (var key in that.formData) {
                        if (key !== 'check_source_sure') {
                            data[key] = that.formData[key]
                        }
                    }
                    layList.loadFFF();
                    layList.basePost(layList.U({
                        a: 'save_special',
                        q: {id: id, special_type: '{$special_type}'}
                    }), data, function (res) {
                        layList.loadClear();
                        if (parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加专栏吗?', {
                                btn: ['继续添加', '立即提交'] //按钮
                            }, function (index) {
                                layList.layer.close(index);
                            }, function () {
                                parent.layer.closeAll();
                            });
                        } else {
                            layList.msg('修改成功', function () {
                                parent.layer.closeAll();
                            })
                        }
                    }, function (res) {
                        layList.msg(res.msg);
                        layList.loadClear();
                    });
                },
                clone_form: function () {
                    var that = this;
                    parent.layer.closeAll();
                },
                //素材
                search_task: function () {
                    var that = this;
                    var url="{:Url('special.special_type/special_task')}?special_id="+id+"&special_type={$special_type}";
                    var title='选择专题';
                    that.searchTask=true;
                    layer.open({
                        type: 2 //Page层类型
                        ,area: ['80%', '90%']
                        ,title: title
                        ,shade: 0.6 //遮罩透明度
                        ,maxmin: true //允许全屏最小化
                        ,anim: 1 //0-6的动画形式，-1不开启
                        ,content: url,
                        btn: '确定',
                        btnAlign: 'c', //按钮居中
                        closeBtn:1,
                        yes: function(){
                            layer.closeAll();
                            var source_tmp = $("#check_source_tmp").val();
                            that.source_tmp_list = JSON.parse(source_tmp);
                            var array=that.formData.check_source_sure;
                            that.formData.check_source_sure=array.concat(JSON.parse(source_tmp));
                            that.formData.check_source_sure=that.duplicate_removal(that.formData.check_source_sure);
                            that.show_source_list();
                        }
                    });
                },
                duplicate_removal:function(array)
                {
                    var new_arr=[];
                    var check_source_sure=[];
                    for(var i=0;i<array.length;i++) {
                        var items=array[i];
                        var id=array[i].id;
                        if($.inArray(id,new_arr)==-1) {
                            new_arr.push(id);
                            check_source_sure.push(items);
                        }
                    }
                    return check_source_sure;
                },
                add_source:function(title,special_type){
                    var that=this;
                    var url="{:Url('special.special_type/add')}?special_type="+special_type;
                    layer.open({
                        type: 2 //Page层类型
                        ,area: ['90%', '95%']
                        ,title: title
                        ,shade: 0.6 //遮罩透明度
                        ,maxmin: true //允许全屏最小化
                        ,anim: 1 //0-6的动画形式，-1不开启
                        ,content: url
                        ,end:function () {
                            layer.closeAll();
                        }
                    });
                },
                add_single_source:function(){
                    var that=this;
                    var url="{:Url('special.special_type/single_add')}?special_type=6";
                    var title='添加轻专题';
                    layer.open({
                        type: 2 //Page层类型
                        ,area: ['90%', '95%']
                        ,title: title
                        ,shade: 0.6 //遮罩透明度
                        ,maxmin: true //允许全屏最小化
                        ,anim: 1 //0-6的动画形式，-1不开启
                        ,content: url
                        ,end:function () {
                            layer.closeAll();
                        }
                    });
                },
                show_source_list: function () {
                    var that = this;
                    var table = layui.table, form = layui.form;
                    table.render({
                        elem: '#showSourceList',
                        cols: [[
                            {field: 'id', title: '编号', align: 'center',width:60},
                            {field: 'title', title: '标题', align: 'center'},
                            {field: 'image', title: '封面', align: 'center', templet: '<div><img src="{{ d.image }}" style="width: 100%;height: 100%;"></div>'},
                            {field: 'is_mer_visible', title: '仅会员可见',align: 'center',templet:function(d){
                                    var is_checked = d.is_mer_visible == 1 ? "checked" : "";
                                    return "<input type='checkbox' disabled name='is_mer_visible' lay-skin='switch' value='"+d.id+"'  lay-text='是|否' "+is_checked+">";
                                }},
                            {field: 'sort', title: '排序',edit:'sort',align: 'center'},
                            {field: 'validity', title: '有效期(天)',align: 'center'},
                            {field: 'right', title: '状态', align: 'center', templet:function(d){
                                    return '<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon">&#xe640;</i> 移除</a>';
                                }},
                        ]]
                        ,data:that.formData.check_source_sure
                        , page: {
                            theme: '#0092DC'
                        }
                        ,id: 'table'
                    });
                    table.on('tool(showSourceList)', function(obj){
                        var data = obj.data;
                        if(obj.event === 'del'){
                            if (that.formData.check_source_sure) {
                                for(var i=0;i<that.formData.check_source_sure.length;i++){
                                    if(that.formData.check_source_sure[i].id==data.id){
                                        that.formData.check_source_sure.splice(i,1);
                                    }
                                }
                                that.formData.check_source_sure=that.formData.check_source_sure;
                                that.show_source_list();
                                that.del_source_sure(data.id);
                            }
                        }
                    });
                    //监听单元格编辑
                    table.on('edit(showSourceList)', function(obj){
                        var id=obj.data.id,values=obj.value;
                        switch (obj.field) {
                            case 'sort':
                                if (that.formData.check_source_sure) {
                                    $.each(that.formData.check_source_sure, function(index, value){
                                        if(value.id == id){
                                            that.formData.check_source_sure[index].sort = values;
                                        }
                                    })
                                }
                                break;
                        }
                    });
                    //监听素材是否免费操作
                    form.on('switch(pay_status)', function (obj) {
                        if (that.formData.check_source_sure) {
                            $.each(that.formData.check_source_sure, function (index, value) {
                                if (value.id == obj.value) {
                                    that.formData.check_source_sure[index].pay_status = obj.elem.checked == false ? 0 : 1;
                                }
                            })
                        }
                    });
                },
                del_source_sure:function (source_id) {
                    var that = this;
                    layList.baseGet(layList.U({a: 'del_source_source',q: {source_id: source_id, special_id: id, special_type: '{$special_type}'}}), function (res) {
                        return layList.msg('移除成功！');
                    });
                },
                get_check_source_sure:function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'get_check_source_sure',q: {id: id, special_type:"{$special_type}"}}), function (res) {
                        that.formData.check_source_sure=res.data.sourceCheckList;
                        that.formData.check_store_sure=res.data.storeCheckList;
                        that.show_source_list();
                    });
                },
                // 上一步、下一步
                tabChange: function (value) {
                    layui.element.tabChange('tab', this.tabIndex + value);
                }
            },
            mounted: function () {
                var that = this;
                window.changeIMG = that.changeIMG;
                layList.date({
                    elem: '#start_time',
                    type: 'datetime',
                    done: function (value) {
                        that.formData.pink_strar_time = value;
                    }
                });
                layList.date({
                    elem: '#end_time',
                    type: 'datetime',
                    done: function (value) {
                        that.formData.pink_end_time = value;
                    }
                });
                //选择图片
                function changeIMG(index, pic) {
                    $(".image_img").css('background-image', "url(" + pic + ")");
                    $(".active").css('background-image', "url(" + pic + ")");
                    $('#image_input').val(pic);
                }
                //选择图片插入到编辑器中
                window.insertEditor = function (list) {
                    that.ue.execCommand('insertimage', list);
                };
                this.$nextTick(function () {
                    layList.form.render();
                    //实例化编辑器
                    layui.element.on('tab(tab)', function (data) {
                        layui.table.resize('table');
                        that.tabIndex = data.index;
                    });
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
                });
                //获取科目
                that.get_subject_list();
                //获取已添加的素材
                that.get_check_source_sure();
                //图片上传和视频上传
                layList.form.on('radio(is_pink)', function (data) {
                    that.formData.is_pink = parseInt(data.value);
                });
                layList.form.on('radio(pay_type)', function (data) {
                    that.formData.pay_type = parseInt(data.value);
                    if (that.formData.pay_type != 1) {
                        that.formData.is_pink = 0;
                        that.formData.member_pay_type = 0;
                        that.formData.member_money = 0;
                        that.formData.is_alone = 0;
                        that.formData.brokerage_ratio = 0;
                        that.formData.brokerage_two = 0;
                    };
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.form.on('radio(is_alone)', function (data) {
                    that.formData.is_alone = parseInt(data.value);
                    if (that.formData.is_alone != 1) {
                        that.formData.brokerage_ratio = 0;
                        that.formData.brokerage_two = 0;
                    };
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.form.on('radio(sort_order)', function (data) {
                    that.formData.sort_order = parseInt(data.value);
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.form.on('radio(member_pay_type)', function (data) {
                    that.formData.member_pay_type = parseInt(data.value);
                    if (that.formData.member_pay_type != 1) {
                        that.formData.member_money = 0;
                    };
                    that.$nextTick(function () {
                        layList.form.render('radio');
                    });
                });
                layList.select('subject_id', function (obj) {
                    that.formData.subject_id = obj.value;
                });
                layList.form.on('radio(is_fake_pink)', function (data) {
                    that.formData.is_fake_pink = parseInt(data.value);
                });
                layList.form.on('radio(is_mer_visible)', function (data) {
                    that.formData.is_mer_visible = parseInt(data.value);
                });
                that.$nextTick(function () {
                    that.uploader = ossUpload.upload({
                        id: 'ossupload',
                        mime_types: [
                            {title: "Mp4 files", extensions: "mp4"}
                        ],
                        FilesAddedSuccess: function () {
                            that.is_video = true;
                        },
                        uploadIng: function (file) {
                            that.videoWidth = file.percent;
                        },
                        success: function (res) {
                            layList.msg('上传成功');
                            that.videoWidth = 0;
                            that.is_video = false;
                            that.setContent(res.url);
                        },
                        fail: function (err) {
                            that.videoWidth = 0;
                            that.is_video = false;
                            layList.msg(err);
                        }
                    })
                });
            }
        })
    })
</script>
{/block}
