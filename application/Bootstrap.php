<?php

class Bootstrap
        extends Zend_Application_Bootstrap_Bootstrap
{

    private $_config;

    public function __construct($application)
    {
        $this->_config = new Zend_Config($application->getOptions(), true);
        Zend_Registry::set('db', $this->_config->resources->db);
        Zend_Registry::set('config', $this->_config);

        parent::__construct($application);
    }

    protected function _initAuth()
    {
        $oAuthSession = new Zend_Session_Namespace('Zend_Auth');
        $oAuthSession->setExpirationSeconds($this->_config->auth->expirationSeconds);
        
    }

    protected function config()
    {
        return $this->_config;
    }

    protected function _initAutoload()
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => '',
                    'basePath' => APPLICATION_PATH));

        $moduleLoader->setResourceTypes(array(
            'form' => array(
                'namespace' => 'Form',
                'path' => 'forms',
            ),
            'model' => array(
                'namespace' => 'Model',
                'path' => 'models',
            ),
            'plugin' => array(
                'namespace' => 'Plugin',
                'path' => 'plugins',
            ),
            'service' => array(
                'namespace' => 'Service',
                'path' => 'services',
            ),
            'viewhelper' => array(
                'namespace' => 'View_Helper',
                'path' => 'views/helpers',
            ),
            'viewfilter' => array(
                'namespace' => 'View_Filter',
                'path' => 'views/filters',
            ),
            'helper' => array(
                'namespace' => 'Helper',
                'path' => 'helpers',
            ),
        ));


        return $moduleLoader;
    }

    protected function _initController()
    {
        $oFront = Zend_Controller_Front::getInstance();

        $oFront->setModuleControllerDirectoryName('controllers')
                ->addModuleDirectory(APPLICATION_PATH . '/modules')
                ->setDefaultModule('default')
                ->setDefaultAction('index')
                ->setDefaultControllerName('index')
                ->setParam('noErrorHandler', false);

        $oFront->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array('module' => 'default', 'controller' => 'error', 'action' => 'index')));
        return $oFront;
    }


    protected function _initLayout()
    {
        $config = $this->config();

        Zend_Layout::startMvc();
        $oLayout = Zend_Layout::getMvcInstance();
        $oLayout->setLayoutPath($config->resources->layout->layoutPath);
        $oLayout->setLayout($config->resources->layout->layout);
    }

    protected function _initViews()
    {
        $oView = new Zend_View();
        $oView->headTitle($this->config()->resources->view->title);

        $oAuth = Zend_Auth::getInstance();
        if ($oAuth->hasIdentity())
        {
            $oView->oUserData = $oAuth->getIdentity();
        }

        $oViewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($oView);
        $oViewRenderer->setViewSuffix('phtml')
                ->setViewBasePathSpec(':moduleDir/views/');

        Zend_Controller_Action_HelperBroker::addHelper($oViewRenderer);

        $oView->addHelperPath('Core/View/Helper/', 'Core_View_Helper');
    }

    protected function _initViewHelpers()
    {
        $oLayout = Zend_Layout::getMvcInstance();
        $oView = $oLayout->getView();

        $oView->doctype('XHTML1_STRICT');
        $oView->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    }

    protected function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addPrefix('Core_Controller_Action_Helper');
    }

    public function _initMail()
    {
        if ('development' === APPLICATION_ENV)
        {
            $oTransport = new Core_Mail_Transport_File();
            $oTransport->setSavePath(APPLICATION_PATH . '/../cache/mail');
        }
        else
        {
            $oTransport = new Zend_Mail_Transport_Sendmail();
        }

        Zend_Mail::setDefaultTransport($oTransport);
    }
}

