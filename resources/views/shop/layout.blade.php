<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.min.css') }}">



    @stack('css')
    <style type="text/css">
        .navbar{
            border-radius: 0px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('shop.show.home') }}">{{ config('app.name') }}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="{{ route('shop.show.cart') }}">
                            <i class="glyphicon glyphicon-shopping-cart"></i>
                            My Cart
                            @if(MyCart::getItemCount())
                                <span class="badge">{{ MyCart::getItemCount() }}</span>
                            @endif
                        </a>
                    </li>
                    <li><a href="{{ route('shop.show.custom-pizza-form') }}"><i class="glyphicon glyphicon-star"></i> Create your own pizza!</a></li>
                    <li><a href="{{ route('shop.show.contact-us') }}"><i class="glyphicon glyphicon-envelope"></i> Contact us</a></li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    @auth
                        <li class="top-head-dropdown" >
                               <a data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" id="show-notification"><i class="fa fa-globe"></i>
                                    @if($unreadNotificationsCount > 0)
                                        <span class="badge">{{ $unreadNotificationsCount }}</span>
                                    @endif
                                     <span class="caret"></span>
                                </a>

                              <ul class="dropdown-menu dropdown-menu-right">
                                @forelse($notifications AS $notif)
                                    <li>
                                      <a href="{{ route('customer.show.order-history') }}" class="top-text-block">
                                        <div class="top-text-heading">{{ $notif->message }}</div>
                                        <div class="top-text-light">{{ date_create($notif->created_at)->format('m/d/Y h:i A') }}</div>
                                      </a>
                                    </li>
                                @empty
                                    <li>No new notifications</li>
                                @endforelse
                              </ul>
                            </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->fullname }} <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('customer.show.profile') }}">Profile</a></li>
                                <li><a href="{{ route('customer.show.order-history') }}">Order History</a></li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    {!! Form::open(['url' => route('shop.do.logout'), 'method' => 'post', 'id' => 'logout-form']) !!}
                                    {!! Form::close() !!}
                                    <a onclick="javascript:confirm('Are you sure') ? document.getElementById('logout-form').submit() : void(0)">Logout</a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li><a href="{{ route('shop.show.registration') }}">Register</a></li>
                        <li><a href="{{ route('shop.show.login') }}">Sign in</a></li>
                    @endauth

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

    <div class="container">
        @yield('content')

    </div>


    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#show-notification').click(function () {
                var $this = $(this);
                $.post("{{ route('customer.clear.notifications') }}")
                    .done(function () {
                        $this.find('.badge').remove();
                    })
            })
        })
    </script>
    @stack('js')
    @stack('modals')
</body>
</html>
