<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Require the Composer autoloader.
require APPPATH . 'third_party/aws-sdk/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class Aws_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->key = $this->storage_settings->aws_key;
        $this->secret = $this->storage_settings->aws_secret;
        $this->bucket = $this->storage_settings->aws_bucket;
        $this->region = $this->storage_settings->aws_region;

        $credentials = new Aws\Credentials\Credentials($this->key, $this->secret);
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => $credentials
        ]);
    }

    // Custom Function Here

    //put object
    public function put_object($key, $temp_path)
    {
        if (file_exists($temp_path)) {
            try {
                $file = fopen($temp_path, 'r');
                $this->s3->putObject([
                    'Bucket' => $this->bucket,
                    'Key' => $key,
                    'Body' => $file,
                    'ACL' => 'public-read'
                ]);
                fclose($file);
                return true;
            } catch (S3Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

    //delete object
    public function delete_object($key)
    {
        if (!empty($key)) {
            try {
                $this->s3->deleteObject([
                    'Bucket' => $this->bucket,
                    'Key' => $key
                ]);
            } catch (S3Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

}
