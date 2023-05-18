<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016～2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\wap\model\special;

use app\wap\model\special\SpecialSource;
use app\wap\model\store\StoreOrder;
use app\wap\model\store\StorePink;
use app\wap\model\user\User;
use basic\ModelBasic;
use service\SystemConfigService;
use think\Url;
use traits\ModelTrait;
use think\Db;
use app\wap\model\live\LiveStudio;
use app\wap\model\live\LivePlayback;
use app\wap\model\special\LearningRecords;
use app\wap\model\special\SpecialSubject;
use app\wap\model\material\DataDownload;

/**专题 model
 * Class Special
 * @package app\wap\model\special
 */
class Special extends ModelBasic
{
    use ModelTrait;

    public function profile()
    {
        return $this->hasOne('SpecialContent', 'special_id', 'id')->field('content,is_try,try_content');
    }

    public function singleProfile()
    {
        return $this->hasOne('SpecialContent', 'special_id', 'id')->field('link,videoId,is_try,try_time,try_content');
    }

    //动态赋值
    public static function getPinkStrarTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public static function getPinkEndTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public static function getAddTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public static function getBannerAttr($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public static function getLabelAttr($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    /**
     * 设置专题显示条件
     * @param string $alias 别名
     * @param null $model model
     * @param bool $isAL 是否起别名,默认执行
     * @return $this
     */
    public static function PreWhere($alias = '', $model = null, $isAL = false)
    {
        self::setPinkSpecial();
        if (is_null($model)) $model = new self();
        if ($alias) {
            $isAL || $model = $model->alias($alias);
            $alias .= '.';
        }
        return $model->where(["{$alias}is_del" => 0, "{$alias}is_show" => 1, "{$alias}status" => 1]);
    }

    /**
     * 获取拼团详情页的专题详情和分享连接
     * @param string $order_id 订单id
     * @param int $pinkId 当前拼团id
     * @param int $uid 当前用户id
     * @return array
     * */
    public static function getPinkSpecialInfo($order_id, $pinkId, $uid)
    {
        $special = self::PreWhere()->where('id', StoreOrder::where('order_id', $order_id)->value('cart_id'))
            ->field(['image', 'title', 'abstract', 'money', 'label', 'id', 'is_light', 'light_type', 'is_mer_visible', 'is_pink', 'pink_money'])->find();
        if (!$special) return [];
        $special['image'] = get_oss_process($special['image'], 4);
        if ($special['is_light']) {
            $special['link'] = SystemConfigService::get('site_url') . Url::build('special/single_details') . '?id=' . $special['id'] . '&pinkId=' . $pinkId . '&partake=1#partake';
        } else {
            $special['link'] = SystemConfigService::get('site_url') . Url::build('special/details') . '?id=' . $special['id'] . '&pinkId=' . $pinkId . '&partake=1#partake';
        }
        $special['abstract'] = self::HtmlToMbStr($special['abstract']);
        return $special;
    }

    /**
     * 设置拼团到时间的专题
     * */
    public static function setPinkSpecial()
    {
        self::where('pink_strar_time', '<', time())->where('pink_end_time', '<', time())->update([
            'is_pink' => 0,
            'pink_strar_time' => 0,
            'pink_end_time' => 0
        ]);
    }

    /**
     * 获取单个专题的详细信息,拼团信息,拼团用户信息
     * @param $uid 用户id
     * @param $id 专题id
     * @param $pinkId 拼团id
     * */
    public static function getOneSpecial($uid, $id)
    {
        $special = self::PreWhere()->where('is_light', 0)->find($id);
        if (!$special) return self::setErrorInfo('您要查看的专题不存在!');
        if ($special->is_show == 0) return self::setErrorInfo('您要查看的专题已下架!');
        $title = $special->title;
        if ($uid && $id) {
            $special->collect = self::getDb('special_relation')->where(['link_id' => $id, 'type' => 0, 'uid' => $uid, 'category' => 1])->count() ? true : false;
        } else {
            $special->collect = false;
        }
        $special->content = htmlspecialchars_decode($special->profile->content);
        $special->profile->content = '';
        $swiperlist = json_encode($special->banner);
        $special = json_encode($special->toArray());
        return compact('swiperlist', 'special', 'title');
    }

    /**获取单个轻专题
     * @param $uid
     * @param $id
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSingleOneSpecial($uid, $id)
    {
        $special = self::PreWhere()->where('is_light', 1)->find($id);
        if (!$special) return self::setErrorInfo('您要查看的专题不存在!');
        if ($special->is_show == 0) return self::setErrorInfo('您要查看的专题已下架!');
        $title = $special->title;
        $special->abstract = htmlspecialchars_decode($special->abstract);
        if ($uid && $id) {
            $special->collect = self::getDb('special_relation')->where(['link_id' => $id, 'type' => 0, 'uid' => $uid, 'category' => 1])->count() ? true : false;
        } else {
            $special->collect = false;
        }
        $special->profile = $special->profile;
        $special = json_encode($special->toArray());
        return compact('special', 'title');
    }

    /**获取轻专题内容
     * @param $id
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSingleImgSpecialContent($id)
    {
        $special = self::PreWhere()->where('is_light', 1)->find($id);
        if (!$special) return self::setErrorInfo('您要查看的专题不存在!');
        if ($special->is_show == 0) return self::setErrorInfo('您要查看的专题已下架!');
        $data['title'] = $special->title;
        $data['money'] = $special->money;
        $data['pay_type'] = $special->pay_type;
        $data['member_money'] = $special->member_money;
        $data['member_pay_type'] = $special->member_pay_type;
        $data['image'] = $special->image;
        $data['profile'] = $special->profile;
        $data['content'] = htmlspecialchars_decode($special->profile->content);
        unset($special['profile']['content']);
        return $data;
    }

    /**获取轻专题内容
     * @param $id
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getSingleSpecialContent($id)
    {
        $special = self::PreWhere()->where('is_light', 1)->find($id);
        if (!$special) return self::setErrorInfo('您要查看的专题不存在!');
        if ($special->is_show == 0) return self::setErrorInfo('您要查看的专题已下架!');
        $data['title'] = $special->title;
        $data['abstract'] = $special->abstract;
        $data['light_type'] = $special->light_type;
        $data['image'] = $special->image;
        $data['money'] = $special->money;
        $data['pay_type'] = $special->pay_type;
        $data['member_money'] = $special->member_money;
        $data['member_pay_type'] = $special->member_pay_type;
        $data['singleProfile'] = $special->singleProfile;
        return $data;
    }

    /**
     * 我的课程
     * @param int $active 1=购买的课程,0=赠送的课程
     * @param int $page 页码
     * @param int $limit 每页显示条数
     * @param int $uid 用户uid
     * @return array
     * */
    public static function getMyGradeList($page, $limit, $uid, $is_member, $active = 0)
    {
        $model = self::PreWhere('a')->join('SpecialBuy s', 'a.id=s.special_id')->where('s.is_del', 0)->group('s.special_id')
            ->order('a.sort desc,s.add_time desc');
        $list = $model->where('s.uid',$uid)->field('a.*,a.type as types,s.*')->page($page, $limit)->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['image'] = get_oss_process($item['image'], 4);
            if (is_string($item['label'])) $item['label'] = json_decode($item['label'], true);
            $id = $item['special_id'];
            $item['s_id'] = $id;
            $item['count'] = self::numberChapters($item['types'], $item['s_id']);
            if ($item['is_light']) {
                $item['type'] = self::lightType($item['light_type']);
            }
        }
        $page += 1;
        return compact('list', 'page');
    }

    /**
     * 我的收藏
     * @param int $type 1=收藏,0=我的购买
     * @param int $page 页码
     * @param int $limit 每页显示条数
     * @param int $uid 用户uid
     * @return array
     * */
    public static function getGradeList($page, $limit, $uid, $is_member, $active = 0)
    {
        if ($active) {
            $model = DataDownload::PreWhere('a')->where('s.uid', $uid)->where('s.type', 1)->join('__SPECIAL_RELATION__ s', 'a.id=s.link_id');
            $list = $model->order('a.sort desc')->field('a.*')->page($page, $limit)->select();
        } else {
            $model = self::PreWhere('a')->where('s.uid', $uid)->where('s.type', 0)->join('__SPECIAL_RELATION__ s', 'a.id=s.link_id');
            if (!$is_member) $model = $model->where(['a.is_mer_visible' => 0]);
            $list = $model->order('a.sort desc')->field('a.*,a.type as types')->page($page, $limit)->select();
        }
        $list = count($list) > 0 ? $list->toArray() : [];
        foreach ($list as &$item) {
            if (!$active) {
                $item['image'] = get_oss_process($item['image'], 4);
                if (is_string($item['label'])) $item['label'] = json_decode($item['label'], true);
                $id = $item['id'];
                $item['s_id'] = $id;
                $item['count'] = self::numberChapters($item['types'], $item['s_id']);
                if ($item['is_light']) {
                    $item['type'] = self::lightType($item['light_type']);
                }
            }
        }
        $page += 1;
        return compact('list', 'page');
    }

    /**
     * 获取某个专题的详细信息
     * @param int $id 专题id
     * @return array
     * */
    public static function getSpecialInfo($id)
    {
        $special = self::PreWhere()->find($id);
        if (!$special) return self::setErrorInfo('没有找到此专题');
        $special->abstract = self::HtmlToMbStr($special->abstract);
        return $special->toArray();
    }

    /**
     * 获取推广专题列表
     * @param array $where 查询条件
     * @param int $uid 用户uid
     * @return array
     * */
    public static function getSpecialSpread($where, $is_member)
    {
        $store_brokerage_ratio = SystemConfigService::get('store_brokerage_ratio');
        $store_brokerage_ratio = bcdiv($store_brokerage_ratio, 100, 2);
        $ids = SpecialSubject::where('a.is_show', 1)->alias('a')->join('__SPECIAL__ s', 's.subject_id=a.id')->column('a.id');
        $subjectIds = [];
        foreach ($ids as $item) {
            if (self::PreWhere()->where('subject_id', $item)->count()) array_push($subjectIds, $item);
        }
        $model = SpecialSubject::where('is_show', 1)->order('sort desc')->field('id,name');
        if ($where['grade_id']) $model = $model->where('grade_id', $where['grade_id']);
        $list = $model->where('id', 'in', $subjectIds)->page((int)$where['page'], (int)$where['limit'])->select();
        $data = count($list) ? $list->toArray() : [];
        foreach ($data as &$item) {
            $itm = self::PreWhere()->where('subject_id', $item['id'])->field(['image', 'id', 'is_mer_visible', 'title', 'money']);
            if (!$is_member) $itm = $itm->where(['is_mer_visible' => 0]);
            $item['list'] = $itm->order('sort desc')->select();
            if (count($item['list'])) $item['list'] = $item['list']->toArray();
            foreach ($item['list'] as &$value) {
                $value['image'] = get_oss_process($value['image'], 4);
                if ($value['money'] > 0) $value['spread_money'] = bcmul($value['money'], $store_brokerage_ratio, 2);
                else $value['spread_money'] = 0;
            }
        }
        $page = (int)$where['page'] + 1;
        return compact('data', 'page');
    }

    /**
     * 设置查询条件
     * @param $where
     * @return $this
     */
    public static function setWhere($where)
    {
        if ($where['type']) {
            $model = self::PreWhere('a');
            if ($where['subject_id'] && $where['grade_id']) {
                $model = $model->where('a.subject_id', $where['subject_id']);
            }
            if ($where['search']) {
                $model = $model->where('a.title', 'LIKE', "%$where[search]%");
            }
            if (!$where['is_member']) $model = $model->where(['a.is_mer_visible' => 0]);
            return $model->order('a.sort desc,a.id desc')
                ->join('special_record r', 'r.special_id = a.id')
                ->group('a.id')->where('uid', $where['uid']);
        } else {
            $model = self::PreWhere();
            if ($where['subject_id'] && $where['grade_id'] > 0) {
                $model = $model->where('subject_id', $where['subject_id']);
            } else if ($where['subject_id'] == 0 && $where['grade_id'] > 0) {
                $subject_ids = SpecialSubject::subjectId($where['grade_id']);
                $model = $model->where('subject_id', 'in', $subject_ids);
            }
            if ($where['search']) {
                $model = $model->where('title|abstract', 'LIKE', "%$where[search]%");
            }
            if (!$where['is_member']) $model = $model->where(['is_mer_visible' => 0]);
            return $model->order('sort desc,id desc');
        }
    }

    /**
     * 获取专题列表
     * @param $where
     * @return mixed
     */
    public static function getSpecialList($where)
    {
        if ($where['type']) {
            $alias = 'a.';
            $field = [$alias . 'id', $alias . 'fake_sales', $alias . 'browse_count', $alias . 'image', $alias . 'is_light', $alias . 'light_type', $alias . 'is_mer_visible', $alias . 'title', $alias . 'type', $alias . 'money', $alias . 'pink_money', $alias . 'is_pink', $alias . 'subject_id', $alias . 'label', 'r.number'];
        } else {
            $field = ['browse_count', 'image', 'title', 'type', 'is_light', 'light_type', 'is_mer_visible', 'money', 'pink_money', 'is_pink', 'subject_id', 'label', 'id', 'fake_sales'];
        }
        $list = self::setWhere($where)
            ->field($field)
            ->page($where['page'], $where['limit'])
            ->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['count'] = self::numberChapters($item['type'], $item['id']);
            $count = self::learning_records($item['id']);
            $item['browse_count'] = processingData(bcadd($count, $item['fake_sales'], 0));
            if ($item['is_light']) {
                $item['type'] = self::lightType($item['light_type']);
            }
        }
        return $list;
    }

