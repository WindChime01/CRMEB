<?php

namespace Api;

use think\Config;
use OSS\Model\RefererConfig;
use service\SystemConfigService;
use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Core\OssUtil;

/**
 * Class AliyunOss
 * @package Api
 */
class AliyunOss extends AliyunSdk
{

    /**
     * OSS存储桶名
     * @var string
     */
    protected $OssBucket;

    /**
     * OSS 地域节点
     * @var string
     */
    protected $OssEndpoint;

    /**
     * 外网访问地址
     * @var string
     */
    protected $uploadUrl;

    /**
     * 全局唯一的uploadId
     * @var string
     */
    protected $uploadId;
    /**
     * 正在上传的文件名
     * @var string
     */
    protected $object;

    /**
     * 上传验证规则
     * @var string
     */
    protected $autoValidate;

    /**
     * 是否开启防盗链
     * @var array
     */
    protected $referer;

    /**
     * 初始化参数
     */
    protected function _initialize()
    {
        $this->OssEndpoint = isset($this->config['OssEndpoint']) ? $this->config['OssEndpoint'] : null;
        $this->OssBucket = isset($this->config['OssBucket']) ? $this->config['OssBucket'] : null;
        $this->uploadUrl = isset($this->config['uploadUrl']) ? $this->config['uploadUrl'] : null;
        $this->checkUploadUrl();
        $this->referer = isset($this->config['referer']) && is_array($this->config['referer']) ? $this->config['referer'] : [];
    }

    /**
     * 验证合法上传域名
     */
    protected function checkUploadUrl()
    {
        $site_url=SystemConfigService::get('site_url');
        if($site_url){
            $arr = parse_url($site_url);
            if($arr['scheme']){
                $scheme=$arr['scheme'];
            }else{
                $scheme='http';
            }
        }else{
            $scheme='http';
        }
        if ($this->uploadUrl) {
            if ($scheme=='https') {
                if (strstr($this->uploadUrl, 'https') === false) {
                    $this->uploadUrl = 'https://' . $this->uploadUrl;
                }
            }else{
                if (strstr($this->uploadUrl, 'http') === false) {
                    $this->uploadUrl = 'http://' . $this->uploadUrl;
                }
            }
        }
    }

    /**
     * 初始化
     * @return null|\OSS\OssClient
     * @throws \OSS\Core\OssException
     */
    public function init()
    {
        if ($this->client === null) {
            $this->client = new OssClient($this->AccessKey, $this->AccessKeySecret, $this->OssEndpoint);
            if (!$this->client->doesBucketExist($this->OssBucket)) {
                $this->client->createBucket($this->OssBucket, OssClient::OSS_ACL_TYPE_PUBLIC_READ_WRITE);
            }
            if ($this->referer) {
                $refererConfig = new RefererConfig();
                // 设置允许空Referer。
                $refererConfig->setAllowEmptyReferer(true);
                foreach ($this->referer as $url) {
                    $refererConfig->addReferer($url);
                }
                $this->client->putBucketReferer($this->OssBucket, $refererConfig);
            }
        }
        return $this->client;
    }

    /**
     * 设置防盗链
     * @param array $referer
     * @return $this
     */
    public function setReferer(array $referer = [])
    {
        $this->referer = $referer;
        return $this;
    }

    /**
     * 验证规则
     * @param array $autoValidate
     * @return $this
     */
    public function validate(array $autoValidate = [])
    {
        if (!$autoValidate) {
            $autoValidate = Config::get('upload.Validate');
        }
        $this->autoValidate = $autoValidate;
        return $this;
    }

    /**
     * 设置OSS存储桶名
     * @param string $OssBucket
     * @return $this
     * */
    public function setOssBucketAttr($OssBucket)
    {
        $this->OssBucket = $OssBucket;
        return $this;
    }

    /**
     * 设置OSS存储外网访问域名
     * @param string $OssEndpoint
     * @return $this
     * */
    public function setOssEndpointAttr($OssEndpoint)
    {
        $this->OssEndpoint = $OssEndpoint;
        return $this;
    }

