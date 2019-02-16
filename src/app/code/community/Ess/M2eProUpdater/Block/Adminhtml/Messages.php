<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2016 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2eProUpdater_Block_Adminhtml_Messages extends Ess_M2eProUpdater_Block_Abstract
{
    protected $_template = 'M2eProUpdater/messages.phtml';
    protected $messages = array();

    //########################################

    public function addError($messageText)
    {
        /** @var Mage_Core_Model_Message_Abstract $message */
        $message = Mage::getSingleton('core/message')->error($messageText);
        $this->addMessage($message);
        return $this;
    }

    public function addWarning($messageText)
    {
        /** @var Mage_Core_Model_Message_Abstract $message */
        $message = Mage::getSingleton('core/message')->warning($messageText);
        $this->addMessage($message);
        return $this;
    }

    public function addNotice($messageText)
    {
        //** @var Mage_Core_Model_Message_Abstract $message */
        $message = Mage::getSingleton('core/message')->notice($messageText);
        $this->addMessage($message);
        return $this;
    }

    public function addSuccess($messageText)
    {
        /** @var Mage_Core_Model_Message_Abstract $message */
        $message = Mage::getSingleton('core/message')->success($messageText);
        $this->addMessage($message);
        return $this;
    }

    protected function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        $this->messages[$message->getType()][] = $message;
        return $this;
    }

    //########################################

    public function getErrorMessages()
    {
        return $this->getMessagesByType(Mage_Core_Model_Message::ERROR);
    }

    public function getWarningMessages()
    {
        return $this->getMessagesByType(Mage_Core_Model_Message::WARNING);
    }

    public function getNoticeMessages()
    {
        return $this->getMessagesByType(Mage_Core_Model_Message::NOTICE);
    }

    public function getSuccessMessages()
    {
        return $this->getMessagesByType(Mage_Core_Model_Message::SUCCESS);
    }

    public function getMessages()
    {
        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS
        );

        $messages = array();
        foreach ($types as $type) {
           $messages = array_merge($messages, $this->getMessagesByType($type));
        }
        return $messages;
    }

    protected function getMessagesByType($type)
    {
        return isset($this->messages[$type]) ? $this->messages[$type] : array();
    }

    //----------------------------------------

    public function clearMessages()
    {
        $this->messages = array();
        return $this;
    }

    //########################################

    public function getCssClass(Mage_Core_Model_Message_Abstract $message)
    {
        switch ($message->getType()) {

            default:
            case Mage_Core_Model_Message::ERROR:
                return 'error-msg';
                break;

            case Mage_Core_Model_Message::WARNING:
                return 'warning-msg';
                break;

            case Mage_Core_Model_Message::NOTICE:
                return 'notice-msg';
                break;

            case Mage_Core_Model_Message::SUCCESS:
                return 'success-msg';
                break;
        }
    }

    //########################################
}