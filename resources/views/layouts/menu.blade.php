@extends('layouts.main')

@section('title', request()->is('register') ? 'Munkatárs felvétele' : 'Főoldal')
@section('header1', request()->is('register') ? 'Munkatárs felvétele' : 'Főoldal')

@section('content')
    @guest
        @include('components.login')
    @endguest

    @auth
        @if (request()->is('/'))
            @include('tickets.tickets')
        @elseif(request()->is('register'))
            @include('components.register')
        @else
            <p>Nem található tartalom.</p>
        @endif
    @endauth
@endsection
