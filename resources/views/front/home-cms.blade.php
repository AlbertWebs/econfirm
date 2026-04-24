@extends('front.master')

@section('seo_title')
    {{ $page->title }}
@endsection

@if (filled($page->meta_description))
    @section('seo_description')
        {{ $page->meta_description }}
    @endsection
@endif

@section('content')
    <div class="cms-home-body">
        {!! $page->body !!}
    </div>
@endsection
