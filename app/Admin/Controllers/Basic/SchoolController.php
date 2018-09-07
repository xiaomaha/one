<?php

namespace App\Admin\Controllers\Basic;

use App\Models\Basic\School;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\MessageBag;
use App\Repositories\DictionaryRespository;

class SchoolController extends Controller
{
    use ModelForm;
    protected $header = '学校管理';

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
/*            $content->row(function (Row $row) {
                $row->column(4, $this->grid());
                $row->column(8, function (Column $column) {

                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('basic/school'));
                    $form->text('name', '学校名称')->rules('required', ['required' => '必填项']);
                    $form->text('short_code', '学校代码');
                    $dic_list = DictionaryRespository::getDictionaryByName('school_operation_type');
                    $form->select('operation_type', '直营加盟')->options($dic_list);
                    $dic_list = DictionaryRespository::getDictionaryByName('school_ownership');
                    $form->select('belong_type', '所有制')->options($dic_list);
                    $dic_list = DictionaryRespository::getDictionaryByName('school_class_level');
                    $form->select('degree', '类级')->options($dic_list);
                    $dic_list = DictionaryRespository::getDictionaryByName('school_example_degree');
                    $form->select('example', '示范等级')->options($dic_list);
                    $form->number('teacher_student_rate', '师生比例');
                    $form->mobile('telephone', '电话')->options(['mask' => '9999 9999 9999']);
                    $form->text('address', '地址');
                    $form->text('contactor', '联系人');
                    $form->mobile('mobilephone', '联系人电话')->options(['mask' => '999 9999 9999']);
                    $form->email('email', '电子邮件');
                    $form->url('website', '网址');
                    $form->image('logo', 'logo图');
                    $form->switch('is_active', '可用？')->default(1);
                    $form->hidden('_token')->default(csrf_token());
                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });*/
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
            $content->description('description');

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
        return Admin::grid(School::class, function (Grid $grid) {

            //$grid->id('ID')->sortable();
            $grid->column('name','学校名称')->sortable();
            $grid->column('short_code','学校代码');
//            $grid->column('operation_type','直营加盟');
//            $grid->column('belong_type','所有制');
//            $grid->column('degree','类级');
//            $grid->column('example','示范');
//            $grid->column('excellent_degree','优质');
            $grid->column('teacher_student_rate','师生比例');
            $grid->column('contactor','联系人');
            $grid->column('mobilephone','电话');
            $grid->column('address','地址');
            /*            $grid->column('可用')->display(function (){
                            if($this->is_active==0)
                                return '否';
                            else
                                return '是';
                        });*/
            $grid->created_at();
            //$grid->disableActions();
            $grid->disableExport();
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('name','学校名称');    // 按字段模糊筛选
            });
            // 没有`create`权限的角色不显示创建按钮
            if (!Admin::user()->can('all_create_rights')) {
                $grid->disableCreateButton();
                //禁止批量删除
                $grid->disableRowSelector();
            }
            $grid->actions(function ($actions) {
                // 没有`delete`权限的角色不显示删除按钮
                if (!Admin::user()->can('all_delete_rights')) {
                    $actions->disableDelete();
                }
                // 没有`modify`权限的角色不显示修改按钮
                if (!Admin::user()->can('all_modify_rights')) {
                    $actions->disableEdit();
                }
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
        return Admin::form(School::class, function (Form $form) {
            $form->row(function ($row) use ($form)
            {
                $row->width(4)->text('name', '学校名称')->rules('required', ['required' => '必填项']);
                $row->width(4)->text('short_code', '学校代码');
                $dic_list = DictionaryRespository::getDictionaryByName('school_operation_type');
                $row->width(4)->select('operation_type', '直营加盟')->options($dic_list);
                $row->width(4)->number('teacher_student_rate', '师生比例');
                $row->width(4)->mobile('telephone', '电话')->options(['mask' => '9999 9999 9999']);
                $row->width(4)->text('address', '地址');
                $row->width(4)->text('contactor', '联系人');
                $row->width(4)->mobile('mobilephone', '联系人电话')->options(['mask' => '999 9999 9999']);
                $row->width(4)->email('email', '电子邮件')->default('school@qq.com');
                $row->width(4)->url('website', '网址')->default('http://www.abc.com')
                    ->help('eg: http://www.abc.com');
                $row->width(4)->switch('is_active', '可用？')->default(1);
                $row->width(4)->image('logo', 'logo图')->uniqueName();
            },  $form);

            $form->saving(function(Form $form) {
                if(empty($form->model()->id)){
                    if(School::where('name',$form->name)->value('id')){
                        //错误信息提示
                        $error = new MessageBag(['title'=>'提示','message'=>'学校已存在!'.$form->id]);
                        return back()->withInput()->with(compact('error'));
                    }
                }
            });
        });
    }
}
