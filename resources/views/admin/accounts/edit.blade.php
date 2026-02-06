@extends('layouts.app')
@section('title', 'Edit Admin')

@section('content')
<div class="container mt-4">
    <h3>Edit Admin</h3>
    <form method="POST" action="{{ route('adminsettings.update', $adminsetting->id) }}">
        @csrf @method('PUT')
        <input name="firstname" value="{{ old('firstname', $adminsetting->firstname) }}" required>
        <input name="lastname" value="{{ old('lastname', $adminsetting->lastname) }}" required>
        <input name="username" value="{{ old('username', $adminsetting->username) }}" required>
        <input name="password" type="password" placeholder="New password (optional)">
        <input name="password_confirmation" type="password" placeholder="Confirm new password">
        <select name="role">
            @foreach(['admin','nurse','staff'] as $role)
            <option value="{{ $role }}" {{ $adminsetting->role==$role?'selected':'' }}>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection