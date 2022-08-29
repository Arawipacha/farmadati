<?php
namespace Farmadati\args;
use Farmadati\FarmaDatiSoapClient;


class FarmadatiArgsGetDataSet extends FarmaDatiSoapClient{
    
    public function __construct(
        public $Username = NULL,
        public $Password = NULL,
        public $CodiceSetDati = NULL,
        public $Modalita = Mode::COUNT,
        public $PageN = 1
    )
    {
        parent::__construct(
            [
                'Username'=> $Username,
                'Password'=> $Password,
                'CodiceSetDati'=> $CodiceSetDati,
                'Modalita'=> $Modalita,
                'PageN'=> $PageN
            ],
            false
        );
    } 
}
