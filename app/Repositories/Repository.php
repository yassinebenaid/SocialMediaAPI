<?php

namespace App\Repositories;

use App\Exceptions\GeneralException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

abstract class Repository
{
    // protected string $imagesPath = public_path("resource/images");

    // abstract public function create(Request $request);

    protected function saveImage($image, $section)
    {
        if (is_null($image)) return null;

        if (!$image->isValid()) throw new  GeneralException("somthing went wrong, please check your network connection and try again !", 422);

        $filename = uniqid("static.image.") . md5(now()) .  $image->getClientOriginalName();

        $path =  "/resource/images/$section/" . $filename;

        $image->move(public_path("resource/images/$section/"), $filename);

        return $path;
    }

    // updat specific image
    protected function updateImage($old, $new, $section)
    {
        if (is_null($new) || !$new->isValid()) throw new  GeneralException("somthing went wrong, please check your network connection and try again !", 422);

        if ($old !== "default") {
            $imagePath = public_path() . $old;

            if (!is_file($imagePath)) throw new  GeneralException("somthing went wrong, please check your network connection and try again !", 422);


            // avoide deleting the default image
            if (!str_ends_with($imagePath, "default.png")) {
                File::delete($imagePath);
            }
        }

        $filename = uniqid("static.image.") . md5(now()) .  $new->getClientOriginalName();

        $new->move(public_path("resource/images/$section/"), $filename);

        return "/resource/images/$section/" . $filename;
    }
}
