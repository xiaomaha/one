<?php

namespace App\Admin\Controllers\RegionalActivities;

use App\Models\RegionalActivities\RFIDReader;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Table;

class RFIDReaderController extends Controller
{
    use ModelForm;
    protected $header = 'RFID读卡器管理';

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
        return Admin::grid(RFIDReader::class, function (Grid $grid) {
            //判断哪个学校的班级
            $user = Admin::user();
            $grid->model()->where('school_id', $user->school_id)->orderBy('id', 'desc');
            $grid->column('id','ID')->sortable();
            $grid->column('name','名称')->sortable();
            $grid->column('ip','IP地址')->sortable();
            $grid->column('machine_no','机器号')->sortable();
            $grid->column('order','排序')->sortable();
            $grid->column('memo','备注')->sortable();
            $grid->created_at();

            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('name','名称');    // 按字段模糊筛选
                $filter->like('ip','IP地址');    // 按字段模糊筛选
                $filter->like('machine_no','机器号');
//                $filter->where(function ($query) {
//                //判断哪个学校的班级
//                $user = Admin::user();
//                $query->where('school_id', 'in', "{$user->school_id}");
//            }, '');
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
        return Admin::form(RFIDReader::class, function (Form $form) {
            $form->hidden('id');
            $form->hidden('school_id');
            $form->text('name','名称')->rules('required', ['required' => '必填项']);
            $form->ip('ip','IP地址')->rules('required', ['required' => '必填项']);
            $form->text('machine_no','机器号')->rules('required', ['required' => '必填项']);
            $form->number('order','排序')->default(0);
            $form->textarea('memo','描述信息')->rows(2);

            $form->saving(function(Form $form) {
                if(!isset($form->model()->id)){
                    $user = Admin::user();
                    $form->school_id=$user->school_id;
                    if(RFIDReader::where('ip',$form->ip)->where('school_id',$user->school_id)->value('ip')){
                        //错误信息提示
                        $error = new MessageBag(['title'=>'提示','message'=>'IP已存在!'.$form->id]);
                        return back()->withInput()->with(compact('error'));
                    }
                    if(RFIDReader::where('machine_no',$form->machine_no)->where('school_id',$user->school_id)->value('machine_no')){
                        //错误信息提示
                        $error = new MessageBag(['title'=>'提示','message'=>'机器号已存在!']);
                        return back()->withInput()->with(compact('error'));
                    }
                }
            });
        });
    }
}
