<?php

class Default_AuthController
        extends Core_Controller_Action
{

    public function indexAction()
    {
        $this->_redirect("/auth/login");
    }

    public function loginAction()
    {
        $oLoginForm = new Form_Login();
        $oLoginForm->setAction($this->view->url(array(
                    "action" => "login"
                )));

        if ($this->_request->isPost())
        {

            if ($oLoginForm->isValid($this->_request->getPost()))
            {
                $oUsersModel = new Model_Users ();
                $oUsersModel->setIdentity($oLoginForm->getValue("email"));
                $oUsersModel->setCredential($oLoginForm->getValue("password"));

                $this->_auth->authenticate($oUsersModel);
                if ($this->_auth->hasIdentity())
                {
                    $this->_redirect($this->view->url(array(
                                "controller" => "index",
                                "action" => "index"
                            )));
                }
                else
                {
                    switch ($oUsersModel->authenticate()->getCode())
                    {
                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                            $oLoginForm->getElement("email")->addError(
                                    $this->view->translate("Użytkownik nie istnieje")
                            );
                            break;
                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            $oLoginForm->getElement("password")->addError(
                                    $this->view->translate("Błąd autoryzacji!")
                            );
                            break;
                        default:
                            $oLoginForm->getElement("email")->addError(
                                    $this->view->translate("Użytkownik nie istnieje")
                            );
                    }
                }

                $oLoginForm->populate($this->_request->getParams());
            }
            else
            {
                if ($this->_auth->hasIdentity())
                {
                    $this->_auth->clearIdentity();
                }
            }
        }
        $this->view->oLoginForm = $oLoginForm;
    }

    public function logoutAction()
    {
        $this->_auth->clearIdentity();
        $this->_redirect($this->view->url(array(
                    "controller" => "index",
                    "action" => "index"
                )));
    }

    public function registerAction()
    {
        $oRegisterForm = new Form_Register();
        $oRegisterForm->setAction($this->view->url(array(
                    "action" => "register"
                )));

        $oRegisterForm->setDescription("Podaj swój adres email oraz hasło, a następnie powtórz je dla pewności.");

        $oRepeatPassword = new Zend_Form_Element_Password(array(
                    "name" => "password_r",
                    'required' => true,
                    'Description' => 'Powtórz hasło',
                    'validators' => array(
                        array('validator' => 'NotEmpty'),
                        array('validator' => 'StringLength', array('min' => 6, 'max' => 20)),
                    ),
                    'renderPassword' => true,
                    "decorators" => array(
                        'ViewHelper',
                        'Errors',
                        array(array("cell" => "HtmlTag"), array("tag" => "div", "class" => "cell")),
                        array('Description', array("tag" => "div", "class" => "cell", "placement" => "prepend")),
                        array(array('row' => "HtmlTag"), array("tag" => "div", "class" => "row", "id" => "loginPasswordR")),
                    )
                ));

        $oCheckIfEmailExists = new Zend_Validate_Db_NoRecordExists(array(
                    "table" => "users",
                    "field" => "email"
                ));

        $oRegisterForm->getElement("email")->addValidator($oCheckIfEmailExists);

        $oButton = $oRegisterForm->getElement("Login");
        $oButton->setLabel("Rejestracja");

        $oRegisterForm->removeElement("Login");
        $oRegisterForm->addElement($oRepeatPassword);
        $oRegisterForm->addElement($oButton);

        if ($this->_request->isPost())
        {
            $aPost = $this->_request->getPost();

            if ($aPost["password"] != $aPost["password_r"])
            {
                $oRegisterForm->getElement("password_r")->addError("Hasła się różnią między sobą!");
            }

            if ($oRegisterForm->isValid($aPost))
            {
                $oUsers = new Model_Users();
                $oUsers->insert(array(
                    "email" => $oRegisterForm->getValue("email"),
                    "password" => md5($oRegisterForm->getValue("password"))
                ));

                $this->view->registered = true;
            }
            else
            {
                $oRegisterForm->populate($aPost);
            }
        }
        
        $this->view->oRegisterForm = $oRegisterForm;
    }

    public function recoverAction()
    {

        $oRecoverForm = new Form_Login();
        $oRecoverForm->setDescription("Podaj swój adres email, na który wyślemy Ci Twoje nowe hasło");
        $oRecoverForm->setAction($this->view->url(array(
                    "action" => "recover"
                )));

        $oRecoverForm->removeElement("password");
        $oRecoverForm->getElement("Login")->setLabel("Przypomnij");

        if ($this->_request->isPost())
        {
            $aPost = $this->_request->getPost();

            $oCheckIfEmailExists = new Zend_Validate_Db_RecordExists(array(
                        "table" => "users",
                        "field" => "email"
                    ));

            $oRecoverForm->getElement("email")->addValidator($oCheckIfEmailExists);

            if ($oRecoverForm->isValid($aPost))
            {
                $sNewPass = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()_+|"), rand(1, 30), 8);

                $oModelUsers = new Model_Users();
                $oModelUsers->update(array("password" => md5($sNewPass)), "email = '{$aPost["email"]}'");

                if (APPLICATION_ENV != "development")
                {
                    $oMailer = new Zend_Mail();
                    $oMailer->addTo($aPost["email"]);
                    $oMailer->setFrom("noreply@server.pl", "Panel KSCapoeira");
                    $oMailer->setBodyText("Twoje nowe hasło to $sNewPass");
                    $oMailer->send();
                }
                else
                {
                    echo "Nowe hasło to $sNewPass";
                }
            }
        }

        $this->view->oRecoverForm = $oRecoverForm;
    }

    public function changeAction()
    {
        $oForm = new Form_Login();
        $oForm->setDescription("Podaj swoje stare hasło i powtórz dwa razy nowe");
        $oForm->setAction($this->view->url(array(
                    "action" => "change"
                )));

        $oSubmit = $oForm->getElement("Login")->setLabel("Zmień hasło");
        $oForm->getElement("password")->setDescription("Stare hasło");

        $oForm->removeElement("email");
        $oForm->removeElement("Login");

        $oNewPassword = new Zend_Form_Element_Password(array(
                    "name" => "password_n",
                    'required' => true,
                    'Description' => 'Nowe hasło',
                    'validators' => array(
                        array('validator' => 'NotEmpty'),
                        array('validator' => 'StringLength', array('min' => 6, 'max' => 20)),
                    ),
                    'renderPassword' => true,
                    "decorators" => array(
                        'ViewHelper',
                        'Errors',
                        array(array("cell" => "HtmlTag"), array("tag" => "div", "class" => "cell")),
                        array('Description', array("tag" => "div", "class" => "cell", "placement" => "prepend")),
                        array(array('row' => "HtmlTag"), array("tag" => "div", "class" => "row", "id" => "loginPasswordR")),
                    )
                ));
        $oRepeatPassword = new Zend_Form_Element_Password(array(
                    "name" => "password_r",
                    'required' => true,
                    'Description' => 'Powtórz hasło',
                    'validators' => array(
                        array('validator' => 'NotEmpty'),
                        array('validator' => 'StringLength', array('min' => 6, 'max' => 20)),
                    ),
                    'renderPassword' => true,
                    "decorators" => array(
                        'ViewHelper',
                        'Errors',
                        array(array("cell" => "HtmlTag"), array("tag" => "div", "class" => "cell")),
                        array('Description', array("tag" => "div", "class" => "cell", "placement" => "prepend")),
                        array(array('row' => "HtmlTag"), array("tag" => "div", "class" => "row", "id" => "loginPasswordR")),
                    )
                ));

        $oForm->addElement($oNewPassword);
        $oForm->addElement($oRepeatPassword);
        $oForm->addElement($oSubmit);

        if ($this->_request->isPost())
        {
            $aPost = $this->_request->getPost();

            $oUsers = new Model_Users();
            $oRow = $oUsers->fetchRow("email = '{$this->_oUserData->email}'");

            if ($oRow)
            {
                if ($oRow->password != md5($aPost["password"]))
                {
                    $oForm->getElement("password")->addError("Stare hasło się nie zgadza!");
                }
                if ($aPost["password_n"] != $aPost["password_r"])
                {
                    echo 2;
                    $oForm->getElement("password_r")->addError("Hasła nie są jednakowe!");
                }
                if ($oForm->isValid($aPost))
                {
                    $oRow->password = md5($aPost["password_n"]);
                    $oRow->save();
                     echo "<script>$(function(){\nalert(\"Twoje hasło zostało zmienione\");\nwindow.location='" . $this->view->url(array("controller" => "index","action" => "index"), null, true) . "';});</script>";
                }
            }
            else
            {
                $oForm->getElement("password")->addError("Brak w bazie użytkownika!");
            }
        }


        $this->view->oForm = $oForm;
    }

}

