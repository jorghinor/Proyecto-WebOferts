@extends('template')

@section('banner')
<div class="page-header" style="background: url('{{ asset('assets/img/banner1.jpg') }}');">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-wrapper">
                    <h2 class="product-title">Login</h2>
                    <ol class="breadcrumb">
                        <li><a href="index.html">Home /</a></li>
                        <li class="current">Login</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('section')
<section class="login section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-12 col-xs-12">

                <div class="login-form login-area">
                    <h3>Autentificación Requerida</h3>
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                    @endif
                    <form method="POST" class="login-form" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <div class="input-icon">
                                <i class="lni-user"></i>
                                <input id="email" type="email" class="form-control1 form-control-lg nocap @error('email') is-invalid @enderror" name="email" value="" required autocomplete="off" autofocus>
                            </div>
                            @error('email')
                            <small class="form-text text-muted">
                                <span class="danger1">Usuario ó contraña no validas</span>
                            </small>
                            @enderror

                        </div>
                        <div class="form-group">
                            <div class="input-icon">
                                <i class="lni-lock"></i>
                                <input id="password" type="password" value="" class="form-control1 form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            </div>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">

                            <button class="btn btn-common log-btn" type="submit">
                                {{ __('Ingresar') }}
                            </button>

                            @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('¿Olvidaste tu contraseña??') }}
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
