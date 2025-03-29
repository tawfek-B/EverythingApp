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
        const filterForm = document.querySelector('.filter-dropdown');
        const filterCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="privileges"]');
        const paginationInfoContainer = document.querySelector('.pagination-info');
        const paginationContainer = document.querySelector('.pagination');

        // Function to fetch and update results
        function updateResults() {
            const query = searchBar.value;
            const selectedSort = document.querySelector('input[name="sort"]:checked')?.value || 'newest';
            const selectedPrivileges = Array.from(document.querySelectorAll(
                'input[name="privileges[]"]:checked')).map(el => el.value);

            // Build query string
            const params = new URLSearchParams();
            params.set('search', query);
            params.set('sort', selectedSort);
            selectedPrivileges.forEach(privilege => params.append('privileges[]', privilege));

            fetch(`{{ request()->url() }}?${params.toString()}`)
                .then(response => response.text())
                .then(data => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');
                    const newContent = doc.getElementById('dynamic-content').innerHTML;
                    dynamicContent.innerHTML = newContent;

                    // Update pagination info (show if at least 1 result)
                    const responsePaginationInfo = doc.querySelector('.pagination-info');
                    if (responsePaginationInfo) {
                        paginationInfoContainer.innerHTML = responsePaginationInfo.innerHTML;
                    } else {
                        // Check if we should show pagination info by extracting count from response
                        const countMatch = doc.body.textContent.match(/of (\d+) admins/);
                        const totalCount = countMatch ? parseInt(countMatch[1]) : 0;

                        if (totalCount > 1) {
                            // Reconstruct pagination info
                            const firstItem = 1;
                            const lastItem = Math.min(10, totalCount);
                            paginationInfoContainer.innerHTML =
                                `Showing ${firstItem} to ${lastItem} of ${totalCount} admins`;
                        } else {
                            paginationInfoContainer.innerHTML = '';
                        }
                    }

                    // Update pagination controls (show if >10 results)
                    const responsePagination = doc.querySelector('.pagination');
                    if (responsePagination) {
                        paginationContainer.innerHTML = responsePagination.innerHTML;
                    } else {
                        paginationContainer.innerHTML = '';
                    }

                    attachCircleEffect();
                    refreshAnimations();
                })
                .catch(error => {
                    console.error('Error:', error);
                    dynamicContent.innerHTML = '<div class="error-message">Failed to load results</div>';
                    paginationInfoContainer.innerHTML = '';
                    paginationContainer.innerHTML = '';
                });
        }

        // Handle search input with debounce
        let searchTimeout;
        searchBar.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(updateResults, 0);
        });

        // Handle filter changes
        if (filterForm) {
            filterForm.addEventListener('change', updateResults);
        }

        // Handle individual checkbox changes
        filterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateResults);
        });
    });
</script>
