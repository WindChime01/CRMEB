<?php

/**
 * 文件路径： \application\index\job\Hello.php
 * 这是一个消费者类，用于处理 helloJobQueue 队列中的任务
 */
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016～2023 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace app\index\job;

use app\admin\model\ump\StorePink;
use app\wap\model\special\SpecialBuy;
use think\queue\Job;

class PullDoPink
{

    /**
     * fire方法是消息队列默认调用的方法
     * @param Job $job 当前的任务对象
     * @param array|mixed $data 发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        // 有些消息在到达消费者时,可能已经不再需要执行了
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if (!$isJobStillNeedToBeDone) {
            $job->delete();
            return;
        }
        if (isset($data['doName']) && $data['doName']) {
            $doName = $data['doName'];
            $isJobDone = $this->$doName($data);
        } else
            $isJobDone = $this->doPinkJob($data);


        if ($isJobDone) {
            // 如果任务执行成功， 记得删除任务
            $job->delete();
            //print("<info>Hello Job has been done and deleted"."</info>\n");
        } else {
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                // print("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     * @param array|mixed $data 发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data)
    {
        return true;
    }

    /**
     * 根据消息中的数据进行实际的业务处理...
     */
    private function doPinkJob($data)
    {
        $pink_id = $data['pinkInfo']['pink_id'];
        if ($pink_id) {
            $pink_info = \app\wap\model\store\StorePink::where(['id' => $pink_id, 'k_id' => 0, 'status' => 1])->find();
            if ($pink_info ? $pink_info = $pink_info->toArray() : []) {
                list($pinkAll, $pinkT, $count, $idAll) = \app\wap\model\store\StorePink::getPinkMemberAndPinkK($pink_info);
                \app\wap\model\store\StorePink::PinkFail($pink_info['uid'], $idAll, $pinkAll, $pinkT, $count, 1, [], true, true);
            }
        }
        return true;
    }

    private function doLiveStudioJob($data)
    {
        $item = $data['pinkInfo'];
        if ($item) {
            if ($openId = \app\wap\model\user\WechatUser::where('uid', $item['uid'])->value('openid')) {
                $wechat_notification_message = \service\SystemConfigService::get('wechat_notification_message');
                if ($wechat_notification_message == 1) {
                    \service\WechatTemplateService::sendTemplate($openId, \service\WechatTemplateService::LIVE_START_NOTICE, [
                        'first' => '叮！直播马上开始啦，精彩不容错过！',
                        'keyword1' => $item['live_title'],
                        'keyword2' => $item['start_play_time'],
                        'remark' => '直播间通道'
                    ], $item['site_url'] . \think\Url::build('wap/special/details', ['id' => $item['id']]));
                } else {
                    $data['thing5']['value'] = $item['live_title'];
                    $data['time2']['value'] = $item['start_play_time'];
                    \app\wap\model\routine\RoutineTemplate::sendBroadcastReminder($data, $item['uid'], $item['site_url'] . \think\Url::build('wap/special/details', ['id' => $item['id']]));
                }
            }
            $dat['title'] = $item['live_title'];
            \app\wap\model\wap\SmsTemplate::sendSms($item['uid'], $dat, 'LIVE_START_NOTICE');
        }
        return true;
    }
}
