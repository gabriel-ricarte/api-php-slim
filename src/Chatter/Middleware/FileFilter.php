<?php

namespace Chatter\Middleware;

class FileFilter
{
    protected $allowedFiles = ['image/jpeg', 'image/png'];

    public function __invoke($request, $response, $next)
    {
        $files = $request->getUploadedFiles();
        $newfile = $files['file'];
        $newfileType = $newfile->getClientMediaType();
        if (!in_array($newfileType, $this->allowedFiles)) return $response->withStatus(415); 
        $response = $next($request, $response);
        return $response;
    }
}