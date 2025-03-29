@extends('layouts.main')

@php
    $titles = [
        'register' => 'Munkatárs felvétele',
        'company' => 'Ügyfelek',
        'company/create' => 'Ügyfél felvétele',
        'worksheet' => 'Munkalapok',
        'worksheet/search' => 'Munkalapok',
        'worksheet/create' => 'Munkalap felvétele',
    ];

    if (request()->is('company/*/edit')) {
        $title = 'Ügyfél szerkesztése';
    } elseif (request()->is('worksheet/create')) {
        $title = $titles['worksheet/create'];
    } elseif (request()->is('worksheet/search')) {
        $title = $titles['worksheet/search'];
    } elseif (request()->is('worksheet/*/edit')) {
        $title = 'Munkalap szerkesztése';
    } elseif (request()->is('worksheet/*')) {
        $title = 'Munkalap ' . $worksheet->sheet_number;
    }  elseif (request()->is('computer/*')) {
        $title = 'Számítógép ' . $computer->serial_number;
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
        @elseif(request()->is('worksheet/create') || request()->is('worksheet/*/edit'))
            @include('worksheets.worksheet_form')
        @elseif(request()->is('worksheet/search'))
            @include('worksheets.search')
        @elseif(request()->is('worksheet/*'))
            @include('worksheets.worksheet')
        @elseif(request()->is('computer/*'))
            @include('computers.computer')
        @elseif(request()->is('worksheet/create') || request()->is('worksheet/*/edit'))
            @include('companies.company_form')
        @else
            <p>Nem található tartalom.</p>
        @endif
    @endauth
@endsection
