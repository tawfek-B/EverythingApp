@props(['users' => null, 'sub' => false])

@php
    session([
        'breadcrumb_users' => array_merge(
            ['Home' => url('/welcome')],
            $sub
                ? ['Users subscribed to  ' . App\Models\Subject::findOrFail(session('subject'))->name => Request::url()]
                : ['Users' => Request::url()],
        ),
    ]);

    $usersCount = $users->count();

    // Get the search query and filter values from the request
    $searchQuery = request('search');
    $sort = request('sort', 'newest'); // Default to 'newest'
    $selectedSubjects = request('subjects', []);
    $filterNone = request('none', false);
    $subjectCounts = request('subject_count', []);

    // Normalize the search query by converting to lowercase and splitting into individual terms
    $searchTerms = $searchQuery ? array_filter(explode(' ', strtolower(trim($searchQuery)))) : [];

    // If $users is provided, use it directly; otherwise, fetch users based on search and filters
    if ($users !== null) {
        // Convert the $users collection to a query builder
        $modelToPass = App\Models\User::whereIn('id', $users->pluck('id'));
        $num = App\Models\User::count();
    } else {
        // Fetch all users based on search and filters
        $modelToPass = App\Models\User::query();
        $num = $users->count();
    }

    // Apply search, filters, and sorting
    $modelToPass = $modelToPass
        ->when($searchQuery, function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(userName) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(number) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(CONCAT(countryCode, number)) LIKE ?', ["%{$term}%"]);
                });
            }
            return $query;
        })
        ->when($selectedSubjects || $filterNone, function ($query) use ($selectedSubjects, $filterNone) {
            $query->where(function ($q) use ($selectedSubjects, $filterNone) {
                if ($filterNone) {
                    $q->doesntHave('subjects');
                }
                if ($selectedSubjects) {
                    if ($filterNone) {
                        $q->orWhereHas('subjects', function ($q) use ($selectedSubjects) {
                            $q->whereIn('subjects.id', $selectedSubjects);
                        });
                    } else {
                        $q->whereHas('subjects', function ($q) use ($selectedSubjects) {
                            $q->whereIn('subjects.id', $selectedSubjects);
                        });
                    }
                }
            });
        })
        ->when($subjectCounts, function ($query) use ($subjectCounts) {
            $query->where(function ($q) use ($subjectCounts) {
                foreach ($subjectCounts as $count) {
                    if ($count === '1') {
                        $q->orHas('subjects', '=', 1);
                    } elseif ($count === '2-3') {
                        $q->orWhereHas('subjects', function ($q) {
                            $q->groupBy('user_id')->havingRaw('COUNT(subjects.id) BETWEEN 2 AND 3');
                        });
                    } elseif ($count === '4-5') {
                        $q->orWhereHas('subjects', function ($q) {
                            $q->groupBy('user_id')->havingRaw('COUNT(subjects.id) BETWEEN 4 AND 5');
                        });
                    } elseif ($count === '6+') {
                        $q->orWhereHas('subjects', function ($q) {
                            $q->groupBy('user_id')->havingRaw('COUNT(subjects.id) >= 6');
                        });
                    }
                }
            });
        })
        ->when($sort, function ($query) use ($sort) {
            if ($sort === 'username-a-z') {
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

    // Prepare filter options
    $filterOptions = App\Models\Subject::pluck('name', 'id')->toArray();

    // Split users into chunks
    $chunkSize = 2;
    $chunkedUsers = [];
    for ($i = 0; $i < $chunkSize; $i++) {
        $chunkedUsers[$i] = [];
    }

    foreach ($modelToPass as $index => $user) {
        $chunkIndex = $index % $chunkSize;
        $chunkedUsers[$chunkIndex][] = $user;
    }
@endphp

<x-layout :objects=true
    object="{{ !$sub ? 'USERS' : 'USERS SUBSCRIBED TO ' . Str::upper(App\Models\Subject::findOrFail(session('subject'))->name) }}">
    <x-breadcrumb :links="array_merge(
        ['Home' => url('/welcome')],
        $sub
            ? ['Users subscribed to ' . App\Models\Subject::findOrFail(session('subject'))->name => Request::url()]
            : ['Users' => Request::url()],
    )" />

    <x-cardcontainer :model=$modelToPass :addLink=null :filterOptions=$filterOptions :showSubjectCountFilter=true
        :showUsernameSort=true :showNameSort=false num="{{ $num }}">
        <div id="dynamic-content" style="width:100%; display:flex; flex-direction:row">
            @foreach ($chunkedUsers as $chunk)
                <div class="chunk">
                    @foreach ($chunk as $user)
                        <x-card link="user/{{ $user->id }}">
                            ● User name: {{ $user->userName }}<br>
                            ● Number: {{ $user->countryCode }} {{ $user->number }}<br>
                            ● Subjects subscribed to:
                            @if ($user->subjects->count() == 0)
                                <div style="color:black">none</div>
                            @else
                                <div>
                                    [
                                    @foreach ($user->subjects as $subject)
                                        {{ $subject->name }}
                                        @if (!$loop->last)
                                            -
                                        @endif
                                    @endforeach
                                    ]
                                </div>
                            @endif
                            ● Number of Subscribed Lectures:
                            @if ($user->lectures->count() == 0)
                                <div style="color:black">none</div>
                            @else
                                <div>
                                    {{ $user->lectures->count() }}

                                </div>
                            @endif
                        </x-card>
                    @endforeach
                </div>
            @endforeach
        </div>
    </x-cardcontainer>

    @if ($modelToPass->total() > 1)
        {{-- Only show if more than 1 result --}}
        <div class="pagination-info" style="text-align: center; margin-bottom: 2%; font-size: 24px; color: #000000;">
            Showing {{ $modelToPass->firstItem() }} to {{ $modelToPass->lastItem() }} of {{ $modelToPass->total() }}
            users
        </div>
    @else
        <div class="pagination-info" style="display: none;"></div> {{-- Hidden container --}}
    @endif
    <div class="pagination" style="@if ($modelToPass->total() <= 10) display:none; @endif">
        {{ $modelToPass->appends([
                'search' => $searchQuery,
                'sort' => $sort,
                'subjects' => $selectedSubjects,
                'none' => $filterNone,
                'subject_count' => request('subject_count', []),
            ])->links() }}
    </div>
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
            const selectedSubjects = Array.from(document.querySelectorAll(
                'input[name="subjects[]"]:checked')).map(el => el.value);
            const filterNone = document.getElementById('filter-none')?.checked || false;
            const subjectCounts = Array.from(document.querySelectorAll(
                'input[name="subject_count[]"]:checked')).map(el => el.value);

            // Build the query string
            const params = new URLSearchParams();
            params.set('search', query);
            params.set('sort', selectedSort);
            selectedSubjects.forEach(subject => params.append('subjects[]', subject));
            if (filterNone) {
                params.set('none', 'true');
            }
            subjectCounts.forEach(count => params.append('subject_count[]', count));

            fetch(`{{ request()->url() }}?${params.toString()}`)
                .then(response => response.text())
                .then(data => {
                    // Parse the response and extract the dynamic content
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');
                    const newContent = doc.getElementById('dynamic-content').innerHTML;

                    // Update the dynamic content without changing the structure
                    dynamicContent.innerHTML = newContent;

                    // Reattach the circle effect
                    attachCircleEffect();
                    refreshAnimations();

                    if (@json($usersCount) > 10) {
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
