@extends('layouts.admin')
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active">categories</li>
    </ol>
@endsection
@section('title')
    {{$title}} <a href="{{ route('category.create') }}">Create</a>
@endsection
@section('content')
    @if($success)
        <div class="alert alert-success">
            {{ $success }}
        </div>
    @endif
    <table class="table">
        <thead>
        <tr>
            <th>Loop</th>
            <th>ID</th>
            <th>Name</th>
            <th>Slug</th>
            <th>Parent ID</th>
            <th>Status</th>
            <th>Created At</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $loop->first ? 'First' : ($loop->last ? 'Last' : $loop->iteration) }}</td>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->slug }}</td>
                <td>{{ $category->parent_name }}</td>
                <td>{{ $category->status }}</td>
                <td>{{ $category->created_at }}</td>
                <td><a href="{{ route('category.edit', $category->id) }}" class="btn-sm btn-dark">Edit</a></td>
                <td>
                    <form action="{{ route('category.destroy', $category->id) }}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>
@endsection
