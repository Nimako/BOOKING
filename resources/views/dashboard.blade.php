
   @extends('layouts.tempDashboard')

   @section('content')



    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="wrapper">
        <div class="container">

            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">

                        <h4 class="page-title">Dashboard</h4>
                    </div>
                </div>
            </div>


                <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card-box tilebox-one">
                        <i class="icon-chart float-right text-muted"></i>
                        <h6 class="text-muted text-uppercase m-b-20">Today</h6>
                        <h2 class="m-b-20">GH¢<span class="amount" data-plugin="counterup"><?= !empty($today)?$today:'0.00'; ?></span></h2>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card-box tilebox-one">
                        <i class="icon-chart float-right text-muted"></i>
                        <h6 class="text-muted text-uppercase m-b-20">THIS MONTH</h6>
                        <h2 class="m-b-20">GH¢<span data-plugin="counterup"><?= !empty($thismonth)?$thismonth:'0.00'; ?></span></h2>
                    </div>
                </div>


                <div class="col-md-6 col-xl-3">
                    <div class="card-box tilebox-one">
                        <i class="icon-chart float-right text-muted"></i>
                        <h6 class="text-muted text-uppercase m-b-20">LAST MONTH</h6>
                        <h2 class="m-b-20">GH¢<span data-plugin="counterup"><?= !empty($lastmonth)?$lastmonth:'0.00'; ?></span></h2>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card-box tilebox-one">
                        <i class="icon-chart float-right text-muted"></i>
                        <h6 class="text-muted text-uppercase m-b-20">THIS YEAR</h6>
                        <h2 class="m-b-20">GH¢<span data-plugin="counterup"><?= !empty($thismonth)?$thismonth:'0.00'; ?></span></h2>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-lg-6 col-xl-8">
                    <div class="card-box">

                        <h4 class="header-title m-t-0 m-b-20">Cash Payment (<?= date('d M Y'); ?>)</h4>

                                <table class="table table-bordered mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>TRANSACTION TYPE</th>
                                    <th>TOTAL AMOUNT(GH¢)</th>
                                    <th>COLLECTOR</th>
                                    <th></th>
                                </tr>
                                </thead>
                                 <?php if(!empty($list)): ?>
                                  <?php foreach($list as $row): ?>
                                    <tr>
                                        <td><?= date('d M Y',strtotime($row->date)); ?></td>
                                        <td><?= $row->trans_type; ?></td>
                                        <td><b><?= $row->total_payment; ?></b></td>
                                        <td class="text-muted"><?= ucwords(strtolower($row->collector_name)); ?></td>
                                        <th><a href="{{url('collector-statement/'.$row->collector_id)}}" class="btn btn-sm btn-success">Detail</a></th>
                                    </tr>
                                <?php endforeach; ?>
                                 <?php endif; ?>
                                <tbody>
                                </tbody>
                            </table>

                           {{ $list->links() }}

                           <br><br><br><hr><br><br><br>

                        <div class="text-center">
                            <ul class="list-inline chart-detail-list m-b-0">
                                <li class="list-inline-item">
                                    <h6 style="color: #3db9dc;"><i class="zmdi zmdi-circle-o m-r-5"></i>Series A</h6>
                                </li>
                                <li class="list-inline-item">
                                    <h6 style="color: #1bb99a;"><i class="zmdi zmdi-triangle-up m-r-5"></i>Series B</h6>
                                </li>
                                <li class="list-inline-item">
                                    <h6 style="color: #818a91;"><i class="zmdi zmdi-square-o m-r-5"></i>Series C</h6>
                                </li>
                            </ul>
                        </div>

                        <div id="morris-bar-stacked" style="height: 320px;"></div>


                    </div>
                </div><!-- end col-->

                <div class="col-lg-6 col-xl-4">
                    <div class="card-box">

                        <h4 class="header-title m-t-0 m-b-30">Trends Monthly</h4>

                        <div class="text-center m-b-20">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-sm btn-secondary">Today</button>
                                <button type="button" class="btn btn-sm btn-secondary">This Week</button>
                                <button type="button" class="btn btn-sm btn-secondary">Last Week</button>
                            </div>
                        </div>

                        <div id="morris-donut-example" style="height: 265px;"></div>

                        <div class="text-center">
                            <ul class="list-inline chart-detail-list mb-0 m-t-10">
                                <li class="list-inline-item">
                                    <h6 style="color: #3db9dc;"><i class="zmdi zmdi-circle-o m-r-5"></i>Market Toll</h6>
                                </li>
                                <li class="list-inline-item">
                                    <h6 style="color: #1bb99a;"><i class="zmdi zmdi-triangle-up m-r-5"></i>Lorry Pack</h6>
                                </li>
                                <li class="list-inline-item">
                                    <h6 style="color: #818a91;"><i class="zmdi zmdi-square-o m-r-5"></i>BOP</h6>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div><!-- end col-->


            </div>
            <!-- end row -->


        </div> <!-- container -->


        <!-- Footer -->
        <footer class="footer">
           Jilsoft Technologies.
        </footer>
        <!-- End Footer -->


        <!-- Right Sidebar -->
        <div class="side-bar right-bar">
            <div class="nicescroll">
                <ul class="nav nav-pills nav-justified">
                    <li class="nav-item">
                        <a href="#home-2" class="nav-link active" data-toggle="tab" aria-expanded="false">
                            Activity
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#messages-2" class="nav-link" data-toggle="tab" aria-expanded="true">
                            Settings
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="home-2">
                        <div class="timeline-2">
                            <div class="time-item">
                                <div class="item-info">
                                    <small class="text-muted">5 minutes ago</small>
                                    <p><strong><a href="#" class="text-info">John Doe</a></strong> Uploaded a photo <strong>"DSC000586.jpg"</strong></p>
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="item-info">
                                    <small class="text-muted">30 minutes ago</small>
                                    <p><a href="#" class="text-info">Lorem</a> commented your post.</p>
                                    <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="item-info">
                                    <small class="text-muted">59 minutes ago</small>
                                    <p><a href="#" class="text-info">Jessi</a> attended a meeting with<a href="#" class="text-success">John Doe</a>.</p>
                                    <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="item-info">
                                    <small class="text-muted">1 hour ago</small>
                                    <p><strong><a href="#" class="text-info">John Doe</a></strong>Uploaded 2 new photos</p>
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="item-info">
                                    <small class="text-muted">3 hours ago</small>
                                    <p><a href="#" class="text-info">Lorem</a> commented your post.</p>
                                    <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                                </div>
                            </div>

                            <div class="time-item">
                                <div class="item-info">
                                    <small class="text-muted">5 hours ago</small>
                                    <p><a href="#" class="text-info">Jessi</a> attended a meeting with<a href="#" class="text-success">John Doe</a>.</p>
                                    <p><em>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam laoreet tellus ut tincidunt euismod. "</em></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="messages-2">

                        <div class="row m-t-10">
                            <div class="col-8">
                                <h5 class="m-0">Notifications</h5>
                                <p class="text-muted m-b-0"><small>Do you need them?</small></p>
                            </div>
                            <div class="col-4 text-right">
                                <input type="checkbox" checked data-plugin="switchery" data-color="#1bb99a" data-size="small" />
                            </div>
                        </div>

                        <div class="row m-t-10">
                            <div class="col-8">
                                <h5 class="m-0">API Access</h5>
                                <p class="m-b-0 text-muted"><small>Enable/Disable access</small></p>
                            </div>
                            <div class="col-4 text-right">
                                <input type="checkbox" checked data-plugin="switchery" data-color="#1bb99a" data-size="small" />
                            </div>
                        </div>

                        <div class="row m-t-10">
                            <div class="col-8">
                                <h5 class="m-0">Auto Updates</h5>
                                <p class="m-b-0 text-muted"><small>Keep up to date</small></p>
                            </div>
                            <div class="col-4 text-right">
                                <input type="checkbox" checked data-plugin="switchery" data-color="#1bb99a" data-size="small" />
                            </div>
                        </div>

                        <div class="row m-t-10">
                            <div class="col-8">
                                <h5 class="m-0">Online Status</h5>
                                <p class="m-b-0 text-muted"><small>Show your status to all</small></p>
                            </div>
                            <div class="col-4 text-right">
                                <input type="checkbox" checked data-plugin="switchery" data-color="#1bb99a" data-size="small" />
                            </div>
                        </div>

                    </div>
                </div>
            </div> <!-- end nicescroll -->
        </div>
        <!-- /Right-bar -->

    </div> <!-- End wrapper -->

    @endsection

