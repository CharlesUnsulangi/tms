<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsTmsDriver extends Model
{
    protected $table = 'ms_tms_driver';
    protected $primaryKey = 'ms_tms_driver_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'no_hp',
        'alamat'
    ];
}
