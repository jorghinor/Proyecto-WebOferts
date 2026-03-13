@extends('template')

@section('banner')
@endsection

@section('section')
    <anuncios-por-categoria-component
        :anuncios-data="{{ json_encode($anuncios) }}"
        :categoria="{{ json_encode($categoria) }}"
    ></anuncios-por-categoria-component>
@endsection
