<?php

namespace Chatter\Middleware;

class ImageRemoveExif
{
    public function __invoke($request, $response, $next)
    {
        $files = $request->getUploadedFiles();
        $newFile = $files['file'];
        $uploadedFilename = $newFile->getClientFilename();
        $newFileType = $newFile->getClientMediaType();
        $newFile->moveTo("assets/images/raw/$uploadedFilename");
        $pngFile = "assets/images/" . substr($uploadedFilename, 0, -4) . ".png";
        if ("image/jpeg" == $newFileType) {
            $_img = imagecreatefromjpeg("assets/images/raw" . $uploadedFilename);
            imagepng($_img, $pngFile);
        }
        $request->withAttribute('png_filename', $pngFile);
        $response = $next($request, $response);
        return $response;
    }
}