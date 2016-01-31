<?php /***************************************************************
* Creating H1 Title category attributes for the catalogue
**********************************************************************/
echo 'Running This Upgrade ' . __FILE__ . ' - '.get_class($this)."\n <br /> \n";
$this->startSetup();
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'h1_title', array(
	'group' => 'General Information',
	'input' => 'text',
	'type' => 'text',
	'label' => 'Page H1 Title',
	'backend' => '',
	'visible' => true,
	'required' => false,
	'visible_on_front' => true,
	'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
));
$this->endSetup();
?>