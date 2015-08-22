@if ($menu)
    <{{ $menu->getType() }} {!! $menu->buildAttributes() !!}>
        @foreach ($menu->toArray() as $item)
            @if ($item instanceof \Malezha\Menu\Item)
                <li><a href="{{ $item->getUrl() }}" {!! $item->buildAttributes() !!}>{!! $item->getTitle() !!}</a></li>
            @else
                @include(config('menu.view'), ['menu' => $item]))
            @endif
        @endforeach
    </{{ $menu->getType() }}>
@endif