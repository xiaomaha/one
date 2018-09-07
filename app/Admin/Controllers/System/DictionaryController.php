<?php

namespace App\Admin\Controllers\System;

use App\Models\System\Dictionary;
use App\Repositories\DictionaryRespository;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Tree;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Widgets\Box;

class DictionaryController extends Controller
{
    use ModelForm;
    protected $header = '字典管理';
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header);
            $content->description(trans('admin.list'));
            //$content->body(Dictionary::tree());
            //$content->body($this->grid());
            $content->row(function (Row $row) {
                $row->column(4, $this->treeView()->render());
                $row->column(8, $this->grid());
                /*                $row->column(8, function (Column $column) {
                                    $form = new \Encore\Admin\Widgets\Form();
                                    $form->action(admin_base_path('/dictionary'));
                                    $form->text('name', '字典名称')->rules('required', ['required' => '必填项']);
                                    $form->text('value', '字典值');
                                    $form->text('dic_name', '标识名称');
                                    $dic_list = DictionaryRespository::getDictionary();
                                    $form->select('parent_id', '所属上级')->options($dic_list);
                                    $form->number('order', '排序');
                                    $form->text('desc', '描述');
                                    $form->switch('is_system', '系统？');
                                    $form->switch('is_leaf', '叶节点？');
                                    $form->switch('is_active', '可用？')->default(1);
                                    $form->hidden('_token')->default(csrf_token());
                                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                                });*/
            });
        });
    }

    protected function treeView()
    {
        return Dictionary::tree(function (Tree $tree) {
            //$tree->disableCreate();
            $tree->query(function ($model) {
                return $model->where('parent_id', 0);
            });
            return $tree;
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

            $content->header($this->header);
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

            $content->header($this->header);
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
        return Admin::grid(Dictionary::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('字典名称')->sortable();
            $grid->value('字典值')->sortable();
            $grid->order('排序')->sortable();
            $grid->dic_name('标识名称')->sortable();
            $grid->column('所属上级')->display(function (){
                if($this->parent_id!=0)
                {
                    $model = Dictionary::FindOrFail($this->parent_id);
                    return $model->name;
                }else{
                    return '';
                }
            });
            $grid->column('系统配置')->display(function (){
                if($this->is_system==0)
                    return '否';
                else
                    return '是';
            });
            $grid->column('叶节点')->display(function (){
                if($this->is_leaf==0)
                    return '否';
                else
                    return '是';
            });
            $grid->column('可用')->display(function (){
                if($this->is_active==0)
                    return '否';
                else
                    return '是';
            });
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('name','字典名称');    // 按字段模糊筛选
                $filter->like('dic_name','标识名称');    // 按字段模糊筛选
                $filter->like('value','字典值');    // 按字段模糊筛选
                $filter->equal('is_system', '是否为系统配置')->select([0 => '否', 1 => '是']);
                $filter->equal('is_leaf', '是否叶节点')->select([0 => '否', 1 => '是']);
                $filter->equal('is_active', '是否可用')->select([0 => '否', 1 => '是']);
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
        return Admin::form(Dictionary::class, function (Form $form) {
            $form->text('name', '字典名称')->rules('required', ['required' => '必填项']);
            $form->text('value', '字典值');
            $form->text('dic_name', '标识名称');
            $dic_list = DictionaryRespository::getDictionary();
            $form->select('parent_id', '所属上级')->options($dic_list)->rules(
                'required', ['required' => '必填项']);
            $form->number('order', '排序');
            $form->text('desc', '描述');
            $form->switch('is_system', '系统？');
            $form->switch('is_leaf', '叶节点？');
            $form->switch('is_active', '可用？')->default(1);
        });
    }
}
