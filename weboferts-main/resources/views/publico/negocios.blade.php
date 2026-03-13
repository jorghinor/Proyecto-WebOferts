@extends('template')

@section('banner')
<div class="page-header cabecera">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-wrapper">
                    <h2 class="product-title">Negocios</h2>
                    <ol class="breadcrumb">
                        <li><a href="#">Home /</a></li>
                        <li class="current">My Ads</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('section')
<div class="main-container section-padding">
    <div class="container">
        <div class="row">
            <div class="page-content">
                <negocios></negocios>
            </div>
        </div>
    </div>
</div>
@endsection