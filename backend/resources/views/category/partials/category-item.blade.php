<div class="col-md-3 border-end">
    <h5>Catégories</h5>
    <ul class="list-unstyled">
        @foreach($categories as $cat)
            @include('categories.partials.category-item', ['category' => $cat, 'level' => 0])
        @endforeach
    </ul>
</div>
