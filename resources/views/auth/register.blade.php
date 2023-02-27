@extends('layouts.main')

@section ('content')
{{-- <div style="display: flex;justify-content: center;align-items: center;">
    <div class="margin-top:20px; border border-primary;" style="text-align: start; width:50%">
        <h3 style="margin:10px">REGISTER FOR USER</h3>
        <div style=" margin:30px">
            <div>
                <div class="row g-3 align-items-top">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                    <tr>
                                        <th>ID</th>
                                        <td><input type="id" class="form-control border-dark"></td>
                                    </tr>
                                    <tr>
                                        <th>User ID</th>
                                        <td><input type="user_id" class="form-control border-dark"></td>
                                    </tr>
                                    <tr>
                                        <th>Employee ID</th>
                                        <td><input type="employee_id" class="form-control border-dark"></td>
                                    </tr>
                                    <tr>
                                        <th>Full Name</th>
                                        <td><input type="name" class="form-control border-dark"></td>
                                    </tr>
                                    <tr>
                                        <th>Position</th>
                                        <td><input type="Position" class="form-control border-dark"></td>
                                    </tr>
                            </table>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <input class="btn btn-primary" type="submit" value="Submit">
                              </div>
                        </div>
                </div>
            </div> 
        </div> 
    </div>         
</div> --}}

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('PO Controller Users Registration') }}</div>

                <div class="card-body">
                    <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Department') }}</label>

                            <div class="col-md-6">
                                <select id="department" class="form-control @error('department') is-invalid @enderror" name="department"  required autocomplete="department" autofocus>
                                    <option value="HO">Head Office</option>
                                    <option value="BOD">Directors</option>
                                    <option value="HR">Human Resources</option>
                                    <option value="Finances">Finances</option>
                                    <option value="Sales">Sales & Marketing</option>
                                    <option value="Services">Services</option>
                                  </select>
                                {{-- <input id="department" type="text" class="form-control @error('department') is-invalid @enderror" name="department" value="{{ old('department') }}" required autocomplete="department" autofocus> --}}

                                @error('department')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0 text-right">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
