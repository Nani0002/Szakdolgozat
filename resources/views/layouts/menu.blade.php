@extends('layouts.main')

@php
    $titles = [
        'register' => 'Munkatárs felvétele',
        'company' => 'Ügyfelek',
        'company/create' => 'Ügyfél felvétele',
        'worksheet' => 'Munkalapok',
    ];

    if (request()->is('company/*/edit')) {
        $title = 'Ügyfél szerkesztése';
    } else {
        $title = $titles[request()->path()] ?? 'Főoldal';
    }
@endphp

@section('header1', $title)
@section('title', $title)

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
        @elseif(request()->is('company/create') || request()->is('company/*/edit'))
            @include('companies.company_form')
        @elseif(request()->is('worksheet'))
            @include('worksheets.worksheets')
        @else
            <p>Nem található tartalom.</p>
        @endif
    @endauth
@endsection
