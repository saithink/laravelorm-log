<div style="padding:18px;max-width: 1024px;margin:0 auto;background-color:#fff;color:#333">
<h1>Laravel Orm日志记录</h1>

基于 <a href="https://www.workerman.net/webman" target="_blank">webman</a> 使用Laravel ORM时的日志记录工具

<h1>安装</h1>

composer环境的安装命令如下

``` bash
composer require saithink/laravelorm-log
```

安装之前确保已安装webman

<h1>配置文件</h1>

基础配置：<code>config/plugin/saithink/laravelorm-log/app.php</code>

```php
return [
    // 是否启用日志记录
    'enable' => true,
    // 是否输出到控制台
    'console'   => false,
    // 是否记录到日志文件
    'file'  => true,
];
```
</div>