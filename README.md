# Xunsearch for PHP

---
[![](https://travis-ci.org/ShaoZeMing/translate.svg?branch=master)](https://travis-ci.org/ShaoZeMing/translate) 
[![](https://img.shields.io/packagist/v/ShaoZeMing/translate.svg)](https://packagist.org/packages/shaozeming/translate) 
[![](https://img.shields.io/packagist/dt/ShaoZeMing/translate.svg)](https://packagist.org/packages/stichoza/shaozeming/translate)


## Installing

```shell
$ composer require shaozeming/xunsearch -v
```

### configuration 

```
// config/demo.ini
project.name = teachers
project.default_charset = utf-8
server.index = 127.0.0.1:8383
server.search = 127.0.0.1:8384

[id]
type = id

[email]
index = mixed

[name]
index = mixed

[lesson]
index = mixed

```


## Usage


```php
use ShaoZeMing\Xunsearch\TranslateService;

$config = include($youerpath.'/translate.php')

$obj = new TranslateService($config);
$result = $obj->translate('你知道我对你不仅仅是喜欢');
print_r($result);



```


Example:

```php
 // 动态更改翻译服务商
 $config = include($youerpath.'/translate.php')
 $obj = new TranslateService($config);
 $obj->setDriver('baidu')->translate('你知道我对你不仅仅是喜欢');
 print_r($result);
 //You know I'm not just like you
 
 // 动态更改语种
 
 $from = 'en';
 $to = 'zh';
 $result =  $obj->setDriver('baidu')->setFromAndTo($from,$to)->translate('I love you.');
print_r($result);


```

## License

MIT

