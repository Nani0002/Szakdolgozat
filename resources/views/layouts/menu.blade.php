@extends('layouts.main')

@section('title', request()->is('register') ? 'Munkatárs felvétele' : (request()->is('company') ? 'Ügyfelek' :
    'Főoldal'))
@section('header1', request()->is('register') ? 'Munkatárs felvétele' : (request()->is('company') ? 'Ügyfelek' :
    'Főoldal'))

@section('content')
    @guest
        <div class="container">
            @include('components.login')
        </div>
    @endguest

    @auth
        @if (request()->is('/'))
            @include('tickets.tickets')
        @elseif(request()->is('register'))
            <div class="container">
                @include('components.register')
            </div>
        @elseif(request()->is('company'))
            <div class="container">
                @include('companies.companies')
            </div>
        @else
            <p>Nem található tartalom.</p>
        @endif
    @endauth
@endsection
