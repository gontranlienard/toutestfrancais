<div class="dropdown">
    @foreach($categories as $category)
        <div>
            <a href="{{ route('category', $category->slug) }}">
                {{ $category->name }}
            </a>

            @if($category->children->count())
                @include('front.partials.menu-dropdown', [
                    'categories' => $category->children
                ])
            @endif
        </div>
    @endforeach
</div>

