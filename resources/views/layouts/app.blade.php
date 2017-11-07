<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skins/_all-skins.min.css') }}">
    @stack('css')
</head>
<body class="hold-transition {{ Route::is('login') ? 'login-page' : 'skin-red sidebar-mini fixed' }}">
    <!-- Site wrapper -->
    <div class="wrapper">


        @adminpage
            @include('partials.header')
            @include('partials.sidebar')
        @endadminpage

        @yield('body')

        @adminpage
            <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
            <script src="{{ asset('js/adminlte.min.js') }}" type="text/javascript"></script>
            <script>
                $(document).ready(function () {
                    $('.sidebar-menu').tree()
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    function pollUnprocessedOrders () {
                        $.getJSON("{{ route('admin.poll.unprocessed-orders') }}")
                            .done(function (res) {
                                if(parseInt(res.count)){
                                    if($('#unprocessed-orders-count').length){
                                        $('#unprocessed-orders-count').text(res.count);
                                    }else{
                                        $('#manage-orders-link').append($('<span />', {text: res.count, class: 'badge', id: 'unprocessed-orders-count' }))
                                    }
                                }else{
                                    $('#unprocessed-orders-count').remove();
                                }
                                setTimeout(pollUnprocessedOrders, 1000);
                            });
                    }

                    pollUnprocessedOrders();
                })
            </script>
        @endadminpage

         @stack('modals')
        @stack('js')
    </div>
</body>
</html>
