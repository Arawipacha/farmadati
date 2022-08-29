<?php
namespace Farmadati\args;
use Farmadati\FarmaDatiSoapClient;

class FarmadatiArgsGetSchemaDataSet extends FarmaDatiSoapClient{

    public function __construct(public $Username = NULL,public $Password = NULL,public $CodiceSetDati = NULL)
    {
        parent::__construct(array('Username'=>$Username,'Password'=>$Password,'CodiceSetDati'=>$CodiceSetDati),false);
    }
    /**
     * Get Username value
     * @return string|null
     */
    public function getUsername()
    {
        return $this->Username;
    }
    /**
     * Set Username value
     * @param string $_username the Username
     * @return string
     */
    public function setUsername($_username)
    {
        return ($this->Username = $_username);
    }
    /**
     * Get Password value
     * @return string|null
     */
    public function getPassword()
    {
        return $this->Password;
    }
    /**
     * Set Password value
     * @param string $_password the Password
     * @return string
     */
    public function setPassword($_password)
    {
        return ($this->Password = $_password);
    }
    /**
     * Get CodiceSetDati value
     * @return string|null
     */
    public function getCodiceSetDati()
    {
        return $this->CodiceSetDati;
    }
    /**
     * Set CodiceSetDati value
     * @param string $_codiceSetDati the CodiceSetDati
     * @return string
     */
    public function setCodiceSetDati($_codiceSetDati)
    {
        return ($this->CodiceSetDati = $_codiceSetDati);
    }
}