    /**
     * 提取文件名
     * @param string $path
     * @param string $ext
     * @return string
     */
    protected function saveFileName($path = null, $ext = 'jpg')
    {
        return ($path ? substr(md5($path), 0, 5) : '') . date('YmdHis') . rand(0, 9999) . '.' . $ext;
    }

    /**
     * 获取文件后缀
     * @param \think\File $file
     * @return string
     */
    protected function getExtension(\think\File $file)
    {
        $pathinfo = pathinfo($file->getInfo('name'));
        return isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
    }

    /**
     * 上传图片
     * @param $fileName
     * @return bool
     */
    public function upload($fileName)
    {
        $fileHandle = request()->file($fileName);
        $key = $this->saveFileName($fileHandle->getRealPath(), $this->getExtension($fileHandle));
        try {
            if ($this->autoValidate) {
                $fileHandle->validate($this->autoValidate);
                $this->autoValidate = null;
            }
            $uploadInfo = $this->init()->uploadFile($this->OssBucket, $key, $fileHandle->getRealPath());
            if (!isset($uploadInfo['info']['url'])) {
                return self::setErrorInfo('Upload failure');
            }
            return [
                'url' => $uploadInfo['info']['url'],
                'key' => $key
            ];
        } catch (\Throwable $e) {
            return self::setErrorInfo($e);
        }
    }

    /**文件分片上传
     * @param $fileName
     */
    public function sliceFileUpload($fileName)
    {
        $fileHandle = request()->file($fileName);
        $key = $this->saveFileName($fileHandle->getRealPath(), $this->getExtension($fileHandle));
        $this->object = $object = $key; //上传时生成的文件名
        $uploadFile = $fileHandle->getRealPath(); //文件真实路径
        /**
         *  步骤1：初始化一个分片上传事件，获取uploadId。
         */
        try{
            $ossClient = $this->init();

            //返回uploadId。uploadId是分片上传事件的唯一标识，您可以根据uploadId发起相关的操作，如取消分片上传、查询分片上传等。
            $this->uploadId = $uploadId = $ossClient->initiateMultipartUpload($this->OssBucket, $object);
        } catch(OssException $e) {
            return self::setErrorInfo($e->getMessage());
        }
        /*
         * 步骤2：上传分片。
         */
        $partSize = 10 * 1024 * 1024;
        $uploadFileSize = filesize($uploadFile);
        $pieces = $ossClient->generateMultiuploadParts($uploadFileSize, $partSize);
        $responseUploadPart = array();
        $uploadPosition = 0;
        $isCheckMd5 = true;
        foreach ($pieces as $i => $piece) {
            $fromPos = $uploadPosition + (integer)$piece[$ossClient::OSS_SEEK_TO];
            $toPos = (integer)$piece[$ossClient::OSS_LENGTH] + $fromPos - 1;
            $upOptions = array(
                // 上传文件。
                $ossClient::OSS_FILE_UPLOAD => $uploadFile,
                // 设置分片号。
                $ossClient::OSS_PART_NUM => ($i + 1),
                // 指定分片上传起始位置。
                $ossClient::OSS_SEEK_TO => $fromPos,
                // 指定文件长度。
                $ossClient::OSS_LENGTH => $toPos - $fromPos + 1,
                // 是否开启MD5校验，true为开启。
                $ossClient::OSS_CHECK_MD5 => $isCheckMd5,
            );
            // 开启MD5校验。
            if ($isCheckMd5) {
                $contentMd5 = OssUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
                $upOptions[$ossClient::OSS_CONTENT_MD5] = $contentMd5;
            }
            try {
                // 上传分片。
                $responseUploadPart[] = $ossClient->uploadPart($this->OssBucket, $object, $uploadId, $upOptions);
            } catch(OssException $e) {
                return self::setErrorInfo($e->getMessage());
            }
        }
        // $uploadParts是由每个分片的ETag和分片号（PartNumber）组成的数组。
        $uploadParts = array();
        foreach ($responseUploadPart as $i => $eTag) {
            $uploadParts[] = array(
                'PartNumber' => ($i + 1),
                'ETag' => $eTag,
            );
        }
        /**
         * 步骤3：完成上传。
         */
        try {
            // 执行completeMultipartUpload操作时，需要提供所有有效的$uploadParts。OSS收到提交的$uploadParts后，会逐一验证每个分片的有效性。当所有的数据分片验证通过后，OSS将把这些分片组合成一个完整的文件。
            $res=$ossClient->completeMultipartUpload($this->OssBucket, $object, $uploadId, $uploadParts);
            $this->object='';
            $this->uploadId='';
        }  catch(OssException $e) {
            return self::setErrorInfo($e->getMessage());
        }
        $url=$res['info']['url'];
        $data=explode('?',$url);
        return [
            'url' => $data[0],
            'key' => $key
        ];
    }

