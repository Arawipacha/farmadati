<?php
namespace Farmadati\Get;

use Farmadati\FarmaDatiSoapClient;
use Farmadati\Get\Response\GetDataSetResult;

class GetDataSet extends FarmaDatiSoapClient{
    
    

    /**
    *@var GetDataSetResult
    */
    private $GetDataSetResult;

    public function __construct($_result)
    {
        parent::__construct([
            'GetDataSetResult'=> $_result
        ],false);
    }


    public function setGetDataSetResult($value){
        $this->GetDataSetResult=$value;
    }

    public function getGetDataSetResult(){
        return $this->GetDataSetResult;
    }
    
    public function getResult(){
        return $this->GetDataSetResult;
    }



}
