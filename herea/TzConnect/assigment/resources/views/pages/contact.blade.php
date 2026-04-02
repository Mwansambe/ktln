@extends('layouts.app')

@section('title')
<title>Contact Us</title>
@endsection

@section('content')
<div class="page-content">
    <h1>Contact Us</h1>
    <p>If you have any questions, feedback, or suggestions, feel free to reach out.</p>
    <form>
        <input type="text" placeholder="Your Name" required>
        <input type="email" placeholder="Your Email" required>
        <textarea placeholder="Your Message" rows="5" style="width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:4px;"></textarea>
        <button type="submit">Send Message</button>
    </form>
</div>
@endsection
