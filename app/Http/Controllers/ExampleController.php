<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //key = sha256(ExternalID+sha256(apikey))


    public function getList(Request $request,  $id)
    {

        $externalID = $id;
        $key = $request->key;

        $keyR = hash("sha256", env("KEY", ""));


        $keyCheck = hash("sha256",  $externalID . $key);

        $keyCheckR = hash("sha256",  $externalID . hash("sha256", $externalID. $keyR));

        $hashedKeyR = Hash::make($keyCheckR, ['rounds' => 12,]);

        if (Hash::check($keyCheck, $hashedKeyR)) {

            $photos = (new Photo())->getListByEID($externalID);
           

            $links = $this->getLinks($photos);
            $data["links"] = $links;
            return response()->json($data, 200);
        }

        return response()->json("not found", 404);
    }

    public function getLinks($photos)
    {

        $data = [];
        foreach ($photos as $photo) {
            $data[] = env("IMAGELINK", "") . $photo->uid . "/" . hash("sha256",  $photo->uid . env("SALT", "tfidf"));
        }
        return $data;
    }


    public function getImage($uuid, $hash)
    {

        if ($this->checkUUID($uuid)) {
            if ($hash == hash("sha256",  $uuid . env("SALT", "tfidf"))) {
                $photo = (new Photo())->getByUID($uuid);

                $type = 'image/png';
                $headers = ['Content-Type' => $type];
                $path = './public/FILES/'.$photo->file_path;

                try {
                    $response = new BinaryFileResponse($path, 200, $headers);
                    return $response;
                } catch (\Throwable $th) {
                    //throw $th;
                    return $this->textToImage("Image not found");
                }
                
            }
        }
        //return response()->json("content not found", 404);
        //return $this->textToImage("Image not found");

        new BinaryFileResponse($this->textToImage("Image not found"), 404, ['Content-Type'=>'image/png']);
    }

    /* 
    @####
    @####
    @####
    */

    public function textToImage($text){
        // Создание изображения 100*30
        $im = imagecreate(500, 500);

        $font=5;
        $xMin=1;
        $xMax=500;
        $y=250;


        // Белый фон, синий текст
        $bg = imagecolorallocate($im, 255, 255, 255);
        $textcolor = imagecolorallocate($im, 244, 67, 54);

        
        //imagestring($im, 5, 70, 230, $text, $textcolor);

        $textWidth = imagefontwidth( $font ) * strlen( $text );
        $xLoc = ( $xMax - $xMin - $textWidth ) / 2 + $xMin + $font;
        imagestring( $im, $font, $xLoc, $y, $text,  $textcolor );

        // Вывод изображения
        //header('Content-type: image/png');

        //imagepng($im);
        //imagedestroy($im);
        return $im;
    }



    public function checkUUID($value)
    {
        if (!is_string($value) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $value) !== 1)) {
            return false;
        }
        return true;
    }
}
