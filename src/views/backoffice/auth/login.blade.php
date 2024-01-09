@extends('sfwcomponent::backoffice.layouts.auth')
@section('content')
    <div class="login-box">
        <div class="card">
            <div class="card-header text-center">
                SFW - Backoffice - Login
            </div>
            <div class="card-body">            
                <form action="{{ url('/sfw/auth/login') }}" method="post">
                    <p class="login-box-msg"></p>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="{{ ucfirst(trans('sfwcomponent::email')) }}" name="email" value="" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="{{ ucfirst(trans('sfwcomponent::password')) }}" name="password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block"><i class="las la-sign-in-alt"></i> {{ ucfirst(trans('sfwcomponent::login')) }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>    
@stop