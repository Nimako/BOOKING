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
                        <h4 class="page-title">Facilities </h4>
                    </div>
                </div>
            </div>     
            <!-- end page title --> 


            <div class="row">
                <div class="col-12">
                    <div class="card-box">

                        <section class="row">
                           
                            <div class="col-md-4">

                                <form action="{{url('lookupSetup/save-facilities')}}" method="POST" id="regForm">
                                    {{ csrf_field() }}
                                
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?= @$info->name; ?>"  required=""><br>

                                <label>Icon Class</label>
                                <input type="text" name="icon_class" class="form-control" value="<?= @$info->icon_class; ?>"   ><br>


                                <button type="submit" class="btn btn-success btn-sm">Submit</button>
                            </form>

                            </div>

                            <div class="col-md-8">

                                @if ($message = Session::get('success'))
                                <div class="alert alert-success alert-block">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                        <strong>{{ $message }}</strong>
                                </div>
                                @endif
    
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                 
                                <table id="datatable" class="table table-bordered dt-responsive">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Icon Class</th>
                                        {{-- <th></th> --}}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if(!empty($list)): ?>
                                    <?php foreach($list as $item): ?>
                                    <tr>
                                        <td></td>
                                        <td><?= $item->name; ?></td>
                                        <td><?= $item->icon_class; ?></td>
                                        {{-- <td><a href="#" class="btn btn-success btn-sm">Edit</a></td> --}}
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>

                            </div>



                        </section>

                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

 


 @endsection

