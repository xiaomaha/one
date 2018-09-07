<?php

namespace App\Admin\Controllers\Book;

use App\Models\Book\Category;

use App\Repositories\BookCategoryRespository;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Grid;
use Illuminate\Support\MessageBag;

class CategoryController extends Controller
{
    use ModelForm;
    protected $header = '图书类型管理';
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

            //$content->body($this->grid());
            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());
                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $book_type_list = BookCategoryRespository::getBookCategory();
                    $form->action(admin_base_path('/book/category'));
                    $form->text('name','类型名称');
                    $form->textarea('description','类型描述信息');
                    $form->number('order','排序序号');
                    $form->select('parent_id','父类名称')->options($book_type_list);
                    $form->hidden('_token')->default(csrf_token());
                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });
        });
    }

    protected function treeView()
    {
        return Category::tree(function (Tree $tree) {
            $tree->disableCreate();
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
     *@return Grid
     */
    protected function grid()
    {
        return Admin::grid(Category::class, function (Grid $grid) {
            //判断哪个学校的班级
            $user = Admin::user();
            $grid->model()->where('school_id', $user->school_i3d)->orderBy('id', 'desc');
            $grid->id('编号')->sortable();
            $grid->name('类型名称')->sortable();
            $grid->description('类型说明')->sortable();
            $grid->parent_id('上级ID')->sortable();
            $grid->order('排序')->sortable();
            $grid->creator('操作人')->sortable();
            $grid->created_at('创建时间')->sortable();

            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('name','类型名称');    // 按字段模糊筛选
                $filter->like('creator','创建人');    // 按字段模糊筛选
                //$filter->equal('status', '状态')->select([0 => '下线', 1 => '上线']);
                $filter->between('created_at','创建时间')->datetime();    // 设置created_at字段的范围筛选
                //$filter->between('updated_at','更新时间')->datetime();    // 设置created_at字段的范围筛选
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
        return Admin::form(Category::class, function (Form $form) {
            $form->hidden('id');
            $form->hidden('school_id');
            $form->text('name','类型名称')->rules('required', ['required' => '必填项']);
            $form->textarea('description','描述信息')->rows(2);
            $form->number('order','排序')->default(0);
            $book_type_list = BookCategoryRespository::getBookCategory();
            $form->select('parent_id','父类名称')->options($book_type_list);

            $form->saving(function(Form $form) {
                if(!isset($form->model()->id)){
                    $user = Admin::user();
                    $form->school_id=$user->school_id;
                    if(Category::where('name',$form->name)->value('id')){
                        //错误信息提示
                        $error = new MessageBag(['title'=>'提示','message'=>'名称已存在!']);
                        return back()->withInput()->with(compact('error'));
                    }
                }
            });
        });
    }
}