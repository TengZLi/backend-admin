<?php

namespace App\Common\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Student extends Model
{
    use DefaultDatetimeFormat;
    protected $fillable = ['username', 'password', 'name'];
    protected $hidden = ['password', 'remember_token'];

    const STATUS_NORMAL = 1;
    const STATUS_DISABLE = 0;

    public static function getStatusMap():array
    {
        return [
            self::STATUS_NORMAL => lang('正常'),
            self::STATUS_DISABLE => lang('禁用'),
        ];
    }


}
