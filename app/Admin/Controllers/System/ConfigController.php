<?php

namespace App\Admin\Controllers\System;

use App\Models\System\Config;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ConfigController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description(trans('admin.list'));

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description(trans('admin.edit'));

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description(trans('admin.create'));

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Config::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('名称')->sortable();
            $grid->key('Key')->sortable();
            $grid->value('Value')->sortable();
            $grid->description('描述')->sortable();
            $grid->column('是否为系统配置')->display(function (){
                if($this->is_system==0)
                    return '否';
                else
                    return '是';
            });
            $grid->created_at('创建时间');
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('name','名称');    // 按字段模糊筛选
                $filter->like('key','Key');    // 按字段模糊筛选
                $filter->like('value','键值');    // 按字段模糊筛选
                $filter->equal('is_system', '是否为系统配置')->select([0 => '否', 1 => '是']);
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Config::class, function (Form $form) {
            $form->text('name', '名称')->rules('required', ['required' => '必填项']);
            $form->text('key', 'Key')->rules('required', ['required' => '必填项']);
            $form->text('value', 'Value')->rules('required', ['required' => '必填项']);
            $form->text('description', '描述');
            $form->switch('is_system', '系统？');
        });
    }
}
