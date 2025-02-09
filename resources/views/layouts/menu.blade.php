@extends('layouts.main')

@section('title', 'Főoldal')

@section('header1', "Főoldal")

@section('content')
    @guest
        @include('components.login')
    @endguest
    @auth

    @endauth
@endsection
