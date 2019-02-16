<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Model_Downloader extends Ess_M2eProUpdater_Model_Abstract
{
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_BETA   = 'beta_testers';

    const BUILD_FORMAT_ZIP          = 'zip';
    const BUILD_FORMAT_CONNECT_1    = 'connect1';
    const BUILD_FORMAT_CONNECT_2    = 'connect2';
    const BUILD_MARKETPLACE_PACKAGE = 'marketplace';

    private $extensionCode;
    private $visibility;

    //########################################

    public function __construct($params)
    {
        parent::__construct($params);

        $this->extensionCode = isset($params['extension_code']) ? $params['extension_code'] : NULL;
        $this->visibility    = isset($params['visibility'])     ? $params['visibility']     : NULL;

        $this->validate();
    }

    private function validate()
    {
        if (!isset($this->extensionCode) ||
            !in_array($this->extensionCode, array(Ess_M2eProUpdater_Helper_Module::IDENTIFIER,
                                                  Ess_M2eProUpdater_Helper_M2ePro::IDENTIFIER))) {

            throw new \Exception(sprintf(
                'Unsupported extension code [%s]', $this->extensionCode
            ));
        }

        if ($this->visibility &&
            !in_array($this->visibility, array(self::VISIBILITY_PUBLIC, self::VISIBILITY_BETA))) {

            throw new \Exception(sprintf(
                'Unsupported builds visibility [%s]', $this->visibility
            ));
        }
    }

    //########################################

    /**
     * @param mixed $version (NULL - Latest Version; * All releases; string - Exact Version)
     * @return array
     */
    public function getReleaseInfo($version = NULL)
    {
        $url = $this->getServerUrl('get-release-metadata.php', array(
            'version'    => $version,
            'visibility' => $this->visibility,
        ));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return (array)json_decode($data, true);
    }

    /**
     * @param mixed $version (NULL - Latest Version; string - Exact Version)
     * @param string $format (zip, connect1, connect2, marketplace)
     * @return string
     */
    public function getPackageUrl($version = NULL, $format = self::BUILD_FORMAT_ZIP)
    {
        return $this->getServerUrl('get-release-version.php', array(
            'version'    => $version,
            'visibility' => $this->visibility,
            'format'     => $format
        ));
    }

    //########################################

    private function getServerUrl($relativeUrl, array $query = array())
    {
        $serverLocations = array(
            Ess_M2eProUpdater_Helper_Module::IDENTIFIER => 'https://download.m2epro.com/extension_updater/magento_1/',
            Ess_M2eProUpdater_Helper_M2ePro::IDENTIFIER => 'https://download.m2epro.com/extension/magento_1/',
        );

        return $serverLocations[$this->extensionCode] . $relativeUrl .'?'. http_build_query($query);
    }

    //########################################
}