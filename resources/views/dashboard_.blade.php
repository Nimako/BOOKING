   
   @extends('layouts.tempDashboard')

   @section('content')

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                    
                    <!-- Start Content-->
                    <div class="container-fluid">
                        
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Uplon</a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>
                                    <h4 class="page-title">Dashboard</h4>
                                </div>
                            </div>
                        </div>     
                        <!-- end page title --> 

                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card-box tilebox-one">
                                    <i class="icon-layers float-right m-0 h2 text-muted"></i>
                                    <h6 class="text-muted text-uppercase mt-0">Registered Users</h6>
                                    <h3 class="my-3" data-plugin="counterup">1,587</h3>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-3">
                                <div class="card-box tilebox-one">
                                    <i class="icon-people float-right m-0 h2 text-muted"></i>
                                    <h6 class="text-muted text-uppercase mt-0">Partners</h6>
                                    <h3 class="my-3">46,782</span></h3>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-3">
                                <div class="card-box tilebox-one">
                                    <i class="icon-home float-right m-0 h2 text-muted"></i>
                                    <h6 class="text-muted text-uppercase mt-0">Hotels</h6>
                                    <h3 class="my-3">15.9</span></h3>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-3">
                                <div class="card-box tilebox-one">
                                    <i class="icon-home float-right m-0 h2 text-muted"></i>
                                    <h6 class="text-muted text-uppercase mt-0">Apartment</h6>
                                    <h3 class="my-3" data-plugin="counterup">1,890</h3>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->


                        <div class="row">
                            <div class="col-lg-6 col-xl-8">
                                <div class="card-box">
                                    <h4 class="header-title mb-3">Sales Statistics</h4>

                                    <div class="text-center">
                                        <ul class="list-inline chart-detail-list mb-0">
                                            <li class="list-inline-item">
                                                <h6 class="text-info"><i class="mdi mdi-circle-outline mr-1"></i>Series A</h6>
                                            </li>
                                            <li class="list-inline-item">
                                                <h6 class="text-success"><i class="mdi mdi-triangle-outline mr-1"></i>Series B</h6>
                                            </li>
                                            <li class="list-inline-item">
                                                <h6 class="text-muted"><i class="mdi mdi-square-outline mr-1"></i>Series C</h6>
                                            </li>
                                        </ul>
                                    </div>

                                    <div id="morris-bar-stacked" class="morris-chart" style="height: 320px;"></div>

                                </div>
                            </div><!-- end col-->

                            <div class="col-lg-6 col-xl-4">
                                <div class="card-box">
                                    <h4 class="header-title mb-3">Trends Monthly</h4>

                                    <div class="text-center mb-3">
                                        <div class="btn-group" role="group" aria-label="Basic example">
                                            <button type="button" class="btn btn-sm btn-secondary">Today</button>
                                            <button type="button" class="btn btn-sm btn-secondary">This Week</button>
                                            <button type="button" class="btn btn-sm btn-secondary">Last Week</button>
                                        </div>
                                    </div>

                                    <div id="morris-donut-example" class="morris-chart" style="height: 268px;"></div>

                                    <div class="text-center">
                                        <ul class="list-inline chart-detail-list mb-0 mt-2">
                                            <li class="list-inline-item">
                                                <h6 class="text-info"><i class="mdi mdi-circle-outline mr-1"></i>English</h6>
                                            </li>
                                            <li class="list-inline-item">
                                                <h6 class="text-success"><i class="mdi mdi-triangle-outline mr-1"></i>Italian</h6>
                                            </li>
                                            <li class="list-inline-item">
                                                <h6 class="text-muted"><i class="mdi mdi-square-outline mr-1"></i>French</h6>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div><!-- end col-->
                        </div>
                        <!-- end row -->


                        
                    </div> <!-- end container-fluid -->

                </div> <!-- end content -->

                

                <!-- Footer Start -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                2016 - 2019 &copy;  
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


    @endsection
