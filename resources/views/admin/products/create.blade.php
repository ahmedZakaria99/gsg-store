@extends('layouts.admin')
@section('title','Create New Product')
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">categories</a></li>
        <li class="breadcrumb-item active">create</li>
    </ol>
@endsection
@section('content')
    <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
        {{ csrf_field() }} -->
        @csrf
        @include('admin.products._form')
    </form>
@endsection
