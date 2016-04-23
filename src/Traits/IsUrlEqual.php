<?php
namespace Malezha\Menu\Traits;

trait IsUrlEqual
{
    protected $uriForTrim = [
        '#',
        '/index',
        '/'
    ];

    public function isUrlEqual($first, $second)
    {
        foreach ($this->uriForTrim as $trim) {
            $first = rtrim($first, $trim);
            $second = rtrim($second, $trim);
        }

        return $first == $second;
    }
}