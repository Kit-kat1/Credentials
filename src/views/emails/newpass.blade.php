@extends(Config::get('views.email', 'layouts.email'))

@section('content')
<p>The password for your account on <a href="{{ $url }}">{{ Config::get('platform.name') }}</a> was just changed.</p>
<p>If this was not you, please contact us immediately.</p>
@stop