    /**讲师名下课程
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLecturerSpecialList($mer_id = 0, $page = 1, $limit = 10)
    {
        if(!$mer_id) return [];
        $field = ['browse_count', 'image', 'title', 'type', 'money', 'pink_money', 'is_light', 'light_type', 'is_mer_visible', 'is_pink', 'subject_id', 'label', 'id', 'is_show', 'is_del', 'lecturer_id', 'mer_id'];
        $model = self::PreWhere();
        $model = $model->where(['mer_id' => $mer_id])->order('sort desc,id desc');
        $list = $model->field($field)->page($page, $limit)->select();
        $list = count($list) ? $list->toArray() : [];
        return $list;
    }

    /**拼团专题
     * @param int $page
     * @param int $limit
     */
    public static function getPinkSpecialList($page = 1, $limit = 10)
    {
        $field = ['browse_count', 'image', 'is_light', 'light_type', 'is_mer_visible', 'title', 'type', 'money', 'pink_money', 'is_pink', 'subject_id', 'label', 'id', 'is_show', 'is_del', 'lecturer_id', 'pink_number'];
        $model = self::PreWhere();
        $model = $model->where(['is_pink' => 1])->order('sort desc,id desc');
        $list = $model->field($field)->page($page, $limit)->select();
        $list = count($list) ? $list->toArray() : [];
        foreach ($list as &$item) {
            $item['count'] = StorePink::where(['status' => 2, 'cid' => $item['id']])->count();
        }
        return $list;
    }

