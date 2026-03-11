<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>Catégories non mappées</title>

<style>

body{
font-family:Arial,sans-serif;
background:#f4f6f9;
padding:30px;
}

h1{
margin-bottom:20px;
}

.search{
margin-bottom:20px;
}

table{
width:100%;
background:white;
border-collapse:collapse;
box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

th,td{
padding:12px;
border-bottom:1px solid #eee;
text-align:left;
vertical-align:middle;
}

th{
background:#222;
color:white;
}

tr:hover{
background:#f9f9f9;
}

.badge{
background:#dc3545;
color:white;
padding:4px 8px;
border-radius:4px;
font-size:12px;
}

.bulk{
margin:20px 0;
display:flex;
gap:10px;
align-items:center;
}

select{
padding:6px;
min-width:250px;
}

button{
padding:7px 14px;
background:#28a745;
color:white;
border:none;
border-radius:4px;
cursor:pointer;
}

button:hover{
background:#218838;
}

.pagination{
margin-top:20px;
}

</style>

</head>

<body>

<h1>Catégories non mappées</h1>

@if(session('success'))
<p style="color:green">{{ session('success') }}</p>
@endif


<div class="search">

<form method="GET">

<input
type="text"
name="search"
placeholder="Rechercher une catégorie"
value="{{ request('search') }}"
>

<button type="submit">
Rechercher
</button>

</form>
<form method="POST" action="{{ route('admin.categories.rebuild') }}">
    @csrf
    <button type="submit" style="
        padding:8px 14px;
        background:#007bff;
        color:white;
        border:none;
        border-radius:4px;
        cursor:pointer;
        margin-bottom:15px;
    ">
        🔄 Recalculer les catégories
    </button>
</form>
</div>


@if($unmapped->count() === 0)

<p>Toutes les catégories sont mappées 🎉</p>

@else


<form method="POST" action="{{ route('admin.categories.mapBulk') }}">

@csrf


<div class="bulk">

<select name="category_id">

@foreach($categories as $category)

<option value="{{ $category->id }}">
{{ $category->name }}
</option>

@foreach($category->childrenRecursive as $child)

@include('admin.partials.category-option',[
'category'=>$child,
'level'=>1
])

@endforeach

@endforeach

</select>


<button type="submit">
Mapper sélection
</button>

</div>


<table>

<thead>

<tr>

<th width="40"></th>

<th width="200">
Site
</th>

<th>
Catégorie brute
</th>

<th width="120">
Occurrences
</th>

</tr>

</thead>


<tbody>

@foreach($unmapped as $item)

<tr>

<td>

<input
type="checkbox"
name="raw_categories[]"
value="{{ $item->raw_category }}"
>

</td>


<td>

{{ $item->site->name ?? 'N/A' }}

</td>


<td>

<strong>

{{ $item->raw_category }}

</strong>

</td>


<td>

<span class="badge">

{{ $item->occurrences }}

</span>

</td>

</tr>

@endforeach


</tbody>

</table>


<div class="pagination">

{{ $unmapped->links() }}

</div>


</form>

@endif


</body>

</html>