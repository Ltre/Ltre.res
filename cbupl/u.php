<?php
/**
 * 图片上传类
 * 依赖于：
 *      file/dwImagic
 */
class ImgUpload {
    
    protected $_ret = array(
        'code' => 0,        //状态码
        'msg' => '',        //提示
        'url' => '',        //图片链接
        'sha1' => '',       //SHA-1签名
        'width' => 0,       //宽度
        'height' => 0,      //高度
        'mimeType' => '',   //媒体类型
        'fileExt' => '',    //文件扩展名
        'fileSize' => 0,    //文件字节数
        'fileName' => '',   //客户端文件命名
        'saveFile' => '',   //实际存储文件名，如201508.jpg
        'saveDir' => '',    //实际存储文件相对WEB根目录路径，如res/img/default/2015/09/14
        'fullFile' => '',   //实际存储文件的文件全路径，如/home/wwwroot/res.miku.us/res/img/default/2015/09/14/201508.jpg
        'fullDir' => '',    //实际存储文件的目录全路径，如/home/wwwroot/res.miku.us/res/img/default
    );
    
    //条件限制
    protected $_limit = array(
        'minWidth' => 0,            //最小宽度
        'minHeight' => 0,           //最小高度
        'maxSize' => 2097152,       //最大2M
        'proportion' => array(0, 0),//宽高比
    );
    
    protected function _checkType($file){
        switch (strtolower($file['type'])) {
            case 'image/jpeg': $this->_ret['fileExt'] = 'jpg'; break;
            case 'image/gif': $this->_ret['fileExt'] = 'gif'; break;
            case 'image/png': $this->_ret['fileExt'] = 'png'; break;
            case 'image/bmp': $this->_ret['fileExt'] = 'bmp'; break;
            case 'image/tiff': $this->_ret['fileExt'] = 'tif'; break;
        }
        if (! $this->_ret['fileExt']) {
            $this->_ret['msg'] = "illegal file type [{$file['type']}]";
            return false;
        }
        return true;
    }
    
    //【待完善】捕获其它错误
    protected function _checkError($file){
        if (UPLOAD_ERR_OK == $file['error']) return true;
        $this->_ret['msg'] = "上传出错，错误码{$file['error']}。"; // ...各种不成功的情况
        switch ($file['error']) {
        	case UPLOAD_ERR_INI_SIZE:
        	    $this->_ret['msg'] .= 'The uploaded file exceeds the value of the upload_max_filesize option in the php.ini !';
        	    break;
        	case UPLOAD_ERR_FORM_SIZE:
        	    $this->_ret['msg'] .= 'The uploaded file size exceeds the value specified by the MAX_FILE_SIZE option in the HTML form !';
        	    break;
        	case UPLOAD_ERR_PARTIAL:
        	    $this->_ret['msg'] .= 'Only part of the file is uploaded !';
        	    break;
        	case UPLOAD_ERR_NO_FILE:
        	    $this->_ret['msg'] .= 'No files have been uploaded';
        	    break;
        	case UPLOAD_ERR_NO_TMP_DIR:
        	    $this->_ret['msg'] .= 'Tmp dir is not found !';
        	    break;
        	case UPLOAD_ERR_CANT_WRITE:
        	$this->_ret['msg'] .= 'Error in writting file !';
        	break;
        }
        return false;
    }
    
    protected function _checkName($file){
        if (preg_match('/\\0|\:|\/|\\|\?|\^|\*|\<|\>|\$/', $file['name'])) {
            $this->_ret['msg'] = 'illegal file name';
            return false;
        }
        $this->_ret['fileName'] = $file['name'];
        return true;
    }
    
    protected function _checkTmpName($file){
        if (is_uploaded_file($file['tmp_name'])) return true;
        $this->_ret['msg'] = 'found upload attack..';
        return false;
    }
    
    //检测图片内容
    protected function _checkImgData($file){
        @$getImgSize = getimagesize($file['tmp_name']);
        if (false === $getImgSize) {
            $this->_ret['msg'] = 'file is not an image';
            return false;
        }
        
        list($width, $height, $type, $attr) = $getImgSize;
        $this->_ret['width'] = $width;
        $this->_ret['height'] = $height;
        $imgTypes = array(
            IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_SWF,
            IMAGETYPE_PSD, IMAGETYPE_BMP, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM,
            IMAGETYPE_JPC, IMAGETYPE_JP2, IMAGETYPE_JPX, IMAGETYPE_JB2,
            IMAGETYPE_SWC, IMAGETYPE_IFF, IMAGETYPE_WBMP, IMAGETYPE_XBM,
            IMAGETYPE_ICO,
        );
        if (in_array($type, $imgTypes)) {
            $this->_ret['mimeType'] = image_type_to_mime_type($type);
            if ($type == IMAGETYPE_GIF) {
                $gif = file_get_contents($file['tmp_name']);
                if (preg_match('/<\/?(script){1}>/i',$gif)) {
                    $this->_ret['msg'] = 'illegal gif data';
                    return false;
                }
            }
            return true;
        } else {
            $this->_ret['msg'] = 'illegal mimetype, not an image';
            return false;
        }
    }
    
