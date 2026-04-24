@extends('front.master-page')

@section('content')
<section id="features" class="features text-center">
    <div class="container my-5 text-start">
        <h3 class="mb-4 text-center"><strong>{{ $page->title }}</strong></h3>
        @if($page->meta_description)
            <p class="text-muted small text-center mb-4">{{ $page->meta_description }}</p>
        @endif
        <div class="cms-page-body">
            {!! $page->body !!}
        </div>
    </div>
</section>
@endsection
