<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollComponent extends Model
{
    protected $fillable = ['name','type','is_fixed','default_amount'];
}
