@props(['admins' => null])

@php
    // Get the search query, sort parameter, and filter values from the request
    $searchQuery = request('search');
    $sort = request('sort', 'newest'); // Default to 'newest'
    $selectedPrivileges = request('privileges', []); // Selected privileges for filtering

    // Normalize the search query by converting to lowercase and splitting into individual terms
    $searchTerms = $searchQuery ? array_filter(explode(' ', strtolower(trim($searchQuery)))) : [];

    // Fetch admins based on the search query, sort parameter, and filters
    $modelToPass = App\Models\Admin::when($searchQuery, function ($query) use ($searchTerms) {
        foreach ($searchTerms as $term) {
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(userName) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(CONCAT(countryCode, number)) LIKE ?', ["%{$term}%"]);
            });
        }
        return $query;
    })
        ->when($selectedPrivileges, function ($query) use ($selectedPrivileges) {
            $query->whereIn('privileges', $selectedPrivileges); // Filter by privileges
        })
        ->when($sort, function ($query) use ($sort) {
            if ($sort === 'name-a-z') {
                $query->orderByRaw('LOWER(name) ASC'); // Sort by name A-Z (case-insensitive)
            } elseif ($sort === 'name-z-a') {
                $query->orderByRaw('LOWER(name) DESC'); // Sort by name Z-A (case-insensitive)
            } elseif ($sort === 'username-a-z') {
                $query->orderByRaw('LOWER(userName) ASC'); // Sort by username A-Z (case-insensitive)
            } elseif ($sort === 'username-z-a') {
                $query->orderByRaw('LOWER(userName) DESC'); // Sort by username Z-A (case-insensitive)
            } elseif ($sort === 'newest') {
                $query->orderBy('created_at', 'desc'); // Sort by creation date (newest)
            } elseif ($sort === 'oldest') {
                $query->orderBy('created_at', 'asc'); // Sort by creation date (oldest)
            }
        })
        ->paginate(10);

    // Split admins into chunks
    $chunkSize = 2;
    $chunkedAdmins = [];
    for ($i = 0; $i < $chunkSize; $i++) {
        $chunkedAdmins[$i] = [];
    }

    foreach ($modelToPass as $index => $admin) {
        $chunkIndex = $index % $chunkSize;
        $chunkedAdmins[$chunkIndex][] = $admin;
    }
@endphp

<x-layout :objects=true object="ADMINS">
    <x-breadcrumb :links="['Home' => url('/welcome'), 'Admins' => url('/admins')]" />

    <x-cardcontainer :model=$modelToPass addLink="addadmin" :showUsernameSort=true :showNameSort=true
        :showPrivilegeFilter=true>
        <!-- Add a unique ID to the container for dynamic updates -->
        <div id="dynamic-content" style="width:100%; display:flex; flex-direction:row">
            @foreach ($chunkedAdmins as $chunk)
                <div class="chunk">
                    @foreach ($chunk as $admin)
                        <x-card link="admin/{{ $admin->id }}" image="{{ asset($admin->image) }}" object="Admin">
                            ● Admin Name: {{ $admin->name }}<br>
                            ● Admin User Name: {{ $admin->userName }}<br>
                            ● Admin Number: {{ $admin->countryCode }} {{ $admin->number }}<br>
                            ● Admin Privileges:
                            @if ($admin->privileges == 0)
                                Teacher
                            @elseif ($admin->privileges == 1)
                                Semi-Admin
                            @else
                                Admin
                            @endif
                            <br>
                        </x-card>
                    @endforeach
                </div>
            @endforeach
        </div>
    </x-cardcontainer>

    @if ($modelToPass->total() > 1)
        <div class="pagination-info" style="text-align: center; margin-bottom: 2%; font-size: 24px; color: #000000;">
            Showing {{ $modelToPass->firstItem() }} to {{ $modelToPass->lastItem() }} of {{ $modelToPass->total() }}
            admins
        </div>
    @endif

    <!-- Conditionally render pagination links -->
    @if ($modelToPass->total() > 10)
        <div class="pagination">
            {{ $modelToPass->appends([
                    'search' => $searchQuery,
                    'sort' => $sort,
                    'privileges' => $selectedPrivileges,
                ])->links() }}
        </div>
    @endif
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBar = document.querySelector('.search-bar');
        const dynamicContent = document.getElementById('dynamic-content');

        searchBar.addEventListener('input', function() {
            const query = searchBar.value;

            // Get current filter values
            const selectedSort = document.querySelector('input[name="sort"]:checked')?.value ||
            'newest';
            const selectedPrivileges = Array.from(document.querySelectorAll(
                'input[name="privileges[]"]:checked')).map(el => el.value);

            // Build the query string
            const params = new URLSearchParams();
            params.set('search', query);
            params.set('sort', selectedSort);
            selectedPrivileges.forEach(privilege => params.append('privileges[]', privilege));

            // Fetch results via AJAX
            fetch(`{{ request()->url() }}?${params.toString()}`)
                .then(response => response.text())
                .then(data => {
                    // Parse the response and extract the dynamic content
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');
                    const newContent = doc.getElementById('dynamic-content').innerHTML;

                    // Update the dynamic content without changing the structure
                    dynamicContent.innerHTML = newContent;

                    attachCircleEffect();
                    refreshAnimations();

                    if (@json($modelToPass->count()) > 10) {

                        // Update pagination info text
                        const paginationInfo = doc.querySelector('.pagination-info');
                        const paginationInfoContainer = document.querySelector('.pagination-info');
                        if (paginationInfo) {
                            paginationInfoContainer.innerHTML = paginationInfo.innerHTML;
                        } else {
                            paginationInfoContainer.innerHTML = '';
                        }

                        // Update pagination links conditionally
                        const pagination = doc.querySelector('.pagination');
                        const paginationContainer = document.querySelector('.pagination');
                        if (pagination) {
                            paginationContainer.innerHTML = pagination.innerHTML;
                        } else {
                            paginationContainer.innerHTML = '';
                        }
                    }
                })
                .catch(error => console.error('Error fetching search results:', error));
        });
    });
</script>
