# Example

```php
Menu::make('AdminMenu', 'ul', [], function (\Malezha\Menu\Builder $menu) {
    $menu->setActiveAttributes([
        'data-open-after' => 'true',
    ]);

    $menu->add('index', trans('titles.index'), route('admin.index'));

    $orders = $menu->group('orders', 'ul', ['class' => 'child-menu'], function (\Malezha\Menu\Builder $menu) {

        $menu->thisGroup('Заказы', 'javascript:;');

        $menu->add('all', 'Все', '/admin/orders');

    });
});
```