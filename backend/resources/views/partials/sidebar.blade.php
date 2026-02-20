<aside class="sidebar">
    <h3>Catégories</h3>

    <ul class="category-tree">
        @foreach($categories as $category)
            @include('partials.category-item', [
                'category' => $category,
                'depth' => 0
            ])
        @endforeach
    </ul>
</aside>


