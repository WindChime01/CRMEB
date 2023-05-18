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
                page: 1,
                limit: 16,
                is_master: '',
                total: 0,
                questionList: [],
                finished: false,
                question: {},
                idList: [],
                visible: false,
                tabName: '0'
            };
        },
        computed: {
            index: function () {
                if (!this.question.id) {
                    return -1;
                }
                return this.idList.indexOf(this.question.id);
            }
        },
        watch: {
            activeName: function (value) {
                if (value === 'wrong') {
                    this.getQuestionList();
                }
            },
            is_master: function () {
                this.page = 1;
                this.total = 0;
                this.finished = false;
                this.getQuestionList();
            },
            tabName: function (value) {
                switch (value) {
                    case '0':
                        this.is_master = '';
                        break;
                    case '1':
                        this.is_master = 0;
                        break;
                    case '2':
                        this.is_master = 1;
                        break;
                }
            },
        },
        methods: {
            getQuestionList: function () {
                var vm = this;
                topicApi.userWrongBank({
                    page: this.page,
                    limit: this.limit,
                    is_master: this.is_master
                }).then(function (res) {
                    var questionList = res.data.data;
                    questionList.forEach(function (question) {
                        switch (question.question_type) {
                            case 1:
                                question.questionType = '单选题';
                                break;
                            case 2:
                                question.questionType = '多选题';
                                break;
                            case 3:
                                question.questionType = '判断题';
                                break;
                        }
                    });
                    vm.questionList = questionList;
                    vm.total = res.data.count;
                    vm.finished = vm.limit > vm.questionList.length;
                });
            },
            handleCurrentChange: function () {
                this.getQuestionList();
            },
            lookQuestion: function (id) {
                var vm = this;
                this.nodeId = id;
                vm.getQuestion(id);
                vm.getIdList(1);
                vm.getIdList();
            },
            getQuestion: function (id) {
                var vm = this;
                topicApi.oneWrongBank({
                    id: id
                }).then(function (res) {
                    var question = res.data;
                    var options = {};
                    question.option = JSON.parse(question.option);
                    if (Array.isArray(question.option)) {
                        question.option.forEach(function (option, index) {
                            options[String.fromCharCode(index + 65)] = option;
                        });
                        question.option = options;
                    }
                    switch (question.question_type) {
                        case 1:
                            question.questionType = '单选题';
                            break;
                        case 2:
                            question.questionType = '多选题';
                            break;
                        case 1:
                            question.questionType = '判断题';
                            break;
                    }
                    vm.question = question;
                    vm.visible = true;
                });
            },
            getIdList: function (order) {
                var vm = this;
                topicApi.userWrongBankIdArr({
                    id: this.nodeId,
                    is_master: this.is_master,
                    order: order || 0
                }).then(function (res) {
                    var idList = vm.idList.concat(res.data);
                    idList.sort(function (a, b) {
                        return b - a;
                    });
                    for (var i = idList.length; i--;) {
                        if (idList[i] === idList[i - 1]) {
                            idList.splice(i - 1, 1);
                        }
                    }
                    vm.idList = idList;
                });
            },
            masterQuestion: function (question) {
                var vm = this;
                topicApi.submitWrongBank({
                    wrong_id: question.id,
                    questions_id: question.questions_id,
                    is_master: question.is_master ? 0 : 1
                }).then(function (res) {
                    question.is_master = question.is_master ? 0 : 1;
                    if (!vm.visible && vm.tabName !== '0') {
                        if (vm.questionList.length === 1) {
                            if (vm.page === 1) {
                                vm.getQuestionList();
                            } else {
                                vm.page = vm.page - 1;
                                vm.getQuestionList();
                            }
                        } else {
                            vm.getQuestionList();
                        }
                    }
                });
            },
            // 删除错题
            deleteQuestion: function () {
                var vm = this;
                topicApi.delWrongBank({
                    id: this.question.id
                }).then(function (res) {
                    vm.$message.success('删除成功');
                    var index = vm.idList.indexOf(vm.question.id);
                    vm.idList.splice(index, 1);
                    if (vm.idList[index] === undefined) {
                        if (vm.idList[index - 1] === undefined) {
                            vm.visible = false;
                        } else {
                            vm.getQuestion(vm.idList[index - 1]);
                        }
                    } else {
                        vm.getQuestion(vm.idList[index]);
                    }
                });
            },
            // 上一题、下一题
            changeQuestion: function (value) {
                var index = this.index + value;
                var id = this.idList[index];
                this.getQuestion(id);
                if (!index || (index === this.idList.length - 1)) {
                    this.nodeId = id;
                    this.getIdList(Number(value === 1));
                }
            },
            dialogClose: function () {
                this.getQuestionList();
            }
        },
        template: html
    };
});