    //检测文件大小
    protected function _checkSize($file){
        $this->_ret['fileSize'] = $file['size'];
        if ($file['size'] > $this->_limit['maxSize']) {
            $this->_ret['msg'] = "file size is greater than {$this->_limit['maxSize']} Bytes";
            return false;
        }
        return true;
    }
    
    //检测图片尺寸、比例
    protected function _checkDimension($file){
        $minW = $this->_limit['minWidth'];
        $minH = $this->_limit['minHeight'];
        if ($minW > 0 && $minH > 0 && ($this->_ret['width'] < $minW || $this->_ret['height'] < $minH)) {
            $this->_ret['msg'] = "图片宽高应不低于{$this->_limit['minWidth']}×{$this->_limit['minHeight']}";
            return false;
        }
        list($pW, $pH) = $this->_limit['proportion'];
        if ($pH > 0 && $pW > 0) {
            $accuracy = abs($this->_ret['width'] / $this->_ret['height'] - $pW / $pH);
            if ($accuracy > 0.2) {
                $this->_ret['msg'] = "图片宽高比要接近{$pW}:{$pH}";
                return false;
            }
        }
        return true;
    }
    
    //计算SHA1
    protected function _calcSha1($file){
        $this->_ret['sha1'] = sha1_file($file['tmp_name']);
    }
    
    protected function _check($file){
        if (! $this->_checkError($file)) {
            $this->_ret['code'] = -1;
            return false;
        }
        if (! $this->_checkTmpName($file)) {
            $this->_ret['code'] = -2;
            return false;
        }
        if (! $this->_checkType($file)) {
            $this->_ret['code'] = -3;
            return false;
        }
        if (! $this->_checkName($file)) {
            $this->_ret['code'] = -4;
            return false;
        }
        if (! $this->_checkImgData($file)) {
            $this->_ret['code'] = -5;
            return false;
        }
        if (! $this->_checkSize($file)) {
            $this->_ret['code'] = -6;
            return false;
        }
        $this->_calcSha1($file);
        return true;
    }
    
    //可在处理图片前设定限制
    public function setLimit($limit = array()){
        foreach ($limit as $k => $v) {
            if (isset($limit[$k]) && isset($this->_limit[$k])) {
                $this->_limit[$k] = $v;
            }
        }
    }
    
}

/**
 * 图片上传客户端
 */
class ImgUploadClient extends ImgUpload {
    protected $_uploadHost;
    
    protected $_imgGroupDir;
    
    function __construct($imgGroupDir = '', $uploadHost = ''){
        @$this->_imgGroupDir = $imgGroupDir ?: $GLOBALS['imgUploadSetup']['imgGroupDir'] ?: 'default/';
        @$this->_uploadHost = $uploadHost ?: $GLOBALS['api']['imgUploadHost'] ?: 'http://up.res.miku.us/cbupl/upimg.php';//使用A记录对应up子域名，同res.miku.us共享WEB目录
    }
    
    protected function _postFile($url, $post = array(), $timeout = 59){
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_TIMEOUT, $timeout);
        //暂时解决5.0到5.6的上传兼容问题，详见http://www.tuicool.com/articles/Fvq6Nj
        foreach ($post as $k => $v) {
            if (preg_match('/^\@(.+);type\=([^;]+)/', $v, $matches) && class_exists('\CURLFile', false)) {
                //$post[$k] = new CURLFile(realpath(preg_replace('/^\@|;.*/', '', $v)));
                $post[$k] = new CURLFile(realpath($matches[1]), $matches[2]);
            }
        }
        curl_setopt($c, CURLOPT_POSTFIELDS, $post);
        $data = curl_exec($c);
        curl_close($c);
        return $data;
    }
    
    //按需截图(注意：调用后，需unlink所生成图片)
    protected function _resize($file, $width = 0, $height = 0){
        if (! $width || ! $height) {
            return $file['tmp_name'];
        }
        if ($this->_ret['width'] < $width || $this->_ret['height'] < $height){
            return $file['tmp_name'];//图片过小
        }
        $thumbFile = DI_CACHE_PATH .(microtime(true)*10000).'.'.$this->_ret['fileExt'];
        move_uploaded_file($file['tmp_name'], $thumbFile);
        import('file/dwImagic');
        $im = new dwImagick($thumbFile);
        $im->setCutType(1);
        $im->setDstImage($thumbFile);
        $im->thumbImage($width, $height);
        return $thumbFile;
    }
    
    /*
     * 通过接口上传处理程序
     * @param array $_FILES中具体的某个表单域
     */
    function up($file, $width = 0, $height = 0){
        if (! $this->_check($file)) return $this->_ret;
        
        $file['tmp_name'] = $this->_resize($file, $width, $height);
        @$ret = $this->_postFile($this->_uploadHost, array(
            'tu' => '@'.$file['tmp_name'].';type='.$this->_ret['mimeType'],
            'fileName' => $file['name'],
            'imgGroupDir' => $this->_imgGroupDir,
        ));
        @unlink($file['tmp_name']);
        if (empty($ret)) {
            $this->_ret['code'] = -7;
            $this->_ret['msg'] = 'upload failed';
        } else {
            $this->_ret = json_decode($ret, true);
        }
        
        return $this->_ret;
    }
    
    //客户端调用接口测试用例
    static function testClient(){
        $client = new ImgUploadClient();
        $client->setLimit(array('maxSize' => 5242880));//5MB
        $ret = $client->up($_FILES['tu']);
        dump($ret);
    }
}


