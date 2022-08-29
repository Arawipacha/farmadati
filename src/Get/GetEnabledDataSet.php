<?php
namespace Farmadati\Get;

use Farmadati\FarmaDatiSoapClient;
use Farmadati\Get\Response\GetEnabledDataSetResult;

class GetEnabledDataSet extends FarmaDatiSoapClient{
    
    /**
    *@var GetEnabledDataSetResult
    */
    public  $GetEnabledDataSetResult;
    public function __construct($_result)
    {
            //var_dump($_result);
        //print_r($_GetEnabledDataSetResult->GetEnabledDataSetResult);
        //$this->GetEnabledDataSetResult= new GetEnabledDataSetResult($_result->GetEnabledDataSetResult);
       //$this->GetEnabledDataSetResult=$GetEnabledDataSetResult; 
        parent::__construct([
            'GetEnabledDataSetResult'=>$_result
        ],false);
    }

    public function setGetEnabledDataSetResult($value){
        $this->GetEnabledDataSetResult=$value;
    }
/**
     * Method to call the operation originally named GetEnabledDataSet
     * @return GetEnabledDataSetResult
     */
    public function getGetEnabledDataSetResult(){
        return $this->GetEnabledDataSetResult;
    } 
}
