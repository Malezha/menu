<?php

return [

    /**
     * Available menu elements.
     */
    'elements' => [
        \Malezha\Menu\Element\Link::class => [
            'view' => 'menu::elements.link',
            'factory' => \Malezha\Menu\Factory\LinkFactory::class,
        ],
        \Malezha\Menu\Element\SubMenu::class => [
            'view' => 'menu::elements.submenu',
            'factory' => \Malezha\Menu\Factory\SubMenuFactory::class,
        ],
    ],
    
    /**
     * If you use laravel or illuminate\view set 'illuminate' template render.
     * You can also use a simple embedded template render - 'basic'
     */
    'default' => 'basic',

    /**
     * Available template renders.
     */
    'renders' => [
        'basic' => \Malezha\Menu\Render\Basic::class,
        'illuminate' => \Malezha\Menu\Render\Illuminate::class,
    ],

    /**
     * Default view
     */
    'view' => 'menu::view',

    /**
     * Path to find patterns.
     * Used in render 'basic'.
     */
    'paths' => [
        __DIR__ . '/../views',
    ],

    /**
     * Minify html output
     */
    'minify' => true,

];
