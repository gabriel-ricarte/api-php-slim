<?php

namespace Chatter\Middleware;

use Aws\S3\S3Client;
use Exception;

class FileMove
{
    public function __invoke($request, $response, $next)
    {
        $s3 = new S3Client(['version' => 'latest', 'region' => 'us-east-1']);
        $files = $request->getUploadedFiles();
        $newFile = $files['file'];
        $uploadFileName = $newFile->getClientFilename();
        $png = "assets/images/" . substr($uploadFileName, 0, -4) . ".png";
        try {
            $s3->putObject([
                'Bucket' => 'my-bucket',
                'Key'    => 'my-object',
                'Body'   => fopen($png, 'w'),
                'ACL'    => 'public-read'
            ]);
        } catch (Exception $exc) {
            return $response->withStatus(400);
        }
        $response = $next($request, $response);
        return $response;
    }
}