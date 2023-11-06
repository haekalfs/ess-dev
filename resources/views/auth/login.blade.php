@extends('layouts.login')

@section('landing-page')
<div class="row mb-4 align-items-center flex-lg-row-reverse">
    <div class="col-md-6 col-xl-7 mb-4 mb-lg-0 ">
        <!-- requires glightbox, please flag the option in the picostrap customizer panel-->

        <div class="lc-block position-relative">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block w-100 img-fluid rounded shadow" src="{{ asset('img/hl-login.png') }}"
                            alt="First slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100 img-fluid rounded shadow" src="{{ asset('img/hl-login-2.png') }}"
                            alt="Second slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100 img-fluid rounded shadow" src="{{ asset('img/hl-login-3.png') }}"
                            alt="Third slide">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div><!-- /col -->
    <div class="col-md-6 col-xl-5">
        <div class="lc-block mb-3">
            <div editable="rich">
                <h5 class="fw-bolder display-5"><strong>ESS - Employee Self Service</strong> <i class="fas fa-fw fa-cogs"></i></h5>
            </div>
        </div>
        <div class="lc-block mb-4">
            <div editable="rich">
                <p class="lead">Employee Self Service is a digital system that enables employees to manage their own personal and business information more easily and effectively. With ESS, employees can access various features and services available in it to take care of administrative and management needs related to their work.</p>
            </div>
        </div>
        <div class="lc-block">
            {{-- <a class="btn btn-primary" href="/auth/download">Download PASS App</a> --}}
        </div>
    </div>
</div>
{{-- 
<div class="lc-block position-relative">
    <div id="carouselExampleControls2" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active w-100">
                <div class="row row-cols-1 row-cols-md-3 g-4 w-100">
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/AMS_headline.png') }}" class="card-img-top"
                          alt="Hollywood Sign on The Hill" />
                        <div class="card-body">
                          <h5 class="card-title">AMS</h5>
                          <p class="card-text long-text-2">
                            AMS is a tools to managing company's assets and monitoring their assets. In this tools you could see how much assets that company's owned, manage holders of the assets, monitoring location of the assets.
                          </p>
                          <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/pr-po.jpg') }}" class="card-img-top"
                          alt="Palm Springs Road" />
                        <div class="card-body">
                          <h5 class="card-title">e-Form</h5>
                          <p class="card-text long-text-2">
                            PR-PO Controller is an app to manage purchase orders. It streamlines the process of creating and tracking purchase orders, and makes it easy to stay on top of your spending. PR-PO Controller lets you create and track purchase orders from anywhere, so you can always know where your money is going.
                          </p>
                          <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/PASS.jpg') }}" class="card-img-top"
                          alt="Los Angeles Skyscrapers" />
                        <div class="card-body">
                          <h5 class="card-title">PASS</h5>
                          <p class="card-text long-text-2">PASS V2 For managing tickets issue in perdana's projects.</p>
                            <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                  </div>
            </div>
            <div class="carousel-item w-100">
                <div class="row row-cols-1 row-cols-md-3 g-4 w-100">
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/AMS_headline.png') }}" class="card-img-top"
                          alt="Hollywood Sign on The Hill" />
                        <div class="card-body">
                          <h5 class="card-title">AMS</h5>
                          <p class="card-text long-text-2">
                            AMS is a tools to managing company's assets and monitoring their assets. In this tools you could see how much assets that company's owned, manage holders of the assets, monitoring location of the assets.
                          </p>
                          <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/pr-po.jpg') }}" class="card-img-top"
                          alt="Palm Springs Road" />
                        <div class="card-body">
                          <h5 class="card-title">e-Form</h5>
                          <p class="card-text long-text-2">
                            PR-PO Controller is an app to manage purchase orders. It streamlines the process of creating and tracking purchase orders, and makes it easy to stay on top of your spending. PR-PO Controller lets you create and track purchase orders from anywhere, so you can always know where your money is going.
                          </p>
                          <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/PASS.jpg') }}" class="card-img-top"
                          alt="Los Angeles Skyscrapers" />
                        <div class="card-body">
                          <h5 class="card-title">PASS</h5>
                          <p class="card-text long-text-2">PASS V2 For managing tickets issue in perdana's projects.</p>
                            <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                  </div>
            </div>
            <div class="carousel-item w-100">
                <div class="row row-cols-1 row-cols-md-3 g-4 w-100">
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/AMS_headline.png') }}" class="card-img-top"
                          alt="Hollywood Sign on The Hill" />
                        <div class="card-body">
                          <h5 class="card-title">AMS</h5>
                          <p class="card-text long-text-2">
                            AMS is a tools to managing company's assets and monitoring their assets. In this tools you could see how much assets that company's owned, manage holders of the assets, monitoring location of the assets.
                          </p>
                          <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/pr-po.jpg') }}" class="card-img-top"
                          alt="Palm Springs Road" />
                        <div class="card-body">
                          <h5 class="card-title">e-Form</h5>
                          <p class="card-text long-text-2">
                            PR-PO Controller is an app to manage purchase orders. It streamlines the process of creating and tracking purchase orders, and makes it easy to stay on top of your spending. PR-PO Controller lets you create and track purchase orders from anywhere, so you can always know where your money is going.
                          </p>
                          <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card">
                        <img src="{{ asset('img/PASS.jpg') }}" class="card-img-top"
                          alt="Los Angeles Skyscrapers" />
                        <div class="card-body">
                          <h5 class="card-title">PASS</h5>
                          <p class="card-text long-text-2">PASS V2 For managing tickets issue in perdana's projects.</p>
                            <button class="btn btn-primary btn-sm">Enter</button>
                        </div>
                      </div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection


@section('content-sidebar')
<!-- Nav Item - Dashboard -->
<li class="nav-item active">
    <a class="nav-link" href="#">
        <i class="fas fa-fw fa-user-circle"></i>
        <span>Login</span></a>
</li>
<div class="container">
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <div class="col-xs-8">
                <input style="height: 35px; font-size: small;" id="email" type="email"
                    class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"
                    required autocomplete="email" placeholder="Username" autofocus>

                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 5%;">
            <div class="col-xs-8">
                <input style="height: 35px; font-size: small;" id="password" type="password"
                    class="form-control @error('password') is-invalid @enderror" name="password" required
                    autocomplete="current-password" placeholder="Password">

                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <label class="form-check-label">
            &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;<input class="form-check-input" type="checkbox" name="remember" id="remember"
                {{ old('remember') ? 'checked' : '' }}>
            <span class="form-check-sign" style="color: azure; font-size: 12px;">Remember Me</span>
        </label>

        <div class="form-group text-right" style="margin-top: 5%;">
            <div class="col-xs-8">
                <button type="submit" class="btn btn-primary btn-sm">
                    {{ __('Login') }}
                </button>
            </div>
        </div>

        <div class="col-xs-8 text-center">
            @if (Route::has('password.request'))
            <a class="text-center" style="color: azure; font-size: 12px;" class="btn btn-link"
                href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
            @endif
        </div>
    </form>
</div>
@endsection

@section('sidebar-info-ssl')
<hr class="sidebar-divider my-0">
<div class="text-center text"><br>
    <p><strong><a>Supported Browser</a></strong><br>
        <a>Chrome, Firefox, IE9, Opera, Safari, dan mobile browser terkini
            <br>Min : 1024 x 768</a>
    </p>
</div>
<div class="text-center">
    <img class="text-center" width="150px" height="100px" src="{{ asset('img/ssl.png') }}" />
</div>
@endsection
