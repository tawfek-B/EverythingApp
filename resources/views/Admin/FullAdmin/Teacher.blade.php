@props(['teacher' => App\Models\Teacher::findOrFail(session('teacher'))])

<x-layout>
    <x-breadcrumb :links="['Home' => url('/welcome'), 'Teachers' => url('/teacher'), $teacher->name => Request::url()]" align=true />
    <x-infocard :editLink="'teacher/edit/' . $teacher->id" deleteLink="deleteteacher/{{ $teacher->id }}" :object=$teacher objectType="Teacher"
        image="{{ asset($teacher->image) }}" name="{{ $teacher->name }}">
        Teacher Name: {{ $teacher->name }}<br>
        Teacher User Name: {{ $teacher->userName }}<br>
        Teacher Number: {{ $teacher->countryCode }} {{ $teacher->number }}<br>
        @if ($teacher->subjects->count() == 0)
            Subjects: none
            <br>
        @elseif($teacher->subjects->count() == 1)
            Subject:
            <div>
                @foreach ($teacher->subjects as $subject)
                    <a href="/subject/{{ $subject->id }}" style="color:blue;">
                        {{ $subject->name }}
                    </a>
                @endforeach
            </div>
        @else
            Subjects:
            <div>
                <div>
                    [
                    @foreach ($teacher->subjects as $subject)
                        <a href="/subject/{{ $subject->id }}" style="color:blue;">
                            {{ $subject->name }}
                        </a>
                        @if (!$loop->last)
                            -
                        @endif
                    @endforeach
                    ]
                </div>
            </div>

        @endif

        @if ($teacher->universities->count() == 0)
            Universities: none
        @elseif($teacher->universities->count() == 1)
            University:
            <div>
                @foreach ($teacher->universities as $university)
                    <a href="/university/{{ $university->id }}" style="color:blue;">
                        {{ $university->name }}
                    </a>
                @endforeach
            </div>
        @else
            Universities:
            <div>
                <div>
                    [
                    @foreach ($teacher->universities as $university)
                        <a href="/university/{{ $university->id }}" style="color:blue;">
                            {{ $university->name }}
                        </a>
                        @if (!$loop->last)
                            -
                        @endif
                    @endforeach
                    ]
                </div>
            </div>

        @endif
        <br>
        @php
            $links = json_decode($teacher->links, true);
        @endphp
        Links:
        <br>
        @if ($links['Facebook'])
            <a href="{{ $links['Facebook'] }}" target="_blank">Facebook</a>
            @if ($links['Instagram'] || $links['Telegram'] || $links['YouTube'])
                -
            @endif
        @endif

        @if ($links['Instagram'])
            <a href="{{ $links['Instagram'] }}">Instagram</a>
            @if ($links['Telegram'] || $links['YouTube'])
                -
            @endif
        @endif

        @if ($links['Telegram'])
            <a href="{{ $links['Telegram'] }}">Telegram</a>
            @if ($links['YouTube'])
                -
            @endif
        @endif

        @if ($links['YouTube'])
            <a href="{{ $links['YouTube'] }}">YouTube</a>
        @endif
    </x-infocard>

</x-layout>
