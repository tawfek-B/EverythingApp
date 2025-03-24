@props(['lecture' => App\Models\Lecture::findOrFail(session('lecture')), 'subject' => false])

<x-layout>
    <x-breadcrumb :links="array_merge(['Home'=>url('/welcome'), 'Lectures' =>url('/lectures')], [
        $lecture->name => Request::url(),
    ])" align=true />
    <x-infocard :editLink="'lecture/edit/' . $lecture->id" deleteLink="deletelecture/{{ $lecture->id }}" :object=$lecture
        objectType="Lecture" image="{{ asset($lecture->image) }}" name="{{ $lecture->name }}" :file=true>
        Lecture Name: {{ $lecture->name }}<br>
        Lecture Description: {{ $lecture->description }}<br>
        Subject: <a href="/subject/{{ $lecture->subject_id }}" style="color:blue">{{ App\Models\Subject::findOrFail($lecture->subject_id)->name }}</a>

    </x-infocard>

</x-layout>
