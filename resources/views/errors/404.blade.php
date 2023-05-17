{{-- @extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found')) --}}

@extends('layouts.main')

@section('content')
<div class="text-center">
    <div class="error mx-auto" data-text="404">404</div>
    <p class="lead text-gray-800 mb-5">Page or Route Not Found.</p>
    <p class="text-gray-500 mb-0">You are trying to access non-existent page or routes.</p>
    <a href="{{ url()->previous() }}">&larr; Back to Dashboard</a>
</div>
@endsection
