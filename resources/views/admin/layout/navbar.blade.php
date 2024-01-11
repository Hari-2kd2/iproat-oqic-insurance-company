<!-- ============================================================== -->
<!-- Topbar header - style you can find in pages.scss -->
<!-- ============================================================== -->
<nav class="navbar navbar-default navbar-static-top">
    <div class="navbar-header">
        <!-- Search input and Toggle icon -->

        <ul class="nav navbar-top-links navbar-left">
            <li>
                <div class="logo-visiability hidden-xs" style="max-width: 240px;min-width:30px;padding:0 18px 0 8px">
                    <!-- Logo -->
                    <a class="logo" href="{{ url('dashboard') }}">
                        <!-- Logo icon image, you can use font-icon also --><b>
                            <!--This is dark logo icon-->
                            <p style="object-fit:fill;" title="Home" class="dark-logo img-fluid img-responsive hidden">
                                Home</p>
                            <img style="width: 100%; height: 100%; object-fit: contain;padding:10px 0;"
                                src="{{ url('admin_assets/img/oqic_logo.png') }}" alt="Home"
                                class="dark-logo img-fluid img-responsive hidden-xs" />
                        </b>
                        <!-- Logo text image you can use text also -->
                        <span class="hidden-xs">
                            <!--This is dark logo text-->
                        </span>
                    </a>
                </div>
            </li>
            <li><a href="javascript:void(0)" class="open-close waves-effect waves-light menuIcon"><i
                        class="ti-menu tiMenu"></i></a>
            </li>

            <li class="dropdown">
                <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"
                    style="color:#fff"> {{ App::getLocale() }}
                    <div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                </a>
                <ul class="dropdown-menu mailbox animated bounceInDown">
                    <li>
                        <div class="drop-title">@lang('common.chose_a_language')</div>
                    </li>
                    <li>
                        <div class="message-center">
                            <a href="{{ url('local/en') }}">
                                <h5>English</h5>
                            </a>
                        </div>
                        <div class="message-center">
                            <a href="{{ url('local/ar') }}" title="Spanish">
                                <h5>Arabic</h5>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a class="text-center" href="javascript:void(0);"> <strong>@lang('common.see_all_languages')</strong> <i
                                class="fa fa-angle-right"></i> </a>
                    </li>
                </ul>
                <!-- /.dropdown-messages -->
            </li>

        </ul>

        <ul class="nav navbar-top-links navbar-right pull-right imageIcon">
            <li class="dropdown">
                {{-- <a role="search" class="app-search hidden-sm hidden-xs m-r-10 waves-effect waves-light" href="#">
                    <select class="select2" name="search" id="search">
                        <option value="">Search</option>
                        @foreach (getRouteData('') as $key => $item)
                            <option value="{{ $key }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </a> --}}
                {{-- <a role="search" class="dropdown-toggle app-search hidden-sm hidden-xs waves-effect waves-light"
                    data-toggle="dropdown" href="#" style="color:#fff;">
                    <span class=""
                        style="background: #fff;padding:6px 12px;color:gray;border-radius:12px"><b>Goto</b><span
                            style="padding-left: 12px;"><i class="fa fa-search"></i></span></span>

                </a>
                <ul class="dropdown-menu scrollable-menu mailbox animated bounceInDown">
                    @foreach (getRouteData('') as $key => $menu)
                        <div class="message-center">
                            <a href="{{ route($key) }}">{{ $menu }}</a>
                        </div>
                    @endforeach
                </ul> --}}


                <form role="search" class="app-search hidden-sm hidden-xs m-r-10" hidden>
                    <input type="text" placeholder="Search..." name="search" class="form-control">
                    <a href="{{ route('search') }}"><i class="fa fa-search"></i></a>
                </form>
            </li>
            <li class="dropdown">
                <?php
                    $employeeInfo = employeeInfo();
                    if($employeeInfo[0]->photo != ''){
                    ?>
                <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
                    <img src="{!! asset('uploads/employeePhoto/' . $employeeInfo[0]->photo) !!}" alt="user-img" width="36" height="34" class="img-custom">
                    <b class="hidden-xs " style="color: #fff !important;padding-right: 4px"><span
                            class="text-capitalize">{!! ucwords($employeeInfo[0]->user_name) !!}</span></b>
                    <span class="caret"></span>
                </a>
                <?php  }else{ ?>
                <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
                    <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user-img" width="36" height="34"
                        class="img-custom"><span style="color: #fff !important;"></span>
                    <b class="hidden-xs" style="color: #fff !important; padding-right: 4px"><span class="hideMenu"
                            style="color: #fff !important;">{!! ucwords($employeeInfo[0]->user_name) !!}</span></b>
                    <span class="caret hideMenu"></span>
                </a>
                <?php } ?>
                <ul class="dropdown-menu dropdown-user animated stripMove imageDropdown">
                    <li><a href="{{ url('profile') }}"><i class="ti-user"></i> @lang('common.my_profile')</a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li><a href="{{ url('changePassword') }}"><i class="ti-settings"></i>
                            @lang('common.change_password')</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="{{ URL::to('/logout') }}"><i class="fa fa-power-off"></i>
                            @lang('common.logout')</a></li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->

            {{-- <li class="dropdown">
                <a class="dropdown-toggle waves-effect waves-light" id="notifications" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">
                    <span class="fa fa-bell"></span>

                </a>
                <ul class="dropdown-menu" aria-labelledby="notificationsMenu" id="notificationsMenu">
                    <li class="dropdown-header notificationsMenuLi">No notifications</li>
                </ul>
            </li> --}}

        </ul>
    </div>
    <!-- /.navbar-header -->
    <!-- /.navbar-top-links -->
    <!-- /.navbar-static-side -->
</nav>
<!-- End Top Navigation -->
