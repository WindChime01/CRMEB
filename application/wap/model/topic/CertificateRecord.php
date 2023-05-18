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

namespace app\wap\model\topic;

use traits\ModelTrait;
use basic\ModelBasic;
use service\UtilService as Util;
use app\wap\model\user\User;
use app\wap\model\special\Special;
use app\wap\model\topic\TestPaper;
use app\wap\model\topic\Certificate;
use app\wap\model\topic\ExaminationRecord;

/**
 * 证书获取记录 Model
 * Class CertificateRecord
 */
class CertificateRecord extends ModelBasic
{
    use ModelTrait;

    /**查询条件
     * @param $uid
     * @return CertificateRecord
     */
    public static function setWhere($uid)
    {
        $model = self::where(['status' => 1, 'is_del' => 0, 'uid' => $uid]);
        return $model;
    }

    /**获得证书列表
     * @param $uid
     * @param $page
     * @param $limit
     */
    public static function getUserCertificate($uid, $page, $limit)
    {
        $list = self::setWhere($uid)->page((int)$page, (int)$limit)->select();
        $list = count($list) > 0 ? $list->toArray() : [];
        foreach ($list as $key => &$value) {
            $value['content'] = self::certificate($value['source_id'], $value['obtain']);
        }
        return $list;
    }

    public static function certificate($source_id, $obtain)
    {
        switch ($obtain) {
            case 1:
                return Special::where(['id' => $source_id])->field('title,image')->find();
                break;
            case 2:
                return TestPaper::alias('t')->join('ExaminationRecord e', 't.id=e.test_id')
                    ->where(['t.id' => $source_id, 'is_submit' => 1])->order('e.id desc')
                    ->field('t.title,t.item_number,e.wrong_question,e.duration')->find();
                break;
        }
    }

    /**获取证书信息
     * @param $id
     * @param $obtain
     * @param $uid
     * @return array|bool|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCertificate($id, $obtain, $uid)
    {
        $certificate = self::setWhere($uid)->where(['id' => $id, 'obtain' => $obtain])->find();
        if (!$certificate) return [];
        $certificate['certificate'] = Certificate::getone($certificate['cid'], $obtain);
        return $certificate;
    }

    /**领取证书
     * @param $id
     */
    public static function getUserTheCertificate($id, $obtain, $uid)
    {
        $cid = CertificateRelated::where(['related' => $id, 'obtain' => $obtain, 'is_show' => 1])->value('cid');
        if (!$cid) return false;
        $record = self::setWhere($uid)->where(['source_id' => $id, 'obtain' => $obtain])->find();
        if ($record) return false;
        $nickname = User::where('uid', $uid)->value('nickname');
        $data = [
            'cid' => $cid,
            'uid' => $uid,
            'nickname' => $nickname,
            'source_id' => $id,
            'obtain' => $obtain,
            'status' => 1,
            'add_time' => time()
        ];
        $res = self::insertGetId($data);
        $res1 = false;
        if ($res) {
            $res1 = Certificate::where('id', $cid)->setInc('number');
        }
        if ($res && $res1) return $res;
        else return false;
    }

}
