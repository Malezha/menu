<?php
use Illuminate\Container\Container;
use Malezha\Menu\Contracts\Builder;
use Malezha\Menu\Contracts\Menu;

if (!function_exists('call_if_callable')) {
    /**
     * Call function if it callable
     *
     * @param callable $callable
     * @param array ...$params
     * @return mixed
     */
    function call_if_callable($callable, ...$params) {
        if (is_callable($callable)) {
            return call_user_func_array($callable, $params);
        }

        return null;
    }
}

if (!function_exists('menu')) {
    /**
     * @param string|null $name
     * @return Builder|Menu
     */
    function menu($name = null) {
        /** @var Menu $menu */
        $menu = Container::getInstance()->make(Menu::class);

        if (is_null($name)) {
            return $menu;
        }

        return $menu->make($name);
    }
}