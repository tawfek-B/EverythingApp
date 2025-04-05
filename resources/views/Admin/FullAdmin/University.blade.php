@props(['university' => App\Models\university::findOrFail(session('university'))])

<x-layout>
    <x-breadcrumb :links="['Home' => url('/welcome'), 'University' => url('/universities'), $university->name => Request::url()]" :align=true />
    <x-infocard :editLink="'university/edit/' . $university->id" :deleteLink="'deleteuniversity/' . $university->id" :object=$university
        objectType="University" name="{{ $university->name }}" image="{{ asset($university->image) }}">
        ● University name: {{ $university->name }}<br>
        ● Teachers: @if ($university->teachers->count() == 0)
            0
        @else
            <a href="/university/{{ $university->id }}/teachers" style="color:blue">{{ $university->teachers->count() }}</a>
        @endif
        <br>

    </x-infocard>

</x-layout>
