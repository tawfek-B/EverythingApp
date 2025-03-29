@props(['subjects' => null, 'num' => App\Models\Subject::count()])

@php
    // Get the search query, sort parameter, and filter values from the request
    $searchQuery = request('search');
    $sort = request('sort', 'newest'); // Default to 'newest'
    $selectedTeachers = request('teachers', []); // Selected teachers for filtering
    $filterNone = request('none', false); // Filter for subjects with no teachers
    $teacherCounts = request('teacher_count', []); // Filter by number of teachers
    $lectureCounts = request('lecture_count', []); // Filter by number of lectures
    $userCounts = request('user_count', []); // Filter by number of users subscribed

    // Normalize the search query by converting to lowercase and splitting into individual terms
    $searchTerms = $searchQuery ? array_filter(explode(' ', strtolower(trim($searchQuery)))) : [];

    // Fetch subjects based on the search query, sort parameter, and filters
    $modelToPass = App\Models\Subject::when($searchQuery, function ($query) use ($searchTerms) {
        foreach ($searchTerms as $term) {
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
        }
        return $query;
    })
        ->when($selectedTeachers || $filterNone, function ($query) use ($selectedTeachers, $filterNone) {
            $query->where(function ($q) use ($selectedTeachers, $filterNone) {
                if ($filterNone) {
                    $q->doesntHave('teachers'); // Subjects with no teachers
                }
                if ($selectedTeachers) {
                    if ($filterNone) {
                        $q->orWhereHas('teachers', function ($q) use ($selectedTeachers) {
                            $q->whereIn('teachers.id', $selectedTeachers);
                        });
                    } else {
                        $q->whereHas('teachers', function ($q) use ($selectedTeachers) {
                            $q->whereIn('teachers.id', $selectedTeachers);
                        });
                    }
                }
            });
        })
        ->when($teacherCounts, function ($query) use ($teacherCounts) {
            $query->where(function ($q) use ($teacherCounts) {
                foreach ($teacherCounts as $count) {
                    if ($count === '1') {
                        $q->orHas('teachers', '=', 1); // Subjects with exactly 1 teacher
                    } elseif ($count === '2-3') {
                        $q->orWhereHas('teachers', function ($q) {
                            $q->groupBy('subject_id')->havingRaw('COUNT(teachers.id) BETWEEN 2 AND 3');
                        });
                    } elseif ($count === '4-5') {
                        $q->orWhereHas('teachers', function ($q) {
                            $q->groupBy('subject_id')->havingRaw('COUNT(teachers.id) BETWEEN 4 AND 5');
                        });
                    } elseif ($count === '6+') {
                        $q->orWhereHas('teachers', function ($q) {
                            $q->groupBy('subject_id')->havingRaw('COUNT(teachers.id) >= 6');
                        });
                    }
                }
            });
        })
        ->when($lectureCounts, function ($query) use ($lectureCounts) {
            $query->where(function ($q) use ($lectureCounts) {
                foreach ($lectureCounts as $count) {
                    if ($count === '0') {
                        $q->orDoesntHave('lectures'); // Subjects with 0 lectures
                    } elseif ($count === '1-5') {
                        $q->orHas('lectures', '>=', 1)->has('lectures', '<=', 5); // Subjects with 1-5 lectures
                    } elseif ($count === '6-10') {
                        $q->orHas('lectures', '>=', 6)->has('lectures', '<=', 10); // Subjects with 6-10 lectures
                    } elseif ($count === '11-20') {
                        $q->orHas('lectures', '>=', 11)->has('lectures', '<=', 20); // Subjects with 11-20 lectures
                    } elseif ($count === '21+') {
                        $q->orHas('lectures', '>=', 21); // Subjects with 21+ lectures
                    }
                }
            });
        })
        ->when($userCounts, function ($query) use ($userCounts) {
            $query->where(function ($q) use ($userCounts) {
                foreach ($userCounts as $count) {
                    if ($count === '0') {
                        $q->orDoesntHave('users'); // Subjects with 0 users subscribed
                    } elseif ($count === '1-5') {
                        $q->orHas('users', '>=', 1)->has('users', '<=', 5); // Subjects with 1-5 users subscribed
                    } elseif ($count === '6-10') {
                        $q->orHas('users', '>=', 6)->has('users', '<=', 10); // Subjects with 6-10 users subscribed
                    } elseif ($count === '11-20') {
                        $q->orHas('users', '>=', 11)->has('users', '<=', 20); // Subjects with 11-20 users subscribed
                    } elseif ($count === '21+') {
                        $q->orHas('users', '>=', 21); // Subjects with 21+ users subscribed
                    }
                }
            });
        })
        ->when($sort, function ($query) use ($sort) {
            if ($sort === 'name-a-z') {
                $query->orderByRaw('LOWER(name) ASC'); // Sort by name A-Z (case-insensitive)
            } elseif ($sort === 'name-z-a') {
                $query->orderByRaw('LOWER(name) DESC'); // Sort by name Z-A (case-insensitive)
            } elseif ($sort === 'newest') {
                $query->orderBy('created_at', 'desc'); // Sort by creation date (newest)
            } elseif ($sort === 'oldest') {
                $query->orderBy('created_at', 'asc'); // Sort by creation date (oldest)
            }
        })
        ->paginate(10);

    // Prepare filter options for teachers
    $filterOptions = App\Models\Teacher::pluck('name', 'id')->toArray();

    // Split subjects into chunks
    $chunkSize = 2;
    $chunkedSubjects = [];
    for ($i = 0; $i < $chunkSize; $i++) {
        $chunkedSubjects[$i] = [];
    }

    foreach ($modelToPass as $index => $subject) {
        $chunkIndex = $index % $chunkSize;
        $chunkedSubjects[$chunkIndex][] = $subject;
    }
