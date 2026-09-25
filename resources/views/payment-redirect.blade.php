<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Redirecting to payment...' }}</title>
</head>
<body>
    <p style="font-family: sans-serif; text-align: center; margin-top: 80px;">
        Redirecting to {{ $gatewayName }} for secure payment...
    </p>
    <form id="gateway-form" method="POST" action="{{ $action }}">
        @foreach($fields as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>
    <script>document.getElementById('gateway-form').submit();</script>
</body>
</html>
