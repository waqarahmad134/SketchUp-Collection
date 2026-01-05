@extends('layouts.admin')

@section('title', 'New Post')

@php
$post = null;
$isEditMode = false;
@endphp

@section('content')
@include('new admin.admin.posts._editor-form', ['post' => null, 'isEditMode' => false, 'categories' => $categories, 'tags' => $tags])
@endsection
