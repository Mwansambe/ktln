<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Exam Papers</title>
</head>
<body>
<h1>Exam papers</h1>
@forelse($exams as $exam)
<div>
    <strong>{{ $exam->title }}</stron><br>
        Code: {{ $exam->code }} | Year: {{ $exam->year }} | Downloads: {{ $exam->downloads }}
        <strong><a href="{{ route('exams.show', $exam->id) }}">{{ $exam->title }}</a></strong>
    </div>
    <hr>
    @empty
    <p>No exams found.</p>
    @endforelse
</body>
</html>
