@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2>Brands List</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 position-relative">
        <input type="text" id="searchInput" class="form-control" placeholder="Search brands..." onkeyup="filterBrands()">
        <div id="searchDropdown" class="position-absolute bg-white border border-gray-200 rounded-lg shadow-lg mt-1 w-100 max-h-80 overflow-y-auto" style="display: none; z-index: 10;">
            <!-- Dropdown items will be populated here -->
        </div>
    </div>

    <a href="{{ route('brand.create') }}" class="btn btn-primary mb-3">Add New Brand</a>

    @if($brands->isEmpty())
        <div>No brands found.</div>
    @else
        <table class="table table-bordered" id="brandsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Logo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($brands as $brand)
                <tr class="brand-row">
                    <td class="brand-name">{{ $brand->name }}</td>
                    <td>
                        <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" style="max-height: 60px;">
                    </td>
                    <td>
                        <form action="{{ url('/brand/delete/' . $brand->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script>
function filterBrands() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase().trim();
    const dropdown = document.getElementById('searchDropdown');
    const table = document.getElementById('brandsTable');
    const rows = table.getElementsByClassName('brand-row');

    // Clear previous dropdown content
    dropdown.innerHTML = '';

    if (filter) {
        let hasMatches = false;
        for (let i = 0; i < rows.length; i++) {
            const nameCell = rows[i].getElementsByClassName('brand-name')[0];
            const logoCell = rows[i].querySelector('img');
            if (nameCell && logoCell) {
                const txtValue = nameCell.textContent || nameCell.innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    hasMatches = true;
                    // Create dropdown item
                    const item = document.createElement('div');
                    item.style.display = 'flex';
                    item.style.alignItems = 'center';
                    item.style.gap = '12px';
                    item.style.padding = '8px';
                    item.style.cursor = 'pointer';
                    item.style.borderBottom = '1px solid #e5e7eb';
                    item.innerHTML = `
                        <img src="${logoCell.src}" width="50" height="50" alt="${txtValue}" style="border-radius: 4px; object-fit: cover;" />
                        <div style="flex-grow: 1; color: #374151;">
                            ${txtValue}
                        </div>
                    `;
                    item.onmouseover = function() { this.style.backgroundColor = '#f9fafb'; };
                    item.onmouseout = function() { this.style.backgroundColor = 'transparent'; };
                    item.onclick = function() {
                        input.value = txtValue;
                        dropdown.style.display = 'none';
                        filterTable(txtValue.toLowerCase());
                    };
                    dropdown.appendChild(item);
                }
            }
        }
        if (hasMatches) {
            dropdown.style.display = 'block';
        } else {
            dropdown.innerHTML = '<div class="py-2 px-3 text-gray-500">No Match Found</div>';
            dropdown.style.display = 'block';
        }
    } else {
        dropdown.style.display = 'none';
        // Show all rows if no filter
        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = '';
        }
    }
}

function filterTable(filter) {
    const table = document.getElementById('brandsTable');
    const rows = table.getElementsByClassName('brand-row');

    for (let i = 0; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByClassName('brand-name')[0];
        if (nameCell) {
            const txtValue = nameCell.textContent || nameCell.innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(event) {
    const input = document.getElementById('searchInput');
    const dropdown = document.getElementById('searchDropdown');
    if (!input.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
</script>
@endsection
