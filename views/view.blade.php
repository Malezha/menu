@if ($menu)
    <{{ $menu->getType() }}{!! $menu->buildAttributes() !!}>
    @foreach ($menu->all() as $value)
        <?php $isGroup = $value instanceof \Malezha\Menu\Entity\Group; if ($isGroup) $item = $value->item(); else $item = $value; ?>
        @if($item->canDisplay())
        <li{!! $item->buildAttributes() !!}>
            <a href="{{ $item->getLink()->getUrl() }}"{!! $item->getLink()->buildAttributes() !!}>{!! $item->getLink()->getTitle() !!}</a>
            @if($isGroup)
                @include(config('menu.view'), ['menu' => $value->menu()])
            @endif
        </li>
        @endif
    @endforeach
    </{{ $menu->getType() }}>
@endif