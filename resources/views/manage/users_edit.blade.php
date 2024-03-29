@extends('layouts.main')

@section('title', 'Edit User - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<form method="POST" action="/users/update/{{ $user->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h4 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-user-edit"></i>&nbsp; {{ $user->name }} #{{ $user->users_detail->employee_id }}</h4>
    <div class="d-sm-flex justify-content-end ">
        <a href="/manage/users" class="btn btn-secondary mr-3" id="manButton"><i class="fas fa-backward"></i>&nbsp; Kembali</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save" style="color: #ffffff;"></i> Simpan
        </button>
    </div>
</div>

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

<style>
.img-thumbnail.no-image {
  position: relative;
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100px;
  height: 100px;
}

.no-image-text {
  text-align: center;
}
</style>
    <div class="row zoom90">
        <!-- Area Chart -->
        <div class="col-xl-4 col-lg-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="dropdown">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Profile Picture</h6>
                            </div>
                                <!-- Card Body -->
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="text-center">
                                            @if($user->users_detail->profile_pic)
                                                <img class="img-profile rounded-circle" height="150px"width="150px" style="object-fit:fill;" src="{{ url('/images_storage/'.$user->users_detail->profile_pic) }}" data-toggle="modal" data-target="#profileModal">
                                            @else
                                                <div class="img-profile rounded-circle no-image"><i class="no-image-text">No Image Available</i></div>
                                            @endif
                                            <div class="mt-2"><span>JPG or PNG no larger than 5 MB</span></div>
                                            </div>
                                            <div class="form-group mt-3 text-center">
                                                <div class="custom-file">
                                                    <label for="profile" class="btn btn-primary">
                                                        Upload New Image
                                                        <input type="file" class="custom-file-input" id="profile" name="profile" style="display: none;" onchange="changeFileName('profile', 'profile-label')">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                        <!-- Card Header - Dropdown -->
                        <div class="dropdown">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Employee's Resume</h6>
                            </div>
                                <!-- Card Body -->
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>CV:</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="cv" name="cv" value="" onchange="changeFileName('cv', 'cv-label')">
                                                    <label class="custom-file-label" for="cv" id="cv-label">Choose file</label>
                                                </div>
                                            </div>
                                            @if($user->users_detail->cv)
                                            <a data-toggle="modal" data-target="#CVModal">
                                                    <img class="img-thumbnail" width="110px" src="https://img.icons8.com/cute-clipart/64/pdf.png" alt="CV Image">
                                                </a>
                                            @else
                                                <div class="img-thumbnail no-image"><i class="no-image-text">No CV Available</i></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <span class="text-danger font-weight-bold">User Biodata Update</span>
                        </div>
                        <div class="card-body" style="background-color: rgb(247, 247, 247);">
                            <h6 class="h6 mb-2 font-weight-bold text-gray-800">Guidelines for Biodata Update</h6>
                            <ul>
                                <li>Ensure accurate and updated user biodata per company policies.</li>
                                <li>Verify and validate user information for data integrity.</li>
                                <li>Prohibit unauthorized changes to user records.</li>
                                <li>Review user details for completeness and correctness before saving.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <span class="text-danger font-weight-bold">Delete Account</span>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <span>Deleting your account is a permanent action and cannot be undone. If you are sure you want to delete your account, select the button below.</span>
                            </div>
                            <div>
                                <a class="btn btn-outline-danger" onclick='isconfirm();' href="/users/delete/{{ Crypt::encrypt($user->id) }}">I Understand, delete the account</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-8">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Employee Details</h6>
                </div>
                <ul class="nav nav-tabs zoom90" id="pageTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-user-circle"></i> Account & Profile Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="page3-tab" data-toggle="tab" href="#page3" role="tab" aria-controls="page3" aria-selected="false"><i class="fas fa-university" style="color: #00d55c;"></i> Identity Card & Bank Account Details</a>
                    </li>
                </ul>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="tab-content" id="pageTabContent">
                        <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
                            <div class="col-md-12">
                                <h6 class="h5 m-0 font-weight-bold text-primary mt-2 mb-4"><i class="fas fa-user-circle"></i> Employee's Account & Profile Information</h6>
                                <hr class="sidebar-divider mb-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Employee ID :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control"   name="employee_id" placeholder="Employee ID..." value="{{ $user->users_detail->employee_id }}" readonly/>
                                                        @if($errors->has('employee_id'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('employee_id')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">User ID :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control flex" name="usr_id" placeholder="User ID..." value="{{ $user->id }}" readonly/>
                                                        @if($errors->has('usr_id'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_id')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="d-flex align-items-center mb-4">
                                                            <div style="width: 140px;" class="mr-2">
                                                                <p style="margin: 0;">Full Name :</p>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <input class="form-control" type="text" name="name" placeholder="Name..." value="{{ $user->name }}">
                                                                @if($errors->has('name'))
                                                                    <div class="text-danger">
                                                                        {{ $errors->first('name')}}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-4">
                                                            <div style="width: 140px;" class="mr-2">
                                                                <p style="margin: 0;">Email :</p>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <input class="form-control" name="email" placeholder="Email..." value="{{ $user->email }}" />
                                                                @if($errors->has('email'))
                                                                    <div class="text-danger">
                                                                        {{ $errors->first('email')}}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Birth Date :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control" type="date" name="usr_dob" id="usr_dob" value="{{ $user->users_detail->usr_dob }}"/>
                                                        @if($errors->has('usr_dob'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_dob')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Gender :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <select class="form-control " name="usr_gender">
                                                            <option selected disabled>Choose...</option>
                                                            <option value="M" @if($user->users_detail->usr_gender == 'M') selected @endif>Male</option>
                                                            <option value="F" @if($user->users_detail->usr_gender == 'F') selected @endif>Female</option>
                                                        </select>
                                                        @if($errors->has('usr_gender'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_gender')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Marital Status :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <select class="form-control " name="usr_merital_status">
                                                            <option selected disabled>Choose...</option>
                                                            <option value="S" @if($user->users_detail->usr_merital_status == 'S') selected @endif>Single</option>
                                                            <option value="M" @if($user->users_detail->usr_merital_status == 'M') selected @endif>Married</option>
                                                            <option value="Widow" @if($user->users_detail->usr_merital_status == 'Widow') selected @endif>Widow (Janda)</option>
                                                            <option value="Widower" @if($user->users_detail->usr_merital_status == 'Widower') selected @endif>Widower (Duda)</option>
                                                            <option value="Divorced" @if($user->users_detail->usr_merital_status == 'Divorced') selected @endif>Divorced (Cerai)</option>
                                                        </select>
                                                        @if($errors->has('usr_merital_status'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_merital_status')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 130px;" class="mr-2">
                                                        <p style="margin: 0;">Birth Place :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control" type="text" name="usr_birth_place" placeholder="Birth Place..." value="{{ $user->users_detail->usr_birth_place }}"/>
                                                        @if($errors->has('usr_birth_place'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_birth_place')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 130px;" class="mr-2">
                                                        <p style="margin: 0;">Religion :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <select class="form-control " name="usr_religion">
                                                            <option selected disabled>Choose...</option>
                                                            <option value="Islam" @if($user->users_detail->usr_religion == 'Islam') selected @endif>Islam</option>
                                                            <option value="Kristen" @if($user->users_detail->usr_religion == 'Kristen') selected @endif>Kristen Protestan</option>
                                                            <option value="Katholik" @if($user->users_detail->usr_religion == 'Katholik') selected @endif>Kristen Katholik</option>
                                                            <option value="Konghucu" @if($user->users_detail->usr_religion == 'Konghucu') selected @endif>Konghucu</option>
                                                            <option value="Hindu" @if($user->users_detail->usr_religion == 'Hindu') selected @endif>Hindu</option>
                                                            <option value="Buddha" @if($user->users_detail->usr_religion == 'Buddha') selected @endif>Buddha</option>
                                                            <option value="O" @if($user->users_detail->usr_religion == 'O') selected @endif>Others</option>
                                                        </select>
                                                        @if($errors->has('usr_religion'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_religion')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 130px;" class="mr-2">
                                                        <p style="margin: 0;">Num of Children :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control" type="text" name="usr_children" placeholder="Number of Childern..." value="{{ $user->users_detail->usr_children }}"/>
                                                        @if($errors->has('usr_children'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_children')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="h5 m-0 font-weight-bold text-primary mt-4 mb-4"><i class="fas fa-address-book"></i> Employee's Address Information</h6>
                                        <hr class="sidebar-divider mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Home Number :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control"   name="usr_phone_home" placeholder="Home Phone Number..." value="{{ $user->users_detail->usr_phone_home }}"/>
                                                        @if($errors->has('usr_phone_home'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_phone_home')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Phone Number :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control" type="text" name="usr_phone_mobile" placeholder="Mobile Phone Number..." value="{{ $user->users_detail->usr_phone_mobile }}"/>
                                                        @if($errors->has('usr_phone_mobile'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_phone_mobile')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">City :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control" name="usr_address_city" placeholder="Address City..." value="{{ $user->users_detail->usr_address_city }}"/>
                                                        @if($errors->has('usr_address_city'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_address_city')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Postal Code :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input class="form-control"   name="usr_address_postal" placeholder="Address Postal..." value="{{ $user->users_detail->usr_address_postal}}"/>
                                                        @if($errors->has('usr_address_postal'))
                                                            <div class="text-danger">
                                                                {{ $errors->first('usr_address_postal')}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Address :</label>
                                                    <textarea class="form-control" name="usr_address" placeholder="User Address..." style="height: 120px;">{{ $user->users_detail->usr_address }}</textarea>
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
                                                    <textarea class="form-control" name="current_address" placeholder="Current Address..." style="height: 120px;">{{ $user->users_detail->current_address }}</textarea>
                                                    @if($errors->has('current_address'))
                                                        <div class="text-danger">
                                                            {{ $errors->first('current_address')}}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="h5 m-0 font-weight-bold text-primary mt-4 mb-4"><i class="fas fa-user-tag"></i> Employement Status</h6>
                                        <hr class="sidebar-divider mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="comment">Position :</label>
                                                    <select class="form-control" id="position" name="position" >
                                                        <option selected disabled>Choose...</option>
                                                        @foreach($pos_data as $pos)
                                                        <option value="{{ $pos->id }}" @if($pos->id == $user->users_detail->position_id) selected @endif>{{ $pos->position_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Department :</label>
                                                    <select class="form-control" id="department" name="department"  >
                                                        <option selected disabled>Choose...</option>
                                                        @foreach($dep_data as $depart)
                                                        <option value="{{ $depart ->id }}" @if($depart ->id == $user->users_detail->department_id) selected @endif>{{ $depart ->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Status :</label>
                                                    <select class="form-control" name="status"  >
                                                        <option selected disabled>Choose...</option>
                                                        <option value="Active" @if($user->users_detail->status_active == 'Active') selected @endif>Active</option>
                                                        <option value="nonActive" @if($user->users_detail->status_active == 'nonActive') selected @endif>Non Active</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Employement Status :</label>
                                                    <select class="form-control " name="employee_status" >
                                                        <option selected disabled>Choose...</option>
                                                        <option value="Freelance" @if($user->users_detail->employee_status == 'Freelance') selected @endif>Freelance</option>
                                                        <option value="Probation" @if($user->users_detail->employee_status == 'Probation') selected @endif>Probation</option>
                                                        <option value="MT" @if($user->users_detail->employee_status == 'MT') selected @endif>Management Trainee</option>
                                                        <option value="Contract" @if($user->users_detail->employee_status == 'Contract') selected @endif>Contract</option>
                                                        <option value="Permanent"@if($user->users_detail->employee_status == 'Permanent') selected @endif>Permanent</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <h6 class="h5 m-0 font-weight-bold text-danger mt-4 mb-4"><i class="fas fa-user-check"></i> Hired & Resignation Date</h6>
                                        <hr class="sidebar-divider mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Hired Date :</label>
                                                    <input class="form-control" type="date"  name="hired_date" id="hired_date" value="{{ $user->users_detail->hired_date }}" />
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
                                                    <input class="form-control" type="date"  name="resignation_date" id="resignation_date" value="{{ $user->users_detail->resignation_date }}" />
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
                        <div class="tab-pane fade" id="page3" role="tabpanel" aria-labelledby="page3-tab">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <h6 class="h5 m-0 font-weight-bold text-danger mt-2 mb-4"><i class="fas fa-id-card"></i> Employee's Identity Card Information</h6>
                                        <hr class="sidebar-divider mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Identity Type :</label>
                                                    <select class="form-control" name="usr_id_type" >
                                                        <option selected disabled>Choose...</option>
                                                        <option value="KTP" @if($user->users_detail->usr_id_type == 'KTP') selected @endif>KTP</option>
                                                        <option value="SIM" @if($user->users_detail->usr_id_type == 'SIM') selected @endif>SIM</option>
                                                        <option value="Passport" @if($user->users_detail->usr_id_type == 'Passport') selected @endif>Passport</option>
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
                                                    <label for="email">Identity No :</label>
                                                    <input class="form-control" type="text" name="usr_id_no" placeholder="Number Identity..." value="{{ $user->users_detail->usr_id_no }}" />
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
                                                    <label for="password">Identity Expiration :</label>
                                                    <input class="form-control" type="date" name="usr_id_expiration" placeholder="User Identity Expiration..." value="{{ $user->users_detail->usr_id_expiration }}" />
                                                    @if($errors->has('usr_id_expiration'))
                                                        <div class="text-danger">
                                                            {{ $errors->first('usr_id_expiration')}}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="comment">NPWP :</label>
                                                    <input class="form-control" type="text" name="usr_npwp" placeholder="NPWP Number..." value="{{ $user->users_detail->usr_npwp }}" />
                                                    @if($errors->has('usr_npwp'))
                                                        <div class="text-danger">
                                                            {{ $errors->first('usr_npwp')}}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <h6 class="h5 m-0 font-weight-bold text-success mt-2 mb-4"><i class="fas fa-university" style="color: #00d55c;"></i> Employee's Bank Account Information</h6>
                                        <hr class="sidebar-divider mb-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Bank Name :</label>
                                                    <select class="form-control" name="usr_bank_name" id="banks">
                                                        <option value="">Choose Bank</option>
                                                        @foreach ($bankNames as $bank)
                                                            <option value="{{ $bank }}" @if($user->users_detail->usr_bank_name == $bank) selected @endif>{{ $bank }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if($errors->has('usr_bank_name'))
                                                        <div class="text-danger">
                                                            {{ $errors->first('usr_bank_name')}}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Bank Branch :</label>
                                                    <input class="form-control" type="text" name="usr_bank_branch" placeholder="Bank Branch ..." value="{{ $user->users_detail->usr_bank_branch }}"/>
                                                    @if($errors->has('usr_bank_branch'))
                                                        <div class="text-danger">
                                                            {{ $errors->first('usr_bank_branch')}}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Bank Account Number :</label>
                                                    <input class="form-control" type="text" name="usr_bank_account" placeholder="Bank Account Number..." value="{{ $user->users_detail->usr_bank_account }}"/>
                                                    @if($errors->has('usr_bank_account'))
                                                        <div class="text-danger">
                                                            {{ $errors->first('usr_bank_account')}}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password">Bank Account Name :</label>
                                                    <input class="form-control" type="text" name="usr_bank_account_name" placeholder="Bank Account Name..." value="{{ $user->users_detail->usr_bank_account_name }}"/>
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
            </div>
        </div>
    </div>
</form>
<!-- Modal Foto -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Picture</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <img src="{{ url('/images_storage/'.$user->users_detail->profile_pic) }}" class="img-fluid" alt="Profile Picture">
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="CVModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">CV</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <iframe height="600px" src="{{ url('/cv_storage/'.$user->users_detail->cv )}}"></iframe>
    </div>
  </div>
</div>


<style>
    .close-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 9999;
    cursor: pointer;
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    $('#user-id-error').text('User ID Sudah Digunakan');
                } else {
                    $('#user-id-error').text('User ID Dapat Digunakan');
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
</script>
@endsection
