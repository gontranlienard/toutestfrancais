<li class="category-item depth-{{ $depth }}">

    <a href="{{ route('category.show', $category->slug) }}">
        {{ $category->name }}

        @if(isset($category->product_count))
            <span class="count">({{ $category->product_count }})</span>
        @endif
    </a>

    @if(isset($category->children) && $category->children->count() && $depth < 4)

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

