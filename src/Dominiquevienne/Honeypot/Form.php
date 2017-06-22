<?php
/**
 * Created by PhpStorm.
 * User: dvienne
 * Date: 22/06/2017
 * Time: 10:42
 */

namespace Dominiquevienne\Honeypot;


class Form {

  private $_timeCheckSessionVarName     = 'honeypotTimeCheckStart';
  private $_methodSessionVarName        = 'honeypotMethod';
  private $_honeypotInputSessionVarName = 'honeypotInputName';
  private $_honeypotInputMask           = '<input class="[$honeypotInputClass]" type="[$honeypotInputType]" name="[$honeypotInputName]" autocomplete="off" value="" />';
  private $_honeypotInputClass          = 'hide';
  private $_honeypotInputType           = 'text';
  private $_honeypotInputName           = null;
  private $_honeypotInputNames          = [
    'phoneNumber',
    'address',
    'zipCode',
    'contactPerson',
    'completeName',
  ];
  private $_method                      = 'POST';



  public function __construct($config = []) {
    if(empty($_SESSION)) {
      session_start();
    }

    if(is_array($config)) {
      if(!empty($config['honeypotInputMask'])) {
        $this->setHoneypotInputMask($config['honeypotInputMask']);
      }
      if(!empty($config['honeypotInputClass'])) {
        $this->setHoneypotInputClass($config['honeypotInputClass']);
      }
      if(!empty($config['honeypotInputType'])) {
        $this->setHoneypotInputType($config['honeypotInputType']);
      }
      if(!empty($config['honeypotInputName'])) {
        $this->setHoneypotInputName($config['honeypotInputName']);
      }
      if(!empty($config['honeypotInputNames'])) {
        $this->setHoneypotInputNames($config['honeypotInputNames']);
      }
      if(!empty($config['formMethod'])) {
        $this->setFormMethod($config['formMethod']);
      }
    }
  }


  /**
   * Getter for honeypotInputMask
   *
   * @return string
   */
  public function getHoneypotInputMask()
  {
    return $this->_honeypotInputMask;
  }


  /**
   * Setter for honeypotInputMask
   *
   * @param $mask
   *
   * @return $this
   */
  public function setHoneypotInputMask($mask)
  {
    $this->_honeypotInputMask = $mask;

    return $this;
  }


  /**
   * Getter for honeypotInputClass
   *
   * @return string
   */
  public function getHoneypotInputClass()
  {
    return $this->_honeypotInputClass;
  }


  /**
   * Setter for honeypotInputClass
   *
   * @param $class
   *
   * @return $this
   */
  public function setHoneypotInputClass($class)
  {
    $this->_honeypotInputClass  = $class;

    return $this;
  }


  /**
   * Getter for honeypotInputType
   *
   * @return string
   */
  public function getHoneypotInputType()
  {
    return $this->_honeypotInputType;
  }


  /**
   * Setter for honeypotInputType
   *
   * @param $type
   *
   * @return $this
   */
  public function setHoneypotInputType($type)
  {
    $this->_honeypotInputType = $type;

    return $this;
  }


  /**
   * Getter for honeypotInputName
   *
   * @return null|string
   */
  public function getHoneypotInputName()
  {
    return $this->_honeypotInputName;
  }


  /**
   * Setter for honeypotInputName
   *
   * @param $name
   *
   * @return $this
   */
  public function setHoneypotInputName($name)
  {
    $this->_honeypotInputName = $name;

    return $this;
  }


  /**
   * Getter for honeypotInputNames
   *
   * @return array
   */
  public function getHoneypotInputNames()
  {
    return $this->_honeypotInputNames;
  }


  /**
   * Setter for honeypotInputNames
   *
   * @param $names
   */
  public function setHoneypotInputNames($names)
  {
    if(!is_array($names)) {
      $names  = [
        $names,
      ];
    }

    $this->_honeypotInputNames  = $names;
  }


  /**
   * Used to store current time in Session in order to measure form completion time
   */
  public function timeCheck()
  {
    $_SESSION[$this->getTimeCheckSessionVarName()]  = time();

    if(empty($_SESSION[$this->getMethodSessionVarName()])) {
      $this->_registerMethod();
    }
  }


  /**
   * Getter for timeCheckSessionVarName
   *
   * @return string
   */
  public function getTimeCheckSessionVarName()
  {
    return $this->_timeCheckSessionVarName;
  }


  /**
   * Getter for honeypotInputSessionVarname
   *
   * @return string
   */
  public function getHoneypotInputSessionVarName()
  {
    return $this->_honeypotInputSessionVarName;
  }


  /**
   * Will generate honeypotInput and save its name in Session
   */
  private function _generateHoneypotInput()
  {
    if(empty($this->getHoneypotInputName())) {
      $names  = $this->getHoneypotInputNames();
      $name   = $names[rand(0, count($names)-1)];
      $name   .= Helpers::generateRandomString(3);
      $this->setHoneypotInputName($name);
    }

    $_SESSION[$this->getHoneypotInputSessionVarName()]  = $this->getHoneypotInputName();
  }


  /**
   * Generates the honeypot input field
   *
   * @return mixed
   */
  public function honeypotInput()
  {
    $this->_generateHoneypotInput();

    $mask   = $this->getHoneypotInputMask();
    $input  = preg_replace_callback(
      '/\[\$([^\]]+)\]/si',
      function($m) {
        $functionName = 'get' . $m[1];
        return $this->$functionName();
      },
      $mask
    );

    if(empty($_SESSION[$this->getMethodSessionVarName()])) {
      $this->_registerMethod();
    }

    return $input;
  }


  /**
   * Getter for methodSessionVarName
   *
   * @return string
   */
  public function getMethodSessionVarName()
  {
    return $this->_methodSessionVarName;
  }


  /**
   * Used to register form method
   */
  private function _registerMethod()
  {
    $_SESSION[$this->getMethodSessionVarName()] = $this->getFormMethod();
  }


  /**
   * Setter for method
   *
   * @return string
   */
  public function getFormMethod()
  {
    return $this->_method;
  }


  /**
   * Setter for method
   *
   * @param $method
   *
   * @return $this
   */
  public function setFormMethod($method)
  {
    $this->_method  = strtoupper($method);

    return $this;
  }

}