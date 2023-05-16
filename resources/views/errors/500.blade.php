{{-- @extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error')) --}}

@extends('layouts.main')

@section('content')
<div class="text-center">
    <div class="error mx-auto" data-text="500">500</div>
    <p class="lead text-gray-800 mb-5">Unable to access this page.</p>
    <p class="text-gray-500 mb-0">You are encountered server-side error, probably the database...</p>
    <a href="{{ url()->previous() }}">&larr; Back to Dashboard</a>
</div>
@endsection