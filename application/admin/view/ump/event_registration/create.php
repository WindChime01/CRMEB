{extend name="public/container"}
{block name="head_top"}
<link href="{__ADMIN_PATH}module/wechat/news/css/style.css" type="text/css" rel="stylesheet">
<link href="{__FRAME_PATH}css/plugins/chosen/chosen.css" rel="stylesheet">
<script src="{__ADMIN_PATH}frame/js/ajaxfileupload.js"></script>
<script src="{__ADMIN_PATH}plug/validate/jquery.validate.js"></script>
<script src="{__FRAME_PATH}js/plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/third-party/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="{__ADMIN_PATH}plug/ueditor/ueditor.all.min.js"></script>
<style>
.layui-form-item.event .layui-input-inline {
    width: 130px;
}
.layui-form-item.event .layui-inline:nth-child(4) .layui-input-inline {
    width: 500px;
}
.layui-form-item.event .layui-inline:nth-child(5) .layui-input-inline {
    width: 60px;
}
.layui-form-item.event .layui-form-label {
    width: 50px;
}
.layui-form-item.price .layui-input-inline {
    width: 130px;
}
.layui-form-item.price .layui-input-block .layui-form-label {
    width: 50px;
}
.layui-form-item.price .layui-inline:nth-child(4) .layui-form-label {
    width: 80px;
}
</style>
{/block}
{block name="content"}
<div v-cloak id="app" class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="">
                <div class="layui-tab layui-tab-brief" lay-filter="tab">
                    <ul class="layui-tab-title">
                        <li class="layui-this" lay-id="0">基本设置</li>
                        <li lay-id="1">规则设置</li>
                        <li lay-id="2">详情设置</li>
                        <li lay-id="3">资料设置</li>
                        <li lay-id="4">价格设置</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item required">
                                <label class="layui-form-label">活动标题：</label>
                                <div class="layui-input-block">
                                    <input type="text" required lay-verify="required" placeholder="请输入活动标题" maxlength="50" autocomplete="off" class="layui-input" v-model.trim="formData.title">
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <div class="layui-inline">
                                    <label class="layui-form-label">报名时间：</label>
                                    <div class="layui-input-inline" style="width: 380px;">
                                        <input type="text" class="layui-input" autocomplete="off" id="date1" placeholder=" - ">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <div class="layui-inline">
                                    <label class="layui-form-label">活动时间：</label>
                                    <div class="layui-input-inline" style="width: 380px;">
                                        <input type="text" class="layui-input" autocomplete="off" id="date2" placeholder=" - ">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">活动人数：</label>
                                <div class="layui-input-inline">
                                    <input type="number" required lay-verify="required" min="1" placeholder="活动人数必须大于1" autocomplete="off" class="layui-input" v-model.number="formData.number">
                                </div>
                                <div class="layui-form-mid layui-word-aux">活动人数必须大于1</div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">活动封面：（710*400）</label>
                                <div class="layui-input-inline">
                                    <div v-show="formData.image" class="upload-image-box" @mousemove="mask.image = true" @mouseleave="mask.image = false">
                                        <img :src="formData.image">
                                        <div v-show="mask.image" class="mask" style="display: block;">
                                            <p>
                                                <i class="fa fa-eye" @click="look(formData.image)"></i>
                                                <i class="fa fa-trash-o" @click="delect('image')"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div v-show="!formData.image" class="upload-image" @click="upload('image')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">二维码：（200*200）</label>
                                <div class="layui-input-inline" style="width: auto;">
                                    <div v-show="formData.qrcode_img" class="upload-image-box" @mousemove="mask.qrcode_img = true" @mouseleave="mask.qrcode_img = false">
                                        <img :src="formData.qrcode_img">
                                        <div v-show="mask.qrcode_img" class="mask" style="display: block;">
                                            <p>
                                                <i class="fa fa-eye" @click="look(formData.qrcode_img)"></i>
                                                <i class="fa fa-trash-o" @click="delect('qrcode_img')"></i>
                                            </p>
                                        </div>
                                    </div>
                                    <div v-show="!formData.qrcode_img" class="upload-image" @click="upload('qrcode_img')">
                                        <div class="fiexd"><i class="fa fa-plus"></i></div>
                                        <p>选择图片</p>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item" id="area-picker">
                                <label class="layui-form-label">活动地址：</label>
                                <div class="layui-input-inline" style="width: 380px;">
                                    <select name="province" class="province-selector" data-value="" lay-filter="province-1" v-model="formData.province">
                                        <option value="">请选择省</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline" style="width: 380px;">
                                    <select name="city" class="city-selector" data-value="" lay-filter="city-1" v-model="formData.city">
                                        <option value="">请选择市</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline" style="width: 380px;">
                                    <select name="county" class="county-selector" data-value="" lay-filter="county-1" v-model="formData.district">
                                        <option value="">请选择区</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item required">
                                <label class="layui-form-label">详细地址：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="detail" required lay-verify="required" placeholder="请输入详细地址" maxlength="50" autocomplete="off" class="layui-input" v-model="formData.detail">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">排序：</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="sort" required lay-verify="required" min="0" autocomplete="off" class="layui-input" v-model.number="formData.sort">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">限购：</label>
                                <div class="layui-input-inline">
                                    <input type="number" name="restrictions" required lay-verify="required" min="0" autocomplete="off" class="layui-input" v-model.number="formData.restrictions">
                                </div>
                                <div class="layui-form-mid layui-word-aux">设置每人可以购买的次数，0默认不限购</div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">活动状态：</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" lay-skin="switch" lay-text="开启|关闭" lay-filter="is_show" :checked="formData.is_show">
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item required">
                                <label class="layui-form-label">活动规则：</label>
                                <div class="layui-input-block">
                                    <script id="ueditor1" name="content1" type="text/plain">{{ formData.activity_rules }}</script>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item required">
                                <label class="layui-form-label">活动详情：</label>
                                <div class="layui-input-block">
                                    <script id="ueditor2" name="content2" type="text/plain">{{ formData.content }}</script>
                                </div>
                            </div>
                        </div>
                        <div class="layui-tab-item">
                            <div class="layui-form-item">
                                <label class="layui-form-label">填写资料：</label>
                                <div class="layui-input-block">
                                    <input type="checkbox" lay-skin="switch" lay-text="开启|关闭" lay-filter="is_fill" :checked="formData.is_fill">
                                </div>
                            </div>
                            <template v-if="formData.is_fill">
                                <div v-for="(item, index) in event" :key="index" class="layui-form-item event">
                                    <div class="layui-input-block">
                                        <div class="layui-inline">
                                            <div class="layui-form-label">排序</div>
                                            <div class="layui-input-inline">
                                                <input v-model.trim="item.sort" type="text" autocomplete="off" class="layui-input" @change="sortChange(item)">
                                            </div>
                                        </div>
                                        <div class="layui-inline">
                                            <div class="layui-form-label">标题</div>
                                            <div class="layui-input-inline">
                                                <input v-model.trim="item.event_name" type="text" placeholder="4字以内" maxlength="4" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-inline">
                                            <div class="layui-form-label">类型</div>
                                            <div class="layui-input-inline">
                                                <div class="layui-unselect layui-form-select" :class="{ 'layui-form-selected': item.show }">
                                                    <div class="layui-select-title" @click="onSelect(item)">
                                                        <input type="text" placeholder="请选择类型" :value="item.event_type_name" readonly="" class="layui-input layui-unselect" @blur="selectBlur(item)">
                                                        <i class="layui-edge"></i>
                                                    </div>
                                                    <dl class="layui-anim layui-anim-upbit" style="">
                                                        <dd lay-value="" class="layui-select-tips" :class="{ 'layui-this': item.event_type === -1 }">请选择类型</dd>
                                                        <dd lay-value="1" :class="{ 'layui-this': item.event_type === 1}" @click.stop="onSelectChange(1, item, $event)">文本框</dd>
                                                        <dd lay-value="2" :class="{ 'layui-this': item.event_type === 2}" @click.stop="onSelectChange(2, item, $event)">单选框</dd>
                                                        <dd lay-value="3" :class="{ 'layui-this': item.event_type === 3}" @click.stop="onSelectChange(3, item, $event)">多选框</dd>
                                                        <dd lay-value="4" :class="{ 'layui-this': item.event_type === 4}" @click.stop="onSelectChange(4, item, $event)">手机号</dd>
                                                        <dd lay-value="5" :class="{ 'layui-this': item.event_type === 5}" @click.stop="onSelectChange(5, item, $event)">下拉框</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-show="item.event_type !== 1 && item.event_type !== 4" class="layui-inline">
                                            <div class="layui-form-label">选项</div>
                                            <div class="layui-input-inline">
                                                <input v-model.trim="item.event_value" type="text" placeholder="请输入选项（不小于2项，以“#”隔开，如：选项一#选项二#选项三）" autocomplete="off" class="layui-input">
                                            </div>
                                        </div>
                                        <div class="layui-inline">
                                            <div class="layui-form-label">必填</div>
                                            <div class="layui-input-inline">
                                                <div :class="{ 'layui-form-onswitch': item.is_required }" class="layui-unselect layui-form-switch" @click="item.is_required = item.is_required ? 0 : 1">
                                                    <em>{{ item.is_required ? '是' : '否' }}</em><i></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="event.length > 1" class="layui-inline">
                                            <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" @click="delEvent(index)">删除</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="addEvent">再加一项</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="layui-tab-item">
                            <div v-for="(item, index) in price" class="layui-form-item price">
                                <div v-if="!index" class="layui-form-label">价格设置：</div>
                                <div class="layui-input-block">
                                    <div class="layui-inline">
                                        <div class="layui-form-label">排序</div>
                                        <div class="layui-input-inline">
                                            <input v-model.trim="item.sort" :readonly="!index" :min="index ? 1 : 0" type="number" autocomplete="off" class="layui-input" @change="sortChange2(item)">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-form-label">人数</div>
                                        <div class="layui-input-inline">
                                            <input v-model.trim="item.event_number" :readonly="!index" type="text" autocomplete="off" class="layui-input" @change="peopleChange(item)">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-form-label">价格</div>
                                        <div class="layui-input-inline">
                                            <input v-model.trim="item.event_price" type="text" autocomplete="off" class="layui-input" @change="priceChange(item, 'event_price')">
                                        </div>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-form-label">会员价格</div>
                                        <div class="layui-input-inline">
                                            <input v-model.trim="item.event_mer_price" type="text" autocomplete="off" class="layui-input" @change="priceChange(item, 'event_mer_price')">
                                        </div>
                                    </div>
                                    <div v-show="index" class="layui-inline">
                                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" @click="delPrice(index)">删除</button>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" @click="addPrice">再加一项</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-primary" @click="clone_form">取消</button>
                        <button v-show="tabIndex" type="button" class="layui-btn layui-btn-primary" @click="tabChange(-1)">上一步</button>
                        <button v-show="tabIndex != 4" type="button" class="layui-btn layui-btn-normal" @click="tabChange(1)">下一步</button>
                        <button v-show="tabIndex == 4" type="button" class="layui-btn layui-btn-normal" lay-submit lay-filter="submit">{$id ?'确认修改':'立即提交'}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{__ADMIN_PATH}js/layuiList.js"></script>
