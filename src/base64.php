<?php

use \Aws\S3\Exception\S3Exception;
require '../app/start.php';


$config = require('../app/config.php');

//Get base64 image data
$imageBase64 = require('../app/img.php');


if ($imageToSave = validImage($config, $imageBase64)) {
    $tmpFullPath = $config['pathToTmp'].$imageToSave;
    //Save into S3
    try{
        $s3->putObject([
            'Bucket' => $config['s3']['bucket'],
            'Key' => "uploads/lms/{$imageToSave}",
            'Body' => fopen($tmpFullPath, 'rb'),
            //Access control level
            'ACL' => 'public-read'
        ]);

        unlink($tmpFullPath);

        echo "Image uploaded to S3!".PHP_EOL;
        echo "Path to the uploaded file : ".$config['AWS'].'/'.$config['s3']['bucket']."/uploads/lms/{$imageToSave}";
    }
    catch(S3Exception $error){
        //var_dump($error);
        die("There was an error uploading that file.");
    }

} else {
    print 'Not an image!';
}

//Get image extenstion e.g. png, jpeg, jpg
function getB64Ext($base64) {
    // $str should start with 'data:' (= 5 characters long!)
    return end(explode('/',substr($base64, 5, strpos($base64, ';')-5)));
}

function validImage($config, $base64) {
    
    $extension =  getB64Ext($base64);
    
    if(!in_array($extension, $config['supportedExtensions'])){
        return false;
    }

    $imageData = end(explode(",", $base64));
    $img = imagecreatefromstring(base64_decode($imageData));

    if (!$img) {
        return false;
    }

    //Temp details
    $tmpFile = md5(uniqid()).".$extension";
    $tmpFullPath = $config['pathToTmp'].$tmpFile;

    switch (strtolower($extension)){
        case 'png'  :
                imagepng($img, $tmpFullPath);
            break;
        case 'jpeg' :
        case 'jpg'  :
                imagejpeg($img, $tmpFullPath);
            break;
        case 'gif'  :
                imagegif($img, $tmpFullPath);
            break;
        default     :
                imagepng($img, $tmpFullPath);
    }

    $info = getimagesize($tmpFullPath);

    if ($info[0] > 0 && $info[1] > 0 && $info['mime']) {
        return $tmpFile;
    }

    return false;
}
?>