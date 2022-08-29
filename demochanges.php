<?php
include __DIR__ . '/vendor/autoload.php';
use Farmadati\args\FarmadatiArgsGetEnabledSet;
use Farmadati\args\FarmadatiArgsGetSchemaDataSet;
use Farmadati\Get\ServiceGet;
use Dotenv\Dotenv;
use Farmadati\args\FarmadatiArgsGetDataSet;
use Farmadati\args\FarmadatiArgsGetDataSetChanges;
use Farmadati\args\Mode;
use Farmadati\Get\GetDataSet;
use Illuminate\Support\Collection;

//echo date("Ymd");
//return;
$service= new ServiceGet();
$dotenv= Dotenv::createImmutable(__DIR__);
$dotenv->load();

$user=$_ENV['FARMA_USER'];
$password= $_ENV['FARMA_PASSWORD'];

//print_r($_ENV['FARMA_USER']);
//return;
$result= $service->GetEnabledDataSet(new FarmadatiArgsGetEnabledSet($user,$password));

//$result=new GetEnabledDataSetResult($service->getResult());
$codice=$result->GetEnabledDataSetResult->SetDatiAbilitati->SetDati->Key;

$schema=$service->GetSchemaDataSet(new FarmadatiArgsGetSchemaDataSet($user,$password,$codice));

$arrayField=[
    'FDI_0001'=> 'cod_product',
    'FDI_0004' => 'description',
    'FDI_0041' => 'ditta',
    'FDI_0008' => 'tipo_product',
    'FDI_9007' => 'prezzo'
];


$fields=implode(',',$arrayField);

/**
 *@type Field
 */
 function cube($n)
{
    return $n;
}

$map= array_map(fn($i) => cube($i), $schema->GetSchemaDataSetResult->Fields->Field);
$map= collect($map);


$getData= $service->GetDataSetChanges(new FarmadatiArgsGetDataSetChanges($user,$password, $codice,'11/08/2022 11:30', Mode::DATA_AGG,1 , 5000));

var_dump($getData);
$getData= $service->GetDataSetChanges(new FarmadatiArgsGetDataSetChanges($user,$password, $codice, '11/08/2022 11:30', Mode::GETRECORDS,1 , 5000));


var_dump($getData);

//return ;


//print_r($getData->getResult()->DescEsito);
$pathZip=$getData->save($getData->getResult()->ByteListFile,'./temp/changes','./temp/changes');
var_dump($pathZip);

return ;
$getData= new GetDataSet(new FarmadatiArgsGetDataSet());
$rows= simplexml_load_file($pathZip);


$sql=$getData->generateFileSql($pathZip,$arrayField, $map);

var_dump($sql);
return ;
/*
function mapXml($item, $fields, $mapFields){
    $values = array();
    foreach($fields as $key => $_){
        $field=$mapFields->firstWhere('Key',$key);
        var_dump($field->Type);
        if($field){
            if($field->Type=='VARCHAR' || $field->Type=='DATE'){
                array_push($values, "'$item[$key]'");
            }else{
                array_push($values, "$item[$key]");
            }
        }else{
            die('Field non trovato');
        }
    }
    $values=implode(",",$values);
    return "($values)\n"; //"($item->FDI_0001,$item->FDI_0004,$item->FDI_0041,$item->FDI_0008)\n";
}


$i=0;
$values=array();
foreach ($rows->RECORD as $item) {
    array_push($values, mapXml((array) $item, $arrayField, $map));
    if($i=1) break;
    var_dump($item);
    $i++;
}
var_dump($arrayField);
var_dump("INSERT INTO farmadati($fields) VALUES \n". implode(",",$values).";");

*/

