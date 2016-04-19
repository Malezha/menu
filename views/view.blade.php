@if ($menu)
    <{{ $menu->getType() }}{!! $menu->buildAttributes() !!}>
        @foreach ($menu->all() as $item)
            @if ($item instanceof \Malezha\Menu\Entity\Item)
                <li{!! $item->buildAttributes() !!}><a href="{{ $item->getLink()->getUrl() }}"{!! $item->getLink()->buildAttributes() !!}>{!! $item->getLink()->getTitle() !!}</a></li>
            @elseif ($item instanceof \Malezha\Menu\Entity\Group)
                <li{!! $item->item()->buildAttributes() !!}>
                    <a href="{{ $item->item()->getLink()->getUrl() }}{!! $item->item()->getLink()->buildAttributes() !!}">{!! $item->item()->getLink()->getTitle() !!}</a>
                    @include(config('menu.view'), ['menu' => $item->menu()])
                </li>
            @endif
        @endforeach
    </{{ $menu->getType() }}>
@endif