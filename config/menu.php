<?php

return [

    /**
     * If you use laravel or illuminate\view set 'blade' template system.
     * You can also use a simple embedded template system - 'basic'
     */
    'template-system' => 'basic',
    
    'available-template-systems' => [
        'blade' => \Malezha\Menu\Render\Blade::class,
        'basic' => \Malezha\Menu\Render\Basic::class,
    ],

    /**
     * Default view
     */
    'view' => 'menu::view',

    /**
     * Path to find patterns.
     * Used in template 'basic'.
     */
    'paths' => [
        __DIR__ . '/../views',
    ],

    /**
     * Minify html output
     */
    'minify' => true,

];
