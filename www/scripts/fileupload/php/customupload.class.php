<?php

defined('SYSPATH') or die('No direct script access.');

require_once 'upload.class.php';

class CustomUploadHandler extends UploadHandler
{
    public static $allowedMimeTypes = [
        'text/plain' => 'txt',
        'text/csv' => 'csv',
        'text/richtext' => 'rtx',

        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/png' => 'png',
        'image/bmp' => 'bmp',
        'image/x-windows-bmp' => 'bmp',
        'image/tiff' => 'tiff',
        'image/svg+xml' => 'svg',
        'image/x-cmu-raster' => 'ras',
        'image/cgm' => 'cgm',
        'image/x-cmx' => 'cmx',
        'image/x-portable-pixmap' => 'ppm',

        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.ms-excel' => 'xls',
        "application/zip" => 'docx',
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => 'docx',
        "application/rtf" => 'rtf',
        'application/x-shockwave-flash' => 'swf',
        'application/mp4' => 'mp4',
        'application/vnd.ms-powerpoint' => 'ppt',

        'audio/x-aac' => 'aac',
        'audio/x-aiff' => 'aif',
        'audio/ogg' => 'oga',
        'audio/mp4' => 'mp4a',
        'audio/mpeg' => 'mpga',

        'video/x-msvideo' => 'avi',
        'video/quicktime' => 'qt',
        'video/ogg' => 'ogv',
        'video/mp4' => 'mp4',
        'video/mpeg' => 'mpeg',
        'video/jpeg' => 'jpgv',
        'video/3gpp' => '3gp',
        'video/3gpp2' => '3g2',
    ];

    protected function validate($uploaded_file, $file, $error, $index)
    {
        if (!parent::validate($uploaded_file, $file, $error, $index)) {
            return false;
        }

        $mime = static::getMimeType($uploaded_file);
        $isValidType = static::validateFileType($mime);

        if (!$isValidType) {
            $file->error = 'Unsupported mime type: ' . $mime;

            return false;
        }

        return true;
    }

    /**
     * @param string $filePath
     * @return bool|string
     */
    protected static function getMimeType($filePath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return $mime;
    }

    /**
     * @param string $mime
     * @param null|array $allowedMimeTypes
     * @return bool
     */
    public static function validateFileType($mime, $allowedMimeTypes = null)
    {
        if (empty($mime)) {
            return false;
        }

        if ($allowedMimeTypes === null) {
            $allowedMimeTypes = static::$allowedMimeTypes;
        }

        return array_key_exists($mime, $allowedMimeTypes);
    }
}
