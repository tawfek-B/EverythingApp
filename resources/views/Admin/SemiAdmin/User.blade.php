@props(['user' => App\Models\User::findOrFail(session('user'))])

<x-layout>
    <x-breadcrumb :links="['Home' => url('/welcome'), 'Users' => url('/users'), $user->userName => Request::url()]" :align=true />
    <x-infocard :editLink="'user/edit/' . $user->id" :deleteLink=null :object=$user objectType="User"
        name="{{ $user->userName }}">
        User name: {{ $user->userName }}<br>
        User Number: {{ $user->countryCode }} {{ $user->number }}<br>
        Subjects subscribed to:
        @if ($user->subjects->count() == 0)
            <div style="color:black">none</div>
        @else
            <div>
                @if ($user->subjects->count() != 1)
                    [
                @endif
                @foreach ($user->subjects as $subject)
                    <span>{{ $subject->name }}</span>
                    @if (!$loop->last)
                        -
                    @endif
                @endforeach
                @if ($user->subjects->count() != 1)
                    ]
                @endif
            </div>
        @endif
        Number of lectures subscribed to:

        @if ($user->lectures->count() == 0)
        0
        @else
        <span>{{ $user->lectures->count() }}</span>
            @endif
    </x-infocard>

</x-layout>
