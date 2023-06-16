<?php
namespace App;


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

    /**
     * UploadFile constructor.
     * @param $file_name
     * @param $upload_name
     * @param $upload_mime_type
     * @param $upload_error_code
     */
    public function __construct($file_name, $upload_name, $upload_mime_type, $upload_error_code)
    {
        $this->_uploadName = $upload_name;
        $this->_uploadMimeType = $upload_mime_type;
        $this->_uploadErrorCode = $upload_error_code;
        parent::__construct($file_name);
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
