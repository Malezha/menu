@if ($menu)
    <{{ $menu->type() }}{!! $menu->buildAttributes() !!}>
        @foreach ($menu->values() as $item)
            @if ($item instanceof \Malezha\Menu\Item)
                <li{!! $item->buildAttributes() !!}><a href="{{ $item->link()->url() }}"{!! $item->link()->buildAttributes() !!}>{!! $item->link()->title() !!}</a></li>
            @elseif ($item instanceof \Malezha\Menu\Group)
                <li{!! $item->item()->buildAttributes() !!}>
                    <a href="{{ $item->item()->link()->url() }}{!! $item->item()->link()->buildAttributes() !!}">{!! $item->item()->link()->title() !!}</a>
                    @include(config('menu.view'), ['menu' => $item->menu()])
                </li>
            @endif
        @endforeach
    </{{ $menu->type() }}>
@endif