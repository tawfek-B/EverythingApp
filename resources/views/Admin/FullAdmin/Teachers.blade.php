@props(['teachers' => null, 'num' => App\Models\Teacher::count()])

@php
    // Get the search query and filter values from the request
    $searchQuery = request('search');
    $sort = request('sort', 'newest'); // Default to 'newest'
    $selectedSubjects = request('subjects', []);
    $filterNone = request('none', false);
    $subjectCounts = request('subject_count', []); // Get selected subject counts as an array

    // Normalize the search query by converting to lowercase and splitting into individual terms
    $searchTerms = $searchQuery ? array_filter(explode(' ', strtolower(trim($searchQuery)))) : [];

    if ($teachers !== null) {
        // Convert the $lectures collection to a query builder
        $query = App\Models\Teacher::whereIn('id', $teachers->pluck('id'));
    } else {
        // Fetch all lectures based on search and filters
        $query = App\Models\Teacher::query();
    }
    // Fetch teachers based on the search query and filters
    $modelToPass = $query
        ->when($searchQuery, function ($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(userName) LIKE ?', ["%{$term}%"])
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
                            $q->groupBy('teacher_id')->havingRaw('COUNT(subjects.id) BETWEEN 2 AND 3');
                        });
                    } elseif ($count === '4-5') {
                        $q->orWhereHas('subjects', function ($q) {
                            $q->groupBy('teacher_id')->havingRaw('COUNT(subjects.id) BETWEEN 4 AND 5');
                        });
                    } elseif ($count === '6+') {
                        $q->orWhereHas('subjects', function ($q) {
                            $q->groupBy('teacher_id')->havingRaw('COUNT(subjects.id) >= 6');
                        });
                    }
                }
            });
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

    // Prepare filter options
    $filterOptions = App\Models\Subject::pluck('name', 'id')->toArray();

    // Split teachers into chunks
    $chunkSize = 2;
    $chunkedTeachers = [];
    for ($i = 0; $i < $chunkSize; $i++) {
        $chunkedTeachers[$i] = [];
    }

    foreach ($modelToPass as $index => $teacher) {
        $chunkIndex = $index % $chunkSize;
        $chunkedTeachers[$chunkIndex][] = $teacher;
    }
@endphp

<x-layout :objects=true
    object="{{ !$teachers ? 'TEACHERS' : 'TEACHERS FROM ' . Str::upper(App\Models\university::findOrFail(session('university'))->name) }}">
    <x-breadcrumb :links="array_merge(
        ['Home' => url('/welcome')],
        $teachers != null
            ? ['Teachers From ' . App\Models\university::findOrFail(session('university'))->name => Request::url()]
            : ['Teachers' => Request::url()],
    )" />
    <x-cardcontainer :model=$modelToPass addLink="addteacher" :filterOptions=$filterOptions :showSubjectCountFilter=true
        :showUsernameSort=true :showNameSort=true>
        <!-- Add a unique ID to the container for dynamic updates -->
        <div id="dynamic-content" style="width:100%; display:flex; flex-direction:row">
            @foreach ($chunkedTeachers as $chunk)
                <div class="chunk">
                    @foreach ($chunk as $teacher)
                        <x-card link="teacher/{{ $teacher->id }}" image="{{ asset($teacher->image) }}" object="Teacher">
                            ● Teacher Name: {{ $teacher->name }}<br>
                            ● Teacher User Name: {{ $teacher->userName }}<br>
                            ● Teacher Number: {{ $teacher->countryCode }} {{ $teacher->number }}<br>
                            ● Subjects:
                            @if ($teacher->subjects->count() == 0)
                                <div style="color:black; margin-right:auto">&emsp;none</div>
                            @else
                                <br>
                                &emsp;
                                [
                                @foreach ($teacher->subjects as $subject)
                                    {{ $subject->name }}
                                    @if (!$loop->last)
                                        -
                                    @endif
                                @endforeach
                                ]
                            @endif
                            <br>
                            ● Universities:
                            @if ($teacher->universities->count() == 0)
                                <div style="color:black; margin-right:auto">&emsp;none</div>
                            @else
                                <br>
                                &emsp;
                                [
                                @foreach ($teacher->universities as $university)
                                    {{ $university->name }}
                                    @if (!$loop->last)
                                        -
                                    @endif
                                @endforeach
                                ]
                            @endif
                        </x-card>
                    @endforeach
                </div>
            @endforeach
        </div>
    </x-cardcontainer>

    @if ($modelToPass->total() > 1)
        <div class="pagination-info" style="text-align: center; margin-bottom: 2%; font-size: 24px; color: #000000;">
            Showing {{ $modelToPass->firstItem() }} to {{ $modelToPass->lastItem() }} of {{ $modelToPass->total() }}
            teachers
        </div>
    @endif

    <!-- Conditionally render pagination links -->
    @if ($num > 10)
        <div class="pagination">
            {{ $modelToPass->appends([
                    'search' => $searchQuery,
                    'sort' => $sort,
                    'subjects' => $selectedSubjects,
                    'none' => $filterNone,
                    'subject_count' => request('subject_count', []), // Include subject_count as an array
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

                    if (@json($num) > 10) {
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