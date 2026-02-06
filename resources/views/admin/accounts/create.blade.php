@extends('layouts.app')
@section('title', 'Create Admin')

@section('content')
<div class="container mt-4">
    <h3>Create Admin</h3>
    <form method="POST" action="{{ route('adminsettings.store') }}">
        @csrf
        <input name="firstname" placeholder="First name" required>
        <input name="lastname" placeholder="Last name" required>
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <input name="password_confirmation" type="password" placeholder="Confirm Password" required>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="nurse">Nurse</option>
            <option value="staff">Staff</option>
        </select>
        <button class="btn btn-primary">Create</button>
    </form>
</div>
@endsection