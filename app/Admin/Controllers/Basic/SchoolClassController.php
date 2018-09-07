<?php

namespace App\Admin\Controllers\Basic;

use App\Models\Basic\SchoolClass;
use App\Models\Basic\School;
use App\Models\Basic\User;
use App\Models\System\Dictionary;
use App\Repositories\DictionaryRespository;
use Encore\Admin\AdminServiceProvider;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class SchoolClassController extends Controller
{
    use ModelForm;
    protected $header = '班级管理';
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->header);
            $content->description('');

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

            $content->header($this->header);
            $content->description('编辑');

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
            $content->description('创建');

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
        return Admin::grid(SchoolClass::class, function (Grid $grid) {
            //判断哪个学校的班级
            $user = Admin::user();
            $grid->model()->where('school_id', $user->school_id)->orderBy('id', 'desc');
            $grid->column('name','班级名称')->sortable();
            $grid->column('class_type','班级类型')->sortable()->display(function($classType) {
                $model= Dictionary::find($classType);
                if($model!=null)
                    return $model->name;
                else
                    return '';
                //return DictionaryRespository::getDictionaryValueByName('school_class_type',$classType);
            });
            $grid->column('class_num','多少班')->sortable()->display(function($classNum) {
                $model= Dictionary::find($classNum);
                if($model!=null)
                    return $model->name;
                else
                    return '';
                //return DictionaryRespository::getDictionaryValueByName('school_class_num',$classNum);
            });
            $grid->column('headmaster_id','班主任')->sortable()->display(function($userId) {
                $model= User::find($userId);
                if($model!=null)
                    return $model->name;
                else
                    return '';
            });
            $grid->column('student_amount','班级人数')->sortable();
            $grid->column('charge_degree','收费标准')->sortable()->display(function($chargeDegree) {
                $model= Dictionary::find($chargeDegree);
                if($model!=null)
                    return $model->name;
                else
                    return '';
                //return DictionaryRespository::getDictionaryValueByName('school_class_charge_degree',$classNum);
            });
            $grid->column('是虚拟班')->display(function ($fictitious){
                return $fictitious ? '是' : '否';
            });
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('name','班级名称');    // 按字段模糊筛选
                $dic_list = DictionaryRespository::getDictionaryByName('school_class_type');
                $filter->equal('class_type','班级类型')->select($dic_list);
                $dic_list = DictionaryRespository::getDictionaryByName('school_class_num');
                $filter->equal('class_num','多少班')->select($dic_list);
                $dic_list = DictionaryRespository::getDictionaryByName('school_class_charge_degree');
                $filter->equal('charge_degree','收费标准')->select($dic_list);
                $filter->equal('student_amount','班级人数');    // 按字段模糊筛选
                $filter->between('enter_date','入园时间')->datetime();
                $filter->between('exit_date','出园时间')->datetime();
                $filter->equal('is_fictitious', '是虚拟班')->select([0 => '不是', 1 => '是']);
                //$book_type_list = BookCategoryRespository::getBookCategory();
                //$filter->equal('book_category_id','图书类型')->select($book_type_list);    // 按给定选项匹配查找
                //$filter->equal('words_count','字数');    // 按字段模糊筛选
                //$filter->equal('page_num','页数');    // 按字段模糊筛选
                //$filter->like('creator','创建人');    // 按字段模糊筛选
                //$filter->between('created_at','创建时间')->datetime();    // 设置created_at字段的范围筛选
                //$filter->between('updated_at','更新时间')->datetime();    // 设置created_at字段的范围筛选
                //$filter->equal('status', '状态')->select([0 => '下线', 1 => '上线']);
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
        return Admin::form(SchoolClass::class, function (Form $form) {
            $form->hidden('school_id');
            $form->hidden('full_name');

            $form->row(function ($row) use ($form)
            {
                $row->width(4)->text('name', '班级名称')->rules('required', ['required' => '必填项']);
                $dic_list = DictionaryRespository::getDictionaryByName('school_class_type');
                $row->width(4)->select('class_type', '班级类型')->options($dic_list)->rules('required', ['required' => '必填项']);
                $dic_list = DictionaryRespository::getDictionaryByName('school_class_num');
                $row->width(4)->select('class_num', '多少班')->options($dic_list)->rules('required', ['required' => '必填项']);
                $dic_list = DictionaryRespository::getDictionaryByName('school_class_charge_degree');
                $row->width(4)->select('charge_degree', '收费标准')->options($dic_list);
                $row->width(4)->number('student_amount', '班级人数');
                $row->width(4)->date('enter_date', '入园时间');
                $row->width(4)->date('exit_date', '出园时间');
                $row->width(4)->select('headmaster_id', '班主任')->options(Administrator::all()->pluck('name', 'id'));
                $row->width(4)->switch('is_fictitious', '是虚拟班？')->default(0);
                $row->width(12)->textarea('memo', '备注');
            },  $form);

            $form->saving(function(Form $form) {
                $user = Admin::user();
                $form->school_id=$user->school_id;
                //获得类型名称
                $result = Dictionary::find($form->class_type);
                //获得多少班名称
                $result1 = Dictionary::find($form->class_num);
                //组合全名：班级名称+班级类型+多少班
                //如2018+小班+01
                $form->full_name=$form->name.$result->name.$result1->name;
/*                if($form->model()->email && SchoolClass::where('email',$form->email)->value('id')){
                    //错误信息提示
                    $error = new MessageBag(['title'=>'提示','message'=>'邮箱已存在!']);
                    return back()->withInput()->with(compact('error'));
                }*/
            });
        });
    }
}
