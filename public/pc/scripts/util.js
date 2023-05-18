define({
    getParmas: function (params) {
        var reg = new RegExp('(^|&)' + params + '=([^&]*)(&|$)');
        var obj = window.location.search.substr(1).match(reg);
        if (obj) {
            return decodeURI(obj[2]);
        }
        return null;
    }
});