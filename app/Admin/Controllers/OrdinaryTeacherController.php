<?php

namespace App\Admin\Controllers;

use App\Admin\Services\AccountService;
use App\Common\Models\OrdinaryTeacher;
use App\Common\Models\Teacher;
use App\Helpers\Helper;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;


class OrdinaryTeacherController extends AdminController
{

    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return lang('教师管理');
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        Grid::init(function (Grid $grid) {
            $grid->disableRowSelector();
            $grid->disableExport();
        });
        $grid = new Grid(new OrdinaryTeacher);
        $grid->model()->orderBy('id', 'desc');
        $grid->column('id', 'ID');
        $grid->column('username', trans('admin.username'));
        $grid->column('name', trans('admin.name'));
        $grid->column('status', lang('status'))->using(Teacher::getStatusMap());
        $grid->column('created_at', trans('admin.created_at'));
        $grid->column('updated_at', trans('admin.updated_at'));


        $grid->filter(function ($filter) {

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('username', trans('admin.username'));

        });

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableDelete();
        });
        $grid->tools(function (Grid\Tools $tools) {
//            $tools->batch(function (Grid\Tools\BatchActions $actions) {
//                $actions->disableDelete();
//            });
            $tools->disableBatchActions();

        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {

        $show = new Show(OrdinaryTeacher::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });

        $show->field('username', trans('admin.username'));
        $show->field('name', trans('admin.name'));
        $show->field('status', lang('status'))->using(Teacher::getStatusMap());

        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        Form::init(function (Form $form) {
            $form->tools(function (Form\Tools $tools) {
                $tools->disableDelete();
            });
        });

        //只能操作普通教师
        $form = new Form(new OrdinaryTeacher);

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->display('id', 'ID');

        $usernameNotice = lang('只能填写字母、数字、下划线，长度2-20个字符');
        $form->text('username', trans('admin.username'))
            ->placeholder($usernameNotice)
            ->creationRules(['required', 'regex:'.AccountService::USERNAME_REGEX], ['regex' => $usernameNotice])
            ->updateRules(['required', 'regex:'.AccountService::USERNAME_REGEX], ['regex' => $usernameNotice]);

        $form->text('name', trans('admin.name'))->rules('required');
        $form->radio('status', lang('status'))->options(Teacher::getStatusMap())->rules('required')->default(Teacher::STATUS_NORMAL);
        //编辑时密码可以为空
        $passwordRequired = $form->isEditing() ? '' : 'required|';
        $form->password('password', trans('admin.password'))
            ->placeholder(lang('密码只能包含字母、数字、下划线，长度6-20个字符且必须包含小写字母、大写字母、数字'))
            ->rules($passwordRequired . 'confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'));

        //设定角色
        $form->multipleSelect('roles', trans('admin.roles'))->setDisplay(false);

        $form->ignore(['password_confirmation']);
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));
        $form->saving(function (Form $form) {
            if ($form->isCreating()) {
                //密码校验
                $checkResult = AccountService::checkPasswordFormat($form->password);
                if (true !== $checkResult) {
                    return back()->withInput()->withErrors([
                        'password' => $checkResult
                    ]);
                }
            }
            //用户名校验
            $checkResult = AccountService::checkUsernameGlobalUnique(new Teacher(),$form->username, (int)($form->model()->id ?? 0));
            if (true !== $checkResult) {
                return back()->withInput()->withErrors([
                    'username' => $checkResult
                ]);
            }

            //角色永远是教师
            $form->input('roles', Teacher::ROLE_TEACHER_ID);

            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
            if (empty($form->password)) {
                $form->password = $form->model()->password;
            }
        });


        return $form;
    }
}
