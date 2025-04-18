<?php


namespace App\Admin\Middleware;


use App\Admin\Services\AccountService;
use App\Common\Models\Teacher;
use Encore\Admin\Auth\Permission as Checker;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class CheckUserStatus
{

    public function handle(Request $request, \Closure $next)
    {
        if(Admin::user()->status === Teacher::STATUS_DISABLE){
            Checker::error();
        }
        return $next($request);
    }

}