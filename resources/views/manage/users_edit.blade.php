@extends('layouts.main')

@section('active-page-users')
active
@endsection

@section('content')
<!-- Page Heading -->
<form method="POST" action="/users/update/{{ $user->id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class="m-0 font-weight-bold text-grey">Edit Employee #{{ $user->users_detail->employee_id }}</h4>
    <div class="d-sm-flex justify-content-end ">
        <a href="/manage/users" class="btn btn-danger btn-sm mr-2" id="manButton">Kembali</a>
        <input type="submit" class="btn btn-success btn-sm" value="Simpan">
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

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
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
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="dropdown">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Profile Picture & CV</h6>
                    </div>
                        <!-- Card Body -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Profile Picture:</label>
                                        <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="profile" name="profile" value="" onchange="changeFileName('profile', 'profile-label')">
                                        <label class="custom-file-label" for="profile" id="profile-label">Choose file</label>
                                        </div>
                                    </div>
                                    @if($user->users_detail->profile_pic)
                                        <img style="max-width: 110px; max-height: 110px;" class="img-thumbnail" src="{{ url('/storage/profile_pic/'.$user->users_detail->profile_pic) }}" data-toggle="modal" data-target="#profileModal">
                                    @else
                                    <div class="img-thumbnail no-image"><i class="no-image-text">No Image Available</i></div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>CV:</label>
                                        <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="cv" name="cv" value="" onchange="changeFileName('cv', 'cv-label')">
                                        <label class="custom-file-label" for="cv" id="cv-label">Choose file</label>
                                        </div>
                                    </div>
                                    @if($user->users_detail->cv)
                                    <a href="{{ url('/storage/cv/'.$user->users_detail->cv) }}" target="_blank">
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
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Employee ID :</label>
                                    <input class="form-control"   name="employee_id" placeholder="Employee ID..." value="{{ $user->users_detail->employee_id }}" readonly/>
                                    @if($errors->has('employee_id'))
                                        <div class="text-danger">
                                            {{ $errors->first('employee_id')}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="password">User ID :</label>
                                <input class="form-control flex"    name="usr_id" placeholder="User ID..." value="{{ $user->id }}" readonly/>
                                @if($errors->has('usr_id'))
                                    <div class="text-danger">
                                        {{ $errors->first('usr_id')}}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Email :</label>
                                    <input class="form-control"   name="email" placeholder="Email..." value="{{ $user->email }}" />
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

        <!-- Area Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Employee Information</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
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
                                    {{-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Employement Status :</label>
                                    <select class="form-control " name="employee_status" >
                                        <option selected disabled>Choose...</option>
                                        <option value="Freelance" @if($user->users_detail->employee_status == 'Freelance') selected @endif>Freelance</option>
                                        <option value="Probation" @if($user->users_detail->employee_status == 'Probation') selected @endif>Probation</option>
                                        <option value="Contract" @if($user->users_detail->employee_status == 'Contract') selected @endif>Contract</option>
                                        <option value="Permanent"@if($user->users_detail->employee_status == 'Permanent') selected @endif>Permanent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
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
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="comment">Name :</label>
                                    <input class="form-control" type="text" name="name" placeholder="Name..." value="{{ $user->name }}">
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
                                    <input class="form-control" type="date" name="usr_dob" id="usr_dob" value="{{ $user->users_detail->usr_dob }}"/>
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
                                    <input class="form-control" type="text"   name="usr_birth_place" placeholder="Birth Place..." value="{{ $user->users_detail->usr_birth_place }}"/>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Religion :</label>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Num of Children :</label>
                                    <input class="form-control" type="text"   name="usr_children" placeholder="Number of Childern..." value="{{ $user->users_detail->usr_children }}"/>
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
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Contact Information</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">City :</label>
                                    <input class="form-control"   name="usr_address_city" placeholder="Address City..." value="{{ $user->users_detail->usr_address_city }}"/>
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
                                    <input class="form-control"   name="usr_address_postal" placeholder="Address Postal..." value="{{ $user->users_detail->usr_address_postal}}"/>
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
                                    <input class="form-control"   name="usr_phone_home" placeholder="Home Phone Number..." value="{{ $user->users_detail->usr_phone_home }}"/>
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
                                    <input class="form-control" type="text" name="usr_phone_mobile" placeholder="Mobile Phone Number..." value="{{ $user->users_detail->usr_phone_mobile }}"/>
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

        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Document & Bank Details</h6>
                </div>
                    <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="comment">NPWP :</label>
                                    <input class="form-control" type="text"   name="usr_npwp" placeholder="NPWP Number..." value="{{ $user->users_detail->usr_npwp }}" />
                                    @if($errors->has('usr_npwp'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_npwp')}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Bank Name :</label>
                                    <input class="form-control" type="text" name="usr_bank_name" placeholder="Bank Name..." value="{{ $user->users_detail->usr_bank_name }}"/>
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
                                    <label for="password">Bank Branch :</label>
                                    <input class="form-control" type="text" name="usr_bank_branch" placeholder="Bank Branch ..." value="{{ $user->users_detail->usr_bank_branch }}"/>
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
                                    <input class="form-control" type="text"   name="usr_id_no" placeholder="Number Identity..." value="{{ $user->users_detail->usr_id_no }}" />
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
                                    <label for="password">Identity Expiration :</label>
                                    <input class="form-control" type="text"   name="usr_id_expiration" placeholder="User Identity Expiration..." value="{{ $user->users_detail->usr_id_expiration }}" />
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
</form>
<!-- Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="close-icon">
                    <img width="35" height="35" src="https://img.icons8.com/ios-glyphs/60/macos-close.png" alt="macos-close" data-dismiss="modal">
                </div>
                <img src="{{ url('/storage/profile_pic/'.$user->users_detail->profile_pic) }}" class="img-fluid" alt="Profile Picture">
            </div>
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
