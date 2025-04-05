@props(['user' => App\Models\User::findOrFail(session('user'))])

<x-layout>
    <x-breadcrumb :links="['Home' => url('/welcome'), 'Users' => url('/users'), $user->userName => Request::url()]" :align=true />
    <x-infocard :editLink="'user/edit/' . $user->id" deleteLink="deleteuser/{{ $user->id }}" :object=$user objectType="User"
        name="{{ $user->userName }}">
        ● User name: {{ $user->userName }}<br>
        ● User Number: {{ $user->countryCode }} {{ $user->number }}<br>
        ● Subjects subscribed to:
        @if ($user->subjects->count() == 0)
            <div style="color:black">none</div>
        @else
            <div>
                @if ($user->subjects->count() != 1)
                    [
                @endif
                @foreach ($user->subjects as $subject)
                    <a href="/subject/{{ $subject->id }}" style="color:blue">{{ $subject->name }}</a>
                    @if (!$loop->last)
                        -
                    @endif
                @endforeach
                @if ($user->subjects->count() != 1)
                    ]
                @endif
            </div>
        @endif
        ● Number of lectures subscribed to:

        @if ($user->lectures->count() == 0)
        0
        @else
        <a href="/user/{{$user->id}}/lectures" style="color:blue">{{ $user->lectures->count() }}</a>
            @endif

            @if ($user->isBanned)
                <div style="color: red; font-weight: bold; margin-top: 1rem; font-size:60px;">BANNED</div>
            @endif
    </x-infocard>

</x-layout>
