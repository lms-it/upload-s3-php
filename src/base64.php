<?php

use \Aws\S3\Exception\S3Exception;
require '../app/start.php';

$config = require('../app/config.php');

//Get base64 image data
$imageBase64 = require('../app/img.php');


if ($imageToSave = validImage($imageBase64)) {
    $tmpFullPath = $config['pathToTmp'].$imageToSave;
    
    //Save into S3
    try{
        $s3->putObject([
            'Bucket' => $config['s3']['bucket'],
            'Key' => "uploads/{$imageToSave}",
            'Body' => fopen($tmpFullPath, 'rb'),
            //Access control level
            'ACL' => 'public-read'
        ]);

        unlink($tmpFullPath);

        //http(s)://<bucket>.s3.amazonaws.com/<object>
        //http(s)://s3.amazonaws.com/<bucket>/<object>
        printf($s3->getObjectUrl($config['s3']['bucket'], "uploads/{$imageToSave}"));
    }
    catch(S3Exception $error){
        throw new Exception("There was an error uploading that file.");
    }
}

function validImage($base64) {
    global $config;
    $info = array();
    
    //Get image extenstion e.g. png, jpeg, jpg
    $extension =  end(explode('/',substr($base64, 5, strpos($base64, ';')-5)));
    
    if(!in_array($extension, $config['supportedExtensions'])){
        return false;
    }

    $imageData = end(explode(",", $base64));
    $img = imagecreatefromstring(base64_decode($imageData));

    if (!$img) {
        throw new InvalidArgumentException('File is not valid.');
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
            throw new InvalidArgumentException('File "'.$tmpFile.'" is not valid jpg, png or gif image.');
    }
    
    if(file_exists($tmpFullPath)){
        $info = getimagesize($tmpFullPath);
    }

    if (sizeof($info)>2 && $info[0] > 0 && $info[1] > 0 && $info['mime']) {
        return $tmpFile;
    }

    return false;
}
?>