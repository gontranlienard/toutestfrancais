<option value="{{ $category->id }}">
    {{ str_repeat('--', $level) }} {{ $category->name }}
</option>

@if($level < 3)
    @foreach($category->children as $child)
        @include('admin.partials.category-option', [
            'category' => $child,
            'level' => $level + 1
        ])
    @endforeach
@endif