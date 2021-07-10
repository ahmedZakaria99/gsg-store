@extends('layouts.admin')
@section('title','Edit Category')
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('category.index') }}">categories</a></li>
        <li class="breadcrumb-item active">edit</li>
    </ol>
@endsection
@section('content')
    <form action="{{ route('category.update',$category->id) }}" method="post" enctype="multipart/form-data">
    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}">
        {{ csrf_field() }} -->
        @csrf
        <!-- <input type="hidden" name="_method" value="put"> -->
        @method('put')
        @include('admin.categories._form')
    </form>
@endsection
