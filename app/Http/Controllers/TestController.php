<?php

namespace App\Http\Controllers;

use Faker\Provider\Uuid;
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


}
