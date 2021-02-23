@extends('layouts.tempDashboard')

@section('content')


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
                            </ol>
                        </div>
                        <h4 class="page-title">
                            <span><?= !empty($info)?$info->name:null; ?></span>
                             <span class="text-success ml-5">Status: <?= !empty($info)?$info->text_status:null; ?></span>
                             <span class="ml-5">Type: <?= @$info->property_type_text; ?></span>
                             <span class="ml-5">Owner: <?= @$info->created_by; ?></span>
                        </h4>

                    </div>
                </div>
            </div>     
            <!-- end page title --> 

            <div class="row">
                <div class="col-12">
                    <div class="card-box">

                        <section class="row">
                            <div class="col-md-3">

                                <label>Property Type</label>
                                <input type="text" class="form-control" value="<?= @$info->property_type_text; ?>" readonly=""><br>

                                <label>Name</label>
                                <input type="text" class="form-control" value="<?= @$info->name; ?>" readonly=""><br>

                                <label>Street Address 1</label>
                                <input type="text" class="form-control" value="<?= @$info->street_address_1; ?>" readonly=""><br>

                                <label>Street Address 2</label>
                                <input type="text" class="form-control" value="<?= @$info->street_address_2; ?>" readonly=""><br>
                                
                                <label>Postal Code</label>
                                <input type="text" class="form-control" value="<?= @$info->postal_code; ?>" readonly=""><br>
                                
                                <label>Country</label>
                                <input type="text" class="form-control" value="<?= @$info->country_id; ?>" readonly=""><br>

                                <label>City</label>
                                <input type="text" class="form-control" value="<?= @$info->city; ?>" readonly=""><br>

                                <label>Area</label>
                                <input type="text" class="form-control" value="<?= @$info->area; ?>" readonly=""><br>

                                <label>Email</label>
                                <input type="text" class="form-control" value="<?= @$info->email; ?>" readonly=""><br>

                                <label>Website</label>
                                <input type="text" class="form-control" value="<?= @$info->website; ?>" readonly=""><br>
                             
                            </div>

                            <div class="col-md-4">

                             
                                <label>Primary Telephone</label>
                                <input type="text" class="form-control" value="<?= @$info->primary_telephone; ?>" readonly=""><br>

                                <label>Secondary Telephone</label>
                                <input type="text" class="form-control" value="<?= @$info->secondary_telephone; ?>" readonly=""><br>

                                <label>Geolocation</label>
                                <input type="text" class="form-control" value="<?= @$info->geolocation; ?>" readonly=""><br>

                                <label>Text Location</label>
                                <input type="text" class="form-control" value="<?= @$info->text_location; ?>" readonly=""><br>

                                <label>Serve breakfast ?</label>
                                <input type="text" class="form-control" value="<?= @$info->serve_breakfast; ?>" readonly=""><br>

                                <label>Languages spoken</label>
                                <input type="text" class="form-control" value="<?= @$info->languages_spoken; ?>" readonly=""><br>


                                <label>Facilities</label>
                                <?php if(!empty($info->facilities)): ?>
                                <?php foreach($info->facilities as $item): ?>
                                    <div class="custom-control custom-checkbox">
                                        <input   type="checkbox" checked class="custom-control-input" id="<?= @$item->name ?>">
                                        <label class="custom-control-label"  for="<?= @$item->name ?>"><?= @$item->name; ?></label>
                                    </div>
                                <?php endforeach; ?>
                                <?php endif; ?><br>

                                <label>Policies</label>
                                <?php if(!empty($info->policies)): ?>
                                <?php foreach($info->policies as $item): ?>
                                <div class="custom-control custom-checkbox">
                                    <input   type="checkbox" checked class="custom-control-input" id="<?= @$item; ?>">
                                    <label class="custom-control-label"  for="<?= @$item; ?>"><?= @$item; ?></label>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <br>

                                <label>Description</label>
                                <textarea class="form-control"><?= @$info->about_us; ?></textarea><br>

                                <label>Summary Text</label>
                                <textarea class="form-control"><?= @$info->summary_text; ?></textarea>

                            </div>

                            <div class="col-md-5">
                                
                                <h5>HOTEL DETAILS</h5>

                                <?php if(!empty($info->hoteldetails)): $x=1; ?>

                                <?php foreach($info->hoteldetails as $row): ?>

                                <div class="accordion" id="accordionExample">
                                    <div class="card">
                                      <div class="card-header" id="heading<?= $x; ?>">
                                        <h2 class="mb-0">
                                          <button class="btn btn-link btn-block text-left h3" type="button" data-toggle="collapse" data-target="#collapse<?= $x; ?>" aria-expanded="true" aria-controls="collapse<?= $x; ?>">
                                            <?= $row->room_name; ?>
                                          </button>
                                        </h2>
                                      </div>
                                  
                                      <div id="collapse<?= $x; ?>" class="collapse" aria-labelledby="heading<?= $x; ?>" data-parent="#accordionExample">
                                        <div class="card-body">

                                            <label>Room Name</label>
                                            <input type="text" class="form-control" value="<?= @$row->room_name; ?>" readonly=""><br>

                                            <label>Custom Room Name</label>
                                            <input type="text" class="form-control" value="<?= @$row->custom_room_name; ?>" readonly=""><br>

                                            <label>Smokking Policy</label>
                                            <input type="text" class="form-control" value="<?= @$row->smoking_policy; ?>" readonly=""><br>

                                            <label>Total Guest Capacity</label>
                                            <input type="text" class="form-control" value="<?= @$row->total_guest_capacity; ?>" readonly=""><br>

                                            <label>Dimension</label>
                                            <input type="text" class="form-control" value="<?= @$row->dimension; ?>" readonly=""><br>


                                            <p>
                                                <?php if(!empty($row->bed_type_options)): ?>
                                                    <label>Bed Types Options</label>
                                                    <table class="table table-bordered">
                                                        <thead style="background-colo:gray">
                                                            <tr>
                                                                <th>Bed Type</th>
                                                                <th>Qty.</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($row->bed_type_options as $item): ?>
                                                            <tr>
                                                                <td><?= $item->bed_type; ?></td>
                                                                <td><?= $item->bed_qty; ?></td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                <?php endif; ?>
                                                </p>


                                            <p>
                                            <?php if(!empty($row->price_list)): ?>
                                                <label>Price List</label>
                                                <table class="table table-bordered">
                                                    <thead style="background-colo:gray">
                                                        <tr>
                                                            <th>No. of Guest</th>
                                                            <th>Amount</th>
                                                            <th>Discount (%)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($row->price_list as $item): ?>
                                                        <tr>
                                                            <td><?= $item->guest_occupancy; ?></td>
                                                            <td><?= $item->amount; ?></td>
                                                            <td><?= $item->discount; ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
                                            </p>

                                            <hr>

                                        </div>
                                      </div>
                                    </div>                                 
                                  </div>
                                <?php $x++; ?>
                                  <?php endforeach; ?>
                                  <?php endif; $x++; ?>

                                  <h5>OTHER HOTEL DETAILS</h5>

                                  
                                <?php if(!empty($info->other_hotel_details)): ?>

                                <?php foreach($info->other_hotel_details as $row): ?>

                                  <label>Listed On</label>
                                  <input type="text" class="form-control" value="<?= @$info->listed_on; ?>" readonly=""><br>

                                <?php endforeach; ?>
                                <?php endif; ?>




                            </div>

                            <div class="col-md-12">
                                <p>Change Status</p>
                                <form action="{{url('change-status')}}" method="POST" id="regForm">
                                    {{ csrf_field() }}

                                 <select class="form-control" name="status" required>
                                     <option value=""></option>
                                     <option value="1">PENDING</option>
                                     <option value="3">APPROVED</option>
                                     <option value="2">CANCEL</option>
                                 </select><br>

                                <input type="hidden" name="id" class="form-control" value="<?= @$info->uuid; ?>"><br>
                               
                                <button type="submit" class="btn btn-success btn-sm">Submit</button>

                                </form>

                            </div>



                        </section>

                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 


 @endsection

