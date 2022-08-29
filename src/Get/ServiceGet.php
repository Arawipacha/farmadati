<?php
namespace Farmadati\Get;

use Farmadati\args\FarmadatiArgsGetDataSet;
use Farmadati\args\FarmadatiArgsGetDataSetChanges;
use Farmadati\args\FarmadatiArgsGetEnabledSet;
use Farmadati\args\FarmadatiArgsGetSchemaDataSet;
use Farmadati\FarmaDatiSoapClient;
use Farmadati\Get\GetEnabledDataSet;
use Farmadati\Get\GetDataSet;
use Farmadati\Get\Response\GetDataSetResult;
use Farmadati\Get\Response\GetEnabledDataSetResult;
use Farmadati\Get\Response\GetSchemaDataSetResult;
use Farmadati\Get\traitBinaryFile;



class ServiceGet extends FarmaDatiSoapClient{
    
    use traitBinaryFile;
    
    public function GetEnabledDataSet(FarmadatiArgsGetEnabledSet $_argsGetEnabledDataSet): GetEnabledDataSet {
        return new GetEnabledDataSet($this->setResult(self::getSoapClient()->GetEnabledDataSet($_argsGetEnabledDataSet))->GetEnabledDataSetResult);
    }

    public function GetSchemaDataSet(FarmadatiArgsGetSchemaDataSet $_argsGetSchemaDataSet): GetSchemaDataSet{

        //return $this->setResult(self::getSoapClient()->GetSchemaDataSet($_argsGetSchemaDataSet));
        return new GetSchemaDataSet($this->setResult(self::getSoapClient()->GetSchemaDataSet($_argsGetSchemaDataSet))->GetSchemaDataSetResult);

    }

    public function GetDataSet(FarmadatiArgsGetDataSet $_argsGetDataSet): GetDataSet{
        //var_dump($_argsGetDataSet);
        return new GetDataSet($this->setResult(self::getSoapClient()->GetDataSet($_argsGetDataSet))->GetDataSetResult);
    }
    
    public function GetDataSetChanges(FarmadatiArgsGetDataSetChanges $_argsGetDataSetChanges): GetDataSetChanges{
    
        return new GetDataSetChanges($this->setResult(self::getSoapClient()->GetDataSetChanges($_argsGetDataSetChanges))->GetDataSetChangesResult);
    }

    /**
     * Returns the result
     * @see FarmaDatiSoapClient::getResult()
     * @return GetDataSetResult|GetSchemaDataSetResult|GetEnabledDataSetResult
     */    
    public function getResult()
    {
        return parent::getResult();
    }


}
