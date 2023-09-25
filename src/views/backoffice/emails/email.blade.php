@extends('sfwcomponent::backoffice.layouts.emails')
@section('subject')
    {!! $subject !!}
@endsection
@section('message')    
	{!! $body !!}
@endsection