@extends('layouts.talentsys')

@section('title', 'Create Activity')

@section('content')
@include('itc.activities._form', ['activity' => null, 'action' => route('itc.activities.store'), 'method' => 'POST'])
@endsection
