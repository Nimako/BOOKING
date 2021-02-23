
          <!-- Topbar Start -->
        <div class="navbar-custom">
            <ul class="list-unstyled topnav-menu float-right mb-0">

              
                <li class="dropdown notification-list">
                    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="assets/images/users/avatar-1.jpg" alt="user-image" class="rounded-circle">
                        <span class="d-none d-sm-inline-block ml-1 font-weight-medium">{{Auth::guard('admin')->User()->fullname}}</span>
                        <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow text-white m-0">Welcome !</h6>
                        </div>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-outline"></i>
                            <span>Profile</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="mdi mdi-settings-outline"></i>
                            <span>Settings</span>
                        </a>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="mdi mdi-lock-outline"></i>
                            <span>Lock Screen</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="{{url('logout')}}" class="dropdown-item notify-item">
                            <i class="mdi mdi-logout-variant"></i>
                            <span>Logout</span>
                        </a>

                    </div>
                </li>

                {{-- <li class="dropdown notification-list">
                    <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                        <i class="mdi mdi-settings-outline noti-icon"></i>
                    </a>
                </li> --}}

            </ul>

            <!-- LOGO -->
            <div class="logo-box">
                <a href="{{url("dashboard")}}" class="logo text-center logo-dark">
                    <span class="logo-lg">
                        {{-- <img src="assets/images/logo.png" alt="" height="22"> --}}
                        <span class="logo-lg-text-dark">StayLug</span> 
                    </span>
                    <span class="logo-sm">
                        <span class="logo-lg-text-dark">StayLug</span> 
                        {{-- <img src="assets/images/logo-sm.png" alt="" height="24"> --}}
                    </span>
                </a>

                <a href="{{url("dashboard")}}" class="logo text-center logo-light">
                    <span class="logo-lg">
                        {{-- <img src="assets/images/logo-light.png" alt="" height="22"> --}}
                         <span class="logo-lg-text-dark">StayLug</span> 
                    </span>
                    <span class="logo-sm">
                        <span class="logo-lg-text-dark">StayLug</span> 
                        {{-- <img src="assets/images/logo-sm-light.png" alt="" height="24"> --}}
                    </span>
                </a>
            </div>

            <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                <li>
                    <button class="button-menu-mobile waves-effect waves-light">
                        <i class="mdi mdi-menu"></i>
                    </button>
                </li>
    


                {{-- <li class="dropdown dropdown-mega d-none d-lg-block"></li> --}}
            </ul>
        </div>
        <!-- end Topbar -->


        <!-- ========== Left Sidebar Start ========== -->
        <div class="left-side-menu">

            <div class="slimscroll-menu">

                <!--- Sidemenu -->
                <div id="sidebar-menu">

                    <ul class="metismenu" id="side-menu">

                        <li class="menu-title">Navigation</li>

                        <li>
                            <a href="{{url("/dashboard")}}">
                                <i class="mdi mdi-view-dashboard"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>

                        <li>
        
                        <li>
                            <a href="javascript: void(0);">
                                <i class="mdi mdi-home"></i>
                                <span> Properties </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="{{url("pending-properties")}}">Pending Properties</a></li>
                                <li><a href="{{url("approved-properties")}}">Approved Properties</a></li>
                            </ul>
                        </li>


                        {{-- <li class="menu-title mt-2">Components</li> --}}

                        <li>
                            <a href="javascript: void(0);">
                                <i class="icon-people"></i>
                                <span> Users </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="{{url("partner-users")}}">Partner Users</a></li>
                                <li><a href="{{url("guest-users")}}">Guest users</a></li>
                            </ul>
                        </li>
                        

                      <li class="menu-title mt-2">Management</li>

                      <li>
                        <a href="javascript: void(0);">
                            <i class="mdi mdi-settings-outline"></i>
                            <span> Configuration </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{url("lookupSetup/amenities")}}">Amenities</a></li>
                            <li><a href="{{url("lookupSetup/facilities")}}">Facilities</a></li>
                            <li><a href="{{url("lookupSetup/bedTypes")}}">Bed Types</a></li>
                        </ul>
                    </li>

                  
                    </ul>

                </div>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
        <!-- Left Sidebar End -->

