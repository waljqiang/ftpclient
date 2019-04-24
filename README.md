# ftpclient
## put

```
require_once __DIR__ . "/vendor/autoload.php";
use Nova\FtpClient\FtpClient;

try{
    $ftp = FtpClient::getInstance()->connect("127.0.0.1",21,"user","password",90,true);
    FtpClient::getInstance()->put("/11.txt",/fireware/12.txt");
    FtpClient::getInstance()->close();
}catch(\Exception $e){
    var_dump($e);
}
```

## puts

```
require_once __DIR__ . "/vendor/autoload.php";
use Nova\FtpClient\FtpClient;

try{
    $ftp = FtpClient::getInstance()->connect("127.0.0.1",21,"user","password",90,true);
    FtpClient::getInstance()->puts("/11",/fireware");
    FtpClient::getInstance()->close();
}catch(\Exception $e){
    var_dump($e);
}
```

## get

```
require_once __DIR__ . "/vendor/autoload.php";
use Nova\FtpClient\FtpClient;

try{
    $ftp = FtpClient::getInstance()->connect("127.0.0.1",21,"user","password",90,true);
    FtpClient::getInstance()->get("/11.txt",/fireware/11.txt");
    FtpClient::getInstance()->close();
}catch(\Exception $e){
    var_dump($e);
}
```

## gets

```
require_once __DIR__ . "/vendor/autoload.php";
use Nova\FtpClient\FtpClient;

try{
    $ftp = FtpClient::getInstance()->connect("127.0.0.1",21,"user","password",90,true);
    FtpClient::getInstance()->gets("/11",/fireware/11");
    FtpClient::getInstance()->close();
}catch(\Exception $e){
    var_dump($e);
}
```

## 支持是否替换文件，ftp传输模式，复制内容位置，具体请看方法内部参数