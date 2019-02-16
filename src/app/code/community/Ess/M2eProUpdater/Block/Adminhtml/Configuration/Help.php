<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Help
    extends Ess_M2eProUpdater_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    //########################################

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml($element);
    }

    //########################################

    protected function _toHtml($element = NULL)
    {
        $url = Mage::helper('M2eProUpdater/Support')->getDocumentationUrl('x/6BhPAQ');

        $helpBlock = $this->getLayout()->createBlock('M2eProUpdater/adminhtml_helpBlock', '', array(
            'id'      => 'm2epro_updater_block_notice_installation_upgrade_help',
            'content' => <<<HTML
<p>
<strong>Important note:</strong> We highly recommend that you use 
<a target="_blank" href="{$url}">Magento Marketplace</a> as the most preferred option to install/upgrade 
M2E Pro Extension.
</p>

<p>
The Installation/Upgrade Module allows simplifying the procedure of M2E Pro Extension installation/upgrade.
The process can be performed automatically (the recommended way) or using one of the alternative ways.
</p>

<p>
Installation/Upgrade Module provides you with notifications about the new M2E Pro version released. 
You can set your preferences below.
</p>

<p>
<strong>Note</strong>, your Magento Instance will be in Maintenance Mode until 
M2E Pro installation/upgrade process is completed.
</p>
HTML
        ));

        return $helpBlock->toHtml();
    }

    //########################################
}