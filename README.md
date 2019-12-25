# Larabread

An IDE-friendly Breadcrumbs package for Laravel.

## Install

```sh
composer require n1215/larabread
```

## Usage

### 1. Factory and method chain 

- routes/web.php

```
Route::get('path/to/page', 'AppController@page');
```

- app/Http/Controllers/AppController.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use N1215\Larabread\BreadcrumbListFactory;

class AppController
{
    public function page(BreadcrumbListFactory $factory): View
    {
        // create BreadcrumbList instance with BreadcrumbListFactory::make() and BreadcrumbList::add() method
        $breadcrumbs = $factory->make()
            ->add('Home', '/')
            ->add('Path', '/path')
            ->add('To', '/path/to')
            ->add('Page', '/path/to/page');

        // pass BreadcrumbList to a blade template. the variable name should be 'breadcrumbs'
        return view('page', compact('breadcrumbs'));
    }
}
```

- resources/views/page.blade.php

```html
<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Page</title>
</head>
<body>

// include default template
@include(config('larabread.templates.default'))

</body>
</html>
```

HTML is rendered with default template (using bootstrap4 css classes).

```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="/path">Path</a></li>
        <li class="breadcrumb-item"><a href="/path/to">To</a></li>
        <li class="breadcrumb-item active">Page</li>
    </ol>
</nav>
```

### 2. Trail classes
You can define breadcrumbs outside of controller by making POPO class called "Trail".

#### 2-1. Simple Trail

- app/Http/Breadcrumbs/AppTrail.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Breadcrumbs;

use N1215\Larabread\BreadcrumbList;
use N1215\Larabread\BreadcrumbListFactory;

class AppTrail
{
    /**
     * @var BreadcrumbListFactory
     */
    private $factory;

    // inject BreadcrumbListFactory
    public function __construct(BreadcrumbListFactory $factory)
    {
        $this->factory = $factory;
    }

    // create method which returns BreadcrumbList
    public function page(): BreadcrumbList
    {        
        return $this->factory->make()
            ->add('Home', '/')
            ->add('Path', '/path')
            ->add('To', '/path/to')
            ->add('Page', '/path/to/page');
    }
}

```

- app/Http/Controllers/AppController.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Breadcrumbs\AppTrail;
use Illuminate\Contracts\View\View;

class AppController
{
    // inject AppTrail
    public function page(AppTrail $trail): View
    {
        $breadcrumbs = $trail->page();
        return view('page', compact('breadcrumbs'));
    }
}
```

#### 2-2. Nested definition
You can reuse other BreadcrumbList by method chain

- app/Http/Breadcrumbs/AppTrail.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Breadcrumbs;

use N1215\Larabread\BreadcrumbList;
use N1215\Larabread\BreadcrumbListFactory;

class AppTrail
{
    /**
     * @var BreadcrumbListFactory
     */
    private $factory;

    public function __construct(BreadcrumbListFactory $factory)
    {
        $this->factory = $factory;
    }

    public function home(): BreadcrumbList
    {
        return $this->factory->make()->add('Home', '/');
    }

    public function path(): BreadcrumbList
    {
        // you can reuse other BreadcrumbList as a parent
        return $this->home()->add('Path', '/path');
    }

    public function to(): BreadcrumbList
    {
        return $this->path()->add('To', '/path/to');
    }

    public function page(): BreadcrumbList
    {
        return $this->to()->add('Page', '/path/to/page');
    }
}

```


- app/Http/Controllers/AppController.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Breadcrumbs\AppTrail;
use Illuminate\Contracts\View\View;

class AppController
{
    /**
     * @var AppTrail
     */
    private $trail;

    public function __construct(AppTrail $trail)
    {
        $this->trail = $trail;
    }

    public function home(): View
    {
        return view('home', ['breadcrumbs' => $this->trail->home()]);
    }
    
    public function path(): View
    {
        return view('path', ['breadcrumbs' => $this->trail->path()]);
    }

    public function to(): View
    {
        return view('to', ['breadcrumbs' => $this->trail->to()]);
    }

    public function page(): View
    {
        return view('to', ['breadcrumbs' => $this->trail->page()]);
    }
}

```

#### 2-3. Nested definitions by Constructor Injection
You can separate definitions into multiple Trail classes.

- app/Http/Breadcrumbs/HomeTrail.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Breadcrumbs;

use N1215\Larabread\BreadcrumbList;
use N1215\Larabread\BreadcrumbListFactory;

class HomeTrail
{
    /**
     * @var BreadcrumbListFactory
     */
    private $factory;

    public function __construct(BreadcrumbListFactory $factory)
    {
        $this->factory = $factory;
    }

    public function home(): BreadcrumbList
    {
        return $this->factory->make()->add('Home', '/');
    }
}

```

