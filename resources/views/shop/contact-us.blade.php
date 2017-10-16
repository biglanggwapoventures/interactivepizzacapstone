@extends('shop.layout')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-header">
            <h1 class="text-primary"> Interactive Pizza Ordering System</h1>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">Contact Details</h4>
            </div>
            <div class="panel-body">
                <address style="font-size: 14px;">  
                    <strong>Landline</strong>
                    <br>
                    <span class="text-primary">032-328-0889</span>
                    <br><br>

                    <strong>Cellphone</strong>
                    <br>
                    <span class="text-primary">+63-956-876-8698</span>
                    <br><br>

                    <strong>Address</strong>
                    <br>
                    <span class="text-primary">
                        2F, 33B, Jcentre Mall <br>
                        A.S Fortuna St. , Brgy. Bakilid, <br>
                        Mandaue City
                    </span>
                    <br><br>

                    <strong>Email</strong>
                    <br>
                    <span class="text-primary">Interactivepizzaordering@pizza.com</span>
                </address>
            </div>
        </div>
    </div>
    
    <div class="col-sm-8">
        <img class="img-responsive" src="{{ asset('img/pizza-logo.png') }}" alt="{{ asset('img/pizza-logo.png') }}">
    </div>
</div>
@endsection