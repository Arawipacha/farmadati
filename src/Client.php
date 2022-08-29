<?php
namespace Farmadati;

use App\Models\XmlData;
use ErrorException;
use Farmadati\args\FarmadatiArgsGetDataSet;
use Farmadati\args\FarmadatiArgsGetDataSetChanges;
use Farmadati\args\FarmadatiArgsGetEnabledSet;
use Farmadati\args\FarmadatiArgsGetSchemaDataSet;
use Farmadati\args\Mode;
use Farmadati\Get\ServiceGet;
use Farmadati\Interfaces\FarmadatiClientInterface;
use Illuminate\Support\Facades\DB;

class Client implements FarmadatiClientInterface {
    
    

    private $Username;
    private $Password;

    private ServiceGet $service;
    private $codice;
    public function __construct($_username, $_password)
    {
        $this->Username=$_username;
        $this->Password= $_password;
        $this->service= new ServiceGet();
    }


    public function statusFullData(): ?array{
        
        $count = $this->getDataSetByMode(Mode::COUNT);
        $date = $this->getDataSetByMode(Mode::DATA_AGG);
        
        $result =[
            'source'=>'GetDataSet',
            'rows'=> $count,
            'data_agg_id'=> $date
        ];
        if($count>0){
            $xmlData= XmlData::where('data_agg_id', $date)->first();
            if(!isset($xmlData)){
                $xmlData= XmlData::create($result);
                $result['id']=$xmlData->id;
            }
        }
        return $result;
    }
    

    /**
     * return count data  by COUNT, RITORNA SOLO LA DATA AGGIORNAMENTO DEL SERVIZIO SOAP
     */
    public function statusPartialData(): ?array{
        
        $date = $this->getDataSetChangesByMode(Mode::DATA_AGG);
        $count = $this->getDataSetChangesByMode(Mode::COUNT, $date);
        $result =[
            'source'=>'GetDataSetChanges',
            'rows'=> $count,
            'data_agg_id'=> $date
        ];
        if($count>0){
            $xmlData= XmlData::where('data_agg_id', $date)->first();
            if(!isset($xmlData)){
                $xmlData= XmlData::create($result);
                $result['id']=$xmlData->id;
            }
        }
        return $result;
    }



    public function extractBinaryZipToXml($xmldata_id): ?array
    {

        
       /* $codiceSetDati= $this->getCodiceSetDati();


        $dataSet=$this->service->GetDataSet(new FarmadatiArgsGetDataSet(
            $this->Username,
            $this->Password,
            $codiceSetDati,
            Mode::GETRECORDS
        ));
        if($dataSet->getResult()->CodEsito!='OK'){
            throw new ErrorException($dataSet->getResult()->DescEsito);
        }
        */

        $xmlData= XmlData::where('data_agg_id', $xmldata_id)->with(['files'=>function($q){
            $q->where('ext','xml');
        }])->first();

        if(!isset($xmlData)){
            return 'row  not found';
        }

        if($xmlData->source=='GetDataSet'){
            if($xmlData->xml_files!=null){
                return json_decode($xmlData->xml_files);
            }
            $binaryZipData= $this->getDataSetByMode(Mode::GETRECORDS);
            
            
            $directory=$this->formatDateDir($xmlData->data_agg_id);
            $paths= $this->service->save($binaryZipData, $directory);
            //$xmlData->xml_files=json_encode($paths);
            $this->fileableSave($xmlData,$paths,'xml');
            //$xmlData->save();
            return $paths->toArray();
        }

        if($xmlData->source=='GetDataSetChanges'){
            if($xmlData->xml_files!=null){
                return $xmlData->xml_files;
            }
            $binaryZipData= $this->getDataSetChangesByMode(Mode::GETRECORDS, $xmlData->data_agg_id);
            //$dataSet= new GetDataSet(new FarmadatiArgsGetDataSet());

            $directory=$this->formatDateDir($xmlData->data_agg_id);

            $paths= $this->service->save($binaryZipData, $directory);

            $this->fileableSave($xmlData,$paths,'xml');

            return $paths->toArray();
        }

        
        return 'source not found';
    }


    public function getFields(): ?array
    {

        $codice= $this->getCodiceSetDati();

        $fields= $this->service->GetSchemaDataSet(new FarmadatiArgsGetSchemaDataSet(
            $this->Username,
            $this->Password,
            $codice
        ));
        
        $fields= $fields->getResult();
        if($fields->CodEsito!=='OK'){
            throw new ErrorException($fields->DescEsito);
        }
        
        //$fields=$fields->Fields;
        return  (array) $fields->Fields->Field;
    }


    

    public function getSqlByXMLPath(string $path,string $table, $schemasToFields): ?string
    {
        $schemaMap = $this->getFields();
        $schemaMap = collect($schemaMap);
        $sql=$this->service->generateFileSql($path,$table, $schemasToFields, $schemaMap);
        return $sql;
    }

    public function getPathsSql(array $data, XmlData $xmlData,string $table, $schemasToFields): ?array{
        

        if(!isset($xmlData)){
            throw new ErrorException("Row not found");
        }
        
        if($xmlData->files->count()==0){
            return [];
        }
        $schemaMap = collect($this->getFields());
        

        $sqlPaths= collect();
        $directory=$this->formatDateDir($xmlData->data_agg_id,'/sql');

        if(isset($data['path'])){
            $sqlPaths=collect($this->service->generateFileSqlV2($data['path'], $directory, $table, $schemasToFields, $schemaMap));
            //array_push($sqlPaths, $values);
        }else{

            $paths= $xmlData->files->filter(function($item){
                $split=explode(".",$item);
                return 'xml'==$split[1];
            });

            foreach ($paths as $path) {
                $values=$this->service->generateFileSqlV2($path, $directory, $table, $schemasToFields, $schemaMap);
                foreach ($values as $value) {
                    $sqlPaths->push($value);    
                }
                //array_push($sqlPaths, $values);
            }
            //$paths=$this->service->generateFileSqlV2($paths,$table, $schemasToFields, $schemaMap);
        }
        //$this->fileableSave($xmlData,$sqlPaths,'sql');
        
        $sqlPaths=$sqlPaths->map(fn($item)=>['path'=>$item,'completed'=>false]);
        return $sqlPaths->toArray();
    }

