<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Base64Helper
{
    public static function convertImageToBase64String($path)
    {
        if(!$path) return null;
        $storagePath = Storage::path($path);
        $mimeType = Storage::mimeType($path);
        // Log::info(json_encode([
        //     'storagePath' => $storagePath,
        //     'mimeType' => $mimeType,
        // ]));
        try {
            $contents = file_get_contents($storagePath);
            return "data:" . $mimeType . ";base64," . base64_encode($contents);
        } catch (\Throwable $th) {
            Log::info(json_encode([
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                // 'trace' => $th->getTrace(),
            ]));
            return null;
        }


    }
}
