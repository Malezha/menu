@if ($menu)
    <{{ $menu->getType() }}{!! $menu->buildAttributes() !!}>
        @foreach ($menu->items() as $item)
            @if ($item instanceof \Malezha\Menu\Item)
                <li{!! $item->buildAttributes() !!}><a href="{{ $item->getLink()->getUrl() }}"{!! $item->getLink()->buildAttributes() !!}>{!! $item->getLink()->getTitle() !!}</a></li>
            @elseif ($item instanceof \Malezha\Menu\Group)
                <li{!! $item->getItem()->buildAttributes() !!}>
                    <a href="{{ $item->getItem()->getLink()->getUrl() }}{!! $item->getItem()->getLink()->buildAttributes() !!}">{!! $item->getItem()->getLink()->getTitle() !!}</a>
                    @include(config('menu.view'), ['menu' => $item->getMenu()])
                </li>
            @endif
        @endforeach
    </{{ $menu->getType() }}>
@endif