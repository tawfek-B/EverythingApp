@props(['subject' => App\Models\Subject::findOrFail(session('subject'))])

<x-layout>
    <x-breadcrumb :links="['Home'=>url('/welcome'), 'Subjects' =>url('/subjects'), $subject->name=>url(Request::url())]" align=true/>
        <x-infocard :editLink="'subject/edit/' . $subject->id" deleteLink="deletesubject/{{ $subject->id }}"
        editLecturesLink="subject/{{ $subject->id }}/lectures" editSubscriptionsLink="subject/{{ $subject->id }}/users"
        lecturesCount="{{ $subject->lecturesCount }}"
        subscriptionsCount="{{ App\Models\Subject::withCount('users')->find(session('subject'))->users_count }}"
        :object=$subject objectType="Subject" image="{{ asset($subject->image) }}"
        name="{{ $subject->name }}"
        warning="WARNING: Deleting this subject will delete all its lectures and user subscriptions.">
        Subject Name: {{ $subject->name }}<br>
        Lectures: @if ($subject->lectures->count() == 0)
            0
        @else
            <a href="/subject/{{ $subject->id }}/lectures" style="color:blue">{{ $subject->lectures->count() }}</a>
        @endif
        <br>
        Users Subscribed: @if ($subject->users->count() == 0)
            0
        @else
            <a href="/subject/{{ $subject->id }}/users/"
                style="color:blue">{{ App\Models\Subject::withCount('users')->find(session('subject'))->users_count }}</a>
        @endif

        <br>
        @if (App\Models\Subject::withCount('teachers')->find(session('subject'))->teachers_count == 1)
            Teacher:
            @foreach ($subject->teachers as $teacher)
                <br>
                <a href="/teacher/{{ $teacher->id }}" style="color:blue">
                    {{ $teacher->name }}
                </a>
            @endforeach
        @elseif(App\Models\Subject::withCount('teachers')->find(session('subject'))->teachers_count == 0)
            Teachers: none
        @else
            Teachers:<br>[
            @foreach ($subject->teachers as $teacher)
                <a href="/teacher/{{ $teacher->id }}" style="color:blue">
                    {{ $teacher->name }}
                </a>
                @if (!$loop->last)
                    -
                @endif
            @endforeach
            ]
        @endif

        <br>
    </x-infocard>

</x-layout>
