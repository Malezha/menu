# Todo

Write a comprehensive documentation.

# Example

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