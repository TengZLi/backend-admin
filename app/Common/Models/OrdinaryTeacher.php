<?php

namespace App\Common\Models;

use App\Admin\scopes\OrdinaryTeacherScope;
use Encore\Admin\Auth\Database\HasPermissions;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class OrdinaryTeacher extends Teacher
{

    protected $table = 'teachers';


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrdinaryTeacherScope());
    }

}