@endphp

<x-layout :objects=true object="SUBJECTS">
    <x-breadcrumb :links="['Home' => url('/welcome'), 'Subjects' => url('/subjects')]" />

    <x-cardcontainer :model=$modelToPass addLink="addsubject" :filterOptions=$filterOptions :showSubjectCountFilter=true
        filterByTeachers=true :showNameSort=true :showUsernameSort=false>
        <div id="dynamic-content" style="width:100%; display:flex; flex-direction:row">
            @foreach ($chunkedSubjects as $chunk)
                <div class="chunk">
                    @foreach ($chunk as $subject)
                        <x-card link="subject/{{ $subject->id }}" image="{{ asset($subject->image) }}" object="Subject">
                            ● Subject Name: {{ $subject->name }}<br>
                            ● Lectures: {{ $subject->lectures->count() }}<br>
                            ● Users Subscribed: {{ $subject->users->count() }}<br>
                            ● Teachers:
                            @if ($subject->teachers->count() == 0)
                                <div style="color:black; margin-right:auto;">&emsp;none</div>
                            @elseif($subject->teachers->count() == 1)
                                <br>&emsp;
                                @foreach ($subject->teachers as $teacher)
                                    {{ $teacher->name }}
                                @endforeach
                            @else
                                <br>&emsp;
                                [
                                @foreach ($subject->teachers as $teacher)
                                    {{ $teacher->name }}
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
        {{-- Changed from >1 to >0 to show when at least 1 subject --}}
        <div class="pagination-info" style="text-align: center; margin-bottom: 2%; font-size: 24px; color: #000000;">
            Showing {{ $modelToPass->firstItem() }} to {{ $modelToPass->lastItem() }} of {{ $modelToPass->total() }}
            subjects
        </div>
    @endif

    @if ($num > 10)
        {{-- Keep this for pagination controls --}}
        <div class="pagination">
            {{ $modelToPass->appends([
                    'search' => $searchQuery,
                    'sort' => $sort,
                    'teachers' => $selectedTeachers,
                    'none' => $filterNone,
                    'teacher_count' => $teacherCounts,
                    'lecture_count' => $lectureCounts,
                    'user_count' => $userCounts,
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
            'input[type="checkbox"][name^="teachers"], input[name="none"], input[name^="teacher_count"], input[name^="lecture_count"], input[name^="user_count"]'
            );
        const paginationInfoContainer = document.querySelector('.pagination-info');
        const paginationContainer = document.querySelector('.pagination');

        // Function to fetch and update results
        function updateResults() {
            const query = searchBar.value;
            const selectedSort = document.querySelector('input[name="sort"]:checked')?.value || 'newest';
            const selectedTeachers = Array.from(document.querySelectorAll('input[name="teachers[]"]:checked'))
                .map(el => el.value);
            const filterNone = document.getElementById('filter-none')?.checked || false;
            const teacherCounts = Array.from(document.querySelectorAll('input[name="teacher_count[]"]:checked'))
                .map(el => el.value);
            const lectureCounts = Array.from(document.querySelectorAll('input[name="lecture_count[]"]:checked'))
                .map(el => el.value);
            const userCounts = Array.from(document.querySelectorAll('input[name="user_count[]"]:checked')).map(
                el => el.value);

            // Build query string
            const params = new URLSearchParams();
            params.set('search', query);
            params.set('sort', selectedSort);
            selectedTeachers.forEach(teacher => params.append('teachers[]', teacher));
            if (filterNone) params.set('none', 'true');
            teacherCounts.forEach(count => params.append('teacher_count[]', count));
            lectureCounts.forEach(count => params.append('lecture_count[]', count));
            userCounts.forEach(count => params.append('user_count[]', count));

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
                        const countMatch = doc.body.textContent.match(/of (\d+) subjects/);
                        const totalCount = countMatch ? parseInt(countMatch[1]) : 0;

                        if (totalCount > 1) {
                            // Reconstruct pagination info
                            const firstItem = 1;
                            const lastItem = Math.min(10, totalCount);
                            paginationInfoContainer.innerHTML =
                                `Showing ${firstItem} to ${lastItem} of ${totalCount} subjects`;
                        } else {
                            paginationInfoContainer.innerHTML = '';
                        }
                    }

                    // Update pagination controls (show if >10 results)
                    const responsePagination = doc.querySelector('.pagination');
                    if (responsePagination) {
                        paginationContainer.innerHTML = responsePagination.innerHTML;
                    } else {
                        const totalCount = doc.querySelector('.pagination-info')?.textContent.match(
                            /of (\d+) subjects/)?.[1] || 0;
                        if (totalCount > 10) {
                            // We should have pagination but it's missing from response
                            // You may need to reconstruct it here if needed
                        } else {
                            paginationContainer.innerHTML = '';
                        }
                    }

                    attachCircleEffect();
                    refreshAnimations();
                })
                .catch(error => {
                    console.error('Error:', error);
                    dynamicContent.innerHTML = '<div class="error-message">Failed to load subjects</div>';
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
