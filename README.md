# think-laravel-orm

喜欢thinkphp6的轻量和目录结构？又想使用laravel框架中更强大更新频率更高的orm来操作数据库？


laravel中使用orm库是[illuminate/database](https://github.com/illuminate/database)


# 安装

分两步走,毕竟是集成别人的库，多少有点兼容问题，更何况thinkphp6和它如此相似的情况下🤭

## 1.安装think-laravel-orm

```
composer require yzh52521/think-laravel-orm
```

安装完毕后运行`php think`可以看到新增一个指令`make:laravel-model`

~~~
 make
  make:command              Create a new command class
  make:controller           Create a new resource controller class
  make:event                Create a new event class
  make:laravel-model        Create a new laravel model class
  make:listener             Create a new listener class
  make:middleware           Create a new middleware class
  make:model                Create a new model class
  make:service              Create a new Service class
  make:subscribe            Create a new subscribe class
  make:validate             Create a validate class
~~~

## 2.解决兼容

主要是处理助手函数的兼容问题，因为tp6内置的助手函数[topthink/think-helper](https://github.com/top-think/think-helper)有
很多和[illuminate/database](https://github.com/illuminate/database)包里面加载的助手函数冲突
所以需要通过特殊的手段提高一下不同函数的权重才能保证正常使用。

我们需要借助[composer-include-files](https://github.com/funkjedi/composer-include-files)包来帮助我
们完成这个动作(tips:这个包非常有用)。

### 1.安装
```
composer require funkjedi/composer-include-files
```
安装过程会询问你是否向composer.json文件写入新内容。y


### 2.创建重写文件

接着在项目`app`目录下新建`Override.php`,当然文件名随你,把下面的内容复制进去

```php
<?php


if (!function_exists('env')) {
    /**
     * 提高tp框架的助手函数env的权重，保证tp框架的正常运行
     * 获取环境变量值
     * @access public
     * @param string $name    环境变量名（支持二级 .号分割）
     * @param string $default 默认值
     * @return mixed
     */
    function env(string $name = null, $default = null)
    {
        return \think\facade\Env::get($name, $default);
    }
}




if (! function_exists('collect')) {
    /**
     * 提高laravel-orm的collect助手函数的权重,使得laravel orm正常运行
     * Create a collection from the given value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Collection
     */
    function collect($value = null)
    {
        return new \Illuminate\Support\Collection($value);
    }
}
```


### 3.更新composer.json内容

在composer.json文件中添加如下内容,`"app/Override.php"`就是重写文件

```php
"extra": {
    "include_files": [
        "app/Override.php"
    ]
}
```



### 4.更新composer自动加载内容

```php
composer dump
```

执行完这一步，此时的重写文件的函数的优先级已经是最高级别。


# 配置文件

安装完毕后会在config目录下生成一个配置文件`laravelorm.php`

```php
<?php

return [
    // 数据库类型
    'driver' => env('database.type', 'mysql'),

    // 服务器地址
    'host' => env('database.hostname', '127.0.0.1'),

    // 数据库名
    'database' => env('database.database', ''),

    // 用户名
    'username' => env('database.username', 'root'),

    // 密码
    'password' => env('database.password', ''),

    // 数据库编码默认采用utf8mb4
    'charset' => env('database.charset', 'utf8mb4'),

    // 数据库排序的规则默认采用utf8mb4_unicode_ci
    'collation' => env('database.collation', 'utf8mb4_unicode_ci'),

    // 数据库表前缀
    'prefix' => env('database.prefix', ''),
];


```

# 使用

只需要注意laravel的表名是复数形式问题。

## 查询构造器

```php
use Illuminate\Database\Capsule\Manager as DB;

$users = DB::table('users')->where('id', '>', 1)->get();
```




## 模型

```php
php think make:laravel-model User
```

```php
<?php
declare (strict_types = 1);

namespace app\model;

use Illuminate\Database\Eloquent\Model;


class User extends Model
{

   protected $table = 'user';

}

```

```php
$users = User::all();

dd($users);
```


# 文档

- https://learnku.com/docs/laravel/9.x/eloquent/12251#d66211
- https://laravel.com/docs/9.x/eloquent



# 反馈

如果有任何建议和问题欢迎在仓库留言