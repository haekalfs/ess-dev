
@extends('layouts.main')

@section('title', 'Add New User - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<form method="POST" action="/users/store" enctype="multipart/form-data">
    @csrf
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-user-plus"></i>&nbsp; Add New User</h1>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
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
    <div class="row zoom90">
        <!-- Area Chart -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
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
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>CV:</label>
                                    <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="cv" name="cv" value="" onchange="changeFileName('cv', 'cv-label')">
                                    <label class="custom-file-label" for="cv" id="cv-label">Choose file</label>
                                    </div>
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
                    <h6 class="m-0 font-weight-bold text-primary">Employee Details</h6>
                </div>
                <ul class="nav nav-tabs" id="pageTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-user-circle"></i> Account Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-user-tie"></i> Profile Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="page3-tab" data-toggle="tab" href="#page3" role="tab" aria-controls="page3" aria-selected="false"><i class="fas fa-calendar-week" style="color: #ff0000;"></i> Bank Details</a>
                    </li>
                </ul>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="tab-content" id="pageTabContent">
                        <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
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
                                                    <input class="form-control" name="password" value="" placeholder="****"/>
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
                        <div class="tab-pane fade" id="page2" role="tabpanel" aria-labelledby="page2-tab">
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
                        <div class="tab-pane fade" id="page3" role="tabpanel" aria-labelledby="page3-tab">
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
                                            <select class="form-control" name="usr_bank_name" >
                                                <option selected disabled>Choose...</option>
                                                <option value="002">BANK BRI</option>
                                                <option value="003">BANK EKSPOR INDONESIA</option>
                                                <option value="008">BANK MANDIRI</option>
                                                <option value="009">BANK BNI</option>
                                                <option value="011">BANK DANAMON</option>
                                                <option value="013">PERMATA BANK</option>
                                                <option value="014">BANK BCA</option>
                                                <option value="016">BANK BII</option>
                                                <option value="019">BANK PANIN</option>
                                                <option value="020">BANK ARTA NIAGA KENCANA</option>
                                                <option value="022">BANK NIAGA</option>
                                                <option value="023">BANK BUANA IND</option>
                                                <option value="026">BANK LIPPO</option>
                                                <option value="028">BANK NISP</option>
                                                <option value="030">AMERICAN EXPRESS BANK LTD</option>
                                                <option value="031">CITIBANK N.A.</option>
                                                <option value="032">JP. MORGAN CHASE BANK, N.A.</option>
                                                <option value="033">BANK OF AMERICA, N.A</option>
                                                <option value="034">ING INDONESIA BANK</option>
                                                <option value="036">BANK MULTICOR TBK.</option>
                                                <option value="037">BANK ARTHA GRAHA</option>
                                                <option value="039">BANK CREDIT AGRICOLE INDOSUEZ</option>
                                                <option value="040">THE BANGKOK BANK COMP. LTD</option>
                                                <option value="041">THE HONGKONG & SHANGHAI B.C.</option>
                                                <option value="042">THE BANK OF TOKYO MITSUBISHI UFJ LTD</option>
                                                <option value="045">BANK SUMITOMO MITSUI INDONESIA</option>
                                                <option value="046">BANK DBS INDONESIA</option>
                                                <option value="047">BANK RESONA PERDANIA</option>
                                                <option value="048">BANK MIZUHO INDONESIA</option>
                                                <option value="050">STANDARD CHARTERED BANK</option>
                                                <option value="052">BANK ABN AMRO</option>
                                                <option value="053">BANK KEPPEL TATLEE BUANA</option>
                                                <option value="054">BANK CAPITAL INDONESIA, TBK.</option>
                                                <option value="057">BANK BNP PARIBAS INDONESIA</option>
                                                <option value="058">BANK UOB INDONESIA</option>
                                                <option value="059">KOREA EXCHANGE BANK DANAMON</option>
                                                <option value="060">RABOBANK INTERNASIONAL INDONESIA</option>
                                                <option value="061">ANZ PANIN BANK</option>
                                                <option value="067">DEUTSCHE BANK AG.</option>
                                                <option value="068">BANK WOORI INDONESIA</option>
                                                <option value="069">BANK OF CHINA LIMITED</option>
                                                <option value="076">BANK BUMI ARTA</option>
                                                <option value="087">BANK EKONOMI</option>
                                                <option value="088">BANK ANTARDAERAH</option>
                                                <option value="089">BANK HAGA</option>
                                                <option value="093">BANK IFI</option>
                                                <option value="095">BANK CENTURY, TBK.</option>
                                                <option value="097">BANK MAYAPADA</option>
                                                <option value="110">BANK JABAR</option>
                                                <option value="111">BANK DKI</option>
                                                <option value="112">BPD DIY</option>
                                                <option value="113">BANK JATENG</option>
                                                <option value="114">BANK JATIM</option>
                                                <option value="115">BPD JAMBI</option>
                                                <option value="116">BPD ACEH</option>
                                                <option value="117">BANK SUMUT</option>
                                                <option value="118">BANK NAGARI</option>
                                                <option value="119">BANK RIAU</option>
                                                <option value="120">BANK SUMSEL</option>
                                                <option value="121">BANK LAMPUNG</option>
                                                <option value="122">BPD KALSEL</option>
                                                <option value="123">BPD KALIMANTAN BARAT</option>
                                                <option value="124">BPD KALTIM</option>
                                                <option value="125">BPD KALTENG</option>
                                                <option value="126">BPD SULSEL</option>
                                                <option value="127">BANK SULUT</option>
                                                <option value="128">BPD NTB</option>
                                                <option value="129">BPD BALI</option>
                                                <option value="130">BANK NTT</option>
                                                <option value="131">BANK MALUKU</option>
                                                <option value="132">BPD PAPUA</option>
                                                <option value="133">BANK BENGKULU</option>
                                                <option value="134">BPD SULAWESI TENGAH</option>
                                                <option value="135">BANK SULTRA</option>
                                                <option value="145">BANK NUSANTARA PARAHYANGAN</option>
                                                <option value="146">BANK SWADESI</option>
                                                <option value="147">BANK MUAMALAT</option>
                                                <option value="151">BANK MESTIKA</option>
                                                <option value="152">BANK METRO EXPRESS</option>
                                                <option value="153">BANK SHINTA INDONESIA</option>
                                                <option value="157">BANK MASPION</option>
                                                <option value="159">BANK HAGAKITA</option>
                                                <option value="161">BANK GANESHA</option>
                                                <option value="162">BANK WINDU KENTJANA</option>
                                                <option value="164">HALIM INDONESIA BANK</option>
                                                <option value="166">BANK HARMONI INTERNATIONAL</option>
                                                <option value="167">BANK KESAWAN</option>
                                                <option value="200">BANK TABUNGAN NEGARA (PERSERO)</option>
                                                <option value="212">BANK HIMPUNAN SAUDARA 1906, TBK .</option>
                                                <option value="213">BANK TABUNGAN PENSIUNAN NASIONAL</option>
                                                <option value="405">BANK SWAGUNA</option>
                                                <option value="422">BANK JASA ARTA</option>
                                                <option value="426">BANK MEGA</option>
                                                <option value="427">BANK JASA JAKARTA</option>
                                                <option value="441">BANK BUKOPIN</option>
                                                <option value="451">BANK SYARIAH MANDIRI</option>
                                                <option value="459">BANK BISNIS INTERNASIONAL</option>
                                                <option value="466">BANK SRI PARTHA</option>
                                                <option value="472">BANK JASA JAKARTA</option>
                                                <option value="484">BANK BINTANG MANUNGGAL</option>
                                                <option value="485">BANK BUMIPUTERA</option>
                                                <option value="490">BANK YUDHA BHAKTI</option>
                                                <option value="491">BANK MITRANIAGA</option>
                                                <option value="494">BANK AGRO NIAGA</option>
                                                <option value="498">BANK INDOMONEX</option>
                                                <option value="501">BANK ROYAL INDONESIA</option>
                                                <option value="503">BANK ALFINDO</option>
                                                <option value="506">BANK SYARIAH MEGA</option>
                                                <option value="513">BANK INA PERDANA</option>
                                                <option value="517">BANK HARFA</option>
                                                <option value="520">PRIMA MASTER BANK</option>
                                                <option value="521">BANK PERSYARIKATAN INDONESIA</option>
                                                <option value="525">BANK AKITA</option>
                                                <option value="526">LIMAN INTERNATIONAL BANK</option>
                                                <option value="531">ANGLOMAS INTERNASIONAL BANK</option>
                                                <option value="523">BANK DIPO INTERNATIONAL</option>
                                                <option value="535">BANK KESEJAHTERAAN EKONOMI</option>
                                                <option value="536">BANK UIB</option>
                                                <option value="542">BANK ARTOS IND</option>
                                                <option value="547">BANK PURBA DANARTA</option>
                                                <option value="548">BANK MULTI ARTA SENTOSA</option>
                                                <option value="553">BANK MAYORA</option>
                                                <option value="555">BANK INDEX SELINDO</option>
                                                <option value="566">BANK VICTORIA INTERNATIONAL</option>
                                                <option value="558">BANK EKSEKUTIF</option>
                                                <option value="559">CENTRATAMA NASIONAL BANK</option>
                                                <option value="562">BANK FAMA INTERNASIONAL</option>
                                                <option value="564">BANK SINAR HARAPAN BALI</option>
                                                <option value="567">BANK HARDA</option>
                                                <option value="945">BANK FINCONESIA</option>
                                                <option value="946">BANK MERINCORP</option>
                                                <option value="947">BANK MAYBANK INDOCORP</option>
                                                <option value="948">BANK OCBC – INDONESIA</option>
                                                <option value="949">BANK CHINA TRUST INDONESIA</option>
                                                <option value="950">BANK COMMONWEALTH</option>
                                                <option value="425">BANK BJB SYARIAH</option>
                                                <option value="688">BPR KS (KARYAJATNIKA SEDAYA)</option>
                                                <option value="789">INDOSAT DOMPETKU"</option>
                                                <option value="911">TELKOMSEL TCASH</option>
                                                <option value="911">LINKAJA</option>
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
                                            <input class="form-control" type="text"   name="usr_id_expiration" placeholder="User Identity Expiration..." >
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
    </div>
</form>
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
</script>

@endsection
