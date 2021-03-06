@extends(Config::get('credentials.email'))

@section('content')
<p>Thank you for creating an account on <a href="{!! $url !!}" target="_blank">{!! Config::get('app.name') !!}</a>.</p>
@if (isset($link))
    <p>To activate your account, <a href="{!! $link !!}" target="_blank">click here</a>.</p>
@else
    <p>No account activation is required.</p>
@endif
@stop
