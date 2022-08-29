<?php
namespace Farmadati;

use SoapClient;
use stdClass;

class FarmaDatiSoapClient extends stdClass{

    //const WSDL_URL='http://webservices-farmadati.dyndns.ws/WS2/FarmadatiItaliaWebServicesM1.svc?SingleWsdl';   //"http://webservices.farmadati.it/WS2/FarmadatiItaliaWebServicesM2.svc?singleWsdl";
    const WSDL_URL='http://webservices.farmadati.it/WS2/FarmadatiItaliaWebServicesM2.svc?singleWsdl';
    /**
     * Soapclient called to communicate with the actual SOAP Service
     * @var SoapClient
     */
    private static $soapClient;
    
    private $result;

    public function __construct($_argsValues = array(),$_resetSoapClient= true)
    {
        if($_resetSoapClient){
            //print_r('init soapclient');
            $this->initSoapClient($_argsValues);
        }

            
        //var_dump($_argsValues);    
        if(is_array($_argsValues) && count($_argsValues))
        {
            foreach($_argsValues as $name=>$value)
                $this->_set($name,$value);
        }
        
    }




    public function initSoapClient(){
            //$wsdlOptions = array();
           // $wsdlOptions['wsdl_url'] = self::WSDL_URL;

        // $opstion['classmap']=['GetEnabledDataSet' => 'FarmadatiStructGetEnabledDataSet'];
        $soapClientClassName= self::getSoapClientClassName();
        self::setSoapClient(new $soapClientClassName(self::WSDL_URL)); 
    }


    /**
    * usiamo i lazy per ottenere il soapClient 
    * 
    **/

    public static function getSoapClientClassName()
    {
        //if(class_exists('FarmadatiSoapClient') && is_subclass_of('FarmadatiSoapClient','SoapClient'))
        //    return 'FarmadatiSoapClient';
        //else
            return 'SoapClient';
    }



    protected static function setSoapClient(SoapClient $_soapClient)
    {
        return (self::$soapClient = $_soapClient);
    }



    public static function getSoapClient()
    {
        return self::$soapClient;
    }


    protected function setResult($_result)
    {
        return ($this->result = $_result);
    } 

    public function getResult()
    {
        return $this->result;
    }

    public function _set($_name,$_value)
    {
        $setMethod = 'set' . ucfirst($_name);
        //var_dump($setMethod);
        if(method_exists($this,$setMethod))
        {
            //var_dump($setMethod);
            $this->$setMethod($_value);
            return true;
        }
        else
            return false;
    }

    public function _get($_name)
    {
        $getMethod = 'get' . ucfirst($_name);
        if(method_exists($this,$getMethod))
            return $this->$getMethod();
        else
            return false;
    }
    
}
