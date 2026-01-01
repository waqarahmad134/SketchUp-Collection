@extends('layouts.app')

@section('content')
    @include('partials.sections.hero')
    @include('partials.sections.stats')
    @include('partials.sections.bundles', ['products' => $products ?? collect()])
    @include('partials.sections.features')
    @include('partials.sections.blog', ['featuredPosts' => $featuredPosts ?? collect()])
    @include('partials.sections.testimonials')
    @include('partials.sections.cta')
@endsection

