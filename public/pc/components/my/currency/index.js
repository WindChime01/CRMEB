define([
    'api/auth',
    'qrcode',
    'decimal',
    'text!./index.html',
    'css!./index.css'
], function (authApi, QRCode, Decimal, html) {
    return {
        inject: ['getUserInfo'],
        props: {
            activeName: {
                type: String,
                default: 'coin'
            },
            isYue: {
                type: Boolean,
                default: true
            },
            isAlipay: {
                type: Boolean,
                default: true
            },
            isWechat: {
                type: Boolean,
                default: true
            },
            nowMoney: {
                type: String,
                default: '0'
            },
            isLogin: {
                type: Boolean,
                default: false
            }
        },
        data: function () {
            return {
                active1: 'first',
                custom: 1,
                user_gold_num: 0,
                recharge: 0,
                consumption: 0,
                recharge_price_list: [],
                gold_image: '',
                gold_rate: 1,
                active2: '',
                goldList1: [],
                goldList2: [],
                goldList3: [],
                page1: 1,
                page2: 1,
                page3: 1,
                limit: 20,
                total1: 0,
                total2: 0,
                total3: 0,
                filterData: {
                    picked: 0,
                    payType: 'yue',
                },
                isReset: true,
                count: 0
            };
        },
        computed: {
            customIcon: function () {
                return Decimal.mul(this.custom || 0, this.gold_rate);
            }
        },
        watch: {
            isLogin: function (value) {
                if (value) {
                    this.get_gold_coins();
                    this.user_gold_num_list1();
                    this.user_gold_num_list2();
                    this.user_gold_num_list3();
                }
            },
            filterData: {
                handler: function () {
                    this.isReset = true;
                },
                deep: true
            }
        },
        mounted: function () {
            this.$nextTick(function () {
                if (!this.isYue) {
                    if (this.isWechat) {
                        this.filterData.payType = 'weixin';
                    } else {
                        if (this.isAlipay) {
                            this.filterData.payType = 'zhifubao';
                        } else {
                            this.filterData.payType = '';
                        }
                    }
                }

            });
        },
        methods: {
            // 我的金币
            get_gold_coins: function () {
                var vm = this;
                authApi.get_gold_coins().then(function (res) {
                    var data = res.data;
                    vm.user_gold_num = data.user_gold_num;
                    vm.recharge = data.recharge;
                    vm.consumption = data.consumption;
                    vm.recharge_price_list = data.recharge_price_list;
                    vm.gold_image = data.gold_info.gold_image;
                    vm.gold_rate = data.gold_info.gold_rate;
                    vm.$parent.$parent.$parent.tabs.forEach(function (item) {
                        if (item.value == "coin") {
                            item.name = data.gold_name + '充值';
                        }
                    });
                });
            },
            // 支付
            create_order: function () {
                var vm = this;
                if (this.filterData.picked === -1 && !this.custom) {
                    return this.$message.warning('请输入或选择充值数量');
                }
                authApi.create_order({
                    special_id: this.filterData.picked == -1 ? this.custom : Decimal.div(this.recharge_price_list[this.filterData.picked], this.gold_rate),
                    pay_type_num: 30,
                    payType: this.filterData.payType
                }).then(function (res) {
                    switch (res.data.status) {
                        case "PAY_ERROR":
                        case 'ORDER_EXIST':
                        case 'ORDER_ERROR':
                            vm.$message.error(res.msg);
                            break;
                        case 'WECHAT_PAY':
                            vm.isReset = false;
                            if (vm.qrcode) {
                                vm.qrcode.makeCode(res.data.result.jsConfig);
                            } else {
                                vm.$nextTick(function () {
                                    vm.qrcode = new QRCode(vm.$refs.qrcode, res.data.result.jsConfig);
                                });
                            }
                            vm.testing_order_state(res.data.result.orderId);
                            break;
                        case 'ZHIFUBAO_PAY':
                            vm.isReset = false;
                            if (vm.qrcode) {
                                vm.qrcode.makeCode(res.data.result.jsConfig);
                            } else {
                                vm.$nextTick(function () {
                                    vm.qrcode = new QRCode(vm.$refs.qrcode, res.data.result.jsConfig);
                                });
                            }
                            vm.testing_order_state(res.data.result.orderId);
                            break;
                        case 'SUCCESS':
                            vm.$message.success(res.msg);
                            vm.payAfterClick();
                            break;
                    }
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            // 明细
            user_gold_num_list1: function () {
                var vm = this;
                authApi.user_gold_num_list({
                    page: this.page1,
                    limit: this.limit,
                    index: ''
                }).then(function (res) {
                    vm.goldList1 = res.data.list;
                    vm.total1 = res.data.count;
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            user_gold_num_list2: function () {
                var vm = this;
                authApi.user_gold_num_list({
                    page: this.page2,
                    limit: this.limit,
                    index: 1
                }).then(function (res) {
                    vm.goldList2 = res.data.list;
                    vm.total2 = res.data.count;
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            user_gold_num_list3: function () {
                var vm = this;
                authApi.user_gold_num_list({
                    page: this.page3,
                    limit: this.limit,
                    index: 2
                }).then(function (res) {
                    vm.goldList3 = res.data.list;
                    vm.total3 = res.data.count;
                }).catch(function (err) {
                    vm.$message.error(err.msg);
                });
            },
            handleChange: function () {
                this.filterData.picked = -1;
            },
            inputNumberFocus: function () {
                this.filterData.picked = -1;
                this.isReset = true;
            },
            payAfterClick: function () {
                var vm = this;
                vm.custom = 1;
                vm.isReset = true;
                vm.get_gold_coins();
                vm.getUserInfo();
                vm.page1 = 1;
                vm.page2 = 1;
                vm.page3 = 1;
                vm.goldList1 = [];
                vm.goldList2 = [];
                vm.goldList3 = [];
                vm.user_gold_num_list1();
                vm.user_gold_num_list2();
                vm.user_gold_num_list3();
            },
            // 扫码回调
            testing_order_state: function (orderId) {
                var vm = this;
                if (vm.timer) {
                    return;
                }
                this.timer = setInterval(function () {
                    vm.count++;
                    authApi.testing_order_state({
                        order_id: orderId,
                        type: 2
                    }).then(function (res) {
                        if (res.data == 1) {
                            clearInterval(vm.timer);
                            vm.count = 0;
                            vm.timer = null;
                            vm.payAfterClick();
                        }
                    }).catch(function (err) {
                        console.error(err.msg);
                    });
                    if (vm.count == 12) {
                        clearInterval(vm.timer);
                        vm.count = 0;
                        vm.timer = null;
                    }
                }, 5000);
            }
        },
        template: html
    };
});