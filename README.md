# Example

```php
Menu::make('main', 'ul', null, function (\Malezha\Menu\Builder $menu) {
    $index = $menu->add('index', 'Index Page', '/');
    $index->getLink()->setAttributes(['class' => 'menu-link']);
    $orders = $menu->group('orders', function (\Malezha\Menu\Item $item) {
        $item->setAttributes(['class' => 'child-menu'])->getLink()->setTitle('Orders')->setUrl('javascript:;');
    }, function (\Malezha\Menu\Builder $menu) {
        $menu->add('all', 'All', '/orders/all');
        $menu->add('type_1', 'Type 1', '/orders/1', null, ['class' => 'text-color-red']);
        $menu->add('type_2', 'Type 2', '/orders/2', null, null, function (\Malezha\Menu\Item $item) {
            $item->getLink()->setAttributes(['data-attribute' => 'value']);
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