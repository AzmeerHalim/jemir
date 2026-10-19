@extends('layouts.talentsys')

@section('title', 'Edit Activity')

@section('content')
@include('itc.activities._form', ['activity' => $activity, 'action' => route('itc.activities.update', $activity), 'method' => 'PUT'])
@endsection
