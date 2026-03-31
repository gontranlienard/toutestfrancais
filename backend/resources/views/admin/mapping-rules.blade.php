<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Règles de mapping</title>

<style>
body {
    font-family: Arial;
    background: #f4f6f9;
    padding: 30px;
}
h1 { margin-bottom: 20px; }

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

input, select { padding: 6px; }

.btn {
    padding: 6px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-save { background:#28a745; color:white; }
.btn-delete { background:#dc3545; color:white; }

.bulk-bar, .add-bar {
    margin-bottom: 15px;
    display:flex;
    gap:10px;
    align-items:center;
}
</style>
</head>

<body>

<h1>Règles de mapping</h1>

{{-- 🔥 TEMPLATE UNIQUE (ULTRA IMPORTANT) --}}
<select id="categories-template" style="display:none;">
@foreach($categories as $category)

    <option value="{{ $category->id }}">{{ $category->name }}</option>

    @foreach($category->children as $child)
        @include('admin.partials.category-option', [
            'category'=>$child,
            'level'=>1
        ])
    @endforeach

@endforeach
</select>

{{-- AJOUT REGLE --}}
<div class="add-bar">

<input type="text" id="new-keyword" placeholder="ex: gant ete, gant hiver" style="width:250px;">

<select id="new-category"></select>

<select id="new-priority">
    <option value="100">100</option>
    <option value="80">80</option>
    <option value="60" selected>60</option>
    <option value="40">40</option>
    <option value="20">20</option>
</select>

<button class="btn btn-save" onclick="createRule()">Ajouter</button>

</div>

<div class="bulk-bar">
    <button class="btn btn-delete" onclick="deleteSelected()">
        Supprimer sélection
    </button>
</div>

<table>

<thead>
<tr>
<th><input type="checkbox" onclick="toggleAll(this)"></th>
<th>ID</th>
<th>Keyword</th>
<th>Catégorie</th>
<th>Priorité</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

@foreach($rules as $rule)

<tr id="row-{{ $rule->id }}">

<td><input type="checkbox" class="rule-checkbox" value="{{ $rule->id }}"></td>

<td>{{ $rule->id }}</td>

<td>
<input type="text" id="keyword-{{ $rule->id }}"
value="{{ $rule->keyword }}" style="width:250px;">
</td>

<td>
<select class="category-select" data-id="{{ $rule->id }}"
data-selected="{{ $rule->rule_category_id }}"></select>
</td>

<td>
<select id="priority-{{ $rule->id }}">
    <option value="100" {{ $rule->priority == 100 ? 'selected' : '' }}>100</option>
    <option value="80" {{ $rule->priority == 80 ? 'selected' : '' }}>80</option>
    <option value="60" {{ $rule->priority == 60 ? 'selected' : '' }}>60</option>
    <option value="40" {{ $rule->priority == 40 ? 'selected' : '' }}>40</option>
    <option value="20" {{ $rule->priority == 20 ? 'selected' : '' }}>20</option>
</select>
</td>

<td style="display:flex; gap:8px;">

<button class="btn btn-save" onclick="updateRule({{ $rule->id }})">
    Sauver
</button>

<button class="btn btn-delete" onclick="deleteRule({{ $rule->id }})">
    Supprimer
</button>

</td>

</tr>

@endforeach

</tbody>

</table>

<div style="margin-top:20px;">
{{ $rules->links() }}
</div>

<script>

const csrf = document.querySelector('meta[name="csrf-token"]').content;

/* 🔥 CLONE DES CATEGORIES */
document.addEventListener('DOMContentLoaded', function() {

    const template = document.getElementById('categories-template').innerHTML;

    // select ajout
    document.getElementById('new-category').innerHTML = template;

    // select par ligne
    document.querySelectorAll('.category-select').forEach(select => {

        select.innerHTML = template;

        const selected = select.dataset.selected;

        if(selected){
            select.value = selected;
        }
    });

});

/* CRUD */

function createRule() {
    const keyword = document.getElementById('new-keyword').value;
    const category_id = document.getElementById('new-category').value;
    const priority = document.getElementById('new-priority').value;

    if(!keyword){
        alert("Keyword obligatoire");
        return;
    }

    fetch('/admin/mapping-rules/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ keyword, category_id, priority })
    }).then(() => location.reload());
}

function toggleAll(source) {
    document.querySelectorAll('.rule-checkbox').forEach(cb => {
        cb.checked = source.checked;
    });
}

function updateRule(id) {
    fetch('/admin/mapping-rules/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({
            id,
            keyword: document.getElementById('keyword-' + id).value,
            priority: document.getElementById('priority-' + id).value,
            category_id: document.querySelector(`[data-id="${id}"]`).value
        })
    }).then(() => {
        const row = document.getElementById('row-'+id);
        row.style.background = '#d4edda';
        setTimeout(() => row.style.background = '', 800);
    });
}

function deleteRule(id) {
    if(!confirm("Supprimer cette règle ?")) return;

    fetch('/admin/mapping-rules/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ id })
    }).then(() => {
        document.getElementById('row-'+id).remove();
    });
}

function deleteSelected() {
    const ids = Array.from(document.querySelectorAll('.rule-checkbox:checked'))
        .map(cb => cb.value);

    if(ids.length === 0){
        alert("Sélectionne au moins une règle");
        return;
    }

    fetch('/admin/mapping-rules/delete-bulk', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ ids })
    }).then(() => {
        ids.forEach(id => {
            document.getElementById('row-'+id)?.remove();
        });
    });
}

</script>

</body>
</html>