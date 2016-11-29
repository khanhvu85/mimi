<div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="">WEIMI</a>
</div>
<!-- /.navbar-header -->

<ul class="nav navbar-top-links navbar-right">
    <!-- /.dropdown -->
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            @if (Auth::guard('admin')->user())
                <img class="img-circle" src="{{URL::to('avatars')}}/50x50_avatar_default.png" alt="" height="40"> <i class="fa fa-caret-down"></i>
            @endif
        </a>

        <ul class="dropdown-menu dropdown-user">
            <li>
                <a href="{{URL::to('admin/logout')}}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
            </li>
        </ul>
        <!-- /.dropdown-user -->
    </li>
    <!-- /.dropdown -->
</ul>
<!-- /.navbar-top-links -->