<?php



namespace Apps\Controllers;

ini_set('display_errors',1);
error_reporting(E_ALL);

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;



class AmazonS3{

	
	function uploaddp($key,$TempFilePath){

		$bucketName = 'coloborativelearning';
		$IAM_KEY = 'AKIAIQ5ZXPZHSNYVTGPA';
		$IAM_SECRET = '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';



		try {

		$s3 = S3Client::factory(
			array(
				'credentials' => array(
					'key' => $IAM_KEY,
					'secret' => $IAM_SECRET
				),
				'version' => 'latest',
				'region'  => 'us-east-2'
			)
		);
		} catch (Exception $e) {

			die("Error: " . $e->getMessage());
		}


		$keyName = 'profileDP/' . basename($TempFilePath);
		$pathInS3 = 'https://s3.us-east-2.amazonaws.com/' . $bucketName . '/' . $keyName;

		try {
		// Uploaded:
			$file = $TempFilePath;
			$s3->putObject(
			array(
				'Bucket'=>$bucketName,
				'Key' =>  $keyName,
				'SourceFile' => $file,
				'StorageClass' => 'REDUCED_REDUNDANCY'
				)
			);

			#unlink($file);


		} catch (S3Exception $e) {

			die('Error:' . $e->getMessage());

		} catch (Exception $e) {

			die('Error:' . $e->getMessage());
		}
	echo 'Done';

	}

	function getdp($TempFilePath){

		$bucketName = 'coloborativelearning';
		$IAM_KEY = 'AKIAIQ5ZXPZHSNYVTGPA';
		$IAM_SECRET = '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';

		try {

			$s3 = S3Client::factory(
			array(
				'credentials' => array(
					'key' => $IAM_KEY,
					'secret' => $IAM_SECRET
				),
				'version' => 'latest',
				'region'  => 'us-east-2'
			)
		);
		} catch (Exception $e) {

			die("Error: " . $e->getMessage());
		}

		$keyName = 'profileDP/' . $TempFilePath;

		$result = $s3->getObject(array(
        'Bucket' => $bucketName,
        'Key'    => $keyName
    	));
    	#header("Content-Type: {$result['ContentType']}");

    	header("Content-Type: image/jpeg");

    	$base64 = base64_encode($result['Body']) ;

    	echo "<img src=data:image/gif;base64,$base64 alt='Base64 encoded image' width='150' height='150'/>";
	}	
}