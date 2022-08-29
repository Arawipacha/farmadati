<?php

namespace Farmadati\Get;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;
use App\Models\Farmadati;

trait traitBinaryFile{
    




    public function getDisk(){
        return Storage::disk('local');
    }
    
   /**
    *
    *
    */ 
    public function save($contentBinary,$directory){
        
        if($contentBinary==null || $contentBinary=='') die('Content file is null');
        $path= env('FARMA_DIR', 'farmadati');
        $disk= Storage::disk('local');
        $pathname="$path/xml_file.zip";
        $disk->put($pathname,$contentBinary);
        $url=$disk->path($pathname);
        
        $to="$directory";
        
        $this->extractZip($url,$to);
        
        $dir=Storage::disk('local')->allFiles($to);

        return collect($dir);//implode(',',$dir);
    }



    public function extractZip($pathZip, $to){

        $zip = new ZipArchive;
        
        if ($zip->open($pathZip) === TRUE) {
            
            $to=Storage::disk('local')->path($to);    
            $zip->extractTo($to);
            
            $zip->close();
            return  $to;//"/APP01.xml";
        } else {
            return 'failed';
        }
    }




    function mapInsertXml($item, $fields, $schemaFields){
        $values = array();
        foreach($fields as $key => $_){
            if($key=='TIPO_VAR' || $key=='DATA_VAR'){
                $value="\"$item[$key]\"";
                array_push($values, $value);
            }else{
                $schema=$schemaFields->firstWhere('Key',$key);
                if($schema){
                    $value=isset($item[$key])? $item[$key]: null;
                    $value=str_replace("\"","'", $value);

                    if($schema->Type=='VARCHAR' || $schema->Type=='DATE'){
                        if($value==''){
                            array_push($values, "NULL");
                        }else{
                            array_push($values, "\"$value\"");
                        }
                    }else{
                        array_push($values, "$value");
                    }
                }else{
                    die('Field non trovato');
                }
            }
        }
        $values=implode(",",$values);
        return "($values)"; //"($item->FDI_0001,$item->FDI_0004,$item->FDI_0041,$item->FDI_0008)\n";
    }


    /**
     * item record
     * primaryKey= cod_product
     * fields custom
     * schemafields soap
     */
    function mapUpdateXml($item, $primaryKey, $fields, $schemaFields){
        $values = array();
        $valuePrimaryKey='';


        foreach($item as $key => $value){
            if(isset($fields[$key])){
                if($key=='TIPO_VAR' || $key=='DATA_VAR'){
                    $value="$fields[$key]=\"$value\"";
                    array_push($values, $value);
                }else{
                    $schema=$schemaFields->firstWhere('Key',$key);
                    
                    if($schema){
                        
                            //$value=isset($item[$key])? $item[$key]: null;
                            $value=str_replace("\"","'", $value);
                            //column1 = value1, column2 = value2, ...
                            if($schema->Type=='VARCHAR' || $schema->Type=='DATE'){
                                    
                                        if($value==''){
                                            $value ="$fields[$key]=NULL";
                                        }else{
                                            $value="$fields[$key]=\"$value\"";
                                        }

                                        if($fields[$key]!=$primaryKey){
                                            array_push($values, $value);
                                        }else{
                                            $valuePrimaryKey=$value;
                                        }
                                    
                            }else{
                                array_push($values, "$fields[$key]=$value");
                            }
                        
                    }else{
                        die('Field non trovato');
                    }
                }
            }
        }
        $values=implode(",",$values);
        return "$values where $valuePrimaryKey;"; //"($item->FDI_0001,$item->FDI_0004,$item->FDI_0041,$item->FDI_0008)\n";
    }
    
    /**
    *@param string $pathXml location source file xml
    *@param array $_arrayFields bridge schema field to column table
    *@param Collection $schemaDataSet list schema dataset soap
    *@return string
    */
    public function generateFileSql($pathXml, $table, $arrayFields, $schemaDataSet){
        //$rows=file_get_contents($pathXml); 
        $rows= Storage::disk('local')->get($pathXml);
        $rows= simplexml_load_string($rows);
        $fields=implode(',',$arrayFields);

        $i=0;
        $valuesInsert=array();
        $valuesUpdate=array();
        foreach ($rows->RECORD as $item) {
            array_push($valuesInsert, $this->mapXml((array) $item, $arrayFields, $schemaDataSet));
            //if($i=120) break;
            $i++;
        }
        $valuesInsert= "INSERT INTO $table($fields) VALUES \n". implode(",\n",$valuesInsert).";";
        $url=Storage::disk('local')->put('temp/18082022/tabel.sql',$valuesInsert);

        return $url;
        
    }



    public function getPath($path){
        return $this->getDisk()->path($path);
    }


 




