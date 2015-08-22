@if ($menu)
    <{{ $menu->getType() }} {!! $menu->buildAttributes() !!}>
        @foreach ($menu->toArray() as $item)
            @if ($item instanceof \Malezha\Menu\Item)
                <li><a href="{{ $item->getUrl() }}" {!! $item->buildAttributes() !!}>{!! $item->getTitle() !!}</a></li>
            @else
                <li {!! $item->buildAttributes($item->getGroup()->attributes) !!}>
                    <a href="{{ $item->getGroup()->url }}">{!! $item->getGroup()->title !!}</a>
                    @include(config('menu.view'), ['menu' => $item])
                </li>
            @endif
        @endforeach
    </{{ $menu->getType() }}>
@endif
