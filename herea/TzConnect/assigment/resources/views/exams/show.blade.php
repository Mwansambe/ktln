@extends('layouts.app')
@section('title', $exam->title)

@section('content')
<a href="{{ route('exams.index') }}"> Back to all exams</a>
<h1>{{ $exam->title }}</h1>
    <p><strong>Code:</strong> {{ $exam->code }}</p>
    <p><strong>Year:</strong> {{ $exam->year }}</p>
    <p><strong>Downloads:</strong> {{ number_format($exam->downloads) }}</p>
@endsection

