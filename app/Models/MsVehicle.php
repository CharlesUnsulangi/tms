<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsVehicle extends Model
{
    use HasFactory;

    protected $table = 'ms_vehicle'; // sesuaikan dengan nama tabel armada
    protected $primaryKey = 'id'; // sesuaikan dengan primary key tabel
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'no_polisi',
        'jenis',
        'merk',
        'tahun'
    ];
}
