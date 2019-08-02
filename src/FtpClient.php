<?php
namespace Nova\FtpClient;
use Nova\FtpClient\Exceptions\FtpException;

class FtpClient {

    /**
     * 本地上传错误信息
     * @var string
     */

    /**
     * FTP连接
     * @var resource
     */
    private $link;

    private $config = array(
        'host'     => '', //服务器
        'port'     => 21, //端口
        'timeout'  => 90, //超时时间
        'username' => '', //用户名
        'password' => '', //密码
    );

    private static $_instance;

    public function __construct(){

    }

    public static function getInstance(){
        $className = get_called_class();
        if(!isset(self::$_instance[$className])){
            self::$_instance[$className] = new $className();
        }
        return self::$_instance[$className];
    }

    public function isDir($directory){
        $pwd = @ftp_pwd($this->link);
        $directory = $pwd . '/' . $directory . '/';
        if(!@ftp_chdir($this->link,$directory)){
            return false;
        }
        @ftp_chdir($this->link,$pwd);
        return true;
    }

    public function mkdir($directory){
        $rs = true;
        if(!$this->isDir($directory)){
            $pwd = @ftp_pwd($this->link);
            $parts = explode('/',$directory);
            foreach ($parts as $part) {
                if(empty($part)){
                    continue;
                }
                if(!@ftp_chdir($this->link,$part)){
                    @ftp_mkdir($this->link,$part);
                    $rs = @ftp_chdir($this->link,$part);
                }
            }
            @ftp_chdir($this->link,$pwd);
        }
        return $rs;
    }


    public function put($localFile, $ftpFile,$replace=true,$mode = FTP_BINARY,$startpos = 0) {
        if(!is_file($localFile))
            throw new FtpException("localFile " . $localFile . " is not exists");
        if(!$this->isDir(dirname($ftpFile)) && !$this->mkdir(dirname($ftpFile),true))
            throw new FtpException("Ftp directory " . dirname($ftpFile) . " is not exists");

        if(!$replace && ftp_size($this->link,$ftpFile) > -1){
            return true;
        }

        /* 移动文件 */
        if (!@ftp_put($this->link, $ftpFile, $localFile, $mode,$startpos)) {
            throw new FtpException("put failure");
        }
        return true;
    }

    public function puts($localDirectory,$ftpDirectory,$replace=true,$mode=FTP_BINARY){
        if(!$this->isDir($ftpDirectory) && !$this->mkdir($ftpDirectory,true))
            throw new FtpException("Ftp directory " . $ftpDirectory . " is not exists");
        if(!is_dir($localDirectory)){
            throw new FtpException("Local directory " . $localDirectory . "is not exists");
        }
        $d = dir($localDirectory);
        while($file = $d->read()){
            if($file != "." && $file != ".."){
                if(!$replace && ftp_size($this->link,$ftpDirectory . "/" . $file) > -1){
                    continue;
                }else{
                    @ftp_put($this->link, $ftpDirectory . "/" . $file, $localDirectory . "/" . $file, $mode);
                }
            }
        }
        return true;
    }

    public function get($localFile,$ftpFile,$mode = FTP_BINARY,$resumepos = 0){
        if(!is_dir(dirname($localFile)) && !mkdir(dirname($localFile,'766',true))){
            throw new FtpException("Local directory " . dirname($localFile) ." is not exsits");
        }
        if(!(ftp_size($this->link,$ftpFile) > -1)){
            throw new FtpException("Ftp file is not exsits");
        }
        return @ftp_get($this->link,$localFile,$ftpFile,$mode,$resumepos);
    }

    public function gets($localDirectory,$ftpDirectory,$replace = true,$mode = FTP_BINARY){
        if(!is_dir($localDirectory) && !mkdir($localDirectory,'766',true)){
            throw new FtpException("Local directory " . $localDirectory ." is not exsits");
        }
        if(!$this->isDir($ftpDirectory)){
            throw new FtpException("Ftp directory " . $ftpDirectory . " is not exists");
        }
        @chdir($localDirectory);
        $contents = @ftp_nlist($this->link,$ftpDirectory);

        foreach ($contents as $file) {
            if($file != "." && $file != ".."){
                if(!$replace && is_file($file)){
                    continue;
                }else{
                @ftp_get($this->link,$file,$ftpDirectory . "/" . $file,$mode);
                }
            }
        }
        return true;
    }

    /**
     * 连接到FTP服务器
     * @return boolean true-登录成功，false-登录失败
     */
    public function connect($host, $port = 21,$username = 'anonymous',$password='', $timeout = 90){
        $this->link = @ftp_connect($host, $port, $timeout);
        if($this->link) {
            if (ftp_login($this->link, $username, $password)) {
                ftp_pasv($this->link ,true);
               return $this;
            } else {
                throw new FtpException("无法登录到FTP服务器：username - {$username}");
            }
        } else {
            throw new FtpException("无法连接到FTP服务器：{$host}");
        }
    }

    public function __call($method,$args){
        $fun = substr($method,0,4) == 'ftp_' ? $method : 'ftp_' . $method;
        if(function_exists($fun)){
            array_unshift($args,$this->link);
            return call_user_func_array($fun,$args);
        }else{
            throw new FtpException("Not exists function：${$fun}");
        }
    }

    public function close(){
        ftp_close($this->link);
    }

}