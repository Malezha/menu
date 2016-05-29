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
composer require malezha/laravel-menu
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

TODO

### Simple example

```php
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Element\Link;
use Malezha\Menu\Element\SubMenu;
use Malezha\Menu\Factory\LinkFactory;
use Malezha\Menu\Factory\SubMenuFactory;
use Menu;

Menu::make('main', 'ul', [], function (Builder $builder) {
    $builder->create('index', Link::class, function(LinkFactory $factory) {
        $factory->setTitle('Index Page')
            ->setUrl(url('/'))
            ->getLinkAttributes()->push(['class' => 'menu-link']);
    });

    $builder->create('orders', SubMenu::class, function(SubMenuFactory $factory) {
        $factory->getAttributes()->push(['class' => 'child-menu']);
        $factory->setTitle('Orders')->setUrl('javascript:;');

        $subBuilder = $factory->getBuilder();
        $subBuilder->create('all', Link::class, function(LinkFactory $factory) {
             $factory->setTitle('All')->setUrl(url('/orders/all'));
        });
        $subBuilder->create('type_1', Link::class, function(LinkFactory $factory) {
            $factory->setTitle('Type 1')->setUrl(url('/orders/1'))
                ->getLinkAttributes()->push(['class' => 'text-color-red']);
        });
        $subBuilder->create('type_2', Link::class, function(LinkFactory $factory) {
            $factory->setTitle('Type 2')->setUrl(url('/orders/2'))
                ->getLinkAttributes()->push(['data-attribute' => 'value']);
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
