<?php


namespace App\Admin\scopes;


use App\Common\Models\OrdinaryTeacher;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrdinaryTeacherScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        return $builder->where('role_type', '=', OrdinaryTeacher::ROLE_TYPE_ORDINARY_TEACHER);
    }

}