@extends('layouts.app')
@section('content')
  <div class="container">
    <h2>Register</h2>
    <form>
      <input type="text" placeholder="Full Name" required>
      <input type="email" placeholder="Email" required>
      <input type="password" placeholder="Password" required>
      <button type="submit">Sign Up</button>
    </form>
    <div class="switch">
      Already have an account? <a href="{{route('login')}}">Login</a>
    </div>
  </div>
@endsection
