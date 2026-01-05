@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
@include('new admin.admin.products._form', ['product' => $product, 'isEditMode' => true, 'categories' => $categories, 'tags' => $tags, 'allProducts' => $allProducts])
@endsection
