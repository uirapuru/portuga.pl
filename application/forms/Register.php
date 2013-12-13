<?php

class Form_Register
        extends Zend_Form
{

    public function init()
    {

        $this->setDescription("Podaj swój adres email oraz hasło, a następnie powtórz je dla pewności.");
        
        /**
         * Email
         */
        $this->addElement('text', 'email', array(
            'required' => true,
            'description' => 'Email',
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                array('validator' => 'NotEmpty'),
                array('validator' => 'StringLength', array('min' => 6, 'max' => 64)),
                array('validator' => 'EmailAddress'),
            ),
            'decorators' => array(
                'ViewHelper',
                'Errors',
                array(array("cell" => "HtmlTag"), array("tag" => "div", "class" => "cell")),
                array('Description', array("tag" => "div", "class" => "cell", "placement" => "prepend")),
                array(array('row' => "HtmlTag"), array("tag" => "div", "class" => "row", "id" => "loginEmail")),
            )
        ));

        /**
         * Password
         */
        $this->addElement('password', 'password', array(
            'required' => true,
            'Description' => 'Hasło',
            'validators' => array(
                array('validator' => 'NotEmpty', 'breakChainOnFailure' => true),
                array('validator' => 'StringLength', 'breakChainOnFailure' => true, array('min' => 6, 'max' => 20)),
            ),
            'renderPassword' => true,
            "decorators" => array(
                'ViewHelper',
                'Errors',
                array(array("cell" => "HtmlTag"), array("tag" => "div", "class" => "cell")),
                array('Description', array("tag" => "div", "class" => "cell", "placement" => "prepend")),
                array(array('row' => "HtmlTag"), array("tag" => "div", "class" => "row", "id" => "loginPassword")),
            )
        ));

        /**
         * Retype password
         */

        $this->addelement('password','password_r',array(
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

        /**
         * Submit
         */
        $this->addElement('submit', 'Login', array(
            'label' => 'Logowanie',
            'decorators' => array(
                'ViewHelper',
                array(array("cell" => 'HtmlTag'), array("tag" => "div", "class" => "cell")),
                array(array("row" => 'HtmlTag'), array("tag" => "div", "id" => "loginButton", "class" => "row"))
            )
        ));

        $this->setDecorators(array(
            array("Description",array("tag" => "div", "class"=>"formDescription")),
            "FormElements",
            "Form",
            array("HtmlTag",array("tag" => "div", "id" => "loginForm"))
        ));

        /**
         * Stylowanie
         */

        $sStyle = <<< EOS
    #loginForm {
        width: 300px;
        border: 1px solid #8aa5bf;
        margin: 20px auto;
        padding: 50px;
        display: table
    }

    #loginEmail,
    #loginPassword,
    #loginButton {
        display: table-row
    }
    .row {
        display: table-row
    }
    .cell {
        display: table-cell;
        padding: 10px;
    }

    #loginEmail {

    }
    #loginPassword {

    }
    #loginButton {
        text-align: center;
    }


    #loginForm input {
        border: 1px solid #8aa5bf
    }

.errors {
    color: red;
}
EOS;

        $this->getView()->headStyle()->appendStyle($sStyle);
    }

}

