<?php

namespace Farmadati\Get;

use Farmadati\FarmaDatiSoapClient;
use Farmadati\Get\Response\GetSchemaDataSetResult;

class GetSchemaDataSet extends FarmaDatiSoapClient{

    /**
 *@var GetSchemaDataSetResult
 */
    public  $GetSchemaDataSetResult;
    public function __construct($_result)
    {
        parent::__construct([
            'GetSchemaDataSetResult'=>$_result
        ],false);
    }
    
    public function setGetSchemaDataSetResult($value){
        $this->GetSchemaDataSetResult=$value;
    }

    public function getGetSchemaDataSetResult(){
        return $this->GetSchemaDataSetResult;
    }

    
    public function getResult(){
        return $this->GetSchemaDataSetResult;
    }
}
