@if(session('cartMessage'))
    <div class="alert alert-success">
        <strong><i class="fa fa-check"></i></strong> {{ session('cartMessage') }}
    </div>
@endif
