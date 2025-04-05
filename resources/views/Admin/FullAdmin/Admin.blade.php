@props(['admin' => App\Models\Admin::findOrFail(session('admin'))])

<x-layout>
    <x-breadcrumb :links="array_merge(session('breadcrumb_admins', ['Home' => url('/welcome')]), ['Admins' =>url('/admins')], [
        $admin->name => Request::url(),
    ])" align=true style="align-self:flex-start"/>
    <x-infocard :editLink="'admin/edit/' . $admin->id" deleteLink="deleteadmin/{{ $admin->id }}" :object=$admin id="{{$admin->id}}"
        objectType="Admin" privileges="{{ $admin->privileges }}" image="{{ asset($admin->image) }}"
        name="{{ $admin->name }}">
        ● Admin Name: {{ $admin->name }}<br>
        ● Admin User Name: {{ $admin->userName }}<br>
        ● Admin Number: {{$admin->countryCode}} {{ $admin->number }}<br>
        ● Privileges:
        @if ($admin->privileges == 0)
            <a href="/teacher/{{ $admin->teacher_id }}" style="color:blue">Teacher</a>
        @elseif ($admin->privileges == 1)
            Semi-Admin
        @else
            Admin
        @endif

    </x-infocard>

</x-layout>
