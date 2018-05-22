## 简介

预排序遍历树算法 (modified preorder tree traversal algorithm) 的 Laravel / lumen 实现。

假定使用的模型名为 `Tree` ，对应表应至少包含下列字段，字段类型建议为无符号整数。
* `id ` 为主键
* `pid` 为父级的 `id` ,此项在 `MPTT` 算法中非必须，但这个扩展中必须有。
* `lft` 为左值。
* `rgt` 为右值。
* `lvl` 为层级，此项在 `MPTT` 算法中非必须，但这个扩展中必须有。

其他的 `name` , `title` 等自行添加，可以使用 `created_at` , `updated_at` ,不使用软删除 `deleted_at`。

观察者监听创建（即新增节点）`created` 和删除 `deleting` ，自动维护其左右值，层级等。

`trit` 提供以下模型方法:
* `ChildCount` 返回其子孙数量，不包含当前节点本身，包含其下所有节点总数量。
* `SonCount` 返回其子代数量，不包含当前节点，仅有下一级节点数量。
* `Path` 返回从根节点到当前节点的路径（不包含根节点），注意其 `lvl` 表示对应层级。


## 安装

使用  `composer` 安装，执行如下命令：

    composer require qufo/mptt


## 使用方法

1.  在模型文件中，引入 `MPTTModel` trait 。形如:
```
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Qufo\MPTT\MPTTModel;

class Tree extends Model
{
    use MPTTModel;
    
}

```

2.  在 `App\Providers\AppServiceProvider` 的 `boot` 方法中注册观察者：
```
<?php

namespace App\Providers;

use App\Tree;
use Illuminate\Support\ServiceProvider;
use Qufo\MPTT\MPTTObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Tree::observe(MPTTObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

```


## License

MIT
