<option value="{{ $category->id }}">
    {{ str_repeat('— ', $level) }} {{ $category->name }}
</option>

@if($category->childrenRecursive)
    @foreach($category->childrenRecursive as $child)
        @include('admin.partials.category-option', [
            'category' => $child,
            'level' => $level + 1
        ])
    @endforeach
@endif