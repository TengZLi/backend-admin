<?php


namespace App\Admin\Services;


use App\Common\Models\Student;
use App\Common\Models\Teacher;

class AccountService
{
    const USERNAME_REGEX = '/^[a-zA-Z0-9_]{2,20}$/';
    const PASSWORD_REGEX = '/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z0-9_]{6,20}$/';
    public static function checkUsernameGlobalUnique($model, $username, $id = 0)
    {
        $where = [
            ['username', '=', $username],
            ['id', '<>', $id],
        ];
        if($model->where($where)->exists()){
            return lang('用户名已存在');
        }
        return true;
    }

    public static function checkPasswordFormat($password)
    {
        if(!preg_match(self::PASSWORD_REGEX,$password)){
            return lang('密码只能包含字母、数字、下划线，长度6-20个字符且必须包含小写字母、大写字母、数字');
        }
        return true;
    }


}