<script src="/static/plug/reg-verify.js"></script>
{/block}
{block name="script"}
<script>
    var id = {$id}, news = {$news}, event = {$event}, price = {$price};
    if (Array.isArray(news)) {
        news = {};
    }
    require(['vue', 'moment', 'zh-cn','request','aliyun-oss','plupload','OssUpload'], function (Vue, moment) {
        var vm = new Vue({
            el: "#app",
            data: {
                formData: {
                    id:id,
                    title: news.title || '',
                    image: news.image || '',
                    qrcode_img: news.qrcode_img || '',
                    start_time: news.start_time || '',
                    end_time: news.end_time || '',
                    signup_start_time: news.signup_start_time || '',
                    signup_end_time: news.signup_end_time || '',
                    province: news.province || '北京市',
                    city: news.city || '北京市',
                    district: news.district || '东城区',
                    detail: news.detail || '',
                    number: news.number || 1,
                    activity_rules: news.activity_rules || '',
                    content: news.content || '',
                    sort: news.sort || 0,
                    restrictions: news.restrictions || 0,
                    is_show: news.is_show || 0,
                    is_fill: news.is_fill || 0
                },
                mask: {
                    image: false,
                    qrcode_img: false
                },
                event: [
                    {
                        event_name: '',
                        event_type: 1,
                        event_type_name: '文本框',
                        event_value: '',
                        sort: 0,
                        is_required: 1,
                        show: false
                    }
                ],
                price: [
                    {
                        sort: 0,
                        event_number: 1,
                        event_price: 0,
                        event_mer_price: 0
                    }
                ],
                priceVerify: true,
                tabIndex: 0
            },
            methods: {
                sortChange: function (item) {
                    var sort = parseInt(item.sort);
                    item.sort = isNaN(sort) ? 0 : Math.abs(sort);
                },
                sortChange2: function (item) {
                    var sort = parseInt(item.sort);
                    item.sort = isNaN(sort) ? 1 : Math.abs(sort);
                },
                peopleChange: function (item) {
                    var eventNumber = parseInt(item.event_number);
                    eventNumber = isNaN(eventNumber) ? 2 : Math.abs(eventNumber);
                    if (eventNumber < 2) {
                        eventNumber = 2;
                    }
                    item.event_number = eventNumber;
                },
                priceChange: function (item, key) {
                    var price = item[key];
                    var label = key === 'event_price' ? '价格' : '会员价格';
                    this.priceVerify = true;
                    if (price) {
                        if (isNaN(price)) {
                            this.priceVerify = false;
                            layer.msg('请正确输入' + label);
                        } else {
                            var index = price.indexOf('.');
                            if (index === -1) {
                                price = parseInt(price);
                            } else {
                                var number = price.length - index -1;
                                if (number) {
                                    if (number > 2) {
                                        this.priceVerify = false;
                                        layer.msg('小数点后最多保留2位小数');
                                        return;
                                    } else {
                                        price = parseFloat(price);
                                    }
                                } else {
                                    this.priceVerify = false;
                                    layer.msg('小数点后最少保留1位小数');
                                    return;
                                }
                            }
                            if (price < 0) {
                                this.priceVerify = false;
                                layer.msg(label + '不能为负数');
                            }
                        }
                    } else {
                        this.priceVerify = false;
                        layer.msg('请输入' + label);
                    }
                },
                // 添加价格
                addPrice: function () {
                    this.price.push({
                        sort: 1,
                        event_number: 2,
                        event_price: 0,
                        event_mer_price: 0
                    });
                },
                // 删除价格
                delPrice: function (index) {
                    this.price.splice(index, 1);
                },
                // 添加资料
                addEvent: function () {
                    this.event.push({
                        event_name: '',
                        event_type: 1,
                        event_type_name: '文本框',
                        event_value: '',
                        sort: 0,
                        is_required: 1,
                        show: false
                    });
                },
                // 删除资料
                delEvent: function (index) {
                    this.event.splice(index, 1);
                },
                // 显示、隐藏下拉框
                onSelect: function (item) {
                    item.show = !item.show;
                },
                // 改变下拉框的值
                onSelectChange: function (value, item, event) {
                    item.event_type = value;
                    item.event_type_name = event.target.innerHTML;
                },
                // 下拉框失焦
                selectBlur: function (item) {
                    setTimeout(function () {
                        item.show = false;
                    }, 200);
                },
                //删除图片
                delect: function (key, index) {
                    var that = this;
                    if (index != undefined) {
                        that.formData[key].splice(index, 1);
                        that.$set(that.formData, key, that.formData[key]);
                    } else {
                        that.$set(that.formData, key, '');
                    }
                },
                //查看图片
                look: function (pic) {
                    parent.$eb.openImage(pic);
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
                clone_form: function () {
                    var that = this;
                    if (parseInt(id) == 0) {
                        parent.layer.closeAll();
                    }
                    parent.layer.closeAll();
                },
                // 验证资料组件
                verifyFormEvent: function (formData) {
                    var status = true;
                    if (formData.is_fill) {
                        var formEvent = this.event;
                        for (var i = formEvent.length; i--;) {
                            if (formEvent[i].event_name) {
                                if (formEvent[i].event_type === -1) {
                                    layer.msg('资料设置中存在未选择类型项，请检查');
                                    status = false;
                                    break;
                                } else {
                                    if (formEvent[i].event_type !== 1 && formEvent[i].event_type !== 4) {
                                        if (formEvent[i].event_value) {
                                            var index = formEvent[i].event_value.indexOf('#');
                                            if (index === -1) {
                                                layer.msg('资料设置中存在选项格式不正确或小于2个的项<br>请检查');
                                                status = false;
                                                break;
                                            } else {
                                                if (!index || index === formEvent[i].event_value.length - 1) {
                                                    layer.msg('资料设置中存在选项以#为首尾的项，请检查');
                                                    status = false;
                                                    break;
                                                }
                                            }
                                        } else {
                                            layer.msg('资料设置中存在未输入选项的项，请检查');
                                            status = false;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                layer.msg('资料设置中存在未输入标题的项，请检查');
                                status = false;
                                break;
                            }
                        }
                    }
                    return status;
                },
                // 上一步、下一步
                tabChange: function (value) {
                    layui.element.tabChange('tab', this.tabIndex + value);
                },
                createEditor: function (name) {
                    return UE.getEditor(name);
                }
            },
            created: function () {
                if (event.length) {
                    event.forEach(function (item) {
                        switch (item.event_type) {
                            case 1:
                                item.event_type_name = '文本框';
                                break;
                            case 2:
                                item.event_type_name = '单选框';
                                break;
                            case 3:
                                item.event_type_name = '多选框';
                                break;
                            case 4:
                                item.event_type_name = '手机号';
                                break;
                            case 5:
                                item.event_type_name = '下拉框';
                                break;
                        }
                        item.show = false;
                    });
                    this.event = event;
                }
                if (price.length) {
                    this.price = price;
                }
            },
            mounted: function () {
                this.$nextTick(function () {
                    var vm = this;
                    layui.config({
                        base: '{__ADMIN_PATH}mods/',
                        version: '1.0'
                    });

                    layui.use([
                        'layarea',
                        'laydate',
                        'form',
                        'element'
                    ], function () {
                        var layarea = layui.layarea;
                        var laydate = layui.laydate;
                        var form = layui.form;
                        var layer = layui.layer;
                        var element = layui.element;
                        var dateOption1 = {
                            elem: '#date1',
                            type: 'datetime',
                            range: true,
                            done: function (value) {
                                var arr = value.split(' - ');
                                vm.formData.signup_start_time = arr[0];
                                vm.formData.signup_end_time = arr[1];
                            }
                        };
                        var dateOption2 = {
                            elem: '#date2',
                            type: 'datetime',
                            range: true,
                            done: function (value) {
                                var arr = value.split(' - ');
                                vm.formData.start_time = arr[0];
                                vm.formData.end_time = arr[1];
                            }
                        };
                        var areaPicker = {
                            elem: '#area-picker',
                            change: function (res) {
                                vm.formData.province = res.province;
                                vm.formData.city = res.city;
                                vm.formData.district = res.county;
                            }
                        };

                        if (news.signup_start_time) {
                            dateOption1.value = vm.formData.signup_start_time + ' - ' + vm.formData.signup_end_time;
                            dateOption2.value = vm.formData.start_time + ' - ' + vm.formData.end_time;
                            areaPicker.data = {
                                province: vm.formData.province,
                                city: vm.formData.city,
                                district: vm.formData.district
                            };
                        }

                        laydate.render(dateOption1);
                        laydate.render(dateOption2);
                        layarea.render(areaPicker);
                        form.render();

                        element.on('tab(tab)', function (data) {
                            vm.tabIndex = data.index;
                        });

                        form.on('switch(is_show)', function (data) {
                            vm.formData.is_show = Number(data.elem.checked);
                        });

                        form.on('switch(is_fill)', function (data) {
                            vm.formData.is_fill = Number(data.elem.checked);
                        });

                        form.on('switch(pay_type)', function (data) {
                            vm.formData.pay_type = Number(data.elem.checked);
                        });

                        form.on('switch(member_pay_type)', function (data) {
                            vm.formData.member_pay_type = Number(data.elem.checked);
                        });

                        form.on('submit(submit)', function () {
                            var formData = vm.formData;
                            var activity_rules = vm.ueditor1.getContent();
                            var content = vm.ueditor2.getContent();
                            if (!formData.signup_start_time) {
                                layer.msg('请选择报名时间');
                            } else if (!formData.start_time) {
                                layer.msg('请选择活动时间');
                            } else if (formData.number <= 1 || parseInt(formData.number) !== parseFloat(formData.number)) {
                                layer.msg('请输入正确的活动人数');
                            } else if (!formData.image) {
                                layer.msg('请上传活动封面');
                            } else if (!formData.qrcode_img) {
                                layer.msg('请上传二维码');
                            } else if (!formData.detail) {
                                layer.msg('请输入详细地址');
                            } else if (!activity_rules) {
                                layer.msg('请输入活动规则');
                            } else if (!content) {
                                layer.msg('请输入活动详情');
                            } else {
                                if (vm.verifyFormEvent(formData)) {
                                    for (let i = 0; i < vm.price.length; i++) {
                                      if (vm.price[i].event_number > vm.formData.number) {
                                        return layer.msg('价格设置中输入的人数不能大于输入的活动人数');
                                      }
                                      if (Number(vm.price[i].event_price) < 0 || Number(vm.price[i].event_mer_price) < 0) {
                                        return layer.msg('设置的价格不能为负数');
                                      }
                                    }
                                    formData.activity_rules = activity_rules;
                                    formData.content = content;
                                    if (formData.is_fill) {
                                        formData.event = JSON.stringify(vm.event);
                                    }
                                    formData.event_price = JSON.stringify(vm.price);
                                    formData.price = vm.price[0].event_price;
                                    formData.member_price = vm.price[0].event_mer_price;
                                    var loadIndex = layer.load(1);
                                    layList.basePost(layList.U({
                                        a: 'add_new',
                                    }), formData, function (res) {
                                        layer.close(loadIndex);
                                        if (parseInt(id) === 0) {
                                            layer.confirm('添加成功,您要继续添加活动吗?', {
                                                btn: ['继续添加', '立即提交']
                                            }, function (index) {
                                                layer.close(index);
                                                window.location.reload();
                                            }, function () {
                                                parent.layer.closeAll();
                                            });
                                        } else {
                                            layList.msg('修改成功', function () {
                                                parent.layer.closeAll();
                                                window.location.reload();
                                            })
                                        }
                                    }, function (res) {
                                        layer.close(loadIndex);
                                        layer.msg(res.msg)
                                    });
                                }
                            }
                            return false;
                        });
                    });

                    UE.registerUI('选择图片', function (editor, uiName) {
                        var btn = new UE.ui.Button({
                            name: uiName,
                            title: uiName,
                            cssRules: 'background-position: -380px 0;',
                            onclick: function() {
                                ossUpload.createFrame(uiName, { fodder: editor.key }, { w: 800, h: 550 });
                            }
                        });
                        return btn;
                    });
                    ueditorList = document.querySelectorAll('[id*="ueditor"]');
                    ueditorList.forEach(function (ueditor) {
                      vm[ueditor.id] = UE.getEditor(ueditor.id);
                    });

                    function changeIMG(index, pic) {
                        $(".image_img").css('background-image', "url(" + pic + ")");
                        $(".active").css('background-image', "url(" + pic + ")");
                        $('#image_input').val(pic);
                    };

                    window.insertEditor = function (list, fodder) {
                        vm[fodder].execCommand('insertimage', list);
                    };

                    window.changeIMG = vm.changeIMG;
                });
            }
        })
    })
</script>
{/block}
