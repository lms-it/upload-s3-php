<?php

//http://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.Common.Enum.Region.html

return [
    's3' => [
        'key' => 'YOUR_KEY',
        'secret' => 'YOUR_SECRET_KEY',
        'bucket' => 'YOUR_BUCKET_NAME',
        'version' => 'latest',
        'region'  => 'YOUR_REGION'
    ],
    'pathToTmp' => dirname(__DIR__).'/files/',
    'supportedExtensions' => [
        'png', 'jpg', 'jpeg', 'gif'
    ]
];