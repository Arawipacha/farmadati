<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XmlData extends Model
{
  use HasFactory;

  protected $fillable = [
    'id',
    'data_agg_id',
    'source',
    'rows',
    //'xml_files',
    //'sql_files'
  ];

  protected $table='xml';


  public function files(){
    return $this->morphMany(FileData::class, 'fileable');
  }
}
