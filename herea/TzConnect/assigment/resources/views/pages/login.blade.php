@extends('layouts.app')
@section('content')
  <div class="container">
    <h2>Login</h2>
    <form>
      <input type="email" placeholder="Email" required>
      <input type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <div class="switch">
      Don’t have an account? <a href="{{route('register')}}">Register</a>
    </div>
  </div>
@endsection
