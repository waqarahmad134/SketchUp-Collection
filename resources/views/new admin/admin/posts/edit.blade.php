@extends('layouts.admin')

@section('title', 'Editar Post')

@php
$isEditMode = true;
@endphp

@section('content')
@include('admin.posts._editor-form', ['post' => $post, 'isEditMode' => true, 'categories' => $categories, 'tags' => $tags])
@endsection
