<?php
declare(strict_types=1);

namespace WEBFONTS\Webfonts\Utilities;


use ZipArchive;

class ZipUtilities
{

    /**
     * ZipUtilities constructor.
     */
    private function __construct()
    {
    }

    public static function unzip($zipFile, $targetPath): bool
    {
        $zip = new ZipArchive();
        $res = $zip->open($zipFile);
        if ($res === TRUE) {
            $zip->extractTo($targetPath);
            $zip->close();
            if (is_file($zipFile)) {
                unlink($zipFile);
            }
            return true;
        }
        return false;
    }
}
