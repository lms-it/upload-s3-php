<?php

require '../vendor/autoload.php';

use \Aws\S3\S3Client;

$config = require('config.php');

/* It is recommended to use AWS credentials file
* http://docs.aws.amazon.com/aws-sdk-php/v2/guide/credentials.html#credential-profiles
*/

$s3 = new S3Client([
    'version' => $config['s3']['version'],
    'region' => $config['s3']['region'],
    'credentials' => [
        'key'    => $config['s3']['key'],
        'secret' => $config['s3']['secret'],
    ],
]);