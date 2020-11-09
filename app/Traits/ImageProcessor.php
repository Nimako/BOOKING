<?php


namespace App\Traits;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageProcessor
{
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

   public static function UploadImage($fileResource, $propertyUuId, $imageCountNum = 1)
   {
      // variable declaration
      $currentYear = date('Y');
      $currentMonth = date('F');
      $storagePath = "properties/{$currentYear}/{$currentMonth}/{$propertyUuId}/";

      if(!empty($fileResource))
      {
         $quality = self::GenerateQuality($fileResource->getSize());
         $info = getimagesize($fileResource);

         if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg' )
            $image = imagecreatefromjpeg($fileResource);
         elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($fileResource);
         elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($fileResource);
         elseif ($info['mime'] == 'image/webp')
            $image = imagecreatefromwebp($fileResource);

         $NewFileName  = str_replace(' ', '-',microtime()).'-'.$imageCountNum.".webp"; //rename
         $imagePath = $storagePath.$NewFileName;
         Storage::disk('public')->put($imagePath, File::get($fileResource));

         imagewebp($image,storage_path('app/public/'.$imagePath),$quality);
         imagedestroy($image);

         return $imagePath;
      }
   }

   public static function __UploadImage($fileResource, $propertyUuId, $imageCountNum = 1)
   {
      // variable declaration
      $currentYear = date('Y');
      $currentMonth = date('F');
      $storagePath = "properties/{$currentYear}/{$currentMonth}/{$propertyUuId}/";

      $UploadFile =  $fileResource->file('image');
      if($fileResource->hasfile('image'))
      {
         $quality = self::GenerateQuality($UploadFile->getSize());
         $info = getimagesize($UploadFile);

         if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg' )
            $image = imagecreatefromjpeg($UploadFile);
         elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($UploadFile);
         elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($UploadFile);
         elseif ($info['mime'] == 'image/webp')
            $image = imagecreatefromwebp($UploadFile);

         $NewFileName  = microtime().'-'.$imageCountNum.".webp"; //rename
         $imagePath = $storagePath.$NewFileName;
         Storage::disk('public')->put($imagePath, File::get($UploadFile));


         imagewebp($image,'storage/'.$imagePath,$quality);
         imagedestroy($image);

         return 'storage/'.$imagePath;
      }
   }
}
