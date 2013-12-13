<?php

/**
 * Description of Action
 *
 * @author Uirapuru
 */
class Core_Controller_Action_Admin
        extends Core_Controller_Action
{

    public function init()
    {
        parent::init();

        if ($this->_auth->hasIdentity())
        {
            $this->_oUserData = $this->_auth->getIdentity();
            $oRoutingTable = new Model_Navigation();
            $this->view->layout()->admin_navigation = $oRoutingTable->getNavigation(8);
        }
        else
        {
            if ($this->_request->getControllerName() != "auth")
            {
                $this->_redirect($this->view->url(array(
                            "module" => "admin",
                            "controller" => "auth",
                            "action" => "login"
                                ), "zaloguj"));
            }
        }

        if ($this->_request->isPost() || $this->_request->isXmlHttpRequest())
        {
            $this->_oLog->log("Użytkownik: {$this->_oUserData->email}\n<br />Parametry odwołania: " . Zend_Debug::dump($this->_request, null, false), Zend_Log::INFO);
        }
    }

}