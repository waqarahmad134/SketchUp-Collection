@extends('layouts.admin')

@section('title', 'New Product')

@section('content')
@include('admin.products._form', ['product' => null, 'isEditMode' => false, 'categories' => $categories, 'tags' => $tags, 'allProducts' => $allProducts])
@endsection
