<?php

namespace Farmadati\args;

use Farmadati\FarmaDatiSoapClient;

class FarmadatiArgsGetDataSetChanges extends FarmaDatiSoapClient{

    public function __construct(
        public $Username=NULL, 
        public $Password=NULL, 
        public $CodiceSetDati=NULL,
        public $DataIstanza='',
        public $Modalita= Mode::COUNT,
        public $PageN = 1,
        public $PagingN= 25000
    )
    {
        
       parent::__construct([], false); 
    }

} 
