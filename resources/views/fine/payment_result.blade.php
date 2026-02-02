@extends('layouts.guest')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        @if($status === 'success')
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-success font-weight-bold mb-2">Payment Successful</h4>
                            <p class="text-muted mb-4">{{ $message ?: 'Your fine has been paid successfully.' }}</p>
                        @elseif($status === 'cancelled')
                            <div class="mb-4">
                                <i class="fas fa-times-circle text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-warning font-weight-bold mb-2">Payment Cancelled</h4>
                            <p class="text-muted mb-4">{{ $message ?: 'You cancelled the payment. Your fine remains pending.' }}</p>
                        @else
                            <div class="mb-4">
                                <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-danger font-weight-bold mb-2">Payment Failed</h4>
                            <p class="text-muted mb-4">{{ $message ?: 'Payment could not be completed. Your fine remains pending.' }}</p>
                        @endif
                        @auth
                            <a href="{{ route('fines.index') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-list mr-2"></i> Go to My Fines
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg ml-2">
                                <i class="fas fa-home mr-2"></i> Home
                            </a>
                        @else
                            <p class="text-muted small mb-3">You may have been signed out after payment. Log in to see your updated fines.</p>
                            <a href="{{ url('/') }}?redirect={{ urlencode(route('fines.index')) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt mr-2"></i> Log in to view My Fines
                            </a>
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg ml-2">
                                <i class="fas fa-home mr-2"></i> Home
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
