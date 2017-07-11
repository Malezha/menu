## Laravel Menu

[![Build](https://img.shields.io/badge/Laravel-5.4-orange.svg)](https://laravel.com) 
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

```php
use Malezha\Menu\Contracts\Builder;
use Menu;

Menu::make('main', function(Builder $builder) {
    // Create menu element
    $builder->create('element_name', ElementClass::class, function (ElementFactory $factory) {
        $factory->elementParameter;
        
        return $factory->build();
    });

    # Avaliable elements
    // Link. Html:
    // <li attributes|activeAttributes><a href="url" linkAttributes>title</a></li>
    use Malezha\Menu\Element\Link;
    use Malezha\Menu\Factory\LinkFactory;
    $builder->create('link', Link::class, function (LinkFactory $factory) {
        $factory->title = 'Title';
        $factory->url = '/';
        $factory->attributes->put('class', 'li');
        $factory->activeAttributes->push(['class' => 'active-element']);
        $factory->linkAttributes->set(['id' => 'link']);
        $factory->displayRule = true; // Boolean or callable witch return boolean
        
        return $factory->build();
    });

    // Submenu. Html:
    // <li attributes|activeAttributes>
    //  <a href="url" linkAttributes>title</a>
    //  <ul>...</ul>
    // </li>
    use Malezha\Menu\Element\SubMenu;
    use Malezha\Menu\Factory\SubMenuFactory;
    $builder->create('submenu', SubMenu::class, function (SubMenuFactory $factory) {
        // Submenu exdends Link so all parameters available
        $factory->builder->create(...); // Create submenu element
        
        return $factory->build();
    });

    // Text. Html:
    // <li attributes>Text</li>
    use Malezha\Menu\Element\Text;
    use Malezha\Menu\Factory\TextFactory;
    $builder->create('submenu', Text::class, function (TextFactory $factory) {
        $factory->text = 'Text';
        $factory->attributes->put('class', 'deliver');
        $factory->displayRule = true;
        
        return $factory->build();
    });
});

// Building menu from array
$array = [
    'type' => 'ul',
    'view' => 'menu::view', // Default view
    'attributes' => [
        'class' => 'menu',
    ],
    'activeAttributes' => [
        'class' => 'active',
    ],
    'elements' => [
        'index' => [
            'type' => 'link',
            'view' => 'menu::elements.link', // Default view may be changed in config
            'url' => 'http://example.com',
            'attributes' => [],
            'activeAttributes' => [
                class' => 'active',
            ],
            'linkAttributes' => [],
            'displayRule' => true,
        ],
        ...
        'settings' => [
            'type' => 'submenu',
            'view' => 'menu::elements.submenu',
            'url' => 'http://example.com',
            'attributes' => [],
            'activeAttributes' => [
                class' => 'active',
            ],
            'linkAttributes' => [],
            'displayRule' => true,
            'builder' => [
                'type' => 'ul',
                'view' => '_partial.submenu', // U can set view for submenu singly
                ...
                'elements' => [
                    ...
                ],
            ],
        ],
    ]
];

$builder = Menu::fromArray('from-array', $array);
$html = $builder->render(); // Menu::render('from-array');
// $builder->toArray() === $array;
```

### Simple example

```php
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Element\Link;
use Malezha\Menu\Element\SubMenu;
use Malezha\Menu\Factory\LinkFactory;
use Malezha\Menu\Factory\SubMenuFactory;
use Menu;

Menu::make('main', function (Builder $builder) {
    $builder->create('index', Link::class, function(LinkFactory $factory) {
        $factory->title = 'Index Page';
        $factory->url = '/';
        $factory->linkAttributes->push(['class' => 'menu-link']);
        
        return $factory->build();
    });

    $builder->create('orders', SubMenu::class, function(SubMenuFactory $factory) {
        $factory->attributes->push(['class' => 'child-menu']);
        $factory->title = 'Orders';
        $factory->url = 'javascript:;';

        $factory->builder->create('all', Link::class, function(LinkFactory $factory) {
            $factory->title = 'All';
            $factory->url = '/orders/all';
            
            return $factory->build();
        });
        
        $factory->builder->create('type_1', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Type 1';
            $factory->url = '/orders/1';
            $factory->linkAttributes->push(['class' => 'text-color-red']);
            
            return $factory->build();
        });
        
        $factory->builder->create('type_2', Link::class, function(LinkFactory $factory) {
            $factory->title = 'Type 2';
            $factory->url = '/orders/2';
            $factory->linkAttributes->push(['data-attribute' => 'value']);
            
            return $factory->build();
        });
        
        return $factory->build();
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
