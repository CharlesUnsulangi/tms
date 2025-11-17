<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsDriver extends Model
{
    protected $table = 'ms_driver';
    protected $primaryKey = 'Drv_Id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'rec_usercreated',
        'rec_userupdate',
        'rec_datecreated',
        'rec_dateupdate',
        'rec_status',
        'Drv_Id',
        'Drv_FistName',
        'Drv_LastName',
        'Drv_Addrase',
        'Drv_BPlace',
        'Drv_Bdate',
        'Drv_StartDate',
        'Drv_EndDate',
        'Drv_Phone',
        'Drv_CellPhone',
        'Drv_License',
        'Drv_LicenseExpire',
        'Drv_LastEducation',
        'Drv_SpvId',
        'Drv_Merid',
        'Drv_ChildNo',
        'Drv_VhCode',
        'Drv_DptCode',
        'Drv_SimDate',
        'Drv_Phone_Drt',
        'Drv_Name_Drt',
        'Drv_Instagram',
        'Drv_Facebook',
        'drv_no_rek',
        'drv_bank_rek',
        'drv_email',
        'drv_branch_code',
        'drv_ranking',
        'drv_gender',
        'nik_driver',
    ];

    protected $casts = [
        'rec_datecreated' => 'datetime',
        'rec_dateupdate' => 'datetime',
        'Drv_Bdate' => 'date',
        'Drv_StartDate' => 'date',
        'Drv_EndDate' => 'date',
        'Drv_LicenseExpire' => 'date',
        'Drv_SimDate' => 'date',
        'drv_ranking' => 'integer',
    ];
}
