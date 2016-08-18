<?php

defined('SYSPATH') or die('No direct script access.');

require_once 'upload.class.php';

class CustomUploadHandler extends UploadHandler
{
    /**
     * @var array
     */
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
        "application/zip" => 'zip',
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => 'docx',
        "application/rtf" => 'rtf',
        'application/x-shockwave-flash' => 'swf',
        'application/mp4' => 'mp4',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/x-rpm' => 'rpm',

        'audio/x-aac' => 'aac',
        'audio/x-aiff' => 'aif',
        'audio/ogg' => 'oga',
        'audio/mp4' => 'mp4a',
        'audio/mpeg' => 'mpga',
        'audio/x-pn-realaudio' => 'ram',
        'audio/x-wav' => 'wav',

        'video/x-msvideo' => 'avi',
        'video/quicktime' => 'qt',
        'video/ogg' => 'ogv',
        'video/mp4' => 'mp4',
        'video/mpeg' => 'mpeg',
        'video/jpeg' => 'jpgv',
        'video/3gpp' => '3gp',
        'video/3gpp2' => '3g2',
        'video/x-ms-wmv' => 'wmv',
        'video/x-flv' => 'flv',
    ];

    
    /**
     * CustomUploadHandler constructor.
     * @param null|array $options
     */
    public function __construct($options = null)
    {
        $this->options = array(
            'script_url' => $this->getFullUrl() . '/',
            'upload_dir' => DOCROOT . 'scripts/fileupload/php/files/',
            'upload_url' => $this->getFullUrl() . '/files/',
            'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            'accept_file_types' => '/.+$/i',
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to true to rotate images based on EXIF meta data, if available:
            'orient_image' => false,
            'image_versions' => array(
                // Uncomment the following version to restrict the size of
                // uploaded images. You can also add additional versions with
                // their own upload directories:
                /*
                'large' => array(
                    'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/files/',
                    'upload_url' => $this->getFullUrl().'/files/',
                    'max_width' => 1920,
                    'max_height' => 1200,
                    'jpeg_quality' => 95
                ),
                */
                'thumbnail' => array(
                    'upload_dir' => DOCROOT . 'scripts/fileupload/php/thumbnails/',
                    'upload_url' => $this->getFullUrl() . '/thumbnails/',
                    'max_width' => 80,
                    'max_height' => 80
                )
            )
        );
        if ($options) {
            $this->options = array_replace_recursive($this->options, $options);
        }
    }

    /**
     * @param $uploaded_file
     * @param $file
     * @param $error
     * @param $index
     * @return bool
     */
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
     * @return string
     */
    protected function getFullUrl()
    {
        return URL::base(true) . 'scripts/fileupload/php';
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
