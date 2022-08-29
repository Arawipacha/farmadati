<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmadati extends Model
{
    use HasFactory;

    protected $table = "farmadati";
    protected $fillable = [
        "cod_product",
        "description", 
        "ditta", 
        "tipo_product",
        "atc_gmp",
        "cod_principio_attivo",
        "cod_forma_farmaceutica",
        "prezzo",
        "data_prezzo",
        "prezzo2",
        "data_prezzo2",
        "iva",
        "data_variazione",
        "tipo_variazione"
    ];

}