    /**专题下章节数量
     * @param int $type
     * @param int $id
     * @return int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function numberChapters($type = 0, $id = 0)
    {
        $count = 0;
        if ($type != 5 && $type != 4) {
            $specialSourceId = SpecialSource::getSpecialSource($id);
            if ($specialSourceId) $count = count($specialSourceId);
        } else if ($type == 5) {
            $specialSourceId = SpecialSource::getSpecialSource($id);
            if (count($specialSourceId)) {
                $specialSource = $specialSourceId->toArray();
                foreach ($specialSource as $key => $value) {
                    $specialSourcetaskId = SpecialSource::getSpecialSource($value['source_id']);
                    if (count($specialSourcetaskId) == 0) {
                        $is_light = self::PreWhere()->where('id', $value['source_id'])->value('is_light');
                        if ($is_light) {
                            $count = bcadd($count, 1, 0);
                        }
                    } else {
                        $count = bcadd($count, count($specialSourcetaskId), 0);
                    }
                }
            }
            $count = (int)$count;
        } else if ($type == 4) {
            $liveStudio = LiveStudio::where(['special_id' => $id])->find();
            if (!$liveStudio)  $count = 0;
            if (!$liveStudio['stream_name'])  $count = 0;
            if ($liveStudio['is_playback'] == 1) {
                $where['stream_name'] = $liveStudio['stream_name'];
                $where['start_time'] = '';
                $where['end_time'] = '';
                $count = LivePlayback::setUserWhere($where)->count();
            }
        }
        return $count;
    }

    /**轻专题 类型
     * @param $light_type
     * @return int
     */
    public static function lightType($light_type)
    {
        switch ($light_type) {
            case 1:
                $type = 1;
                break;
            case 2:
                $type = 2;
                break;
            case 3:
                $type = 3;
                break;
        }
        return $type;
    }

    /**获得专题真实学习人数
     * @param int $special_id
     * @return int
     */
    public static function learning_records($special_id = 0)
    {
        $uids = LearningRecords::where(['special_id' => $special_id])->column('uid');
        $uids = array_unique($uids);
        return count($uids);
    }

    /**
     * 获取单独分销设置
     */
    public static function getIndividualDistributionSettings($id = 0)
    {
        $data = self::where('id', $id)->field('is_alone,brokerage_ratio,brokerage_two')->find();
        if ($data) return $data;
        else return [];
    }

    /**获取专题标题
     * @param $id
     * @return float|mixed|string
     */
    public static function getName($id = 0)
    {
        if (!$id) return '';
        return self::where(['id' => $id])->value('title');
    }
}
