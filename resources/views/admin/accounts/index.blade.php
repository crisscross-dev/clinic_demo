@extends('layouts.app')
@section('title', 'Admin List')

@section('content')
<div class="container mt-4">
    <a href="{{ route('adminsettings.create') }}" class="btn btn-primary mb-3">Create Admin</a>

    @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Role</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $a)
            <tr>
                <td>{{ $a->username }}</td>
                <td>{{ $a->firstname }} {{ $a->lastname }}</td>
                <td>{{ $a->role }}</td>
                <td class="text-end">
                    <a href="{{ route('adminsettings.edit', $a->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('adminsettings.destroy', $a->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No admins found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection