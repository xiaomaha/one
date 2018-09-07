<?php

namespace App\Admin\Controllers\RegionalActivities;

use App\Models\RegionalActivities\RFIDRead;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Basic\Student;

class RFIDReadController extends Controller
{
    use ModelForm;
    protected $header = '区域活动数据';
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
        return Admin::grid(RFIDRead::class, function (Grid $grid) {
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableRowSelector();
            //判断哪个学校的班级
            $user = Admin::user();
            $grid->model()->where('school_id', $user->school_id)->orderBy('id', 'desc');
            $grid->column('id','ID')->sortable();
            $grid->column('card_no','学生姓名')->sortable()->display(function ($student_card_no){
                $student = Student::where('rfid_id',$student_card_no)->first();
                if(isset($student))
                    return $student->name;
                else
                    return '';
            });
            $grid->column('card_no_hex','卡号十六进制')->sortable();
            $grid->column('from_ip','IP地址')->sortable();
            $grid->column('machine_no','机器号')->sortable();
            $grid->column('serial_no','序列号')->sortable();
            $grid->column('read_at','读取时间')->sortable();

            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('card_no','卡号');    // 按字段模糊筛选
                $filter->like('machine_no','机器号');    // 按字段模糊筛选
                $filter->like('from_ip','IP地址');
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
        return Admin::form(RFIDRead::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