    public function getOnlySqlFile(array $data,string $table, $schemasToFields){
        $directory=$this->formatDateDir($data['data_agg_id'],'/sql');
        $schemaMap = collect($this->getFields());
        return $sqlPaths=collect($this->service->generateFileSqlV2($data['path'], $directory, $table, $schemasToFields, $schemaMap));
    }



    public function saveDirectoryContentPathSql($dirSql, $xmlData){
        $disk = $this->service->getDisk();
        //$path= $disk->path($directory);
        if(!$disk->exists($dirSql)){
            return false;
        }
        
        $files= collect($disk->allFiles($dirSql));
        $files=$files->sort(SORT_NATURAL);
        foreach ($files as $file) {
            $path=$xmlData->files->firstWhere('path',$file);
            if(!isset($path)){
                var_dump($file);
                $xmlData->files()->create([
                    'path'=>$file,
                    'ext'=> 'sql',
                    'name'=> '',
                    'completed'=>false
                ]);
                
            }

        }

        return  $files;


    }


    public function runSqlPathById($data){
        if(!isset($data['id'])){
            return false;
        }
        if(!isset($data['path'])){
            return false;
        }

        //$xmlData= XmlData::where('id',$data['id'])->with(['files'])->first();
        
        $user=env("DB_USERNAME");
        $pass=env("DB_PASSWORD");
        $tbl=env("DB_DATABASE");
        $path= $this->service->getPath($data['path']);
        
        //$cmd="mysqldump -u $user -p $pass --one-database $tbl < $path";
        //$cmd="mysql --user=\"$user\" --password=\"$pass\" <<-EOSQL use shopcenter;   EOSQL";
        
        /*mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS testing;
    GRANT ALL PRIVILEGES ON \`testing%\`.* TO '$MYSQL_USER'@'%';
EOSQL
*/
        $content= $this->service->getDisk()->get($data['path']);
        //$result = exec($cmd);
        $content= explode('::',$content);
        foreach ($content as $value) {
            $result=DB::statement($value);
            if(!$result){
                throw new ErrorException('Error query');
            }
        }
        
        return $result;
        
    }



    public function savePathsSql($paths, $xmlData){
        $this->fileableSave($xmlData, $paths, 'sql');
    }



    function fileableSave($xmlData, &$paths, $ext){
        $paths=$paths->filter(function($item)use($ext){
            $split=explode(".",$item);
            return $ext==$split[1];
        });
        foreach ($paths as $value) {
            $file=$xmlData->files->firstWhere('path',$value);
            if(!isset($file)){
                $xmlData->files()->create([
                    'path'=>$value,
                    'ext'=> $ext,
                    'name'=> '',
                ]);
            }
        }
    }

    
    /**
     * 
     */
     function getCodiceSetDati(): string{

        if($this->codice!=null){
            return $this->codice;
        }
        $this->service->GetEnabledDataSet(new FarmadatiArgsGetEnabledSet($this->Username,$this->Password));
        
        
        $getEnabled= $this->service->getResult();
        
        if(isset($getEnabled->GetEnabledDataSetResult)){
            $getEnabled= $getEnabled->GetEnabledDataSetResult;
            if($getEnabled->CodEsito=='ERR'){
                throw new ErrorException($getEnabled->DescEsito);
            }
            $this->codice= $getEnabled->SetDatiAbilitati->SetDati->Key;
        }else{
            throw new ErrorException("Instance not found");
        }

        return $this->codice; 

    }




    function getDataSetByMode($mode){
        $codiceSetDati= $this->getCodiceSetDati();

        $dataSet=$this->service->GetDataSet(new FarmadatiArgsGetDataSet(
            $this->Username,
            $this->Password,
            $codiceSetDati,
            $mode,
        ));
        
        
        if($dataSet->getResult()->CodEsito!='OK'){
            throw new ErrorException($dataSet->getResult()->DescEsito);
        }

        if(Mode::GETRECORDS==$mode){
            return $dataSet->getResult()->ByteListFile;
        }
        return $result=$dataSet->getResult()->OutputValue;
    }


    function getDataSetChangesByMode($mode,$dataInstanza='',$PageN = 1, $PagingN = 25000){
        $codiceSetDati= $this->getCodiceSetDati();

        $dataSet=$this->service->GetDataSetChanges(new FarmadatiArgsGetDataSetChanges(
            $this->Username,
            $this->Password,
            $codiceSetDati,
            $dataInstanza,
            $mode,
            
            $PageN, 
            $PagingN
        ));
        
        $dataSet=$dataSet->getResult();
        if($dataSet->CodEsito!='OK'){
            throw new ErrorException($dataSet->DescEsito);
        }

        if(Mode::GETRECORDS==$mode){
            return $dataSet->ByteListFile;
        }
        return $dataSet->OutputValue;
    }

   

    function formatDateDir($data_agg_id, $sql=''){
        $date = str_replace("/","-",$data_agg_id);
        $endDir= env('FARMA_DIR');
        $date=now()->parse($date.':00');
        $date=$date->format('dmYHi');
        return "$endDir/$date$sql";
    }

    

}
