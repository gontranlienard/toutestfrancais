<li class="category-item depth-{{ $depth }}">
    <a href="{{ route('category.show', $category->slug) }}">
        {{ $category->name }}
    </a>

    @if($category->children->count() && $depth < 4)
        <ul>
            @foreach($category->children as $child)
                @include('partials.category-item', [
                    'category' => $child,
                    'depth' => $depth + 1
                ])
            @endforeach
        </ul>
    @endif
</li>

