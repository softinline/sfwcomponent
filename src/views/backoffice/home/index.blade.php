@extends('sfwcomponent::backoffice.layouts.default')

@section('content')
    <div class="content-header"></div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-1">SFW</h5>
                        <p class="text-muted mb-0">{{ ucfirst(trans('sfwcomponent::dashboard')) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection