define([
    'require',
    'api/topic',
    'text!./index.html',
    'css!./index.css'
], function (require, topicApi, html) {
    return {
        props: {
            activeName: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                type: '1',
                page: 1,
                limit: 16,
                total: 0,
                paperList: [],
                finished: false
            };
        },
        watch: {
            activeName: {
                handler: function (value) {
                    if (value === 'question') {
                        this.getPaper();
                    }
                },
                immediate: true
            },
            type: function () {
                this.paperList = [];
                this.page = 1;
                this.total = 0;
                this.finished = false;
                this.getPaper();
            }
        },
        methods: {
            getPaper: function () {
                var vm = this;
                topicApi.myTestPaper({
                    page: this.page,
                    limit: this.limit,
                    type: this.type
                }).then(function (res) {
                    vm.total = res.data.total;
                    vm.paperList = res.data.data;
                    vm.finished = vm.limit > vm.paperList.length;
                });
            },
            handleCurrentChange: function () {
                this.getPaper();
            },
            answer: function (id) {
                if (this.type == 1) {
                    window.location.assign(this.$router.problem_index + '?id=' + id);
                } else {
                    window.location.assign(this.$router.question_index + '?id=' + id);
                }
            },
            goProblemResult: function (problem) {
                window.location.assign(this.$router.problem_result + '?test_id=' + problem.test_id);
            },
            goQuestionResult: function (question) {
                window.location.assign(this.$router.question_result + '?test_id=' + question.test_id);
            }
        },
        template: html
    };
});