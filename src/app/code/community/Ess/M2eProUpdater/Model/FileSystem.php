<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Model_FileSystem extends Ess_M2eProUpdater_Model_Abstract
{
    //########################################

    protected $directory = null;

    //########################################

    public function __construct($arguments)
    {
        $directoryPath = isset($arguments[0]) ? $arguments[0] : null;

        if (is_null($directoryPath)) {
            throw new Exception('Directory path is not provided.');
        }

        $this->directory = $directoryPath;

        if (!$this->isDirectory($directoryPath)) {
            $this->createDir();
        }

        parent::__construct($arguments);
    }

    //########################################

    public function createDir($dirPath = null, $permissions = 0777)
    {
        $fullPath = $this->_getFullPath($dirPath);

        if ($this->isDirectory($fullPath)) {
            return true;
        }

        mkdir($fullPath, $permissions, true);

        if (!$this->isDirectory($fullPath)) {
            throw new Exception("Unable to create a directory '{$fullPath}'. Check permissions.");
        }

        return true;
    }

    public function removeDir($dirPath = null)
    {
        $fullPath = $this->_getFullPath($dirPath);

        if (!$this->isDirectory($fullPath)) {
            return true;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileInfo) {
            /**@var SplFileInfo $fileInfo */

            if ($fileInfo->isDir()) {
               $this->_removeDir($fileInfo->getRealPath());
            } else {
                $this->_removeFile($fileInfo->getRealPath());
            }
        }

        $this->_removeDir($fullPath);
        return true;
    }

    //########################################

    public function createFile($filePath, $content)
    {
        $fullPath = $this->_getFullPath($filePath);

        file_put_contents($fullPath, $content);

        if (!$this->isFile($fullPath)) {
            throw new Exception(
                "Unable to write a file '{$fullPath}'. Check permissions."
            );
        }

        return true;
    }

    public function removeFile($filePath)
    {
        $fullPath = $this->_getFullPath($filePath);

        if (!$this->isFile($fullPath)) {
            return true;
        }

        $this->_removeFile($fullPath);

        return true;
    }

    //########################################

    public function isFile($filePath)
    {
        clearstatcache();
        return is_file($filePath);
    }

    public function isDirectory($dirPath = null)
    {
        clearstatcache();
        return is_dir($dirPath);
    }

    //########################################

    public function copyFile($sourcePath, $filePath)
    {
        $destinationFullPath = $this->_getFullPath($filePath);

        if (!$this->isFile($sourcePath)) {
            throw new Exception("File '{$sourcePath}' is not exists.");
        }

        $content = file_get_contents($sourcePath);
        file_put_contents($destinationFullPath, $content);

        if (!$this->isFile($destinationFullPath)) {
            throw new Exception(
                "Unable to write a file '{$destinationFullPath}'. Check permissions."
            );
        }

        return true;
    }

    //########################################

    public function getDirectoryPath()
    {
         return $this->directory;
    }

    //########################################

    private function _getFullPath($path = null)
    {
        return is_null($path) ? $this->directory : $this->directory .DS. $path;
    }

    private function _removeDir($fullPath)
    {
        rmdir($fullPath);

        if ($this->isDirectory($fullPath)) {
            throw new Exception(
                "Unable to remove a directory '{$fullPath}'. Check permissions."
            );
        }

        return true;
    }

    private function _removeFile($fullPath)
    {
        unlink($fullPath);

        if ($this->isFile($fullPath)) {
            throw new Exception(
                "Unable to remove a file '{$fullPath}'. Check permissions."
            );
        }
    }

    //########################################
}