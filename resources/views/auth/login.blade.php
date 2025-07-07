@extends('layouts.app')

@section('content')
<div style="max-width:400px;margin:40px auto;">
    <h2>Login</h2>
    <form method="POST" action="/login">
        @csrf
        <div>
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div style="margin-top:10px;">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:20px;">Login</button>
    </form>
</div>
@endsection
