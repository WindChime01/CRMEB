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


namespace service;

class UploadService
{

    private static $uploadStatus;

    //上传图片的大小 2MB 单位字节
    private static $imageValidate = ['size' => 2097152, 'ext' => 'jpg,jpeg,png,gif', 'mime' => 'image/jpeg,image/gif,image/png'];

    private static $fileExt = ['pem', 'mp3', 'wma', 'wav', 'amr', 'mp4', 'key', 'xlsx'];//上传文件后缀类型

    private static $fileMime = ['text/plain', 'audio/mpeg', 'application/x-x509-ca-cert', 'application/octet-stream', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']; //上传文件类型

    /**
     * 初始化
     */
    private static function init()
    {
        self::$uploadStatus = new \StdClass();
    }

    /**
     * 返回失败信息
     * @param $error
     * @return mixed
     */
    protected static function setError($error)
    {
        self::$uploadStatus->status = false;
        self::$uploadStatus->error = $error;
        return self::$uploadStatus;
    }

    /**
     * 返回成功信息
     * @param $path
     * @param \think\File $fileInfo
     * @return mixed
     */
    protected static function successful($path, \think\File $fileInfo)
    {
        $filePath = DS . $path . DS . $fileInfo->getSaveName();
        self::$uploadStatus->filePath = self::pathToUrl($filePath);
        self::$uploadStatus->fileInfo = $fileInfo;
        self::$uploadStatus->uploadPath = $path;
        self::$uploadStatus->dir = $filePath;
        self::$uploadStatus->status = true;
        return self::$uploadStatus;
    }

    /**
     * 检查上传目录不存在则生成
     * @param $dir
     * @return bool
     */
    protected static function validDir($dir)
    {
        return is_dir($dir) == true || mkdir($dir, 0777, true) == true;
    }

    /**
     * 开启/关闭上出文件验证
     * @param bool $bool
     */
    protected static function autoValidate($bool = false)
    {
        self::$autoValidate = $bool;
    }

    /**
     * 生成上传文件目录
     * @param $path
     * @param null $root
     * @return string
     */
    protected static function uploadDir($path, $root = null)
    {
        if ($root === null) $root = 'uploads';
        return $root . DS . $path;
    }

    /**
     * 单图上传
     * @param string $fileName 上传文件名
     * @param string $path 上传路径
     * @param bool $moveName 生成文件名
     * @param bool $autoValidate 是否开启文件验证
     * @param null $root 上传根目录路径
     * @param string $rule 文件名自动生成规则
     * @return mixed
     */
    public static function image($fileName, $path, $moveName = true, $autoValidate = true, $root = null, $rule = 'uniqid')
    {
        self::init();
        $path = self::uploadDir($path, $root);
        $dir = ROOT_PATH . 'public/' . $path;
        if (!self::validDir($dir)) return self::setError('生成上传目录失败,请检查权限!');
        if (!isset($_FILES[$fileName])) return self::setError('上传文件不存在!');
        $file = request()->file($fileName);
        if ($autoValidate) $file = $file->validate(self::$imageValidate);
        $fileInfo = $file->rule($rule)->move($dir, $moveName);
        if (false === $fileInfo) return self::setError($file->getError());
        return self::successful($path, $fileInfo);
    }

    /**
     * 文件上传
     * @param string $fileName 上传文件名
     * @param string $path 上传路径
     * @param bool $moveName 生成文件名
     * @param bool $autoValidate 验证规则 [size:1024,ext:[],type:[]]
     * @param null $root 上传根目录路径
     * @param string $rule 文件名自动生成规则
     * @return mixed
     */
    public static function file($fileName, $path, $moveName = true, $autoValidate = [], $root = null, $rule = 'uniqid')
    {
        self::init();
        $path = self::uploadDir($path, $root);
        $dir = ROOT_PATH . 'public' . DS . $path;
        if (!self::validDir($dir)) return self::setError('生成上传目录失败,请检查权限!');
        if (!isset($_FILES[$fileName])) return self::setError('上传文件不存在!');
        $uploaded_name = $_FILES[$fileName]['name'];
        $uploaded_ext = substr($uploaded_name, strrpos($uploaded_name, '.') + 1);
        $uploaded_type = $_FILES[$fileName]['type'];
        $uploaded_size = $_FILES[$fileName]['size'];
        $extension = strtolower(pathinfo($uploaded_name, PATHINFO_EXTENSION));
        if (strtolower($extension) === 'php' || !$extension) return self::setError('上传文件非法!');
        if (in_array(strtolower($uploaded_ext), self::$fileExt) && ($uploaded_size < 2097152) && in_array($uploaded_type, self::$fileMime)) {
            $file = request()->file($fileName);
            if (count($autoValidate) > 0) $file = $file->validate($autoValidate);
            $fileInfo = $file->rule($rule)->move($dir, $moveName);
            if (false === $fileInfo) return self::setError($file->getError());
            return self::successful($path, $fileInfo);
        } else {
            //无效文件
            return self::setError('上传文件非法或文件太大!');
        }
    }

    public static function pathToUrl($path)
    {
        return trim(str_replace(DS, '/', $path), '.');
    }

    public static function openImage($filePath)
    {
        return \think\Image::open($filePath);
    }


    /**
     * 图片压缩
     *
     * @param string $filePath 文件路径
     * @param int $ratio 缩放比例 1-9
     * @param string $pre 前缀
     * @return string 压缩图片路径
     */
    public static function thumb($filePath, $ratio = 8, $pre = 's_')
    {
        $filePath = '.' . ltrim($filePath, '.');
        $img = self::openImage($filePath);
        $width = $img->width() * $ratio / 10;
        $height = $img->height() * $ratio / 10;
        $dir = dirname($filePath);
        $fileName = basename($filePath);
        $savePath = $dir . DS . $pre . $fileName;
        $img->thumb($width, $height)->save($savePath);
        return ltrim($savePath, '.');
    }
}
