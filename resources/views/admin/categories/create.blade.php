@extends('layouts.admin')
@section('title','Create New Category')
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('category.index') }}">categories</a></li>
        <li class="breadcrumb-item active">create</li>
    </ol>
@endsection
@section('content')
    <form action="{{ route('category.store') }}" method="post" enctype="multipart/form-data">
    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
        {{ csrf_field() }} -->
        @csrf
        @include('admin.categories._form')
    </form>
@endsection