/**
 * 图片上传服务端
 * 要求域名要与web目录完全相同，如res.miku.us对应/home/wwwroot/res.miku.us
 */
class ImgUploadServer extends ImgUpload {
    
    protected $_imgBaseDir;
    
    protected $_imgGroupDir;
    
    protected $_imgBaseDomainName;
    
    protected $_wwwRoot;
    
    function __construct($imgGroupDir = '', $imgBaseDir = '', $imgBaseDomainName = '', $wwwRoot = ''){
        @$this->_imgGroupDir = rtrim($imgGroupDir ?: $GLOBALS['imgUploadSetup']['imgGroupDir'] ?: 'default', '/');
        @$this->_imgBaseDir = rtrim($imgBaseDir ?: $GLOBALS['imgUploadSetup']['imgBaseDir'] ?: 'res/img', '/');
        @$this->_imgBaseDomainName = rtrim($imgBaseDomainName ?: $GLOBALS['imgUploadSetup']['imgBaseDomainName'] ?: 'res.miku.us', '/');
        @$this->_wwwRoot = rtrim($wwwRoot ?: $GLOBALS['imgUploadSetup']['wwwRoot'] ?: '/home/wwwroot', '/');
    }
    
    protected function _mkdirs($dir, $mode = 0777){
        if (! is_dir($dir)) {
            $this->_mkdirs(dirname($dir), $mode);
            return @mkdir($dir, $mode);
        }
        return true;
    }
    
    protected function _calcPaths(){
        //如 192942-782-hex354.jpg
        $this->_ret['saveFile'] = date('His-') . ceil(microtime(true) % 1000) . '-hex' . dechex(rand(1, 1000)) . '.' . $this->_ret['fileExt'];
        //如 res/img/default/2015/09/13
        $this->_ret['saveDir'] = "{$this->_imgBaseDir}/{$this->_imgGroupDir}/" . date('Y').'/'.date('m').'/'.date('d');
        //如 /home/wwwroot/res.miku.us/res/img/default/2015/09/13
        $this->_ret['fullDir'] = "{$this->_wwwRoot}/{$this->_imgBaseDomainName}/{$this->_ret['saveDir']}";
        //如 /home/wwwroot/res.miku.us/res/img/default/2015/09/13/192942-782-hex354.jpg
        $this->_ret['fullFile'] = "{$this->_ret['fullDir']}/{$this->_ret['saveFile']}";
    }
    
    protected function _moveUploadFile($file){
        $this->_calcPaths();
        $this->_mkdirs($this->_ret['fullDir']);
        if (! move_uploaded_file($file['tmp_name'], $this->_ret['fullFile'])) {
            $this->_ret['code'] = -8;
            $this->_ret['msg'] = 'remote move uploaded file failed';
        } else {
            $this->_ret['code'] = 0;
            $this->_ret['url'] = "http://{$this->_imgBaseDomainName}/{$this->_ret['saveDir']}/{$this->_ret['saveFile']}";
            $this->_ret['msg'] = 'remote upload success';
        }
    }
    
    //服务端处理上传，部署在res.miku.us/cbupl/upimg.php。
    function up($file){
        if (! $this->_check($file)) return $this->_ret;
        $this->_moveUploadFile($file);
        return $this->_ret;
    }
    
    //服务接口编写上传测试用例
    static function testServer(){
        $server = new ImgUploadServer($_REQUEST['imgGroupDir'] ?: '');
        $server->setLimit(array('maxSize' => 5242880));//5MB
        $ret = $server->up($_FILES['tu']);
        $ret['fileName'] = $_REQUEST['fileName'];
        echo json_encode($ret);
    }
}

