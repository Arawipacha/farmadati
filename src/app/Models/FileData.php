<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileData extends Model
{
  use HasFactory;

  protected $fillable = [
    'id',
    'path',
    'ext',
    'fileable',
    'fileable_type',
    'name',
    'completed'
  ];
  
  protected $casts = [
    'completed' => 'boolean',
  ];


  protected $table='files';

  public function fileable(){
    return $this->morphTo();
  }


  
}
