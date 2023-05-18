define([
    'require',
    'axios',
    'decimal',
    'text!./index.html',
    'css!./index.css'
], function (require, axios, Decimal, html) {
    return {
        props: {
            open: {
                type: Boolean,
                default: false
            },
            money: {
                type: Number,
                default: 0
            },
            now_money: {
                type: Number,
                default: 0
            },
            special_id: {
                type: Number,
                default: 0
            },
            pay_type_num: {
                type: Number,
                default: -1
            },
            pinkId: {
                type: Number,
                default: 0
            },
            link_pay_uid: {
                type: Number,
                default: 0
            },
            isWechat: {
                type: Boolean,
                default: false
            },
            isAlipay: {
                type: Boolean,
                default: false
            },
            isBalance: {
                type: Boolean,
                default: false
            },
            signs: {
                type: Object,
                default: function () {
                    return {};
                }
            },
            templateId: {
                type: String,
                default: ''
            },
            wxpayH5: {
                type: Boolean,
                default: true
            },
            priceId: {
                type: Number,
                default: 0
            },
            useGold: {
                type: Boolean,
                default: false
            },
            isMember: [Boolean, Number],
            memberMoney: {
                type: Number,
                default: 0
            },
            memberLink: {
                type: String,
                default: ''
            }
        },
        data: function () {
            return {
                payOptions: [
                    {
                        id: 1,
                        name: '微信支付',
                        icon: require.toUrl('./svg/wxpay.svg'),
                        value: 'weixin',
                        canuse: this.isWechat || this.wxpayH5
                    },
                    {
                        id: 2,
                        name: '支付宝支付',
                        icon: require.toUrl('./svg/alipay.svg'),
                        value: 'zhifubao',
                        canuse: this.isAlipay
                    },
                    {
                        id: 3,
                        name: '余额支付',
                        icon: require.toUrl('./svg/yue.svg'),
                        value: 'yue',
                        canuse: this.isBalance
                    }
                ],
                payChecked: '',
                WeixinOpenTagsError: false,  // 无法使用微信开放标签
                showReduce: true
            };
        },
        computed: {
            canReduce: function () {
                return Decimal.sub(this.money, this.memberMoney).toNumber();
            }
        },
        created: function () {
            var find = this.payOptions.find(function (option) {
                return option.canuse;
            });
            if (find) {
                this.payChecked = find.value;
            }
            if (this.isWechat) {
                // 无法使用微信开放标签触发WeixinOpenTagsError事件
                document.addEventListener('WeixinOpenTagsError', function () {
                    this.WeixinOpenTagsError = true;
                }.bind(this));
            }
            var arr = ['order_store_list', 'order'];
            var pathname = window.location.pathname;
            pathname = pathname.split('.');
            pathname = pathname[0].split('/');
            for (var i = 0; i < arr.length; i++) {
              for (var j = 0; j < pathname.length; j++) {
                if (pathname[j] == arr[i]) {
                  this.showReduce = false;
                  break;
                }
              }
            }
        },
        methods: {
            // 支付
            onPay: function () {
                var index = layer.load(1),
                    backUrlCRshlcICwGdGY = {
                        special_id: this.special_id,
                        pay_type_num: this.pay_type_num,
                        pinkId: this.pinkId,
                        link_pay_uid: this.link_pay_uid,
                        payType: this.payChecked,
                        from: this.isWechat ? 'weixin' : 'weixinh5'
                    };

                Object.assign(backUrlCRshlcICwGdGY, this.signs);

                // 报名信息转JSON字符串
                if (this.pay_type_num === 20) {
                    backUrlCRshlcICwGdGY.price_id = this.priceId;
                    backUrlCRshlcICwGdGY.event.forEach(function (item) {
                        if (item.event_type === 3) {
                            item.event_value = item.event_value.join();
                        }
                    });
                }

                if (this.pay_type_num === 40) {
                    backUrlCRshlcICwGdGY.useGold = this.useGold;
                }

                // 创建订单
                axios.post('/wap/special/create_order', backUrlCRshlcICwGdGY).then(function (res) {
                    if (res.data.code === 200) {
                        this.$emit('change', {
                            action: 'pay_order',
                            value: res.data
                        });
                    } else {
                        layer.msg(res.data.msg, {
                            anim: 0
                        }, function () {
                            this.$emit('update:open', false);
                        }.bind(this));
                    }
                }.bind(this)).catch(function (error) {
                    console.error(error);
                }.bind(this)).then(function () {
                    layer.close(index);
                });
            },
            // 订阅按钮操作失败事件
            subscribeError: function (event) {
                this.onPay();
            }
        },
        template: html
    };
});