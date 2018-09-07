<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/1
 * Time: 17:13
 */

namespace App\Admin\Controllers\Basic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Basic\Student;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\StudentCreateRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Repositories\StudentRepository;
use App\Validators\StudentValidator;
use App\Http\Controllers\Controller;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\MessageBag;
use App\Repositories\DictionaryRespository;
use App\Repositories\Basic\SchoolClassRespository;
use App\Models\Basic\SchoolClass;
use App\Models\System\Dictionary;
use App\Exports\StudentExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    use ModelForm;
    protected $header = '学生管理';


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
            $content->description(trans('admin.edit'));
            //$content->body($this->form($id)->edit($id));
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
        return Admin::grid(Student::class, function (Grid $grid) {

            //判断哪个学校的班级
            $user = Admin::user();
            $grid->model()->where('school_id', $user->school_id)->orderBy('id', 'desc');
            $grid->id('编号')->sortable();
            $grid->class_id('班级名称')->sortable()->display(function($classID){
                $model =SchoolClass::find($classID);
                if(isset($model)){
                    return $model->full_name;
                }else{
                    return '';
                }
            });
            $grid->code('学号')->sortable();
            $grid->name('姓名')->sortable();
            $grid->column('sex','性别')->display(function (){
                if($this->sex)
                    return '男';
                else
                    return '女';
            });

            $grid->status('状态')->sortable()->display(function ($status){
                //$dic_list = DictionaryRespository::getDictionaryValueByName('status',$status);
                if(isset($status)){
                    $student = Dictionary::findOrFail($status);
                    if($student!=null){
                        return $student->name;
                    }else{
                        return '';
                    }
                }else{
                    return '';
                }
            });

            $grid->birthday('出生日期')->sortable();
            //$grid->class_type('班级类型')->sortable();

            //$grid->exporter(new StudentExport());
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('code','学号');    // 按字段模糊筛选
                $filter->like('name','姓名');    // 按字段模糊筛选
                $dic_list = DictionaryRespository::getDictionaryByName('sex');
                $filter->equal('sex', '性别')->select($dic_list);
                $dic_list = DictionaryRespository::getDictionaryByName('status');
                $filter->equal('status', '状态')->select($dic_list);
                $filter->equal('class_id', '班级')->select(SchoolClass::all()->pluck('full_name', 'id'));
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
        return Admin::form(Student::class, function (Form $form) {
            $form->hidden('id');
            $form->hidden('school_id');
            $form->row(function ($row) use ($form)
            {
                $row->width(4)->text('name', '姓名')->rules('required', ['required' => '必填项']);
                if(!isset($form->model()->code)){
                    $row->width(4)->display('code', '学号');
                }else{
                    $row->width(4)->text('code', '学号')->rules('required', ['required' => '必填项']);
                }

                $list = SchoolClassRespository::getList();
                $row->width(4)->select('class_id', '所在班级')->options($list);
                $row->width(4)->number('age', '年龄');
                $states = [
                    'on'  => ['value' => 1, 'text' => '男'],
                    'off' => ['value' => 0, 'text' => '女'],
                ];
                $row->width(4)->switch('sex', '性别')->states($states)->default(1);
                $row->width(4)->date('birthday', '出生日期');
                $row->width(4)->text('rfid_id', 'ID卡号');
                $row->width(4)->date('sign_at', '报名日期');
                $row->width(4)->date('register_at', '登记日期');
                $row->width(4)->mobile('telephone', '联系电话')->options(['mask' => '9999 9999 9999']);
                $row->width(4)->text('address', '地址');
                $dic_list = DictionaryRespository::getDictionaryByName('school_class_charge_degree');
                $row->width(4)->select('charge_level', '收费标准')->options($dic_list);
                $dic_list = DictionaryRespository::getDictionaryByName('status');
                $states = [
                    'on'  => ['value' => 1, 'text' => '激活'],
                    'off' => ['value' => 0, 'text' => '禁用'],
                ];
                $row->width(4)->switch('status', '状态')->states($states)->default(1);
                $row->width(4)->text('special_fee', '特殊学费');
                //$dic_list = DictionaryRespository::getDictionaryByName('school_operation_type');
                $row->width(4)->select('school_car_id', '校车')->options(['1' => '默认校车']);
                $row->width(4)->text('guardian', '监护人');
                $row->width(4)->mobile('guardian_phone', '监护人电话')->options(['mask' => '999 9999 9999']);
                $row->width(4)->date('insert_school_at', '插校日期');
                $row->width(4)->date('transfer_garden_at', '转园日期');
                $row->width(4)->date('visit_at', '回访');
                $row->width(4)->date('next_visit_at', '下次回访');
                $row->width(8)->image('pic', '照片');
            },  $form);

            $form->saving(function(Form $form) {
                //Log::info('主键id:'.$form->model()->id);//打印模型id
                if(!isset($form->model()->id)){
                    $user = Admin::user();
                    $form->school_id=$user->school_id;
                    if(Student::where('code',$form->code)->where('school_id',$user->school_id)->value('code')){
                        //错误信息提示
                        $error = new MessageBag(['title'=>'提示','message'=>'学号已存在!'.$form->id]);
                        return back()->withInput()->with(compact('error'));
                    }
                }
            });
        });
    }
}