    /**取消分片上传
     * @param $object 上传的文件名
     * @param $upload_id 上传时生成的上传ID
     * @return string
     */
    public function cancelFragmentUpload()
    {
        try{
            $ossClient = $this->init();
            $res=$ossClient->abortMultipartUpload($this->OssBucket, $this->object, $this->uploadId);
        } catch(OssException $e) {
            return $e->getMessage();
        }
        dump($res);exit;
    }

    /**
     * 文件流上传
     * @param string $fileContent
     * @param string|null $key
     * @return bool|mixed
     */
    public function stream(string $fileContent, string $key = null)
    {
        try {
            if (!$key) {
                $key = $this->saveFileName();
            }
            $uploadInfo = $this->init()->putObject($this->OssBucket, $key, $fileContent);
            if (!isset($uploadInfo['info']['url'])) {
                return self::setErrorInfo('Upload failure');
            }
            return [
                'url' => $uploadInfo['info']['url'],
                'key' => $key
            ];
        } catch (Throwable $e) {
            return self::setErrorInfo($e);
        }
    }

    /**
     * 删除指定资源
     * @param 资源key
     * @return array
     * */
    public function delOssFile($key)
    {
        try {
            return $this->init()->deleteObject($this->OssBucket, $key);
        } catch (\Exception $e) {
            return self::setErrorInfo($e);
        }
    }

    /**
     * 获取签名
     * @param string $callbackUrl
     * @param string $dir
     * @return string
     */
    public function getSignature($callbackUrl = '', $dir = '')
    {

        $base64CallbackBody = base64_encode(json_encode([
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        ]));

        $policy = json_encode([
            'expiration' => $this->gmtIso8601(time() + 30),
            'conditions' =>
                [
                    [0 => 'content-length-range', 1 => 0, 2 => 1048576000],
                    [0 => 'starts-with', 1 => '$key', 2 => $dir]
                ]
        ]);
        $base64Policy = base64_encode($policy);
        $signature = base64_encode(hash_hmac('sha1', $base64Policy, $this->AccessKeySecret, true));
        return [
            'accessid' => $this->AccessKey,
            'host' => $this->uploadUrl,
            'policy' => $base64Policy,
            'signature' => $signature,
            'expire' => time() + 30,
            'callback' => $base64CallbackBody
        ];
    }

    /**
     * 获取ISO时间格式
     * @param $time
     * @return string
     */
    protected function gmtIso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    /**
     * 获取防盗链信息
     * @param string $bucket
     * @return RefererConfig
     * @throws \OSS\Core\OssException
     */
    public function getBucketReferer($bucket = '')
    {
        return $this->init()->getBucketReferer($bucket ? $bucket : $this->OssBucket);
    }

    /**
     * 清除防盗链
     * @param string $bucket
     * @return \OSS\Http\ResponseCore
     * @throws \OSS\Core\OssException
     */
    public function deleteBucketReferer($bucket = '')
    {
        $refererConfig = new RefererConfig();
        return $this->init()->putBucketReferer($bucket ? $bucket : $this->OssBucket, $refererConfig);
    }

}
