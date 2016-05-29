## Laravel Menu

[![Build](https://img.shields.io/badge/Laravel-5.1%7C5.2-orange.svg)](https://laravel.com) 
[![Build Status](https://travis-ci.org/Malezha/menu.svg?branch=master)](https://travis-ci.org/Malezha/menu) 
[![Latest Stable Version](https://poser.pugx.org/malezha/laravel-menu/v/stable)](https://packagist.org/packages/malezha/laravel-menu) 
[![Total Downloads](https://poser.pugx.org/malezha/laravel-menu/downloads)](https://packagist.org/packages/malezha/laravel-menu) 
[![Latest Unstable Version](https://poser.pugx.org/malezha/laravel-menu/v/unstable)](https://packagist.org/packages/malezha/laravel-menu) 
[![Code Coverage](https://scrutinizer-ci.com/g/Malezha/menu/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Malezha/menu/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Malezha/menu/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Malezha/menu/?branch=master) 
[![License](https://poser.pugx.org/malezha/laravel-menu/license)](https://packagist.org/packages/malezha/laravel-menu)

### Install

Require this package with composer using the following command:

```bash
composer require barryvdh/laravel-ide-helper
```

After updating composer, add the ServiceProvider to the providers array in config/app.php

```php
Malezha\Menu\MenuServiceProvider::class,
```

For use facade add alias to config/app.php

```php
'Menu' => Malezha\Menu\MenuFacade::class,
```

Copy the package config to your local config with the publish command:

```bash
php artisan vendor:publish --provider="Malezha\Menu\MenuServiceProvider"
```

### Usage

#### Menu facade

You can use the facade to create a global menu.

```php
use Malezha\Menu\Contracts\Builder;
use Menu;

Menu::make('main-menu', Builder::UI, ['class' => 'menu'], function(Builder $menu {
    // add elements to menu
}));
```

When the display is rendered to

```html
<ul class="menu"><!-- Menu elements --></ul>
```

If you need to edit already created the menu, you can get it.

```php
/** @var Builder $menu */
$menu = Menu::get('main-menu');
```

Also available methods to check exists and delete the menu.

```php
// Exists?
$true = Menu::has('main-menu');

// Delete
Menu::forget('main-menu');
```

At any time you can render your menu. The second parameter can specify an alternative view.

Attention! Setup `$view` variable ignores configuration specified when defining the menu.

```php
$html = Menu::render('main-menu', 'partials.main-menu');
```

#### Builder methods

When you create new menu element, you can directly set parameters `Item` and `Link`.

The required parameters are only the name, title and URL.

```php
$item = $builder->create('element-name', 'Link title', 'http::/example.com/url',
                      ['id' => 'element'], ['id' => 'link'], $callback);
```

As the latter parameter can be the callback.

```php
$callback = function (\Malezha\Menu\Contracts\Item $item {
    $id = $item->getAttributes()->get('id'); // 'element'
});
```

Also, the object `Link` can get in the callback and the returned object.

```php
/** @var Malezha\Menu\Contracts\Link $link */
$link = $item->getLink();
$id = $link->getAttributes()->get('id'); // 'link'
```

To create a sub menu, use the method `submenu`.

```php
$menu->submenu('submenu', function (Item $item) {
    // In this callback, you can customize the element that declares the sub menu.
}, function (Builder $menu) {
    // Submenu elements
});
```

You can insert new elements into menu before or after defined element:

```php
$callback = function (Builder $menu) {
    ...
};

$menu->insertBefore('logout', $callback);
$menu->insertAfter('index', $callback);
```

Builder are also available methods similar builder global menu:

```php
$array = $builder->all(); // Get all menu elements.
$bool = $builder->has('name'); // Check exists element by name.
$item = $builder->get('name', $default = null); // Get element by name if exists or default value.
$item = $builder->getByIndex(2, $default = null); // Get element by index
$builder->forget('name'); // Delete element from menu.
$html = $builder->render('view_name'); // Render view.
```

### Simple example

```php
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\Item;
use Menu;

Menu::make('main', 'ul', [], function (Builder $menu) {
    $index = $menu->create('index', 'Index Page', '/');
    $index->getLink()->getAttributes()->push(['class' => 'menu-link']);

    $menu->submenu('orders', function (Item $item) {
        $item->getAttributes()->push(['class' => 'child-menu']);

        $link = $item->getLink();
        $link->setTitle('Orders');
        $link->setUrl('javascript:;');

    }, function (Builder $menu) {
        $menu->create('all', 'All', '/orders/all');

        $menu->create('type_2', 'Type 2', '/orders/2', [], [], function (Item $item) {
            $item->getLink()->getAttributes()->push(['data-attribute' => 'value']);
        });
    });
});
```

```html
<!-- \Menu::render('main') -->
<ul>
    <li><a href="/" class="menu-link">Index Page</a></li>
    <li class="child-menu">
        <a href="javascript:;">Orders</a>
        <ul>
            <li><a href="/orders/all">All</a></li>
            <li><a href="/orders/1" class="text-color-red">Type 1</a></li>
            <li><a href="/orders/2" data-attribute="value">Type 2</a></li>
        </ul>
    </li>
</ul>
```

### License

[MIT license](https://github.com/Malezha/menu/blob/master/LICENSE)
