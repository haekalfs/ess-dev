@extends('layouts.main')

@section('content')
<div class="text-center">
    <div class="error mx-auto" data-text="403">403</div>
    <p class="lead text-gray-800 mb-5">You are not authorized to access this page.</p>
    <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
    <a href="{{ url()->previous() }}">&larr; Back to Dashboard</a>
</div>
@endsection