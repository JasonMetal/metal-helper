# metal-helper
PHP项目常用助手函数，涵盖了Array,String,Time,Server,File,Number,Image,Byte,Other(ext)等",
- 修复一些函数
- 新增图片处理函数
- 新增字节处理函数
- 新增处理中文文字的函数
```PHP
#引入包
use metal\helper;
 
 public function index()
{

      $cnName = helper\ChineseName::getRandomCnName(2,'-');
        var_dump($cnName); // 费-欣澜

}

...

 






```
