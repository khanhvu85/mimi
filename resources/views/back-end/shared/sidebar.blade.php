<style type="text/css">
    li>a {
        color: #000;
    }
    .active {
        background-color: #c7c7c7;
    }
</style>
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li class="sidebar-search">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                <!-- /input-group -->
            </li>

           {{--  <li>
                <a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dash board</a>
            </li> --}}

            @if(Auth::guard('admin')->check())
                <li @if(Request::is('admin/user-demo')) class="active" @endif>
                    <a href="/admin/user-demo"><i class="fa fa-users" aria-hidden="true"></i> User Demo</a>
                </li>
                <li @if(Request::is('admin/user-behaviors')) class="active" @endif>
                    <a href="/admin/user-behaviors"><i class="fa fa-bar-chart-o fa-fw"></i> User Behaviors</a>
                </li>
                <li @if(Request::is('admin/setting')) class="active" @endif>
                    <a href="/admin/setting"><i class="fa fa-cogs" aria-hidden="true"></i> Setting</a>
                </li>
           	@endif
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>