- app/Http/Breadcrumbs/PathTrail.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Breadcrumbs;

use N1215\Larabread\BreadcrumbList;

class PathTrail
{
    /**
     * @var HomeTrail
     */
    private $from;

    // inject HomeTrail in the constructor
    public function __construct(HomeTrail $from)
    {
        $this->from = $from;
    }

    public function path(): BreadcrumbList
    {
        return $this->from->home()->add('Path', '/path');
    }
}

```


Parent-Child relationship of Trail classes is resolved by the service container and autowiring.

- app/Http/Controllers/AppController.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Breadcrumbs\HomeTrail;
use App\Http\Breadcrumbs\PathTrail;
use Illuminate\Contracts\View\View;

class AppController
{
    public function home(HomeTrail $trail): View
    {
        return view('home', ['breadcrumbs' => $trail->home()]);
    }

    public function path(PathTrail $trail): View
    {
        return view('path', ['breadcrumbs' => $trail->path()]);
    }
}

```

### 3. Create BreadcrumbList in other ways.
Simple blog system example using Category and Post models.

- routes/web.php

```php
Route::get('home', 'AppController@home')->name('home');

Route::get('categories', 'CategoriesController@index')->name('categories.index');
Route::get('categories/{category}', 'CategoriesController@index')->name('categories.show');

Route::get('categories/{category}/posts', 'PostsController@index')->name('categories.posts.index');
Route::get('categories/{category}/posts/{post}', 'PostsController@show')->name('categories.posts.show');
```

- app/Models/Post.php
```php
<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Blog Post
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string $body
 * @property-read Category $category
 */
class Post extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

- app/Models/Category.php
```php
<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Blog Category
 * @property int $id
 * @property string $title
 * @property-read Collection<Post> $posts
 */
class Category extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
```


- app/Http/Breadcrumbs/HomeTrail.php

```php
<?php
declare(strict_types=1);

namespace App\Http\Breadcrumbs;

use N1215\Larabread\BreadcrumbList;
use N1215\Larabread\BreadcrumbListFactory;

class HomeTrail
{
    /**
     * @var BreadcrumbListFactory
     */
    private $factory;

    public function __construct(BreadcrumbListFactory $factory)
    {
        $this->factory = $factory;
    }

    public function home(): BreadcrumbList
    {
        return $this->factory->make()->add('Home', '/');
    }
}
```

- app/Http/Breadcrumbs/CategoriesTrail.php
```php
<?php
declare(strict_types=1);

namespace App\Http\Breadcrumbs;

use App\Models\Category;
use N1215\Larabread\BreadcrumbList;

class CategoriesTrail
{
    /**
     * @var RootTrail
     */
    private $from;

    public function __construct(RootTrail $from)
    {
        $this->from = $from;
    }

    public function index(): BreadcrumbList
    {
        return $this->from->home()
            ->add('Categories', route('categories.index'));
    }

    // any parameters can be passed to Trail methods
    public function show(Category $category): BreadcrumbList
    {
        return $this->index()
            ->add($category->title, route('categories.show', ['category' => $category->id]));
    }
}
```

- app/Http/Breadcrumbs/PostTrail.php
```php
<?php
declare(strict_types=1);

namespace App\Http\Breadcrumbs;

use App\Models\Category;
use App\Models\Post;
use N1215\Larabread\BreadcrumbList;

class PostsTrail
{
    /**
     * @var CategoriesTrail
     */
    private $from;

    public function __construct(CategoriesTrail $from)
    {
        $this->from = $from;
    }

    public function index(Category $category): BreadcrumbList
    {
        return $this->from->show($category)
            ->add('Posts', route('categories.posts.index'));
    }

    public function show(Post $post): BreadcrumbList
    {
        return $this->index($post->category)
            ->add($post->title, route('categories.posts.show', ['category' => $post->category_id,  'post' => $post->id]));
    }
}

