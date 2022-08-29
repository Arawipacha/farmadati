<?php
namespace Farmadati\Get;

use Farmadati\FarmaDatiSoapClient;
//use Farmadati\Get\Response\GetDataSetChangesResult;
//use Farmadati\Get\traitBinaryFile as GetTraitBinaryFile;

class GetDataSetChanges extends FarmaDatiSoapClient{
    

    //use GetTraitBinaryFile;
    
    /**
    *@var Farmadati\Get\Response\GetDataSetChangesResult
    */    
    private $GetDataSetChangesResult;
    
    public function __construct($_result)
    {
        parent::__construct([
            'GetDataSetChangesResult'=> $_result
        ]);
    }
    
    public function setGetDataSetChangesResult($value){
        $this->GetDataSetChangesResult=$value;
    }

    public function getGetDataSetChangesResult(){
        return $this->GetDataSetChangesResult;
    }
    
    public function getResult(){
        return $this->GetDataSetChangesResult;
    }

}
