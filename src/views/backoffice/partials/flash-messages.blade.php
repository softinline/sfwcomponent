@if(isset($errors))
    @if($errors->any())
        <div class="errors-container mt-2">
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        </div>
    @endif
@endif
@if(Session::has('message_error'))
    <div class="errors-container mt-2">
        <div class="alert alert-danger">
            {{ Session::get('message_error') }}
        </div>
    </div>
@endif    
@if(Session::has('message_success'))
    <div class="success-container mt-2">
        <div class="alert alert-success">
            <i class="las la-check-circle"></i> {{ Session::get('message_success') }}
        </div>    
    </div>
@endif