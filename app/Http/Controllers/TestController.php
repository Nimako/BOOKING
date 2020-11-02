<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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


   public static function GenerateQuality($imageSize){

    if($imageSize >= 5000000){

      return 40;

    }elseif($imageSize <= 4000000 && $imageSize >= 3000000){
      
      return 50;

    }elseif($imageSize <= 3000000 && $imageSize >= 2000000){

      return 60;

    }elseif($imageSize <= 2000000){

      return 70;

    }else{
      return 80;
    }

  }


   public function CompressImage(request $request){

    $temp_name =  $request->file('image');

    // $temp_name->getClientOriginalName();
    // $temp_name->getRealPath();
    // $temp_name->getClientOriginalExtension();
    // $temp_name->getSize();
    
    if($request->hasfile('image'))
    {

      $quality = self::GenerateQuality($temp_name->getSize());

      $info = getimagesize($temp_name);
      if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg' ) $image = imagecreatefromjpeg($temp_name);
      elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($temp_name);
      elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($temp_name);
      elseif ($info['mime'] == 'image/webp') $image = imagecreatefromwebp($temp_name);

      $NewFileName  = sha1(time()).".webp";   

      $path = public_path().'/properties/'.date('Y-m').'/';
      File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
      $NewPath = $path.$NewFileName;

      imagewebp($image,$NewPath,$quality);
      imagedestroy($image); 

      echo $quality;
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


}
