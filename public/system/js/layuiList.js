(function (global) {
    function LayList() {
        this.tableIns = null;
        this.index = null;
        this.elemOdj = [];
        this.boxids = 'ids';
        this.odj = '';

        $('.layui-input-block').each(function () {
            var name = $(this).data('type');
            if ($(this).data('type') != undefined) {
                var input = $(this).find('input[name="' + name + '"]');
                $(this).children('button').each(function () {
                    $(this).on('click', function () {
                        $(this).removeClass('layui-btn-primary').siblings().addClass('layui-btn-primary');
                        input.val($(this).data('value'));
                    })
                });
            }
        });

        Array.prototype.getIds = function (field) {
            var ids = [];
            this.forEach(function (value, index) {
                if (value[field] !== undefined) {
                    ids.push(value[field]);
                }
            });
            return ids;
        };
    }

    LayList.prototype = Object.create(layui);

    LayList.prototype.inintclass = function ($names) {
        var that = this;
        $names.find('button').each(function () {
            var type = $names.data('type');
            $(this).on('click', function () {
                var value = $(this).data('value');
                $(this).addClass('layui-btn-radius').siblings().removeClass('layui-btn-primary');
                $names.find('input[name="' + type + '"]').val(value);
                var obj = {};
                obj[type] = value;
                that.reload(obj)
                // that.reload({[type]: value})
            })
        });
    };

    LayList.prototype.basePost = function (url, data, successCallback, errorCallback) {
        var that = this;
        $.ajax({
            headers: this.headers(),
            url: url,
            data: data,
            type: 'post',
            dataType: 'json',
            success: function (rem) {
                if (rem.code == 200 || rem.status == 200)
                    successCallback && successCallback(rem);
                else
                    errorCallback && errorCallback(rem);
            },
            error: function (err) {
                errorCallback && errorCallback(err);
                that.msg(err);
            }
        })
    };

    //ajax GET
    LayList.prototype.baseGet = function (url, successCallback, errorCallback) {
        var that = this;
        $.ajax({
            headers: this.headers(),
            url: url,
            type: 'get',
            dataType: 'json',
            success: function (rem) {
                if (rem.code == 200 || rem.status == 200)
                    successCallback && successCallback(rem);
                else
                    errorCallback && errorCallback(rem);
            },
            error: function (err) {
                errorCallback && errorCallback(err);
                that.msg('服务器异常');
            }
        });
    };
    //设置headers头
    LayList.prototype.headers = function () {
        return {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
        };
    };
    //初始化 layui table
    LayList.prototype.tableList = function (odj, url, data, limit, size, boxids, is_tables) {
        var limit = limit || 20, size = size || 'sm', $data = [], that = this, boxids = boxids || this.boxids,
            done = null;
        var eb = '', toob = '';
        if (typeof odj == 'object') {
            eb = odj.o || '';
            toob = odj.t || '';
            done = odj.done || null;
        } else {
            eb = odj;
        }
        if (toob != '') toob = '#' + toob;
        switch (typeof data) {
            case 'object':
                $data = data;
                break;
            case "function":
                data && ($data = data());
                break;
        }
        if (is_tables != true) this.odj = eb;
        if (that.elemOdj[eb] == undefined) that.elemOdj[eb] = eb;
        var elemOdj = that.elemOdj[this.odj];
        that.tableIns = that.table.render({
            id: boxids,
            elem: '#' + elemOdj,
            url: url,
            page: {
                theme: '#0092DC'
            },
            limit: limit,
            toolbar: toob,
            cols: [$data],
            done: done
        });
        return that.tableIns;
    };
    LayList.prototype.loadFFF = function () {
        this.index = this.layer.load(1, {shade: [0.1, '#fff']});
    }
    LayList.prototype.loadClear = function () {
        var that = this;
        setTimeout(function () {
            that.layer.close(that.index);
        }, 250);
    }
    //获得url PHP获取当前模块 和控制器
    LayList.prototype.Url = function (opt) {
        var m = opt.m || window.module, c = opt.c || window.controlle, a = opt.a || 'index', q = opt.q || '',
            p = opt.p || {}, params = '', gets = '';
        params = Object.keys(p).map(function (key) {
            return key + '/' + p[key];
        }).join('/');
        gets = Object.keys(q).map(function (key) {
            return key + '=' + q[key];
        }).join('&');

        return '/' + m + '/' + c + '/' + a + (params == '' ? '' : '/' + params) + (gets == '' ? '' : '?' + gets);
    };
    LayList.prototype.U = function (obj) {
        return this.Url(obj);
    }
    //表单重构 where 搜索条件 join,page 是否返回到第一页,tableIns 多table时 this.tableList 返回的参数
    LayList.prototype.reload = function (where, page, tableIns, initSort) {
        var whereOdJ = {where: where || {}};
        if (initSort) whereOdJ.initSort = initSort;
        if (page == true) whereOdJ.page = {curr: 1};
        if (typeof tableIns == 'Object') {
            tableIns.reload(whereOdJ);
        } else {
            this.tableIns.reload(whereOdJ);
        }
    }
    //获取排序字符串
    LayList.prototype.order = function (type, filde) {
        switch (type) {
            case 'desc':
                return filde + '-desc';
                break;
            case 'asc':
                return filde + '-asc';
                break;
            case null:
                return '';
                break;
        }
    }
    LayList.prototype.toolbar = function (EventFn, name) {
        var elemOdj = name || this.elemOdj[this.odj];
        this.table.on('toolbar(' + elemOdj + ')', function (obj) {
            var data = obj.data, layEvent = obj.event;
            if (typeof EventFn == 'function') {
                EventFn(layEvent, data, obj);
            }
        })
    }
    //监听列表
    LayList.prototype.tool = function (EventFn, fieldStr, odj) {
        var that = this;
        // var elemOdj=elemOdj || that.elemOdj
        var elemOdj = odj || that.elemOdj[this.odj];
        this.table.on('tool(' + elemOdj + ')', function (obj) {
            var data = obj.data, layEvent = obj.event;
            if (typeof EventFn == 'function') {
                EventFn(layEvent, data, obj);
            } else if (EventFn && (typeof fieldStr == 'function')) {
                switch (layEvent) {
                    case EventFn:
                        fieldStr(data);
                        break;
                    default:
                        console.log('暂未监听到事件');
                        break
                }
            }
        });
    }
    //监听排序 EventFn 需要监听的值 || 函数,page 是否回到第1页,tableIns 多table时 this.tableList 返回的参数
    LayList.prototype.sort = function (EventFn, page, tableIns, odj) {
        var that = this;
        // var elemOdj=elemOdj || that.elemOdj;
        var elemOdj = that.elemOdj[odj || this.odj];
        this.table.on('sort(' + elemOdj + ')', function (obj) {
            var layEvent = obj.field;
            var type = obj.type;
            if (typeof EventFn == 'function') {
                EventFn(obj);
            } else if (typeof EventFn == 'object') {
                for (value in EventFn) {
                    switch (layEvent) {
                        case EventFn[value]:
                            if (page == true)
                                that.reload({order: that.order(type, EventFn[value])}, true, tableIns, obj);
                            else
                                that.reload({order: that.order(type, EventFn[value])}, null, tableIns, obj);
                            continue;
                    }
                }
            } else if (EventFn) {
                switch (layEvent) {
                    case EventFn:
                        if (page == true)
                            that.reload({order: that.order(type, EventFn)}, true, tableIns, obj);
                        else
                            that.reload({order: that.order(type, EventFn)}, null, tableIns, obj);
                        break;
                    default:
                        console.log('暂未监听到事件');
                        break
                }
            }
        });
    }
    LayList.prototype.msg = function (msg, successFn) {
        var msg = msg || '未知错误';
        try {
            return this.layer.msg(msg, successFn);
        } catch (e) {
            console.log(e);
        }
    }
    //时间选择器
    LayList.prototype.date = function (IdName) {
        if (typeof IdName == 'string' && $('#' + IdName).length == 0) return console.info('并没有找到此元素');
        var json = typeof IdName == 'object' ? IdName : {elem: '#' + IdName, range: true};
        this.laydate.render(json);
    }
    //监听复选框
    LayList.prototype.switch = function (switchname, successFn) {
        this.form.on('switch(' + switchname + ')', function (obj) {
            successFn && successFn(obj, this.value, this.name);
        });
    }
    //监听select
    LayList.prototype.select = function (switchname, successFn) {
        this.form.on('select(' + switchname + ')', function (obj) {
            successFn && successFn(obj, this.value, this.name);
        });
    }
    //获取复选框选中的数组
    LayList.prototype.getCheckData = function (boxids) {
        var boxids = boxids || this.boxids;
        return this.table.checkStatus(boxids).data;
    }
    //搜索
    LayList.prototype.search = function (btnname, successFn) {
        var name = typeof btnname == 'string' ? btnname : '';
        var that = this;
        if (name == '') return false;
        this.form.on('submit(' + btnname + ')', function (data) {
            if (typeof successFn == "function") {
                successFn(data.field);
            } else {
                that.reload(data.field);
            }
            return false;
        })
    }
    LayList.prototype.codeType = function (name, type) {
        switch (name) {
            // case :
        }
    }
    LayList.prototype.edit = function (name, successFn, odj) {
        var that = this;
        var elemOdj = that.elemOdj[odj || this.odj];
        this.table.on('edit(' + elemOdj + ')', function (obj) {
            var value = obj.value //得到修改后的值
                , data = obj.data //得到所在行所有键值
                , field = obj.field; //得到字段
            if (typeof name == "function") {
                name && name(obj);
            } else {
                switch (field) {
                    case name:
                        successFn && successFn(obj);
                        break;
                    default:
                        console.log('未检测到指定字段' + name);
                        break;
                }
            }
        });
    }
    //页面有多个table请用此函数包裹起来
    LayList.prototype.tables = function (odj, data, value, successFn) {
        var url = data.url || '', limit = data.limit || 20, size = data.size || 'lg', that = this;
        this.tableList(odj, url, value, limit, size);
    }
    LayList.prototype.createModalFrame = function (title, src, opt) {
        opt === undefined && (opt = {});
        var h = 0;
        if (window.innerHeight < 800 && window.innerHeight >= 700) {
            h = window.innerHeight - 50;
        } else if (window.innerHeight < 900 && window.innerHeight >= 800) {
            h = window.innerHeight - 100;
        } else if (window.innerHeight < 1000 && window.innerHeight >= 900) {
            h = window.innerHeight - 150;
        } else if (window.innerHeight >= 1000) {
            h = window.innerHeight - 200;
        } else {
            h = window.innerHeight;
        }
        var area = [(opt.w || window.innerWidth / 2.4) + 'px', (opt.h || h) + 'px'];
        return this.layer.open({
            type: 2,
            title: title,
            area: area,
            fixed: false, //不固定
            maxmin: true,
            moveOut: false,//true  可以拖出窗外  false 只能在窗内拖
            anim: 5,//出场动画 isOutAnim bool 关闭动画
            offset: 'auto',//['100px','100px'],//'auto',//初始位置  ['100px','100px'] t[ 上 左]
            shade: 0,//遮罩
            resize: true,//是否允许拉伸
            content: src,//内容
            move: '.layui-layer-title',// 默认".layui-layer-title",// 触发拖动的元素
            moveEnd: function () {//拖动之后回调
                console.log(this);
            }
        });
    };

    LayList.prototype.constructor = LayList;

    global.layList = new LayList();
})(this)