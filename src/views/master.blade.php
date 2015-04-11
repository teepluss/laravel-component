<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        {!! component()->styles() !!}
    </head>
    <body>
        @yield('content')

        {!! component()->scripts() !!}
    </body>
</html>