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

                                <label>Email</label>
                                <input type="text" class="form-control" value="<?= @$info->email; ?>" readonly=""><br>

                                <label>Website</label>
                                <input type="text" class="form-control" value="<?= @$info->website; ?>" readonly=""><br>

                                <label>Primary Telephone</label>
                                <input type="text" class="form-control" value="<?= @$info->primary_telephone; ?>" readonly=""><br>

                                <label>Secondary Telephone</label>
                                <input type="text" class="form-control" value="<?= @$info->secondary_telephone; ?>" readonly=""><br>

                             
                            </div>

                            <div class="col-md-4">

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
                                
                                <h5>APARTMENT UNITS</h5>

                                <?php if(!empty($info->details)): $x=1; ?>

                                <?php foreach($info->details as $row): ?>

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

                                            <label>Total Guest Capacity</label>
                                            <input type="text" class="form-control" value="<?= @$row->total_guest_capacity; ?>" readonly=""><br>

                                            <label>Number of rooms</label>
                                            <input type="text" class="form-control" value="<?= @$row->num_of_rooms; ?>" readonly=""><br>

                                            <label>Total bathroom</label>
                                            <input type="text" class="form-control" value="<?= @$row->total_bathrooms; ?>" readonly=""><br>
                                            
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

                                            <label>Amenities</label>
                                            <?php if(!empty($row->amenities)): ?>
                                                <?php foreach($row->amenities as $item): ?>
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" checked class="custom-control-input" id="<?= @$item->name; ?>">
                                                        <label class="custom-control-label"  for="<?= @$item->name; ?>"><?= @$item->name; ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            
                                            <hr>
                                            <p><h4><b>Room Details</b></h4></p>

                                            <?php if(!empty($row->room_details)): ?>
                                                <?php foreach($row->room_details as $item): ?>

                                                <label>Room Name</label>
                                                <input type="text" class="form-control" value="<?= @$row->room_name; ?>" readonly=""><br>
                                                 
                                                <?php if(!empty($item->bed_types)): ?>
                                                 <?php foreach($item->bed_types as $row): ?>

                                                  <span><b>Bed Type:</b> <?= $row->name; ?></span><br>
                                                  <span><b>Expected sleeps:</b> <?= $row->expected_sleeps; ?></span><br>
                                                   <hr>
                                                 <?php endforeach; ?>
                                                 <?php endif; ?>

                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            
                                            <p>
                                             <h6>APARTMENT IMAGES</h6>
                                            <?php if(!empty($row->image_pathss)): ?>
                                                <?php foreach($row->image_pathss as $image): ?>

                                                    <img src="<?= @$image; ?>" width="80"><br>

                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            </p>
                                            



                                            
                                          
                                        </div>
                                      </div>
                                    </div>                                 
                                  </div>
                                <?php $x++; ?>
                                  <?php endforeach; ?>
                                  <?php endif; $x++; ?>
                            </div>

                            <div class="col-md-12">

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
                            </div>



                        </section>

                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 


 @endsection

