<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class FileFormatterService
{
    public static function formatFileName(string $fileName): string
    {
        $slugger = new SluggerInterface();
        $prefixedFileName = date('Y/m/d H:i:s') . '_' . $fileName;
        $cleanedFileName = $slugger->slug($prefixedFileName);

        return $cleanedFileName;
    }
}
