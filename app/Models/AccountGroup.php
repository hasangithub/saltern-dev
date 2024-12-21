<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    public function subAccountGroups()
    {
        return $this->hasMany(SubAccountGroup::class);
    }

}
