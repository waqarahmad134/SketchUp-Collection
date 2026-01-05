@extends('layouts.admin')

@section('title', 'New Product')

@section('content')
@include('new admin.admin.products._form', ['product' => null, 'isEditMode' => false, 'categories' => $categories, 'tags' => $tags, 'allProducts' => $allProducts])
@endsection
