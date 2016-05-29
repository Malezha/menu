<?php

return [

    /**
     * If you use laravel or illuminate\view set 'blade' template render.
     * You can also use a simple embedded template render - 'basic'
     */
    'default' => 'basic',

    /**
     * Available template renders.
     */
    'renders' => [
        'blade' => \Malezha\Menu\Render\Blade::class,
        'basic' => \Malezha\Menu\Render\Basic::class,
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
