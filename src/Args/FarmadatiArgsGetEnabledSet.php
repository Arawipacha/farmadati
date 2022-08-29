<?php
namespace Farmadati\args;

use Farmadati\FarmaDatiSoapClient;

class FarmadatiArgsGetEnabledSet extends FarmaDatiSoapClient{

    //public $username;
    //public $password;

    public function __construct(public $Username=NULL, public $Password=NULL)
    {
        /*
        parent::__construct([
            'Username'=> $Username,
            'Password'=> $Password
        ],false);
        */
       parent::__construct([], false); 
    }
}

