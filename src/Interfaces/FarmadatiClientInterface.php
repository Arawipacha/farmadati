<?php
namespace Farmadati\Interfaces;

use App\Models\XmlData;
use Farmadati\Get\Response\GetSchemaDataSetResult;

interface FarmadatiClientInterface{

    /**
     * return status full data  by DATA_AGG, RITORNA SOLO LA DATA AGGIORNAMENTO DEL SERVIZIO SOAP
     */
    public function statusFullData(): ?array;
    

    /**
     * return count data  by COUNT, RITORNA SOLO LA DATA AGGIORNAMENTO DEL SERVIZIO SOAP
     */
    public function statusPartialData(): ?array;

    /**
     * get full data tabella prodotti farmadati format zip e descomprimiamo in una cartela il file APP001.xml
     * questo si chiama solo una volta al momento della configurazione.
     */
    public function extractBinaryZipToXml($data_agg_id): ?array;

    /**
     * ritorna lo schema dei fields FDI... con le metadata e descrizione e tipo campo
     */
    public function getFields(): ?array;

    
    /**
     * generate (insert or update) sql field xml to field table
     *  Campo [TIPO_VAR], (Tipo di variazione da eseguire) che può contenere:
     * “I” ‐> Record inserito
     * “V” ‐> Record variato
     * “A” ‐> Record annullato
     */
    public function getSqlByXMLPath(string $path, string $table, array $schemaToFields): ?string;


    public function getPathsSql(array $data,XmlData $xmlData, string $table, array $schemaToFields): ?array;


    public function runSqlPathById($data);
    

    public function getOnlySqlFile(array $data,string $table, $schemasToFields);

    

    public function saveDirectoryContentPathSql($directory, XmlData $model);
}
