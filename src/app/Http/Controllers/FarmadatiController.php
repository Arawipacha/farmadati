<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Farmadati\Interfaces\FarmadatiClientInterface;
use App\Models\XmlData;
use Farmadati\Client;
use Validator;


class FarmadatiController extends Controller
{
    private Client $client;
    
    public function __construct(FarmadatiClientInterface $_client)
    {
    $this->client = $_client;
    
    }

    public function welcome(){
        $filesStr= $this->client->getFields();
        //var_dump($filesStr);
        $path='temp/18082022/APP01.xml';//Storage::disk('local')->path('temp/APP01.xml');
        $table='products';
        $fieldsTable= [
            'FDI_0001'=> 'cod_product',
            'FDI_0004'=> 'description',
            'FDI_0041'=> 'ditta',
            
            'FDI_0008'=> 'tipo_product',
            'FDI_0329'=> 'atc_gmp',
            'FDI_0339'=> 'cod_principio_attivo',
            'FDI_0370'=> 'cod_forma_farmaceutica',
            'FDI_9007'=> 'prezzo',
            'FDI_9144'=> 'data_prezzo',
            'FDI_9145'=> 'prezzo2',
            'FDI_9146'=> 'data_prezzo2',
            'FDI_0248'=> 'iva',
        ];
       //var_dump($filesStr);
        
        //$filesStr= $this->client->getSqlByXMLPath($path,$table,$fieldsTable);

        //$dirExtract= 
        //$filesStr= $this->client->extractBinaryZipToXml('/temp');
        
        return response()->json($filesStr);
        //var_dump($filesStr);
        //return view('welcome');
    }


/*
    public function getBinaryToXml(){
        $filesStr= $this->client->getFullDataBinaryPath('.');

        response()->json(
            $filesStr
        );
    }


    public function getToSql(){
        $filesStr= $this->client->getFullDataBinaryPath('.');

        response()->json(
            $filesStr
        );
    }

*/

/**
 * ritorna tutte  rows della tabela xml
 */
    public function getAll(){

        $xmlData= XmlData::with(['files:id,path,ext,fileable_id,completed'])->where('id','>',10)->get();
        return $xmlData;
    }


    /**
     * ritorna la metadata delle colonne xml con prefisso FDI...
     */
    public function getFields(){
        return $this->client->getFields();
    }

    /**
     * ritorna i rows da aggiornare con la ultima data
     */
    public function statusFullData(){
       /* $xmlData = XmlData::first();
        if($xmlData){
            return $this->client->extractBinaryZipToXml($xmlData->data_agg_id);
        }
        */
        return $this->client->statusFullData();

        //return 'nullo';
    }

    /**
     * ritorna i rows da aggiornare con la ultima data
     */
    public function statusPartialData(){
        return $this->client->statusPartialData();
    }


    public function extractZipToXmlByDataAggId(Request $request){
        $data=$request->all();
        $validator= Validator::make($data,[
            'data_agg_id'=> 'required',
          ]);
          
          if ($validator->fails()) {
            return response()->json($validator->errors());
          }

        return $this->client->extractBinaryZipToXml($data['data_agg_id']);
    }



    public function getPathsGeneratorSqlByDataAggId(Request $request){
        $data= $request->all();
        $data=$request->all();
        $validator= Validator::make($data,[
            'data_agg_id'=> 'required',
            'path'=> 'required'
          ]);
          
          if ($validator->fails()) {
            return response()->json($validator->errors());
          }

        return $this->generatorSqlFiles($data);
    }
    



    public function runSqlPath(Request $request){
        $data= $request->all();
        $validator= Validator::make($data,[
            'data_agg_id'=> 'required',
            'path'=> 'required'
          ]);
          
          if ($validator->fails()) {
            return response()->json($validator->errors());
          }
        return $this->client->runSqlPathById($data);
        
    }
    

    function generatorSqlFiles($data){
        $table='farmadati';
        $fieldsTable= [
            'FDI_0001'=> 'cod_product',
            'FDI_0004'=> 'description',
            'FDI_0041'=> 'ditta',
            
            'FDI_0008'=> 'tipo_product',
            'FDI_0329'=> 'atc_gmp',
            'FDI_0339'=> 'cod_principio_attivo',
            'FDI_0370'=> 'cod_forma_farmaceutica',
            'FDI_9007'=> 'prezzo',
            'FDI_9144'=> 'data_prezzo',
            'FDI_9145'=> 'prezzo2',
            'FDI_9146'=> 'data_prezzo2',
            'FDI_0248'=> 'iva',
            'DATA_VAR'=> 'data_variazione',
            'TIPO_VAR'=> 'tipo_variazione'
        ];

        $xmlData=XmlData::where('data_agg_id', $data['data_agg_id'])->with(['files'])->first();
        return $this->client->getPathsSql($data,$xmlData, $table, $fieldsTable);
    }


    public function refreshDataChangesDirectDatabase(){
        $partialData=$this->statusPartialData();
        if(isset($partialData['rows']) && $partialData['rows']>0){
            $pathsXml= $this->client->extractBinaryZipToXml($partialData['data_agg_id']);
            return $pathsXml;
            if(is_array($pathsXml)){
                $data = array();
                $pathsSql=array();
                foreach ($pathsXml as  $value) {
                    $data['data_agg_id']=$partialData['data_agg_id'];
                    $data['path']=$value;
                    $paths = $this->generatorSqlFiles($data);
                    array_push($pathsSql, $paths);
                }

                $completeds= array();
                foreach ($pathsSql as  $value) {
                    $data['data_agg_id']=$partialData['data_agg_id'];
                    $data['path']=$value;
                    $complete=$this->client->runSqlPathById($data);
                    array_push($completeds, ['path'=> $value, 'completed'=> $complete]);
                }
                return $completeds;
            }
        }
        return $partialData;
    }
}