@extends('template')

@section('banner')
<div class="page-header" style="background: url('{{ asset('assets/img/banner1.jpg') }}');">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-wrapper">
                    <h2 class="product-title">Join Us</h2>
                    <ol class="breadcrumb">
                        <li><a href="#">Home /</a></li>
                        <li class="current">Register</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('section')
<section class="register section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-12 col-xs-12">
                <registro></registro>
            </div>
        </div>
    </div>
</section>
@endsection
