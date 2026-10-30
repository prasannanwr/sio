@extends('admin.app')
@section('page_title', 'Home')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title fw-semibold mb-4">Settings</h5>
                @if (Session::has('message') or isset($message))
                    <div
                        class="alert alert-{{ Session::get('message_type', $message_type ?? 'danger') }} alert-dismissable">
                        <strong>Message &nbsp;</strong> {{ Session::get('message') ?? $message }}
                    </div>
                @endif
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="mb-4">Reset Password</h5>
                        <form action="{{ route('updatePassword') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="password">{{ __('Password') }} <i class="text-danger">*</i></label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mt-3">
                                <label for="password-confirm">{{ __('Confirm Password') }}
                                    <i class="text-danger">*</i></label>
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="text-end mt-3">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