    public function generateFileSqlV2($pathXml,$directory, $table, $arrayFields, $schemaDataSet)
    {
        //$rows=file_get_contents($pathXml); 
        $disk=$this->getDisk();
        $rows = $disk->get($pathXml);
        $rows = simplexml_load_string($rows);
        $fields = implode(',', $arrayFields);
        //$path= env('FARMA_DIR', 'farmadati');
        $i = 0;
        $valuesInsert = array();
        $valuesUpdate = array();
        $paths= array();
        $aggiornato=0;
        foreach ($rows->RECORD as $item) {            
            // print_r(($item->TIPO_VAR));

            if (isset($item->TIPO_VAR)) {
                //$this->saveDati($item);
                $aggiornato = 1;
                if($item->TIPO_VAR=='I'){
                    array_push($valuesInsert, $this->mapInsertXml((array) $item, $arrayFields, $schemaDataSet));
                }else{
                    $queryUpdate="UPDATE $table SET ".$this->mapUpdateXml((array) $item,'cod_product', $arrayFields, $schemaDataSet);
                    array_push($valuesUpdate, $queryUpdate);
                }
            }else
            // else {  
            //    if($i=2){
            //         break;
            //     } 
            //     $i++;
            // }
            array_push($valuesInsert, $this->mapInsertXml((array) $item, $arrayFields, $schemaDataSet));
        }
        

        if ($aggiornato == 1) {
            //$valuesUpdate = "UPDATE INTO $table($fields) VALUES \n" . implode(",\n", $valuesUpdate) . ";";
            if(count($valuesUpdate)>0){
                $path="$directory/update.sql";
                $valuesUpdate= implode("::", $valuesUpdate);
                $disk->put($path, $valuesUpdate);
                array_push($paths,$path);
            }
            if(count($valuesInsert)>0){
                $valuesInsert = "INSERT INTO $table($fields) VALUES \n" . implode(",\n", $valuesInsert) . ";";
                $pathNew="$directory/insert.sql";
                $disk->put($pathNew, $valuesInsert);
                array_push($paths,$pathNew);
            }
            //Storage::disk('local')->put($pathNew, $valuesInsert);
        } else {
            // print_r($valuesInsert);
            $p = array_chunk($valuesInsert, 100000);

            foreach ($p as $item) {
                // var_dump($item[4]);
                $valuesInsert = "INSERT INTO $table($fields) VALUES \n" . implode(",\n", $item) . ";";
                $pathNew="$directory/tabel" . $i . '.sql';
                Storage::disk('local')->put($pathNew, $valuesInsert);
                array_push($paths,$pathNew);
                $i++;
            }

            //  $valuesInsert = "INSERT INTO $table($fields) VALUES \n" . implode(",\n", $valuesInsert) . ";";
            //  $url = Storage::disk('local')->put('temp/18082022/tabel.sql', $valuesInsert);      
            
        }
        return $paths;
    }


    function mapItemUpdate($item, $fields, $schemaDataSet){
        //$valuesUpdate = "UPDATE INTO $table($fields) VALUES \n" . implode(",\n", $valuesUpdate) . ";";
    }

    public function saveDati($dati){
        if ($dati->TIPO_VAR == "A") {
            Farmadati::where("cod_product", $dati->FDI_0001)->update(['tipo_variazione' => "A"]);
        } else {
            if ($dati->TIPO_VAR == "V") {
                $farma = Farmadati::where("cod_product", $dati->FDI_0001)->first();
            } else {
                $farma = new Farmadati();
            }
            $farma->data_variazione  =  $dati['DATA_VAR'];
            $farma->tipo_variazione  = $dati->TIPO_VAR;
            $farma->cod_product  =  $dati->FDI_0001;
            if (isset($dati->FDI_0004))
                $farma->description  = $dati->FDI_0004;
            if (isset($dati->FDI_0041))
                $farma->ditta  = $dati->FDI_0041;
            if (isset($dati->FDI_0008))
                $farma->tipo_product  = $dati->FDI_0008;
            if (isset($dati->FDI_0329))
                $farma->atc_gmp  = $dati->FDI_0329;
            if (isset($dati->FDI_0339))
                $farma->cod_principio_attivo  = $dati->FDI_0339;
            if (isset($dati->FDI_0370))
                $farma->cod_forma_farmaceutica  = $dati->FDI_0370;
            if (isset($dati->FDI_9007))
                $farma->prezzo  = $dati->FDI_9007;
            if (isset($dati->FDI_9144))
                $farma->data_prezzo =  $dati->FDI_9144;
            if (isset($dati->FDI_9145))
                $farma->prezzo2  = $dati->FDI_9145;
            if (isset($dati->FDI_9146))
                $farma->data_prezzo2  =  $dati->FDI_9146;
            if (isset($dati->FDI_0248))
                $farma->iva   = $dati->FDI_0248;
            $farma->save();
            echo "variato";
        }
    }

}
