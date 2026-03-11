<div class="col-md-3 border-end">
    <h5>Catégories</h5>
    <ul class="list-unstyled">
        @foreach($categories[null] ?? [] as $cat)
            @include('categories.partials.tree', ['category' => $cat, 'categories' => $categories])
        @endforeach
    </ul>
</div>