```

- app/Http/Controllers/PostsController.php

You can make BreadcrumbList in 4 ways

- (1) use injected Trail class
- (2) use injected BreadcrumbManager clas
- (3) use Breadcrumbs Facade
- (4) use breadcrumbs() helper

The first argument of BreadcrumbManager::make() is an array of a Trail class name and its method name.
The second and subsequent are passed to Trail's method.

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Breadcrumbs\PostsTrail;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use N1215\Larabread\BreadcrumbManager;
use N1215\Larabread\Facades\Breadcrumbs;

class PostsController
{
    /**
     * @var PostsTrail
     */
    private $postsTrail;

    public function __construct(PostsTrail $postsTrail)
    {
        $this->postsTrail = $postsTrail;
    }

    public function index(int $categoryId): View
    {
        /** @var Category $category */
        $category = Category::query()->findOrFail($categoryId);
        $posts = $category->posts;

        $breadcrumbs = $this->postsTrail->index($category);

        return view('categories.index', compact('category', 'posts', 'breadcrumbs'));
    }

    public function show(int $categoryId, int $postId, PostsTrail $postsTrail, BreadcrumbManager $breadcrumbManager): View
    {
        /** @var Post $post */
        $post = Post::query()->where('category_id', $categoryId)->findOrFail($postId);

        // (1) make a BreadcrumbList instance from an injected instance of Trail class
        $breadcrumbs = $postsTrail->show($post);

        // (2) make a BreadcrumbLIst instance from an injected instance of BreadcrumbManager
        $breadcrumbs = $breadcrumbManager->make([PostsTrail::class, 'show'], $post);

        // (3) make a BreadcrumbList instance from Facade
        $breadcrumbs = Breadcrumbs::make([PostsTrail::class, 'show'], $post);

        // (4) make a BreadcrumbList instance from helper function
        $breadcrumbs = breadcrumbs()->make([PostsTrail::class, 'show'], $post);

        return view('categories.index', compact('post', 'breadcrumbs'));
    }
}

```


### 4. Make BreadcrumbList in Blade templates.
You can make BreadcrumbList directly in Blade templates.

```HTML
@php
    // using Facade
    $breadcrumbs = Breadcrumbs::make([\App\Http\Breadcrumbs\PostsTrail::class, 'show'], $post);

    // using helper
    $breadcrumbs = breadcrumbs()->make(\App\Http\Breadcrumbs\PostsTrail::class, 'show', $post);
@endphp

@include(config('larabread.templates.default'))

```

### 5. set current BreadcrumbsList

- resources/layouts/default.blade.php

Include the breadcrumbs template in your layout file.

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>@yield('title')</title>
    </head>
    <body>

    @include(config('larabread.templates.default'))

    <div>
        @yield('content')
    </div>
    </body>
</html>
```

Call BreadcrumbManager::set() in child template via Facade or helper.
Arguments are same as ones of BreadcrumbManager::make().

After setting current BreadcrumbList, it's possible to get it by BreadcrumbManager::get() and the current BreadcrumbList is automatically added to the breadcrumbs template via a view composer.


```html
@extends('layouts.default')

@php
  breadcrumbs()->set([\App\Http\Breadcrumbs\PostsTrail::class, 'show'], $post);
@endphp

@section('title', breadcrumbs()->get()->last()->getTitle())

@section('content')
    {{--    --}}
@endsection

```

### 6. Change template
The default template is set in config file.


#### 6-1. Publish config file

Publish packages's config to config/larabread.php.

```sh
php artisan vendor:publish --tag=larabread-config
```

#### 6-2. Change config file

- config/larabread.pph

```php
<?php

return [

    // view variable name
    'variable_name' => 'breadcrumbs',

    // inject breadcrumbs into these blade templates via view composer
    'templates' => [
        'default' => 'elements.breadcrumbs', // change here
    ],
];
```

#### 6-3 Write your own template file

- resources/vies/elements/breadcrumbs.blade.php

```html
@php
    /**
     * @var \N1215\Larabread\BreadcrumbList $breadcrumbs
     * @var \N1215\Larabread\Breadcrumb $breadcrumb
     */
@endphp
@if (isset($breadcrumbs) && !$breadcrumbs->isEmpty())
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb->getUrl() !== null && !$loop->last)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb->getUrl() }}">{{ $breadcrumb->getTitle() }}</a></li>
                @else
                    <li class="breadcrumb-item active">{{ $breadcrumb->getTitle() }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
```


#### 6-4. Custom attribute

The third arguments of BreadcrumbList::add() is attributes for your own customization.
For example, add an icon image url.

```php
<?php
declare(strict_types=1);

namespace App\Http\Breadcrumbs;

use N1215\Larabread\BreadcrumbList;

class PathTrail
{
    /**
     * @var HomeTrail
     */
    private $from;

    public function __construct(HomeTrail $from)
    {
        $this->from = $from;
    }

    public function path(): BreadcrumbList
    {
        return $this->from->home()->add('Path', '/path', ['icon' => 'https://example.com/path.png']);
    }
}
```

use the attribute by Breadcrumb::getAttribute() method.

```html
@php
    /**
     * @var \N1215\Larabread\BreadcrumbList $breadcrumbs
     * @var \N1215\Larabread\Breadcrumb $breadcrumb
     */
@endphp
@if (isset($breadcrumbs) && !$breadcrumbs->isEmpty())
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                <img src="{{ $breadcrumb->getAttribute('icon', 'default.png') }}" alt="{{ $breadcrumb->getTitle() }}">
            @endforeach
        </ol>
    </nav>
@endif

```


## License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
