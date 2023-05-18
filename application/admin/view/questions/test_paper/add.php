{extend name="public/container"}
{block name='head_top'}
<style>
    .layui-table {
        width: 100%!important;
    }

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
                        <li lay-id="1">题库选择</li>
                        <li v-show="type==2" lay-id="2">价格设置</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item">
                                <label class="layui-form-label required">{$type==1 ? '练习名称' : '考试名称'}：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" v-model.trim="formData.title" autocomplete="off" placeholder="请输入{$type==1 ? '练习名称' : '考试名称'}" maxlength="30" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label required">{$type==1 ? '练习分类' : '考试分类'}：</label>
                                <div class="layui-input-block">
                                    <select name="tid" v-model="formData.tid" lay-search="" lay-filter="tid">
                                        <option value="">请选择{$type==1 ? '练习分类' : '考试分类'}</option>
                                        <option v-for="item in cateList" :disabled="item.pid==0 ? true : false" :value="item.id">{{item.html}}{{item.title}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="type==2">
                                <label :class="{ required: type == 2 }" class="layui-form-label">考试封面：（710*400）</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.image">
                                        <img :src="formData.image" alt="">
                                        <div class="mask">
                                            <p><i class="fa fa-eye" @click="look(formData.image)"></i><i class="fa fa-trash-o" @click="delect('image')"></i></p>
                                        </div>
                                    </div>
                                    <div class="upload-image"  v-show="!formData.image" @click="upload('image')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label required">单选题数量：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="single_number" v-model.number="formData.single_number" min="0" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" >
                                    <label class="layui-form-label">题型排序/倒序：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="single_sort" v-model="formData.single_sort" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">每题分数：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="single_score" v-model="formData.single_score" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label required">多选题数量：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="many_number" v-model.number="formData.many_number" min="0" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" >
                                    <label class="layui-form-label">题型排序/倒序：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="many_sort" v-model="formData.many_sort" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">每题分数：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="many_score" v-model="formData.many_score" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label required">判断题数量：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="judge_number" v-model.number="formData.judge_number" min="0" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" >
                                    <label class="layui-form-label">题型排序/倒序：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="judge_sort" v-model="formData.judge_sort" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">每题分数：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="judge_score" v-model="formData.judge_score" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">试题总数：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="item_number" :value="sum" min="0" disabled autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">总分数：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="total_score" :value="total" autocomplete="off" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <label class="layui-form-label">虚拟答题人数：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="fake_sales" v-model="formData.fake_sales" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline" v-show="type==2">
                                    <label :class="{ required: type == 2 }" class="layui-form-label">时长/分：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="txamination_time" v-model="formData.txamination_time" lay-verify="number" autocomplete="off" class="layui-input" min="0">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label :class="{ required: type == 2 }" class="layui-form-label">考试次数：</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="frequency" v-model="formData.frequency" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2" style="color: red;">注：[0为不限制次数]</div>
                            </div>
                            <div class="layui-form-item submit" v-show="type==2">
                                <label class="layui-form-label">分数显示：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_score" lay-filter="is_score" v-model="formData.is_score" value="1" title="显示">
                                    <input type="radio" name="is_score" lay-filter="is_score" v-model="formData.is_score" value="0" title="隐藏">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">试卷难度：</label>
                                <div class="layui-input-block">
                                    <div id="rate"></div>
                                </div>
                            </div>
                            <div v-for="(item, index) in grade" :key="index"  class="layui-form-item" v-show="type==2">
                                <label v-if="!index" class="layui-form-label">分数等级：</label>
                                <div class="layui-input-block">
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <input v-model="item.grade_name" type="text" required  lay-verify="required" maxlength="60" placeholder="名称" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <input v-model="item.grade_standard" type="text" required  lay-verify="required" maxlength="60" placeholder="区间" autocomplete="off" class="layui-input">
                                        </div>
                                    </div>
                                    <div v-if="index && index === grade.length - 1" class="layui-inline">
                                        <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" @click="onDel">删除选项</button>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="type==2">
                                <div class="layui-input-block">
                                    <button type="button" class="layui-btn layui-btn-normal layui-btn-sm" @click="onAdd">添加选项</button>
                                </div>
                            </div>
                            <div class="layui-form-item submit">
                                <label class="layui-form-label">组题方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_group" lay-filter="is_group" v-model="formData.is_group" value="1" title="手动组题">
                                    <input type="radio" name="is_group" lay-filter="is_group" v-model="formData.is_group" value="2" title="随机组题">
                                </div>
                            </div>
                            <div class="layui-form-item submit">
                                <label class="layui-form-label">{$type==1 ? '练习状态' : '考试状态'}：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_show" lay-filter="is_show" v-model="formData.is_show" value="1" title="显示">
                                    <input type="radio" name="is_show" lay-filter="is_show" v-model="formData.is_show" value="0" title="隐藏">
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">

                            <div class="layui-form-item" v-show="formData.is_group==1">
                                <label class="layui-form-label">单选题：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_questions_tmp_1" name="check_questions_tmp_1"/>
                                    <button type="button" class="layui-btn layui-btn-normal" @click='questions_task(1)'>
                                        选择单选试题
                                    </button>
                                    <div class="layui-inline"  style="color: red;">注：选择试题前请先添加单选题数量</div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_group==1">
                                <label class="layui-form-label">题库展示：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_questions_sure_1" name="check_questions_sure_1"/>
                                    <table class="layui-hide" id="showQuestionsList_1" lay-filter="showQuestionsList_1"></table>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_group==1">
                                <label class="layui-form-label">多选题：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_questions_tmp_2" name="check_questions_tmp_2"/>
                                    <button type="button" class="layui-btn layui-btn-normal" @click='questions_task(2)'>
                                        选择多选试题
                                    </button>
                                    <div class="layui-inline"  style="color: red;">注：选择试题前请先添加多选题数量</div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_group==1">
                                <label class="layui-form-label">题库展示：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_questions_sure_2" name="check_questions_sure_2"/>
                                    <table class="layui-hide" id="showQuestionsList_2" lay-filter="showQuestionsList_2"></table>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_group==1">
                                <label class="layui-form-label">判断题：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_questions_tmp_3" name="check_questions_tmp_3"/>
                                    <button type="button" class="layui-btn layui-btn-normal" @click='questions_task(3)'>
                                        选择判断试题
                                    </button>
                                    <div class="layui-inline"  style="color: red;">注：选择试题前请先添加判断题数量</div>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.is_group==1">
                                <label class="layui-form-label">题库展示：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" id="check_questions_sure_3" name="check_questions_sure_3"/>
                                    <table class="layui-hide" id="showQuestionsList_3" lay-filter="showQuestionsList_3"></table>
                                </div>
                            </div>
                            <div class="layui-form-item"  v-show="formData.is_group==2">
                                <label class="layui-form-label">题库分类：</label>
                                <div class="layui-input-block">
                                    <select name="cate_id" v-model="formData.cate_id" lay-search="" lay-filter="cate_id">
                                        <option v-for="item in questionsCateList" :value="item.id">{{item.html}}{{item.title}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
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
                                <div class="layui-input-block">
                                    <input style="width: 300px" type="number" name="money" lay-verify="number" v-model="formData.money" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="padding: 9px 0;">会员付费方式：</label>
                                    <div class="layui-input-block">
                                        <input type="radio" name="member_pay_type" lay-filter="member_pay_type" v-model="formData.member_pay_type" value="1" title="付费">
                                        <input type="radio" name="member_pay_type" lay-filter="member_pay_type" v-model="formData.member_pay_type" value="0" title="免费">
                                    </div>
                                </div>
                                <div class="layui-form-item" v-show="formData.member_pay_type == 1">
                                    <label class="layui-form-label" style="padding: 9px 0;">会员购买金额：</label>
                                    <div class="layui-input-block">
                                        <input style="width: 300px" type="number" name="member_money" lay-verify="number" v-model="formData.member_money" autocomplete="off" class="layui-input" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-primary" @click="clone_form">取消</button>
                        <button v-show="tabIndex" type="button" class="layui-btn layui-btn-primary" @click="tabChange(-1)">上一步</button>
                        <button v-show="(tabIndex != 1 && type == 1) || (tabIndex != 2 && type == 2)" type="button" class="layui-btn layui-btn-normal" @click="tabChange(1)">下一步</button>
                        <button v-show="(tabIndex == 1 && type == 1) || (tabIndex == 2 && type == 2)" type="button" class="layui-btn layui-btn-normal" @click="save">{$id ?'确认修改':'立即提交'}</button>
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
    var id={$id},type={$type},test=<?=isset($test) ? $test : []?>,grades=<?=isset($grade) ? $grade : []?>,
        single_tmp_list =<?= isset($single_tmp_list) ? $single_tmp_list : "{}"?>,
        many_tmp_list =<?= isset($many_tmp_list) ? $many_tmp_list : "{}"?>,
        judge_tmp_list =<?= isset($judge_tmp_list) ? $judge_tmp_list : "{}"?>;
    require(['vue','helper','zh-cn','request','plupload','aliyun-oss','OssUpload'], function (Vue,$h) {
        new Vue({
            el: "#app",
            data: {
                cateList:[],
                questionsCateList:[],
                single_list:[],//用于子页父业选中素材传值的临时变量
                many_list:[],//用于子页父业选中素材传值的临时变量
                judge_list:[],//用于子页父业选中素材传值的临时变量
                formData:{
                    singleIds:'',
                    manyIds:'',
                    judgeIds:'',
                    title:test.title || '',
                    image:test.image || '',
                    tid: test.tid || 0,
                    is_show: test.is_show || 1,
                    item_number:Number(test.item_number) || 0,
                    total_score:Number(test.total_score) || 0,
                    single_number:Number(test.single_number) || 0,
                    single_score:Number(test.single_score) || 0,
                    many_number:Number(test.many_number) || 0,
                    many_score:Number(test.many_score) || 0,
                    judge_number:Number(test.judge_number) || 0,
                    judge_score:Number(test.judge_score) || 0,
                    single_sort:Number(test.single_sort) || 0,
                    many_sort:Number(test.many_sort) || 0,
                    judge_sort:Number(test.judge_sort) || 0,
                    fake_sales:test.fake_sales || 0,
                    difficulty:test.difficulty || 1,
                    pay_type:test.pay_type || 0,
                    money:test.money || 0,
                    member_pay_type:test.member_pay_type || 0,
                    member_money:test.member_money || 0,
                    txamination_time:Number(test.txamination_time) || 0,
                    single_tmp_list:single_tmp_list ? single_tmp_list : {},
                    many_tmp_list:many_tmp_list ? many_tmp_list : {},
                    judge_tmp_list:judge_tmp_list ? judge_tmp_list : {},
                    is_score:test.is_score || 0,
                    frequency:test.frequency > 0 ? test.frequency : (id > 0 ? 0 : 1),
                    sort:Number(test.sort) || 0,
                    is_group:test.is_group || 1,
                    cate_id:test.cate_id || 0,
                },
                type:type,
                mask:{
                    image:false
                },
                grade: [
                    {
                        grade_name:'优秀',
                        grade_standard: '91~100'
                    },
                    {
                        grade_name:'良好',
                        grade_standard: '81~90'
                    },
                    {
                        grade_name:'合格',
                        grade_standard: '61~80'
                    },
                    {
                        grade_name:'不合格',
                        grade_standard: '0~60'
                    }
                ],
                difficultyHover: 0,
                tabIndex: 0
            },
            computed: {
                sum: function () {
                    return this.formData.single_number + this.formData.many_number + this.formData.judge_number;
                },
                total: function () {
                    return this.formData.single_number * this.formData.single_score + this.formData.many_number * this.formData.many_score + this.formData.judge_number * this.formData.judge_score;
                }
            },
            methods: {
                onRateClick: function (value) {
                    this.formData.difficulty = value;
                },
                onRateMousemove: function (value) {
                    this.difficultyHover = value;
                },
                onRateMouseleave: function () {
                    this.difficultyHover = 0;
                },
                onAdd: function () {
                    this.grade.push({
                        grade_name: '',
                        grade_standard: ''
                    });
                },
                onDel: function () {
                    this.grade.pop();
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
                //上传图片
                upload: function (key, count) {
                    ossUpload.createFrame('请选择图片', {fodder: key, max_count: count === undefined ? 0 : count},{w:800,h:550});
                },
                get_subject_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'add_cate_list',p:{type:type}}), function (res) {
                        that.$set(that, 'cateList', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                get_questions_list: function () {
                    var that = this;
                    layList.baseGet(layList.U({a: 'cate_questions',p:{type:type}}), function (res) {
                        that.$set(that, 'questionsCateList', res.data);
                        that.$nextTick(function () {
                            layList.form.render('select');
                        })
                    });
                },
                questions_task:function (question_type) {
                    var that=this;
                    var url="{:Url('questions.test_paper/questions')}?question_type="+question_type+'&id='+id;
                    layer.open({
                        type: 2 //Page层类型
                        ,area: ['80%', '90%']
                        ,title: '关联试题'
                        ,shade: 0.6 //遮罩透明度
                        ,maxmin: true //允许全屏最小化
                        ,anim: 1 //0-6的动画形式，-1不开启
                        ,content: url,
                        btn: '确定',
                        btnAlign: 'c', //按钮居中
                        closeBtn:1,
                        yes: function(){
                            layer.closeAll();
                            var questions_tmp = $("#check_questions_tmp_"+question_type).val();
                            if(question_type==1){
                                that.single_list = JSON.parse(questions_tmp).slice(0, that.formData.single_number);
                                var array=that.formData.single_tmp_list;
                                that.formData.single_tmp_list=array.concat(JSON.parse(questions_tmp).slice(0, that.formData.single_number));
                                that.show_single_list();
                            }else if(question_type==2){
                                that.many_list = JSON.parse(questions_tmp).slice(0, that.formData.many_number);
                                var array=that.formData.many_tmp_list;
                                that.formData.many_tmp_list = array.concat(JSON.parse(questions_tmp).slice(0, that.formData.many_number));
                                that.show_many_list();
                            }else{
                                that.judge_list = JSON.parse(questions_tmp).slice(0, that.formData.judge_number);
                                var array=that.formData.judge_tmp_list;
                                that.formData.judge_tmp_list = array.concat(JSON.parse(questions_tmp).slice(0, that.formData.judge_number));
                                that.show_judge_list();
                            }
                        }
                    });
                },
                show_single_list:function () {
                    var that = this;
                    var table = layui.table,form = layui.form;
                    table.render({
                        elem: '#showQuestionsList_1'
                        ,cols: [[
                            {field: 'id', title: '编号', align: 'center',width:200},
                            {field: 'stem', title: '题干',align: 'center'},
                            {field: 'sort', title: '排序/倒序(以整套试卷排列)',edit:'sort',align: 'center',width:200},
                            {field: 'right', title: '操作',align: 'center',width:200,templet:function(d){
                                    return '<div><a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon">&#xe640;</i> 移除</a></div>';
                                }}
                        ]]
                        ,data: (Object.keys(that.formData.single_tmp_list).length > 0) ? that.formData.single_tmp_list : []
                        ,id: 'table_1'
                    });
                    table.on('tool(showQuestionsList_1)', function(obj){
                        var data = obj.data;
                        if(obj.event === 'del'){
                            if (that.formData.single_tmp_list) {
                                for(var i=0;i<that.formData.single_tmp_list.length;i++){
                                    if(that.formData.single_tmp_list[i].id==data.id){
                                        that.formData.single_tmp_list.splice(i,1);
                                    }
                                }
                                that.formData.single_tmp_list=that.formData.single_tmp_list;
                                that.show_single_list();
                            }
                        }
                    });
                    table.on('edit(showQuestionsList_1)', function(obj){
                        var id=obj.data.id,values=Number(obj.value);
                        switch (obj.field) {
                            case 'sort':
                                if (that.formData.single_tmp_list) {
                                    $.each(that.formData.single_tmp_list, function(index, value){
                                        if(value.id == id){
                                            that.formData.single_tmp_list[index].sort = values;
                                        }
                                    })
                                }
                                break;
                        }
                    });
                    //监听素材是否删除
                    form.on('switch(delect)', function(obj){
                        if (that.formData.single_tmp_list) {
                            for(var i=0;i<that.formData.single_tmp_list.length;i++){
                                if(that.formData.single_tmp_list[i].id==obj.value){
                                    that.formData.single_tmp_list.splice(i,1);
                                }
                            }
                            that.formData.single_tmp_list=that.formData.single_tmp_list;
                            that.show_single_list();
                        }
                    });
                },
                show_many_list:function () {
                    var that = this;
                    var table = layui.table,form = layui.form;
                    table.render({
                        elem: '#showQuestionsList_2'
                        ,cols: [[
                            {field: 'id', title: '编号', align: 'center',width:200},
                            {field: 'stem', title: '题干',align: 'center'},
                            {field: 'sort', title: '排序/倒序(以整套试卷排列)',edit:'sort',align: 'center',width:200},
                            {field: 'right', title: '操作',align: 'center',width:200,templet:function(d){
                                    return '<div><a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon">&#xe640;</i> 移除</a></div>';
                                }}
                        ]]
                        ,data: (Object.keys(that.formData.many_tmp_list).length > 0) ? that.formData.many_tmp_list : []
                        ,id: 'table_2'
                    });
                    table.on('tool(showQuestionsList_2)', function(obj){
                        var data = obj.data;
                        if(obj.event === 'del'){
                            if (that.formData.many_tmp_list) {
                                for(var i=0;i<that.formData.many_tmp_list.length;i++){
                                    if(that.formData.many_tmp_list[i].id==data.id){
                                        that.formData.many_tmp_list.splice(i,1);
                                    }
                                }
                                that.formData.many_tmp_list=that.formData.many_tmp_list;
                                that.show_many_list();
                            }
                        }
                    });
                    table.on('edit(showQuestionsList_2)', function(obj){
                        var id=obj.data.id,values=Number(obj.value);
                        switch (obj.field) {
                            case 'sort':
                                if (that.formData.many_tmp_list) {
                                    $.each(that.formData.many_tmp_list, function(index, value){
                                        if(value.id == id){
                                            that.formData.many_tmp_list[index].sort = values;
                                        }
                                    })
                                }
                                break;
                        }
                    });
                    //监听素材是否删除
                    form.on('switch(delect)', function(obj){
                        if (that.formData.many_tmp_list) {
                            for(var i=0;i<that.formData.many_tmp_list.length;i++){
                                if(that.formData.many_tmp_list[i].id==obj.value){
                                    that.formData.many_tmp_list.splice(i,1);
                                }
                            }
                            that.formData.many_tmp_list=that.formData.many_tmp_list;
                            that.show_many_list();
                        }
                    });
                },
                show_judge_list:function () {
                    var that = this;
                    var table = layui.table,form = layui.form;
                     table.render({
                            elem: '#showQuestionsList_3'
                            ,cols: [[
                                {field: 'id', title: '编号', align: 'center',width:200},
                                {field: 'stem', title: '题干',align: 'center'},
                                {field: 'sort', title: '排序/倒序(以整套试卷排列)',edit:'sort',align: 'center',width:200},
                                {field: 'right', title: '操作',align: 'center',width:200,templet:function(d){
                                        return '<div><a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon">&#xe640;</i> 移除</a></div>';
                                    }}
                            ]]
                            ,data: (Object.keys(that.formData.judge_tmp_list).length > 0) ? that.formData.judge_tmp_list : []
                            ,id: 'table_3'
                        });
                    table.on('tool(showQuestionsList_3)', function(obj){
                        var data = obj.data;
                        if(obj.event === 'del'){
                            if (that.formData.judge_tmp_list) {
                                for(var i=0;i<that.formData.judge_tmp_list.length;i++){
                                    if(that.formData.judge_tmp_list[i].id==data.id){
                                        that.formData.judge_tmp_list.splice(i,1);
                                    }
                                }
                                that.formData.judge_tmp_list=that.formData.judge_tmp_list;
                                that.show_judge_list();
                            }
                        }
                    });
                    table.on('edit(showQuestionsList_3)', function(obj){
                        var id=obj.data.id,values=Number(obj.value);
                        switch (obj.field) {
                            case 'sort':
                                if (that.formData.judge_tmp_list) {
                                    $.each(that.formData.judge_tmp_list, function(index, value){
                                        if(value.id == id){
                                            that.formData.judge_tmp_list[index].sort = values;
                                        }
                                    })
                                }
                                break;
                        }
                    });
                    //监听素材是否删除
                    form.on('switch(delect)', function(obj){
                        if (that.formData.judge_tmp_list) {
                            for(var i=0;i<that.formData.judge_tmp_list.length;i++){
                                if(that.formData.judge_tmp_list[i].id==obj.value){
                                    that.formData.judge_tmp_list.splice(i,1);
                                }
                            }
                            that.formData.judge_tmp_list=that.formData.judge_tmp_list;
                            that.show_judge_list();
                        }
                    });
                },
                save: function () {
                    var that = this;
                    var grade=that.grade;
                    for(var i=0;i<grade.length;i++){
                        var v=i+1;
                        if(grade[i].grade_name=='' || grade[i].grade_standard=='') return layList.msg('第'+v+'没填完整');
                        var arr=grade[i].grade_standard.split('~');
                        if(arr.length<=1) return layList.msg('第'+v+'分数区间有误');
                        if(arr[0]<0 || arr[1]<0) return layList.msg('第'+v+'分数区间分数不能小于0');
                    }
                    that.$nextTick(function () {
                        if(!that.formData.title) return layList.msg('请输入' + (type === 1 ? '练习' : '考试') + '名称');
                        if(that.formData.tid<=0) return layList.msg('请选择' + (type === 1 ? '练习' : '考试') + '分类');
                        if(that.formData.image=='' && type==2) return layList.msg('请输入' + (type === 1 ? '练习' : '考试') + '封面');
                        if(Number(that.formData.txamination_time)<=0 && type==2) return layList.msg((type === 1 ? '练习' : '考试') + '时长填写不正确');
                        if(Number(that.formData.frequency)<0 && type==2) return layList.msg((type === 1 ? '练习' : '考试') + '次数填写不正确');

                        if (!Number(that.formData.single_number) && !Number(that.formData.many_number) && !Number(that.formData.judge_number)) {
                            return layList.msg('至少有一类题型数量大于0');
                        }
                        that.formData.grade=JSON.stringify(grade);
                        if (that.formData.pay_type == 1 && type==2) {
                            if (Number(that.formData.money)<0 || that.formData.money == 0.00) return layList.msg('购买金额未填或填写不正确');
                        }
                        if (that.formData.member_pay_type == 1 && type==2) {
                            if (Number(that.formData.member_money)<0 || that.formData.member_money == 0.00) return layList.msg('会员购买金额未填或填写不正确');
                        }
                        if(that.formData.is_group==1){
                            if(Object.keys(that.formData.single_tmp_list).length == 0 && that.formData.single_number>0) return layList.msg('请选择单选题');
                            if(Object.keys(that.formData.many_tmp_list).length == 0 && that.formData.many_number>0) return layList.msg('请选择多选题');
                            if(Object.keys(that.formData.judge_tmp_list).length == 0 && that.formData.judge_number>0) return layList.msg('请选择判断题');
                            that.formData.singleIds=JSON.stringify(that.formData.single_tmp_list);
                            that.formData.manyIds=JSON.stringify(that.formData.many_tmp_list);
                            that.formData.judgeIds=JSON.stringify(that.formData.judge_tmp_list);
                        }else{
                            if(that.formData.cate_id<=0) return layList.msg('请选择随机组题的题库分类');
                        }
                        var data={};
                        for (var key in that.formData) {
                            if (key !== 'single_tmp_list' && key !== 'many_tmp_list' && key !== 'judge_tmp_list') {
                                data[key] = that.formData[key]
                            }
                        }
                    layList.loadFFF();
                    layList.basePost(layList.U({
                        a: 'save_add',
                        q: {id: id,type:type}
                    }), data, function (res) {
                        layList.loadClear();
                        if (parseInt(id) == 0) {
                            layList.layer.confirm('添加成功,您要继续添加' + (type === 1 ? '练习' : '考试') + '吗?', {
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
                    })
                },
                clone_form: function () {
                    parent.layer.closeAll();
                },
                del:function (e) {

                },
                // 上一步、下一步
                tabChange: function (value) {
                    layui.element.tabChange('tab', this.tabIndex + value);
                }
            },
            mounted: function () {
                var that=this;
                window.changeIMG = that.changeIMG;
                that.get_subject_list();
                that.get_questions_list();
                that.show_single_list();
                that.show_many_list();
                that.show_judge_list();
                if(grades && grades.length){
                    that.grade=grades;
                }
                this.$nextTick(function () {
                    layList.form.render();
                    layList.select('tid', function (obj) {
                        that.formData.tid = obj.value;
                    });
                    layList.select('cate_id', function (obj) {
                        that.formData.cate_id = obj.value;
                    });
                    layList.form.on('radio(pay_type)', function (data) {
                        that.formData.pay_type = parseInt(data.value);
                        if(that.formData.pay_type!=1){
                            that.formData.money=0.00;
                        }
                    });
                    layList.form.on('radio(member_pay_type)', function (data) {
                        that.formData.member_pay_type = parseInt(data.value);
                        if(that.formData.member_pay_type!=1){
                            that.formData.member_money=0.00;
                        }
                    });
                    layList.form.on('radio(is_score)', function (data) {
                        that.formData.is_score = parseInt(data.value);
                    });
                    layList.form.on('radio(is_group)', function (data) {
                        that.formData.is_group = parseInt(data.value);
                    });
                    layList.form.on('radio(is_show)', function (data) {
                        that.formData.is_show = parseInt(data.value);
                    });
                    layList.form.on('radio(difficulty)', function (data) {
                        that.formData.difficulty = parseInt(data.value);
                    });
                    // 难度
                    layui.rate.render({
                        elem: '#rate',
                        value: that.formData.difficulty,
                        choose: function (value) {
                            that.formData.difficulty = value;
                        }
                    });
                    layui.element.on('tab(tab)', function (data) {
                        that.tabIndex = data.index;
                        layui.table.resize('table_1');
                        layui.table.resize('table_2');
                        layui.table.resize('table_3');
                    });
                });
            }
        })
    })
</script>
{/block}
