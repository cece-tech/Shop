@extends('admin.layouts.layout')
@section('admin_page_title')
Create Sub Category
@endsection
@section('admin_layout')
<div class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create Sub Category</h5>
                </div>
                <div class="card-body">
                @if ($errors->any())
    <div class="alert alert-warning alert-dismissible fade show">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('message'))
    <div class="alert alert-success">
        {{session('message')}}
    </div>
@endif
                    <form action="{{route('store.subcat')}}" method="POST">
                        @csrf
                        <layout for="subcategory_name"class="fw-bold mb-2">Give Name me of your Sub Category</label>
                        <input type="text" class="form-control" name="subcategory_name" placeholder="Computer">

                        <layout for="category_id"class="fw-bold mb-2">Select Category</label>
                        <select name="category_id" class="form-control" id="category_id">
                            @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->category_name}}</option>
                            @endforeach
                        </select>
 
                        <button type="submit" class="btn btn-primary w-100 mt-2">Add Sub Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection