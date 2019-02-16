<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Configuration_Fieldset
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    //########################################

    protected function _getHeaderTitleHtml($element)
    {
        return <<<HTML
<div class="entry-edit-head collapseable" collapseable="no">
    <a id="{$element->getHtmlId()}-head" 
       href="#" 
       onclick="return false;" 
       style="background-image: none !important;"
    >
        {$element->getLegend()}
    </a>
</div>
HTML;
    }

    protected function _getFooterHtml($element)
    {
        $tooltipsExist = false;
        $html = '</tbody></table>';

        $html .= $this->_getFooterCommentHtml($element);

        $html .= '</fieldset>' . $this->_getExtraJs($element, $tooltipsExist);

        if ($element->getIsNested()) {
            $html .= '</div></td></tr>';
        } else {
            $html .= '</div>';
        }
        return $html;
    }

    protected function _getFooterCommentHtml($element)
    {
        return '';
    }

    //########################################

    protected function _getCollapseState($element)
    {
        return true;
    }

    //########################################
}