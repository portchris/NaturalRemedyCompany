<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Model_Updater extends Ess_M2eProUpdater_Model_Abstract
{
    /** @var \Exception|null */
    protected $exception;

    protected $temporaryFileName;

    //########################################

    public function validate()
    {
        try {

            $this->_checkPermissions();

        } catch (\Exception $exception) {

            $this->exception = $exception;
            return false;
        }

        return true;
    }

    public function prepareNewPackage()
    {
        try {

            $this->_removeDownloaded();

            $this->_download();
            $this->_unpack();

        } catch (\Exception $exception) {

            $this->exception = $exception;
            return false;
        }

        return true;
    }

    public function updatePackage()
    {
        $this->_removeCurrentPackage();
        $this->_copy();
        $this->_removeDownloaded();
    }

    //########################################

    protected function _checkPermissions()
    {
        $paths = array(
            'app/code/community/Ess',
            'app/etc/modules',
            'app/design/adminhtml/default/default/template',
            'app/design/adminhtml/default/default/layout',
            'skin/adminhtml/default/default',
            'skin/adminhtml/default/enterprise',
            'js',
        );

        foreach ($paths as $path) {

            /** @var Ess_M2eProUpdater_Model_FileSystem $fileSystem */
            $fileSystem = Mage::getModel(
                'M2eProUpdater/FileSystem', array(Mage::getBaseDir() .'/'. $path)
            );

            $fileSystem->createFile('permissions_check.txt', 'test');
            $fileSystem->removeFile('permissions_check.txt');
        }
    }

    protected function _removeCurrentPackage()
    {
        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eHelper */
        $m2eHelper = Mage::helper('M2eProUpdater/M2ePro');

        foreach ($m2eHelper->getCodeDirectoryPaths() as $path) {

            if (is_file($path)) {

                unlink($path);
                continue;
            }

            if (is_dir($path)) {

                /** @var Ess_M2eProUpdater_Model_FileSystem $fileSystem */
                $fileSystem = Mage::getModel('M2eProUpdater/FileSystem', array($path));
                $fileSystem->removeDir();
                continue;
            }
        }
    }

    protected function _removeDownloaded()
    {
        /** @var Ess_M2eProUpdater_Model_FileSystem $fileSystem */
        $fileSystem = Mage::getModel(
            'M2eProUpdater/FileSystem', array($this->getUnpackedDirectoryPath())
        );

        $fileSystem->removeDir();

        if (file_exists($this->getTemporaryFileName())) {
            unlink($this->getTemporaryFileName());
        }
    }

    //########################################

    protected function _download()
    {
        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2ePro */
        $m2ePro = Mage::helper('M2eProUpdater/M2ePro');

        /** @var Ess_M2eProUpdater_Model_Downloader $downloader */
        $downloader = Mage::getModel('M2eProUpdater/Downloader', array(
            'visibility'     => $m2ePro->getPackageVisibility(),
            'extension_code' => Ess_M2eProUpdater_Helper_M2ePro::IDENTIFIER
        ));

        $targetFileHandler = fopen($this->getTemporaryFileName(), 'wb');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $downloader->getPackageUrl());
        curl_setopt($ch, CURLOPT_FILE, $targetFileHandler);

        curl_exec($ch);
        curl_close($ch);

        fclose($targetFileHandler);
    }

    protected function _unpack()
    {
        $zipObject = new \ZipArchive();
        $zipObject->open($this->getTemporaryFileName());

        $zipObject->extractTo($this->getUnpackedDirectoryPath());
        $zipObject->close();
    }

    protected function _copy()
    {
        /** @var RecursiveDirectoryIterator $iterator */
        $sourceDirectory = new RecursiveDirectoryIterator(
            $this->getUnpackedDirectoryPath(), FilesystemIterator::SKIP_DOTS
        );
        $iterator = new RecursiveIteratorIterator($sourceDirectory, RecursiveIteratorIterator::SELF_FIRST);

        /** @var Ess_M2eProUpdater_Model_FileSystem $targetDir */
        $targetDir = Mage::getModel(
            'M2eProUpdater/FileSystem', array(Mage::getBaseDir())
        );

        foreach ($iterator as $item) {
            /**@var SplFileInfo $item */

            if ($item->isDir()) {
                $targetDir->createDir($iterator->getSubPathname());
            } else {
                $targetDir->copyFile($item->getRealPath(), $iterator->getSubPathname());
            }
        }
    }

    //########################################

    protected function getUnpackedDirectoryPath()
    {
        /** @var Ess_M2eProUpdater_Helper_Module $moduleHelper */
        $moduleHelper = Mage::helper('M2eProUpdater/Module');

        $path = $moduleHelper->getTemporaryDirectoryPath() .DS. 'unpacked';
        mkdir($path, 0777, true);

        return $path;
    }

    protected function getTemporaryFileName()
    {
        if (!is_null($this->temporaryFileName)) {
            return $this->temporaryFileName;
        }

        /** @var Ess_M2eProUpdater_Helper_M2ePro $m2eProHelper */
        $m2eProHelper = Mage::helper('M2eProUpdater/M2ePro');
        $latestRelease = $m2eProHelper->getLatestAvailableReleaseInfo();

        /** @var Ess_M2eProUpdater_Helper_Module $moduleHelper */
        $moduleHelper = Mage::helper('M2eProUpdater/Module');
        $this->temporaryFileName = $moduleHelper->getTemporaryDirectoryPath() .DS. $latestRelease['files']['zip'];

        return $this->temporaryFileName;
    }

    //########################################

    public function getException()
    {
        return $this->exception;
    }

    //########################################
}