
@extends('layouts.main')

@section('title', 'Add New User - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')

@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('failed'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="row zoom90">
    <!-- Area Chart -->
       <div class="col-xl-12 col-lg-12">
            <div class="stepper-wrapper text-secondary">
                <div class="stepper-item active">
                    <div class="step-counter text-white">1</div>
                    <div class="step-name">Upload File</div>
                </div>
                <div class="stepper-item">
                    <div class="step-counter text-white">2</div>
                    <div class="step-name">User Account</div>
                </div>
                <div class="stepper-item">
                    <div class="step-counter text-white">3</div>
                    <div class="step-name">Profile Information</div>
                </div>
                <div class="stepper-item">
                    <div class="step-counter text-white">4</div>
                    <div class="step-name">Bank Account</div>
                </div>
            </div>
       </div>
</div>
<form method="POST" action="/users/store" onsubmit="return isConfirm()" id="multiStepForm" enctype="multipart/form-data">
    @csrf
    <div id="step1" class="step">
        <div class="zoom90 d-sm-flex align-items-center justify-content-between">
            <div>
                <h1 class="h4 mb-2 font-weight-bold text-gray-800"><i class="fas fa-user-plus"></i>&nbsp; User Creation</h1>
                <p class="mb-4">Upload Profile Picture & Emp. CV</a>.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary next-step"><i class="fas fa-step-forward"></i> Next</button>
            </div>
        </div>
        <div class="row zoom90">
         <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">File Upload Form</h6>
                    </div>
                        <!-- Card Body -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Profile Picture:</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="profile" name="profile" value="" onchange="openFileInIframe('profile', 'profile-label', 'profile-preview')">
                                            <label class="custom-file-label" for="profile" id="profile-label">Choose file</label>
                                        </div>
                                        <iframe id="profile-preview" src="" frameborder="0" style="display: none; width: 100%; height: 400px; margin-top: 10px;"></iframe>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Emp. CV:</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="cv" name="cv" value="" onchange="openFileInIframe('cv', 'cv-label', 'cv-preview')">
                                            <label class="custom-file-label" for="cv" id="cv-label">Choose file</label>
                                        </div>
                                        <iframe id="cv-preview" src="" frameborder="0" style="display: none; width: 100%; height: 400px; margin-top: 10px;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="step2" class="step" style="display: none;">
        <div class="zoom90 d-sm-flex align-items-center justify-content-between">
            <div>
                <h1 class="h4 mb-2 font-weight-bold text-gray-800"><i class="fas fa-user-plus"></i>&nbsp; Account Details</h1>
                <p class="mb-4">Fill out the field of User Account details that require for Logging in</a>.</p>
            </div>
            <div>
                <button type="button" class="btn btn-secondary prev-step mr-2"><i class="fas fa-step-backward"></i> Previous</button>
                <button type="button" class="btn btn-primary next-step"><i class="fas fa-step-forward"></i> Next</button>
            </div>
        </div>
        <div class="row zoom90">
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">User Account Form</h6>
                    </div>
                        <!-- Card Body -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Employee ID :</label>
                                                <input class="form-control"  name="employee_id" placeholder="Employee ID..." value="{{ $nextEmpID }}"readonly/>
                                                @if($errors->has('employee_id'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('employee_id')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">User ID :</label>
                                                <input class="form-control flex" id="usr_id" name="usr_id" placeholder="User ID..."/>
                                                <span style="color:red; font-size: 13px; font-style: italic" id="user-id-error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="password">Email :</label>
                                                <input class="form-control" name="email" placeholder="Email..." />
                                                @if($errors->has('email'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('email')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="password">Password :</label>
                                                <input class="form-control" name="password" value="" placeholder="***********"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="comment">Position :</label>
                                                <select class="form-control " id="position" name="position"  >
                                                    <option selected disabled>Choose...</option>
                                                    @foreach($pos_data as $pos)
                                                    <option value="{{ $pos ->id }}">{{ $pos ->position_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Department :</label>
                                                <select class="form-control " id="department" name="department"  >
                                                    <option selected disabled>Choose...</option>
                                                    @foreach($dep_data as $depart)
                                                    <option value="{{ $depart ->id }}">{{ $depart ->department_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Status :</label>
                                                <select class="form-control " name="status"  >
                                                    <option selected disabled>Choose...</option>
                                                    <option value="Active">Active</option>
                                                    <option value="nonActive">Non Active</option>
                                                </select>
                                                {{-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Employement Status :</label>
                                                <select class="form-control " name="employee_status" >
                                                    <option selected disabled>Choose...</option>
                                                    <option value="Freelance">Freelance</option>
                                                    <option value="Probation">Probation</option>
                                                    <option value="Contract">Contract</option>
                                                    <option value="Permanent">Permanent</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Hired Date :</label>
                                                <input class="form-control" type="date"  name="hired_date" id="hired_date" value="" />
                                                @if($errors->has('hired_date'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('hired_date')}}
                                                    </div>
                                                @endif
                                                {{-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Resignation Date :</label>
                                                <input class="form-control" type="date"  name="resignation_date" id="resignation_date" value="" />
                                                @if($errors->has('resignation_date'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('resignation_date')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="step3" class="step" style="display: none;">
        <div class="zoom90 d-sm-flex align-items-center justify-content-between">
            <div>
                <h1 class="h4 mb-2 font-weight-bold text-gray-800"><i class="fas fa-user-plus"></i>&nbsp; Profile Information</h1>
                <p class="mb-4">Fill out the field for Emp. Information</a>.</p>
            </div>
            <div>
                <button type="button" class="btn btn-secondary prev-step mr-2"><i class="fas fa-step-backward"></i> Previous</button>
                <button type="button" class="btn btn-primary next-step"><i class="fas fa-step-forward"></i> Next</button>
            </div>
        </div>
        <div class="row zoom90">
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Employment Information</h6>
                    </div>
                        <!-- Card Body -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="comment">Name :</label>
                                                <input class="form-control" type="text" name="name" placeholder="Name..."/>
                                                @if($errors->has('name'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('name')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Birth Date :</label>
                                                <input class="form-control" type="date" name="usr_dob" id="usr_dob" value="" />
                                                @if($errors->has('usr_dob'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_dob')}}
                                                    </div>
                                                @endif
                                                {{-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Birth Place :</label>
                                                <input class="form-control" type="text"   name="usr_birth_place" placeholder="Birth Place...">
                                                @if($errors->has('usr_birth_place'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_birth_place')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Gender :</label>
                                                <select class="form-control " name="usr_gender">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="M">Male</option>
                                                    <option value="F">Female</option>
                                                </select>
                                                @if($errors->has('usr_gender'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_gender')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Religion :</label>
                                                <select class="form-control " name="usr_religion">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="Islam">Islam</option>
                                                    <option value="Kristen">Kristen Protestan</option>
                                                    <option value="Katholik">Kristen Katholik</option>
                                                    <option value="Konghucu">Konghucu</option>
                                                    <option value="Hindu">Hindu</option>
                                                    <option value="Buddha">Buddha</option>
                                                    <option value="O">Others</option>
                                                </select>
                                                @if($errors->has('usr_religion'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_religion')}}
                                                    </div>
                                                @endif
                                                {{-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Marital Status :</label>
                                                <select class="form-control " name="usr_merital_status">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="S">Single</option>
                                                    <option value="M">Married</option>
                                                    <option value="Widow">Widow (Janda)</option>
                                                    <option value="Widower">Widower (Duda)</option>
                                                    <option value="Divorced">Divorced (Cerai)</option>
                                                </select>
                                                @if($errors->has('usr_merital_status'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_merital_status')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Num of Children :</label>
                                                <input class="form-control" type="text"   name="usr_children" placeholder="Number of Childern...">
                                                @if($errors->has('usr_children'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_children')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Address :</label>
                                                <textarea class="form-control" name="usr_address" placeholder="User Address..." style="height: 120px;"></textarea>
                                                @if($errors->has('usr_address'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_address')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Current Address :</label>
                                                <textarea class="form-control" name="current_address" placeholder="Current Address..." style="height: 120px;"></textarea>
                                                @if($errors->has('current_address'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('current_address')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">City :</label>
                                                <input class="form-control"   name="usr_address_city" placeholder="Address City...">
                                                @if($errors->has('usr_address_city'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_address_city')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Postal Code :</label>
                                                <input class="form-control"   name="usr_address_postal" placeholder="Address Postal...">
                                                @if($errors->has('usr_address_postal'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_address_postal')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Home Number :</label>
                                                <input class="form-control" name="usr_phone_home" placeholder="Home Phone Number...">
                                                @if($errors->has('usr_phone_home'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_phone_home')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Phone Number :</label>
                                                <input class="form-control" type="text"   name="usr_phone_mobile" placeholder="Mobile Phone Number...">
                                                @if($errors->has('usr_phone_mobile'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_phone_mobile')}}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="step4" class="step" style="display: none;">
        <div class="zoom90 d-sm-flex align-items-center justify-content-between">
            <div>
                <h1 class="h4 mb-2 font-weight-bold text-gray-800"><i class="fas fa-user-plus"></i>&nbsp; Bank Account Details</h1>
                <p class="mb-4">Fill out the field for Bank Account Details</a>.</p>
            </div>
            <div>
                <button type="button" class="btn btn-secondary prev-step mr-2"><i class="fas fa-step-backward"></i> Previous</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
            </div>
        </div>
        <div class="row zoom90">
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Bank Account Form</h6>
                    </div>
                        <!-- Card Body -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="comment">NPWP :</label>
                                        <input class="form-control" type="text" name="usr_npwp" placeholder="NPWP Number..." >
                                        @if($errors->has('usr_npwp'))
                                            <div class="text-danger">
                                                {{ $errors->first('usr_npwp')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Bank Name :</label>
                                        <select class="form-control" name="usr_bank_name" id="banks">
                                            <option value="">Choose Bank</option>
                                            @foreach ($bankNames as $bank)
                                                <option value="{{ $bank }}">{{ $bank }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('usr_bank_name'))
                                            <div class="text-danger">
                                                {{ $errors->first('usr_bank_name')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Identity Type :</label>
                                        <select class="form-control " name="usr_id_type" >
                                            <option selected disabled>Choose...</option>
                                            <option value="KTP">KTP</option>
                                            <option value="SIM">SIM</option>
                                            <option value="Passport">Passport</option>
                                        </select>
                                        @if($errors->has('usr_id_type'))
                                            <div class="text-danger">
                                                {{ $errors->first('usr_id_type')}}
                                            </div>
                                        @endif
                                        {{-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Bank Branch :</label>
                                        <input class="form-control" type="text"   name="usr_bank_branch" placeholder="Bank Branch ...">
                                        @if($errors->has('usr_bank_branch'))
                                            <div class="text-danger">
                                                {{ $errors->first('usr_bank_branch')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Identity No :</label>
                                        <input class="form-control" type="text"   name="usr_id_no" placeholder="Number Identity..." >
                                        @if($errors->has('usr_id_no'))
                                            <div class="text-danger">
                                                {{ $errors->first('usr_id_no')}}
                                            </div>
                                        @endif
                                        {{-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Bank Account Number :</label>
                                        <input class="form-control" type="text"   name="usr_bank_account" placeholder="Bank Account Number...">
                                        @if($errors->has('usr_bank_account'))
                                            <div class="text-danger">
                                                {{ $errors->first('usr_bank_account')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Identity Expiration :</label>
                                        <input class="form-control" type="date"  name="usr_id_expiration" value="<?php echo '2999-01-01' ?>" placeholder="User Identity Expiration..." >
                                        @if($errors->has('usr_id_expiration'))
                                            <div class="text-danger">
                                                {{ $errors->first('usr_id_expiration')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Bank Account Name :</label>
                                        <input class="form-control" type="text" name="usr_bank_account_name" placeholder="Bank Account Name..."/>
                                        @if($errors->has('usr_bank_account_name'))
                                            <div class="text-danger">
                                                {{ $errors->first('usr_bank_account_name')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<style>
.stepper-wrapper {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
.stepper-item {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;

  @media (max-width: 1280px) {
    font-size: 12px;
  }
}

.stepper-item::before {
  position: absolute;
  content: "";
  border-bottom: 2px solid #ccc;
  width: 100%;
  top: 20px;
  left: -50%;
  z-index: 2;
}

.stepper-item::after {
  position: absolute;
  content: "";
  border-bottom: 2px solid #ccc;
  width: 100%;
  top: 20px;
  left: 50%;
  z-index: 2;
}

.stepper-item .step-counter {
  position: relative;
  z-index: 5;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ccc;
  margin-bottom: 6px;
}

.stepper-item.active {
  font-weight: bold;
}

.stepper-item.completed .step-counter {
  background-color: #285cf8;
}

.stepper-item.completed::after {
  position: absolute;
  content: "";
  border-bottom: 2px solid #285cf8;
  width: 100%;
  top: 20px;
  left: 50%;
  z-index: 3;
}

.stepper-item:first-child::before {
  content: none;
}
.stepper-item:last-child::after {
  content: none;
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function isConfirm() {
    // Your confirmation logic here
    var confirmed = confirm("Are you sure you want to submit?");

    // Return false if not confirmed, preventing the form submission
    return confirmed;
}

function openFileInIframe(inputId, labelId, iframeId) {
    var input = document.getElementById(inputId);
    var label = document.getElementById(labelId);
    var iframe = document.getElementById(iframeId);

    if (input.files && input.files[0]) {
        var fileURL = URL.createObjectURL(input.files[0]);

        // Set the iframe src with the file URL
        iframe.src = fileURL;
        iframe.style.display = 'block';

        // Update the label to show the selected file name
        label.innerHTML = input.files[0].name;
    } else {
        // If no file is selected, hide the iframe and reset the label
        iframe.style.display = 'none';
        iframe.src = '';
        label.innerHTML = 'Choose file';
    }
}

// Call the openFileInIframe function on page load to handle initial file selections
window.onload = function() {
    openFileInIframe('profile', 'profile-label', 'profile-preview');
    openFileInIframe('cv', 'cv-label', 'cv-preview');
};
</script>
<script>
$(document).ready(function() {
    $('#usr_id').blur(function() {
        var userId = $(this).val();
        checkUserIdExists(userId);
    });

    function checkUserIdExists(userId) {
        $.ajax({
            url: '/check-user-id',
            method: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'usr_id': userId
            },
            success: function(response) {
                if (response.exists) {
                    $('#user-id-error').text('User ID Sudah Digunakan').css('color', 'red');
                } else {
                    $('#user-id-error').text('User ID Dapat Digunakan').css('color', 'lightgreen');
                }

            },
            error: function() {
                $('#user-id-error').text('Terjadi kesalahan saat memeriksa User ID');
            }
        });
    }
});

function changeFileName(inputId, labelId) {
  var input = document.getElementById(inputId);
  var label = document.getElementById(labelId);
  label.textContent = input.files[0].name;
}

$(document).ready(function () {
    var form = $("#multiStepForm");
    var stepper = $(".stepper-wrapper");
    var steps = form.find(".step");

    // Array of step information
    var stepInfo = [
        { name: "Upload File", description: "Upload Emp. Profile Pic & CV" },
        { name: "User Account", description: "Fill Out Emp. User Account" },
        { name: "Profile Information", description: "Fill Out Employment Information" },
        { name: "Bank Account", description: "Fill Out Emp. Bank Account" }
    ];

    $(".next-step").click(function () {
        var currentStep = $(this).closest(".step");
        var nextStep = currentStep.next(".step");

        currentStep.hide();
        nextStep.show();

        updateStepper();
    });

    $(".prev-step").click(function () {
        var currentStep = $(this).closest(".step");
        var prevStep = currentStep.prev(".step");

        currentStep.hide();
        prevStep.show();

        updateStepper();
    });

    function updateStepper() {
        var totalSteps = steps.length;
        var currentStepIndex = steps.index(form.find(".step:visible"));
        var stepperItems = stepper.find(".stepper-item");

        stepperItems.each(function (index) {
            var stepCounter = $(this).find(".step-counter");
            var stepName = $(this).find(".step-name");

            if (index < currentStepIndex) {
                $(this).addClass("completed").removeClass("active");
            } else if (index === currentStepIndex) {
                $(this).addClass("active").removeClass("completed");
            } else {
                $(this).removeClass("active completed");
            }

            // Update step counter and name based on stepInfo array
            stepCounter.text(index + 1);
            stepName.text(stepInfo[index].name);

            // You can customize other aspects, such as tooltips or descriptions, here
            // For example:
            $(this).attr("title", stepInfo[index].description);
        });
    }
});
</script>
@endsection
