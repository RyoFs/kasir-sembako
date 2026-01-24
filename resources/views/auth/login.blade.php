@extends('layouts.auth')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border-0 rounded-4 login-card">
            <div class="card-header bg-primary text-white text-center rounded-top-4 py-3">
                <h4 class="mb-0 fw-bold">Login Kasir</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="form-control form-control-lg" 
                            placeholder="Masukkan username" 
                            required 
                            autocomplete="off"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="pin" class="form-label fw-semibold">PIN (4 Digit)</label>
                        <input 
                            type="password" 
                            name="pin" 
                            id="pin" 
                            class="form-control form-control-lg" 
                            placeholder="Masukkan PIN" 
                            maxlength="4" 
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="fa fa-sign-in-alt me-2"></i>Masuk
                    </button>
                </form>
            </div>
            <div class="card-footer text-center bg-light rounded-bottom-4 py-2">
                <small class="text-muted">© {{ date('Y') }} Aplikasi Kasir Sembako</small>
            </div>
        </div>
    </div>
</div>
@endsection
