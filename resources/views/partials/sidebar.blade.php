<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->fullname }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">Maintain</li>
            <li>
                <a href="{{ route('ingredient-categories.index') }}">
                    <i class="fa fa-list-alt"></i> <span>Categories</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ingredients.index') }}">
                    <i class="fa fa-shopping-cart"></i> <span>Items</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pizzas.index') }}">
                    <i class="fa fa-superpowers"></i> <span>Pizzas</span>
                </a>
            </li>
             <li>
                <a href="{{ route('delivery-personnel.index') }}">
                    <i class="fa fa-motorcycle"></i> <span>Delivery Personnel</span>
                </a>
            </li>
        </ul>
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">Tasks</li>
            <li>
                <a href="#">
                    <i class="fa fa-list-alt"></i> <span>Stock Adjustments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.manage-orders') }}">
                    <i class="fa fa-list-alt"></i> <span>Manage Orders</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
