@extends('process.master')

@section('title', 'Thank You | eConfirm')

@section('header-actions')
    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-home me-1"></i> Home
    </a>
@endsection

@section('content')
<div class="card mx-auto shadow-sm" style="max-width: 700px;">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="mb-3">
                <i class="fas fa-check-circle text-success" style="font-size: 2.2rem;"></i>
            </div>
            <h4 class="fw-bold mb-2">Thank you for using eConfirm</h4>
            <p class="text-muted mb-0">
                Your escrow process is complete. You can explore more actions using the links below.
            </p>
        </div>

        <div class="row g-2 g-md-3">
            <div class="col-12 col-md-6">
                <a href="{{ route('scam.watch.report') }}" class="btn btn-outline-danger w-100 text-start">
                    <i class="fas fa-flag me-2"></i> Report a Scam
                </a>
            </div>
            <div class="col-12 col-md-6">
                <a href="{{ route('support') }}" class="btn btn-outline-primary w-100 text-start">
                    <i class="fas fa-headset me-2"></i> Contact Support
                </a>
            </div>
            <div class="col-12 col-md-6">
                <a href="{{ route('help') }}" class="btn btn-outline-secondary w-100 text-start">
                    <i class="fas fa-book me-2"></i> Help Center
                </a>
            </div>
            <div class="col-12 col-md-6">
                <a href="{{ route('scam.watch') }}" class="btn btn-outline-dark w-100 text-start">
                    <i class="fas fa-shield-alt me-2"></i> Confirm / Scam Alert
                </a>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-success">
                <i class="fas fa-home me-1"></i> Back to Homepage
            </a>
            <a href="{{ route('portal') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i> Open Portal
            </a>
        </div>
    </div>
</div>
@endsection

