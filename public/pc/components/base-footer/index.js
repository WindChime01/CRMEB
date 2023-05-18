define([
    'text!./index.html',
    'css!./index.css'
], function (html) {
    return {
        inject: ['hasCopyright', 'nncnL_crmeb_copyright'],
        props: {
            publicData: {
                type: Object,
                default: function () {
                    return {};
                }
            }
        },
        data: function () {
            return {
                year: new Date().getFullYear(),
                code_url: '',
                sidebarHeight: 0,
                serviceActive: false,
                is_official_account_switch : is_official_account_switch
            };
        },
        mounted: function () {
            this.$nextTick(function () {
                if (code_url) {
                    this.code_url = code_url;
                }
            });
        },
        updated: function () {
            this.$nextTick(function () {
                this.sidebarHeight = this.$refs.sidebar.clientHeight;
                this.$emit('action', this.$refs.sidebar.clientHeight);
            });
        },
        methods: {
            goPage: function () {
                var vm = this;
                if (this.publicData.sitePhone) {
                    this.$alert('客服电话：' + this.publicData.sitePhone.site_service_phone, {});
                } else {
                    if (this.publicData.customer_service === '2') {
                        this.serviceActive = true;
                        window.addEventListener('message', function () {
                            vm.serviceActive = false;
                        });
                    }
                }
            }
        },
        template: html
    };
});
