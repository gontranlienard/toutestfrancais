<li>

    <div class="category-header">
        <a href="{{ route('category.show', $category->getFullSlug()) }}"
   class="category-link {{ request()->is('categorie/'.$category->getFullSlug()) ? 'active' : '' }}">
    {{ $category->name }}
    <span class="count">({{ $category->product_count ?? 0 }})</span>
</a>

        @if($category->childrenRecursive->count())
            <button class="toggle-btn">▶</button>
        @endif
    </div>

    @if($category->childrenRecursive->count())
        <ul class="subcategory-list">
            @foreach($category->childrenRecursive as $child)
                @include('components.category-item', ['category' => $child])
            @endforeach
        </ul>
    @endif

</li>