<?php

namespace AmazePHP;


/**
 * Class UploadFile
 *
 */
class UploadFile extends \SplFileInfo
{
    /**
     * @var string
     */
    protected $_uploadName = null;

    /**
     * @var string
     */
    protected $_uploadMimeType = null;

    /**
     * @var int
     */
    protected $_uploadErrorCode = null;

    private $imageType=["image/gif","image/jpeg","image/jpg","image/png","image/x-png","image/bmp","image/x-ms-bmp","image/pjpeg"];//image type
    private $fileType=["application/zip","application/msexcel","application/xml","application/vnd.ms-excel","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/mspowerpoint","application/vnd.ms-powerpoint","application/pdf","application/x-shockwave-flash","application/x-rar-compressed","application/x-rar","audio/mpeg","audio/x-ms-wma","flv-application/octet-stream","audio/x-ms-wmv","video/mp4","video/x-flv","audio/x-wav","application/msword","video/mpeg"];//file type


    private $tmpName;
    private $fileSize;
    private $maxSize=10485760;//10M


    /**
     * UploadFile constructor.
     * @param $file_name
     * @param $upload_name
     * @param $upload_mime_type
     * @param $upload_error_code
     */
    //UploadFile($files['tmp_name'], $files['name'], $files['type'], $files['error']);
    public function __construct($file_name, $upload_name, $upload_mime_type, $upload_error_code, $size, $upType="image")
    {
        $this->tmpName = $file_name;

        $this->_uploadName = $upload_name;
        $this->_uploadMimeType = $upload_mime_type;
        $this->_uploadErrorCode = $upload_error_code;



        $this->fileSize =  $size;
        if ($this->fileSize > $this->maxSize) {
            exit("file max than".($this->maxSize / 1024 / 1024)." M");
        }
        if ($this->_uploadErrorCode > 0) {
            exit($error ?? 'no file upload');
        }
        if ($upType== "image") {
            $this->checkImage();
        } else {
            $this->checkFile();
        }

        parent::__construct($file_name);
    }


    //check  image type
    public function checkImage()
    {

        if (!in_array($this->_uploadMimeType, $this->imageType)) {
            exit("invalid image type");
        }

        try {
            $ftype=getimagesize($this->tmpName);
        } catch (\Throwable $th) {
            exit("not a image file");
        }
        if (!in_array($ftype['mime'], $this->imageType)) {
            exit("invalid image type");
        }
    }

    //check file type
    public function checkFile()
    {


        if (!in_array($this->_uploadMimeType, $this->fileType)) {
            exit("invalid file type!");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $ftype= finfo_file($finfo, $this->tmpName);// get file  type by file content
        finfo_close($finfo);
        if (!in_array($ftype, $this->fileType)) {
            exit("invalid file type");
        }
    }


    /**
     * @return string
     */
    public function getUploadName()
    {
        return $this->_uploadName;
    }

    /**
     * @return string
     */
    public function getUploadMineType()
    {
        return $this->_uploadMimeType;
    }

    /**
     * @return mixed
     */
    public function getUploadExtension()
    {
        return pathinfo($this->_uploadName, PATHINFO_EXTENSION);
    }

    /**
     * @return int
     */
    public function getUploadErrorCode()
    {
        return $this->_uploadErrorCode;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->_uploadErrorCode === UPLOAD_ERR_OK;
    }

       /**
     * @param $destination
     * @return File
     */
    public function move($destination)
    {
        set_error_handler(function ($type, $msg) use (&$error) {
            $error = $msg;
        });
        $path = pathinfo($destination, PATHINFO_DIRNAME);
        if (!is_dir($path) && !mkdir($path, 0777, true)) {
            restore_error_handler();
            throw new \Exception(sprintf('Unable to create the "%s" directory (%s)', $path, strip_tags($error)));
        }
        if (!rename($this->getPathname(), $destination)) {
            restore_error_handler();
            throw new \Exception(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $destination, strip_tags($error)));
        }
        restore_error_handler();
        @chmod($destination, 0666 & ~umask());
        return new parent($destination);
    }
}
