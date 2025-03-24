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
        <div class="pagination-info" style="text-align: center; margin-bottom: 2%; font-size: 24px; color: #000000;">
            Showing {{ $modelToPass->firstItem() }} to {{ $modelToPass->lastItem() }} of {{ $modelToPass->total() }}
            subjects
        </div>
    @endif

    @if ($modelToPass->total() > 10)
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

        searchBar.addEventListener('input', function() {
            const query = searchBar.value;

            // Get current filter values
            const selectedSort = document.querySelector('input[name="sort"]:checked')?.value ||
                'newest';
            const selectedTeachers = Array.from(document.querySelectorAll(
                'input[name="teachers[]"]:checked')).map(el => el.value);
            const filterNone = document.getElementById('filter-none')?.checked || false;
            const teacherCounts = Array.from(document.querySelectorAll(
                'input[name="teacher_count[]"]:checked')).map(el => el.value);

            // Build the query string
            const params = new URLSearchParams();
            params.set('search', query);
            params.set('sort', selectedSort);
            selectedTeachers.forEach(teacher => params.append('teachers[]', teacher));
            if (filterNone) {
                params.set('none', 'true');
            }
            teacherCounts.forEach(count => params.append('teacher_count[]', count));

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

                    if (@json($modelToPass->total()) > 1) {
                        const paginationInfo = doc.querySelector('.pagination-info');
                        const paginationInfoContainer = document.querySelector('.pagination-info');
                        if (paginationInfo) {
                            paginationInfoContainer.innerHTML = paginationInfo.innerHTML;
                        } else {
                            paginationInfoContainer.innerHTML = '';
                        }
                    } else {
                        // Hide the pagination-info div if there is only one result
                        const paginationInfoContainer = document.querySelector('.pagination-info');
                        if (paginationInfoContainer) {
                            paginationInfoContainer.innerHTML = '';
                        }
                    }

                    // Update pagination links conditionally
                    const pagination = doc.querySelector('.pagination');
                    const paginationContainer = document.querySelector('.pagination');
                    if (pagination) {
                        paginationContainer.innerHTML = pagination.innerHTML;
                    } else {
                        paginationContainer.innerHTML = '';
                    }
                })
                .catch(error => console.error('Error fetching search results:', error));
        });
    });
</script>
