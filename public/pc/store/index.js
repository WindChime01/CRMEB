define([
    'require',
], function(require) {
    return {
        debug: true,
        state: {
            loginVisible: false,
            agreeVisible: false,
            accountVisible: false,
            isAccount: true,
        },
        setLoginAction: function (newValue) {
            this.state.loginVisible = newValue;
        },
        setAgreeAction: function (newValue) {
            this.state.agreeVisible = newValue;
        },
        setAccountAction: function (newValue) {
            this.state.accountVisible = newValue;
        },
        setIsAccountAction: function (newValue) {
            this.state.isAccount = newValue;
        }
    };
});