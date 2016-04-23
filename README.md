# Laravel Menu

[![Build](https://img.shields.io/badge/Laravel-5.1%7C5.2-orange.svg)](https://laravel.com) 
[![Build Status](https://travis-ci.org/Malezha/menu.svg?branch=master)](https://travis-ci.org/Malezha/menu) 
[![Latest Stable Version](https://poser.pugx.org/malezha/laravel-menu/v/stable)](https://packagist.org/packages/malezha/laravel-menu) 
[![Total Downloads](https://poser.pugx.org/malezha/laravel-menu/downloads)](https://packagist.org/packages/malezha/laravel-menu) 
[![Latest Unstable Version](https://poser.pugx.org/malezha/laravel-menu/v/unstable)](https://packagist.org/packages/malezha/laravel-menu) 
[![Code Coverage](https://scrutinizer-ci.com/g/Malezha/menu/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Malezha/menu/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Malezha/menu/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Malezha/menu/?branch=master) 
[![License](https://poser.pugx.org/malezha/laravel-menu/license)](https://packagist.org/packages/malezha/laravel-menu)

## Todo

Write a comprehensive documentation.

## Example

```php
Menu::make('main', 'ul', [], function (\Malezha\Menu\Entity\Builder $menu) {

    $index = $menu->add('index', 'Index Page', '/');
    $index->getLink()->getAttributes()->push(['class' => 'menu-link']);

    $menu->group('orders', function (\Malezha\Menu\Entity\Item $item) {
        $item->getAttributes()->push(['class' => 'child-menu']);

        $link = $item->getLink();
        $link->setTitle('Orders');
        $link->setUrl('javascript:;');

    }, function (\Malezha\Menu\Entity\Builder $menu) {
        $menu->add('all', 'All', '/orders/all');

        $menu->add('type_2', 'Type 2', '/orders/2', [], [], function ($item) {
            $item->getLink()->getAttributes()->push(['data-attribute' => 'value']);
        });
    });
    
});
```

```html
<!-- Menu::render('main') -->
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