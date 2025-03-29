@props(['lectures' => null, 'lec' => false, 'user' => false, 'num' => App\Models\Lecture::count()])

@php
    // Get the search query and filter values from the request
    $searchQuery = request('search');
    $sort = request('sort', 'newest'); // Default to 'newest'
    $selectedSubjects = request('subjects', []);
    $filterNone = request('none', false);
    $subjectCounts = request('subject_count', []);

    // Normalize the search query
    $searchTerms = $searchQuery ? array_filter(explode(' ', strtolower(trim($searchQuery)))) : [];

    // Initialize base query
    if ($lectures !== null) {
        $query = App\Models\Lecture::whereIn('id', $lectures->pluck('id'));
        $lecturesCount = $query->count(); // Get count before pagination
    } else {
        $query = App\Models\Lecture::query();
        $lecturesCount = $query->count(); // Get total count
    }

    // Apply filters
    $query
        ->when($searchQuery, function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])->orWhereRaw('LOWER(description) LIKE ?', [
                        "%{$term}%",
                    ]);
                });
            }
        })
        ->when($selectedSubjects, function ($query) use ($selectedSubjects) {
            $query->whereHas('subject', function ($q) use ($selectedSubjects) {
                $q->whereIn('id', $selectedSubjects);
            });
        })
        ->when($filterNone, function ($query) {
            $query->whereDoesntHave('subject');
        })
        ->when($subjectCounts, function ($query) use ($subjectCounts) {
            $query->where(function ($q) use ($subjectCounts) {
                foreach ($subjectCounts as $count) {
                    if ($count === '1') {
                        $q->orHas('subject', '=', 1);
                    } // Add other count conditions as needed
                }
            });
        });

    // Apply sorting
    if ($sort === 'name-a-z') {
        $query->orderByRaw('LOWER(name) ASC');
    } elseif ($sort === 'name-z-a') {
        $query->orderByRaw('LOWER(name) DESC');
    } elseif ($sort === 'newest') {
        $query->orderBy('created_at', 'desc');
    } elseif ($sort === 'oldest') {
        $query->orderBy('created_at', 'asc');
    }

    // Get filtered count before pagination
    $filteredCount = $query->count();
    $modelToPass = $query->paginate(10);

    // Prepare filter options
    $filterOptions = App\Models\Subject::pluck('name', 'id')->toArray();

    // Split lectures into chunks
    $chunkSize = 2;
    $chunkedLectures = array_fill(0, $chunkSize, []);

    foreach ($modelToPass as $index => $lecture) {
        $chunkIndex = $index % $chunkSize;
        $chunkedLectures[$chunkIndex][] = $lecture;
    }
@endphp
<x-layout :objects=true
    object="{{ $user ? Str::upper(App\Models\User::findOrFail(session('user'))->userName) . ' SUBSCRIBED LECTURES' : (!$lec ? 'LECTURES' : 'LECTURES FROM ' . Str::upper(App\Models\Subject::findOrFail(session('subject'))->name)) }}">
    <x-breadcrumb :links="array_merge(
        ['Home' => url('/welcome')],
        [
            $user
                ? App\Models\User::findOrFail(session('user'))->userName . ' subscribed lectures'
                : (!$lec
                    ? 'Lectures'
                    : 'Lectures from ' . App\Models\Subject::findOrFail(session('subject'))->name) => Request::url(),
        ],
    )" />

    <x-cardcontainer :model=$modelToPass addLink="addlecture" :filterOptions=$filterOptions :showSubjectCountFilter=false
        :showUsernameSort=false :showNameSort=false>
        <div id="dynamic-content" style="width:100%; display:flex; flex-direction:row">
            @foreach ($chunkedLectures as $chunk)
                <div class="chunk">
                    @foreach ($chunk as $lecture)
                        <x-card link="lecture/{{ $lecture->id }}" image="{{ asset($lecture->image) }}" object="Lecture">
                            ● Lecture Name: {{ $lecture->name }}<br>
                            {{-- ● Lecture Description:
                            <div class="description">
                                @foreach (explode("\n", $lecture->description) as $line)
                                    <div class="description-line">{{ $line }}</div>
                                @endforeach
                            </div> --}}
                            ● For Subject: {{ $lecture->subject->name }}
                        </x-card>
                    @endforeach
                </div>
            @endforeach
        </div>
    </x-cardcontainer>

    @if ($modelToPass->total() > 1)
        <div class="pagination-info" style="text-align: center; margin-bottom: 2%; font-size: 24px; color: #000000;">
            Showing {{ $modelToPass->firstItem() }} to {{ $modelToPass->lastItem() }} of {{ $modelToPass->total() }}
            lectures
        </div>
    @endif

    @if ($modelToPass->total() > 10)
        <div class="pagination">
            {{ $modelToPass->appends([
                    'search' => $searchQuery,
                    'sort' => $sort,
                    'subjects' => $selectedSubjects,
                    'none' => $filterNone,
                ])->links() }}
        </div>
    @endif
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBar = document.querySelector('.search-bar');
        const dynamicContent = document.getElementById('dynamic-content');
        const filterForm = document.querySelector('.filter-dropdown');
        const filterCheckboxes = document.querySelectorAll(
            'input[type="checkbox"][name^="subjects"], input[name="none"], input[name^="subject_count"]');
        const paginationInfoContainer = document.querySelector('.pagination-info');
        const paginationContainer = document.querySelector('.pagination');

        // Function to fetch and update results
        function updateResults() {
            const query = searchBar.value;
            const selectedSort = document.querySelector('input[name="sort"]:checked')?.value || 'newest';
            const selectedSubjects = Array.from(document.querySelectorAll('input[name="subjects[]"]:checked'))
                .map(el => el.value);
            const filterNone = document.getElementById('filter-none')?.checked || false;
            const subjectCounts = Array.from(document.querySelectorAll('input[name="subject_count[]"]:checked'))
                .map(el => el.value);

            // Build query string
            const params = new URLSearchParams();
            params.set('search', query);
            params.set('sort', selectedSort);
            selectedSubjects.forEach(subject => params.append('subjects[]', subject));
            if (filterNone) params.set('none', 'true');
            subjectCounts.forEach(count => params.append('subject_count[]', count));

            paginationInfoContainer.innerHTML = '';
            paginationContainer.innerHTML = '';

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
                        const countMatch = doc.body.textContent.match(/of (\d+) lectures/);
                        const totalCount = countMatch ? parseInt(countMatch[1]) : 0;

                        if (totalCount > 1) {
                            // Reconstruct pagination info
                            const firstItem = 1;
                            const lastItem = Math.min(10, totalCount);
                            paginationInfoContainer.innerHTML =
                                `Showing ${firstItem} to ${lastItem} of ${totalCount} lectures`;
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
                    dynamicContent.innerHTML = '<div class="error-message">Failed to load lectures</div>';
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

        // Initial attachment of effects
        attachCircleEffect();
    });
</script>
