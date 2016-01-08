<?php

/**
 * @category    NaturalRemedyCo
 * @package     NRCLayout
 * @author      Chris Rogers
 */

class NaturalRemedyCo_NRCLayout_Block_Listing extends Inchoo_FeaturedProducts_Block_Listing {
    
    /**
     * Check sort option and limits set in System->Configuration and apply them
     * Additionally, set template to block so call from CMS will look like {{block type="featuredproducts/listing"}}
     */
    public function __construct() {
        parent::__construct();
        $this->setTemplate('nrc_layout/inchoo/featuredproducts/block_featured_products.phtml');
    }
}
