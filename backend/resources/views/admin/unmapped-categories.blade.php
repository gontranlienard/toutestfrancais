<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Produits à classer</title>

<style>
body {
    font-family: Arial;
    background: #f4f6f9;
    padding: 30px;
}

table {
    width: 100%;
    background: white;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

th {
    background: #1f2933;
    color: white;
}

input, select {
    padding: 6px;
}

.btn {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-map {
    background:#28a745;
    color:white;
}
</style>
</head>

<body>

<h1>Produits non classés</h1>

<p class="mb-4 text-sm text-gray-500">
    {{ $unmapped->total() }} catégories non mappées
</p>

<table>

<thead>
<tr>
<th>ID</th>
<th>Produit</th>
<th>Catégorie brute</th>
<th>Catégorie cible</th>
<th>Rule</th>
<th>Priorité</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@if($products->isEmpty())
<tr>
    <td colspan="7">Aucun résultat</td>
</tr>
@endif

@foreach($products as $product)

@php
$suggest = strtolower(collect(explode(' ', $product->name))
    ->filter(fn($w) => strlen($w) > 4)
    ->first());
@endphp

<tr id="row-{{ $product->id }}">

<td>{{ $product->id }}</td>

<td>{{ $product->name }}</td>

<td>{{ $product->raw_category }}</td>

<td>
<select id="cat-{{ $product->id }}">
@foreach($categories as $category)
<option value="{{ $category->id }}">{{ $category->name }}</option>

@foreach($category->childrenRecursive as $child)
@include('admin.partials.category-option', ['category'=>$child,'level'=>1])
@endforeach

@endforeach
</select>
</td>

<td>
<input type="text" id="rule-{{ $product->id }}" value="{{ $suggest }}">
</td>

<td>
<select id="priority-{{ $product->id }}">
<option value="100">100</option>
<option value="80">80</option>
<option value="60" selected>60</option>
<option value="40">40</option>
<option value="20">20</option>
</select>
</td>

<td>
<button class="btn btn-map" onclick="mapProduct({{ $product->id }})">
Mapper
</button>
</td>

</tr>

@endforeach

</tbody>

</table>

<div class="mt-6">
    {{ $unmapped->appends(request()->query())->links() }}
</div>

<script>
function mapProduct(id) {

    const category_id = document.getElementById('cat-'+id).value;
    const rule = document.getElementById('rule-'+id).value;
    const priority = document.getElementById('priority-'+id).value;

    fetch('/admin/products/map', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: id,
            category_id: category_id,
            rule: rule,
            priority: priority
        })
    })
    .then(() => {
        document.getElementById('row-'+id).remove();
    });

}
</script>

</body>
</html>