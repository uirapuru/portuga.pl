<?php

class Default_ErrorController
        extends Core_Controller_Action
{

    public function indexAction()
    {
        $oErrors = $this->_getParam('error_handler');


        switch ($oErrors->type)
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';

                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }

        $this->view->exceptionMessage = $oErrors->exception->getMessage();
        $this->view->exceptionTrace = $oErrors->exception->getTraceAsString();
        $this->view->request = $this->_request;
    }

    public function notfoundAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
        echo "<h1>404</h1>Strona o podanym adresie nie odnaleziona";
    }

}

