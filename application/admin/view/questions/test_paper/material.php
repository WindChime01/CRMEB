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

    .layui-form-radioed.layui-radio-disbaled>i {
        color: #0092DC !important;
    }

    .layui-disabled,
    .layui-disabled:hover {
        color: #333 !important;
        cursor: auto !important;
    }

    .layui-form-radioed.layui-disabled,
    .layui-form-radioed.layui-disabled:hover {
        color: #0092DC !important;
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
            <div class="layui-form">
                <div class="layui-tab layui-tab-brief" lay-filter="tab">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="0">基本设置</li>
                        <li lay-id="1">题库选择</li>
                        <li v-show="type==2" lay-id="2">价格设置</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item">
                                <label class="layui-form-label">{$type==1 ? '练习名称' : '考试名称'}：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" v-model.trim="formData.title" disabled class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">{$type==1 ? '练习分类' : '考试分类'}：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="tid" :value="formData.tid" disabled class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="type==2">
                                <label class="layui-form-label">考试封面：（710*400）</label>
                                <div class="layui-input-block">
                                    <div class="upload-image-box" v-if="formData.image">
                                        <img :src="formData.image" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">单选题数量：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="single_number" v-model.trim="formData.single_number" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" >
                                    <label class="layui-form-label">题型排序/倒序：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="single_sort" v-model.trim="formData.single_sort" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">每题分数：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="single_score" v-model.trim="formData.single_score" disabled class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">多选题数量：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="many_number" v-model.trim="formData.many_number" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" >
                                    <label class="layui-form-label">题型排序/倒序：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="many_sort" v-model.trim="formData.many_sort" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">每题分数：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="many_score" v-model.trim="formData.many_score" disabled class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">判断题数量：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="judge_number" v-model.trim="formData.judge_number" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" >
                                    <label class="layui-form-label">题型排序/倒序：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="judge_sort" v-model.trim="formData.judge_sort" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">每题分数：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="judge_score" v-model.trim="formData.judge_score" disabled class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">试题总数：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="item_number" :value="sum" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">总分数：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="total_score" :value="total" disabled class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">时长/分：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="txamination_time" :value="formData.txamination_time" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline" v-show="type==2">
                                    <label class="layui-form-label">考试次数：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="frequency" :value="formData.frequency" disabled class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-inline">
                                    <label class="layui-form-label">虚拟答题人数：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="fake_sales" :value="formData.fake_sales" disabled class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item submit" v-show="type==2">
                                <label class="layui-form-label">分数显示：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_score" lay-filter="is_score" v-model="formData.is_score" value="1" title="显示" disabled>
                                    <input type="radio" name="is_score" lay-filter="is_score" v-model="formData.is_score" value="0" title="隐藏" disabled>
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
                                            <input type="text" name="fake_sales" :value="item.grade_name" disabled class="layui-input">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <input type="text" name="fake_sales" :value="item.grade_standard" disabled class="layui-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item submit">
                                <label class="layui-form-label">组题方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_group" lay-filter="is_group" v-model="formData.is_group" value="1" title="手动组题" disabled>
                                    <input type="radio" name="is_group" lay-filter="is_group" v-model="formData.is_group" value="2" title="随机组题" disabled>
                                </div>
                            </div>
                            <div class="layui-form-item submit">
                                <label class="layui-form-label">{$type==1 ? '练习状态' : '考试状态'}：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_show" lay-filter="is_show" v-model="formData.is_show" value="1" title="显示" disabled>
                                    <input type="radio" name="is_show" lay-filter="is_show" v-model="formData.is_show" value="0" title="隐藏" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <table class="layui-hide" id="List" lay-filter="List"></table>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">付费方式：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="pay_type" lay-filter="pay_type" v-model="formData.pay_type" value="1" title="付费" disabled>
                                    <input type="radio" name="pay_type" lay-filter="pay_type" v-model="formData.pay_type" value="0" title="免费" disabled>
                                </div>
                            </div>
                            <div class="layui-form-item" v-show="formData.pay_type == 1">
                                <label class="layui-form-label">购买金额：</label>
                                <div class="layui-input-inline">
                                    <input type="text" name="money" :value="formData.money" disabled class="layui-input">
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label" style="padding: 9px 0;">会员付费方式：</label>
                                    <div class="layui-input-block">
                                        <input type="radio" name="member_pay_type" lay-filter="member_pay_type" v-model="formData.member_pay_type" value="1" title="付费" disabled>
                                        <input type="radio" name="member_pay_type" lay-filter="member_pay_type" v-model="formData.member_pay_type" value="0" title="免费" disabled>
                                    </div>
                                </div>
                                <div class="layui-form-item" v-show="formData.member_pay_type == 1">
                                    <label class="layui-form-label" style="padding: 9px 0;">会员购买金额：</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="member_money" :value="formData.member_money" disabled class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form class="layui-form" lay-filter="form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">审核状态：</label>
                    <div class="layui-input-block">
                        <input type="radio" name="status" value="1" title="通过" lay-filter="status">
                        <input type="radio" name="status" value="-1" title="拒绝" lay-filter="status">
                    </div>
                </div>
                <div v-if="status === -1" class="layui-form-item">
                    <label class="layui-form-label">拒绝原因：</label>
                    <div class="layui-input-block">
                        <textarea name="fail_message" required lay-verify="required" placeholder="请输入拒绝原因" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-normal" lay-submit lay-filter="*">提交</button>
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
    var type={$type},details=<?=isset($details) ? $details : []?>,grades=<?=isset($grade) ? $grade : []?>,
        single_tmp_list =<?= isset($single_tmp_list) ? $single_tmp_list : "{}"?>,
        many_tmp_list =<?= isset($many_tmp_list) ? $many_tmp_list : "{}"?>,
        judge_tmp_list =<?= isset($judge_tmp_list) ? $judge_tmp_list : "{}"?>;
        console.log(details);
    require(['vue','helper','zh-cn','request','plupload','aliyun-oss','OssUpload'], function (Vue,$h) {
        var form = layui.form,
        parentLayer = parent.layui.layer;
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
                    title:details.title || '',
                    image:details.image || '',
                    tid: '',
                    is_show: details.is_show || 1,
                    item_number:Number(details.item_number) || 0,
                    total_score:Number(details.total_score) || 0,
                    single_number:Number(details.single_number) || 0,
                    single_score:Number(details.single_score) || 0,
                    many_number:Number(details.many_number) || 0,
                    many_score:Number(details.many_score) || 0,
                    judge_number:Number(details.judge_number) || 0,
                    judge_score:Number(details.judge_score) || 0,
                    single_sort:Number(details.single_sort) || 0,
                    many_sort:Number(details.many_sort) || 0,
                    judge_sort:Number(details.judge_sort) || 0,
                    fake_sales:details.fake_sales || 0,
                    difficulty:details.difficulty || 1,
                    pay_type:details.pay_type || 0,
                    money:details.money || 0,
                    member_pay_type:details.member_pay_type || 0,
                    member_money:details.member_money || 0,
                    txamination_time:Number(details.txamination_time) || 0,
                    single_tmp_list:single_tmp_list ? single_tmp_list : {},
                    many_tmp_list:many_tmp_list ? many_tmp_list : {},
                    judge_tmp_list:judge_tmp_list ? judge_tmp_list : {},
                    is_score:details.is_score || 0,
                    frequency:details.frequency || 0,
                    sort:Number(details.sort) || 0,
                    is_group:details.is_group || 1,
                    cate_id:'',
                },
                type:type,
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
                status: 1
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
                        var data = res.data;
                        for (var i = data.length; i-- ;) {
                            if (data[i].id == details.tid ) {
                                that.formData.tid = data[i].title;
                                break;
                            }
                        }
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
                            {field: 'stem', title: '题干',align: 'center',width:200},
                            {field: 'sort', title: '排序/倒序',edit:'sort',align: 'center',width:100},
                            {field: 'right', title: '操作',align: 'center',width:100,templet:function(d){
                                    return '<div><a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon">&#xe640;</i> 移除</a></div>';
                                }}
                        ]]
                        ,data: (Object.keys(that.formData.single_tmp_list).length > 0) ? that.formData.single_tmp_list : []
                        ,page: {
                            theme: '#0092DC'
                        }
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
                            {field: 'stem', title: '题干',align: 'center',width:200},
                            {field: 'sort', title: '排序/倒序',edit:'sort',align: 'center',width:100},
                            {field: 'right', title: '操作',align: 'center',width:100,templet:function(d){
                                    return '<div><a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon">&#xe640;</i> 移除</a></div>';
                                }}
                        ]]
                        ,data: (Object.keys(that.formData.many_tmp_list).length > 0) ? that.formData.many_tmp_list : []
                        ,page: {
                            theme: '#0092DC'
                        }
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
                                {field: 'stem', title: '题干',align: 'center',width:200},
                                {field: 'sort', title: '排序/倒序',edit:'sort',align: 'center',width:100},
                                {field: 'right', title: '操作',align: 'center',width:100,templet:function(d){
                                        return '<div><a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon">&#xe640;</i> 移除</a></div>';
                                    }}
                            ]]
                            ,data: (Object.keys(that.formData.judge_tmp_list).length > 0) ? that.formData.judge_tmp_list : []
                            ,page: {
                                theme: '#0092DC'
                            }
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
                del:function (e) {

                },
                // 审核通过
                success: function () {
                    layList.baseGet(layList.U({
                        a: 'succ',
                        p: {
                            id: details.id
                        }
                    }), function (res) {
                        layer.msg(res.msg, {
                            icon: 1,
                            time: 2000
                        }, function () {
                            parentLayer.close(parentLayer.getFrameIndex(window.name));
                        });
                    });
                },
                // 审核拒绝
                fail: function (message) {
                    layList.basePost(layList.U({
                        a: 'fail',
                        p: {
                            id: details.id
                        }
                    }), {
                        message: message
                    }, function (res) {
                        layer.msg(res.msg, {
                            icon: 1,
                            time: 2000
                        }, function () {
                            parentLayer.close(parentLayer.getFrameIndex(window.name));
                        });
                    });
                },
            },
            mounted: function () {
                var that=this;
                window.changeIMG = that.changeIMG;
                that.get_subject_list();
                that.show_single_list();
                that.show_many_list();
                that.show_judge_list();
                if(grades && grades.length){
                    that.grade=grades;
                }
                this.$nextTick(function () {
                    layList.tableList({o:'List', done:function () {}},"{:Url('getTestPaperList')}?id=" + details.id + '&type=' + type,function (){
                        return [
                            {field: 'types', title: '题型',align: 'center',width:100},
                            {field: 'title', title: '题干'},
                            {field: 'score', title: '分数',align: 'center',width:100},
                            {field: 'sort', title: '排序',align: 'center',width:100},
                        ];
                    });
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
                        readonly: true,
                        choose: function (value) {
                            that.formData.difficulty = value;
                        }
                    });
                    form.val('form', {
                        status: this.status
                    });
                    form.on('radio(status)', function (data) {
                        that.status = Number(data.value);
                    });
                    form.on('submit(*)', function (data) {
                        that.status === 1 ? that.success() : that.fail(data.field.fail_message);
                        return false;
                    });
                });
            }
        })
    })
</script>
{/block}
