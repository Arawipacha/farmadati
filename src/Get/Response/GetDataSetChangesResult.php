<?php
namespace Farmadati\Get\Response;

class GetDataSetChangesResult{
    

public function __construct(public $CodEsito=NULL,
public $DescEsito=NULL,
public $NumRecords=NULL,
public $OutputValue=NULL,
public $ByteListFile=NULL)
{
    
}

public function getDescEsito(){
    return $this->DescEsito;
}
}    
