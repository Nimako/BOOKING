<?php

namespace App\Http\Controllers;

use App\Models\Models\TecProducts;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{

    public function DeleteUser($email){

      //$query =  DB::table('useraccount')->delete();

      $query = DB::table('users')->where('Email', $email)->delete();


      if($query){
          return "all user accounts deleted";
      }else{
        return "Try again";

      }

    }


   public function _CompressImage(request $request, $imageCountNum = 1)
   {
       $UploadFile =  $request->file('image'); //temp file

       //$UploadFile->getClientOriginalName();
       //$UploadFile->getRealPath();      //temp file
       //$UploadFile->getClientOriginalExtension();
       //$UploadFile->getSize();
       //$UploadFile->getClientMimeType();

       if($request->hasfile('image'))
       {

         $quality = self::GenerateQuality($UploadFile->getSize());

         $info = getimagesize($UploadFile);
         if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg' ) $image = imagecreatefromjpeg($UploadFile);
         elseif ($info['mime'] == 'image/gif')  $image = imagecreatefromgif($UploadFile);
         elseif ($info['mime'] == 'image/png')  $image = imagecreatefrompng($UploadFile);
         elseif ($info['mime'] == 'image/webp') $image = imagecreatefromwebp($UploadFile);

         $propertyUUID  = rand(); //Property UUID which will be part of the request
         $imageCountNum = rand(); //image number which can be added to the request

         $NewFileName  = base64_encode($propertyUUID.$imageCountNum).".webp"; //rename

         $path = public_path().'/properties/'.date('Y').'/'.date('m').'/';
         File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
         $NewPath = $path.$NewFileName;

         imagewebp($image,$NewPath,$quality);
         imagedestroy($image);

         return $NewPath;

      }
   }

   public function resizeImagePost(Request $request)
   {


       $image = $request->file('image');
       $input['imagename'] = time().'.'.$image->getClientOriginalExtension();

       $destinationPath = public_path('/thumbnail');
       $img = Image::make($image->getRealPath());
       $img->resize(100, 100, function ($constraint) {
           $constraint->aspectRatio();
       })->save($destinationPath.'/'.$input['imagename']);

       $destinationPath = public_path('/images');
       $image->move($destinationPath, $input['imagename']);

       $this->postImage->add($input);

       return back()
           ->with('success','Image Upload successful')
           ->with('imageName',$input['imagename']);
   }

   public function fixingProducts()
   {
      $a = "select * from demo.product_details a
        left join demo.product_desc b on b.desc_id = a.desc_id";
      $b = DB::select($a);

      $counter = 0;

      foreach ($b as $key => $item){

         $real_prod_id = explode('#', $item->product_id);
         $bb = "select * from demo.product_codes a where a.prod_id = ".$real_prod_id[1];
         $qq = DB::select($bb);

         if(!empty($qq)) {
            $obj[$key] = (array)$item;
            $obj[$key]['product_name'] = $qq[0]->prod_name;

            // category
            $bb = "select * from dummy.tec_categories a where a.name = '{$item->desc}'";
            $qq2 = DB::select($bb);
            if(!empty($qq2[0])) {
               $category_id = $qq2[0]->id;
            }
            else {
               $qry = "insert into dummy.tec_categories(name,image) values('{$item->desc}','no_image.png')";
               $qR = DB::insert($qry);
               $category_id = DB::table('dummy.tec_categories')->max('id');
            }

            $productArr = [
               'code' => "PRD#".$counter,
               'name' => $qq[0]->prod_name,
               'category_id' => $category_id,
               'price' => $item->unit_price,
               'image' => $item->img_path,
               //'tax' =>,
               'cost' => $item->cost_price,
               'tax_method' => 1,
               'quantity' => $item->avail_qty,
               'barcode_symbology' => "code128",
               'type' => "standard",
               //'details' =>,
               //'alert_quantity' =>,
            ];

            $allKeys = array_keys((array)$productArr);
            $allvalues = array_values((array)$productArr);

            $columns = implode(',', $allKeys);
            $data = '"'.implode('","', $allvalues).'"';

            unset($allKeys, $allvalues);

            $query = "insert into dummy.tec_products({$columns}) values({$data})";
            DB::insert($query);
         }

         $counter++;
      }

      return $productArr;


   }

}
