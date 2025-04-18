<?php

namespace App\Common\Models;

use Encore\Admin\Auth\Database\HasPermissions;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Teacher extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasPermissions;
    use DefaultDatetimeFormat;
    protected $fillable = ['username', 'password', 'name'];
    protected $hidden = ['password', 'remember_token'];

    const ROLE_TYPE_ORDINARY_TEACHER = 1;
    const ROLE_TYPE_ADMIN = 2;
    const ROLE_TYPE_SUPER_ADMIN = 3;

    const STATUS_NORMAL = 1;
    const STATUS_DISABLE = 0;
    //写死教师角色的roles表ID为3
    const ROLE_TEACHER_ID = 3;
    public static function getStatusMap():array
    {
        return [
            self::STATUS_NORMAL => lang('正常'),
            self::STATUS_DISABLE => lang('禁用'),
        ];
    }

    /**
     * 获取教师列表
     * @return array
     */
    public static function getTeacherMap():array
    {
        return self::get()->pluck('name', 'id')->toArray();
    }

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.users_table'));

        parent::__construct($attributes);
    }


    /**
     *
     * @param Builder $query
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeAdminPermission(Builder  $query)
    {
        return $query->where('role_type', self::ROLE_TYPE_SUPER_ADMIN);
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {

        $default = config('admin.default_avatar') ?: '/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg';

        return admin_asset($default);
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');
        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.user_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }
}
