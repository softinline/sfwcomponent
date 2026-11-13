@extends('sfwcomponent::backoffice.layouts.auth')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header text-center">
                    <h4 class="mb-0">{{ trans('sfwcomponent::setup_title') }}</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ trans('sfwcomponent::setup_intro') }}</p>

                    @if(!empty($error))
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endif
                    @if(!empty($success))
                        <div class="alert alert-success">{{ $success }}</div>
                    @endif

                    <form method="post" action="{{ url(\Softinline\SfwComponent\SfwComponent::ROUTE_PREFIX . '/setup') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label>{{ trans('sfwcomponent::setup_current_password') }}</label>
                            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ trans('sfwcomponent::setup_new_password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required autocomplete="new-password">
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ trans('sfwcomponent::setup_confirm_password') }} <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ trans('sfwcomponent::setup_submit') }}</button>
                        </div>
                    </form>

                    @if(!empty($output))
                        <hr>
                        <label>{{ trans('sfwcomponent::result') }}</label>
                        <pre class="bg-light p-2" style="max-height:240px;overflow:auto;white-space:pre-wrap">{{ $output }}</pre>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection