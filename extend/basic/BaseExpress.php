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

namespace basic;

use service\AccessTokenServeService;

/**
 * Class BaseExpress
 * @package crmeb\basic
 */
abstract class BaseExpress extends BaseStorage
{

    /**
     * access_token
     * @var null
     */
    protected $accessToken = NULL;


     public function __construct(string $name, AccessTokenServeService $accessTokenServeService, string $configFile)
    {
        $this->accessToken = $accessTokenServeService;
    }

    /**
     * ��ʼ��
     * @param array $config
     * @return mixed|void
     */
    protected function initialize(array $config = [])
    {
//        parent::initialize($config);
    }


    /**
     * ��ͨ����
     * @return mixed
     */
    abstract public function open();

    /**����׷��
     * @return mixed
     */
    abstract public function query($com, $num);

    /**�����浥
     * @return mixed
     */
    abstract public function dump($data);

    /**��ݹ�˾
     * @return mixed
     */
    //abstract public function express($type, $page, $limit);

    /**�浥ģ��
     * @return mixed
     */
    abstract public function temp($com, $page, $limit);
}
