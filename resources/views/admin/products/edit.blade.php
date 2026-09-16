@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
@include('admin.products._form', ['product' => $product, 'isEditMode' => true, 'categories' => $categories, 'tags' => $tags, 'allProducts' => $allProducts])
@endsection
