<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";
use Nova\FtpClient\FtpClient;

try{
    $ftp = FtpClient::getInstance()->connect("192.168.111.201",21,"ftpuser","123456",90,true);
    $rs = FtpClient::getInstance()->put("./11.txt","./fireware/12.txt");
    FtpClient::getInstance()->close();
}catch(\Exception $e){
    var_dump($e);
}


try{
    $ftp = FtpClient::getInstance()->connect("192.168.111.201",21,"ftpuser","123456",90,true);
    FtpClient::getInstance()->get("./11_tmp.txt","/fireware/12.txt");
    FtpClient::getInstance()->close();
}catch(\Exception $e){
    var_dump($e);
}