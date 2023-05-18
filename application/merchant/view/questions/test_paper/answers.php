{extend name="public/container"}
{block name="head"}
<style>
    .first {
        margin: 30px;
        font-weight: bold;
        font-size: 22px;
        text-align: center;
    }

    .second {
        margin: 30px 0;
        font-weight: bold;
        font-size: 14px;
    }

    .third {
        margin: 30px 0;
        font-weight: bold;
        font-size: 14px;
    }

    .third table {
        width: 100%;
    }

    .third td {
        padding: 5px;
        font-weight: normal;
    }

    .third table,
    .third td {
        border: 1px solid #333;
    }

    .fourth {
        margin: 30px 0;
    }

    .fourth>div:first-child {
        margin: 0 0 15px;
        font-weight: bold;
        font-size: 14px;
    }

    .fourth .userinfo .title {
        margin: 0 0 10px;
    }

    .fourth .userinfo .content>div {
        padding: 5px;
        margin: 0 0 15px;
        background: #efefef;
    }

    .question {
        margin: 0 0 15px;
    }

    .question .content {
        margin: 5px 0;
    }

    .question li {
        margin: 5px 0;
    }

    .question li>div:nth-child(2) {
        margin: 5px 0;
    }

    .result {
        display: inline-block;
        color: #0092db;
    }

    .question li label {
        display: block;
    }

    .question li input {
        margin: 0 5px 0 0;
    }

    .question li input:checked {
        color: #999;
    }

    .btn {
        display: inline-block;
        padding: 5px 10px;
        border: 1px solid #0092DC;
        border-radius: 5px;
        color: #0092DC;
    }

    .layui-icon-radio {
        color: #0092db;
    }
</style>
{/block}
{block name="content"}
<div v-cloak class="layui-fluid" id="app">
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="btn" @click="print">打印</div>
            <div class="first">{{ title }}</div>
            <div class="second">
                <div>答题人</div>
                <div>UID：{{ information.uid }} ｜ 昵称：{{ information.nickname }}</div>
            </div>
            <div class="third" v-if="type==2">
                <div>成绩单</div>
                <div>
                    <table>
                        <tr>
                            <td>得分：{{ score }} ｜ 总分：{{ total_score }}</td>
                            <td>答对题数： {{ yes_questions }} ｜ 总题数：{{ item_number }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="fourth">
                <div>答题解析</div>
                <div v-for="(test, num) in testPaperAnswers" :key="test.type" class="question">
                    <div class="title">
                        {{ num ? (num === 1 ? '二' : '三') : '一' }}、{{ test.name }}（每题{{ test.questions.length && test.questions[0].single_score }}分，共{{ test.questions.length }}题）
                    </div>
                    <div class="content">
                        <ul v-if="test.questions.length">
                            <li v-for="(question, index) in test.questions" :key="index">
                                <div>{{ index + 1 }}.{{ question.stem }}</div>
                                <img v-if="question.image" :src="question.image" width="380" height="266">
                                <div v-if="question.is_img">
                                    <label v-for="(option, key) in question.option" :key="key">
                                        <i :class="!question.user_answer || question.user_answer.indexOf(key) == -1 ? 'layui-icon-circle' : 'layui-icon-radio'" class="layui-icon"></i>
                                        {{ key }}.<img :src="option" width="205" height="144">
                                    </label>
                                </div>
                                <div v-else>
                                    <label v-for="(option, key) in question.option" :key="key">
                                        <i :class="!question.user_answer || question.user_answer.indexOf(key) == -1 ? 'layui-icon-circle' : 'layui-icon-radio'" class="layui-icon"></i>
                                        {{ key }}.{{ option }}
                                    </label>
                                </div>
                                <div class="result">回答{{ question.is_correct === 2 ? '正确' : '错误' }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
{/block}
{block name="script"}
<script>
    require(['vue'], function (Vue) {
        var uid =<?= $uid ?>;
        var record_id =<?= $record_id ?>;
        var test_id =<?= $test_id ?>;
        var type =<?= $type ?>;
        var single_sort =<?= $single_sort ?>;
        var many_sort =<?= $many_sort ?>;
        var judge_sort =<?= $judge_sort ?>;
        var testPaperAnswers = [
            {
                name: '单选题',
                type: 1,
                sort: single_sort,
                questions: []
            },
            {
                name: '多选题',
                type: 2,
                sort: many_sort,
                questions: []
            },
            {
                name: '判断题',
                type: 3,
                sort: judge_sort,
                questions: []
            }
        ].sort(function (a, b) {
            return b.sort - a.sort;
        });
        new Vue({
            el: '#app',
            data: {
                testPaperAnswers: [],
                title: '',
                type: type,
                total_score: 0,
                item_number: 0,
                score: 0,
                yes_questions: 0,
                information: {}
            },
            created: function () {
                this.getUserInformation();
                if (type == 2) {
                    this.getUserAchievement();
                }
                for (var i = 1; i <= 3; i++) {
                    this.getTestPaperAnswers(i);
                }
            },
            methods: {
                // 信息
                getUserInformation: function () {
                    var vm = this;
                    layList.baseGet(layList.U({
                        a: 'getUserInformation',
                        p: {
                            uid: uid
                        }
                    }), function (res) {
                        vm.information = res.data;
                    });
                },
                // 成绩
                getUserAchievement: function () {
                    var vm = this;
                    layList.baseGet(layList.U({
                        a: 'getUserAchievement',
                        p: {
                            uid: uid,
                            test_id: test_id,
                            record_id: record_id
                        }
                    }), function (res) {
                        vm.title = res.data.title;
                        vm.total_score = res.data.total_score;
                        vm.item_number = res.data.item_number;
                        vm.score = res.data.score;
                        vm.yes_questions = res.data.yes_questions;
                    });
                },
                // 试卷中的试题答题情况
                getTestPaperAnswers: function (question_type) {
                    var vm = this;
                    layList.baseGet(layList.U({
                        a: 'getTestPaperAnswers',
                        q: {
                            test_id: test_id,
                            record_id: record_id,
                            question_type: question_type
                        }
                    }), function (res) {
                        var questions = res.data;
                        questions.forEach(function (question) {
                            question.option = JSON.parse(question.option);
                            if (Array.isArray(question.option)) {
                                var options = {};
                                question.option.forEach(function (option, index) {
                                    options[String.fromCharCode(65 + index)] = option;
                                });
                                question.option = options;
                            }
                        });
                        for (var i = 0; i < testPaperAnswers.length; i++) {
                            if (testPaperAnswers[i].type === question_type) {
                                if (questions.length) {
                                    testPaperAnswers[i].questions = questions;
                                } else {
                                    testPaperAnswers.splice(i, 1);
                                }
                                break;
                            }
                        }
                        vm.testPaperAnswers = testPaperAnswers;
                    });
                },
                print: function () {
                    window.print()
                }
            }
        });
    });

</script>
{/block}
