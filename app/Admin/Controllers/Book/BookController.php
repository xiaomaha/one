<?php

namespace App\Admin\Controllers\Book;

use App\Models\Book\Book;
use App\Models\Book\Category;
use App\Repositories\BookCategoryRespository;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;
use App\Services\Book\BookService;
use App\Http\Requests;
use Illuminate\Http\Request;

class BookController extends Controller
{
    use ModelForm;
    protected $header = '图书管理';
    /** @var bookService */
    protected $bookService;
    // 对应的模型
    protected $model;

    /**
     * OrderController constructor.
     * @param OrderService $orderService
     */
    public function __construct(BookService $bookService)
    {
        $this->model = 'App\Models\Book\Book';
        $this->bookService = $bookService;
    }

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
        return Admin::grid(Book::class, function (Grid $grid) {
            //判断哪个学校的班级
            $user = Admin::user();
            $grid->model()->where('school_id', $user->school_id)->orderBy('id', 'desc');
            $grid->id('编号')->sortable();
            $grid->name('图书名称')->sortable();
            $grid->isbn('isbn编号')->sortable();
            //$grid->author('作者')->sortable();
            //$grid->publisher('出版社')->sortable();
            //$grid->book_category_id('类型')->sortable();
            $grid->column('类型')->display(function (){
                $model = Category::FindOrFail($this->book_category_id);
                if($model==null)
                    return '';
                else
                    return $model->name;
            });
            $grid->publish_at('出版时间')->sortable();
            $grid->purchase_at('购买时间')->sortable();
            //$grid->purchase_num('购买数量');
            $grid->shelves_no('书架编号')->sortable();
            $grid->stocks('库存数')->sortable();
//            $grid->keywords('关键词')->sortable();
//            $grid->words_count('字数')->sortable();
//            $grid->page_num('页数')->sortable();
//            $grid->price('价格')->sortable();
//            $grid->first_letter('首字母')->sortable();
            /*            $grid->abstract('摘要')->display(function($abstract){
                            //判断是否为空值。
                            if($abstract){
                                //有数据则按代码输出显示字符数。
                                return str_limit($abstract, 10, '...');
                            }else{
                                //无数据则输出指定字符。
                                return "";
                            }});*/
//            $grid->creator('创建人')->sortable();
//            $grid->created_at('创建时间')->sortable();
//            $grid->updated_at('更新时间')->sortable();
            //$grid->column('type','类型？')->display(function ($type) {
//            return $type == 1 ? '111' : '222';
//        });

            $grid->filter(function ($filter) {
                $filter->disableIdFilter();   // 去掉默认的id过滤器
                $filter->like('name','图书名');    // 按字段模糊筛选
                $filter->like('isbn','isbn编号');    // 按字段模糊筛选
                $book_type_list = BookCategoryRespository::getBookCategory();
                $filter->equal('book_category_id','图书类型')->select($book_type_list);    // 按给定选项匹配查找
                $filter->like('author','作者');    // 按字段模糊筛选
                $filter->like('publisher','出版社');    // 按字段模糊筛选
                $filter->like('publish_at','出版时间');    // 按字段模糊筛选
                $filter->like('purchase_at','购买时间');    // 按字段模糊筛选
                $filter->like('shelves_no','书架位置');    // 按字段模糊筛选
                $filter->like('stocks','库存数');    // 按字段模糊筛选
                $filter->like('keywords','关键词');    // 按字段模糊筛选
                $filter->like('abstract','摘要');    // 按字段模糊筛选
                $filter->equal('first_letter','首字母');    // 按字段模糊筛选
                //$filter->equal('words_count','字数');    // 按字段模糊筛选
                //$filter->equal('page_num','页数');    // 按字段模糊筛选
                //$filter->like('creator','创建人');    // 按字段模糊筛选
                //$filter->between('created_at','创建时间')->datetime();    // 设置created_at字段的范围筛选
                //$filter->between('updated_at','更新时间')->datetime();    // 设置created_at字段的范围筛选
                //$filter->equal('status', '状态')->select([0 => '下线', 1 => '上线']);
            });

            //关闭默认行操作
            /*            $grid->actions(function ($actions) {
                            //关闭删除
                            $actions->disableDelete();
                            //关闭编辑
                            $actions->disableEdit();
                            //自定义操作按钮
                            $actions->append('<button type="button" class="btn btn-danger noShow" data-id="' . $actions->getKey() . '"  >隐藏</button>');
                        });*/
            //关闭批量删除
            /*            $grid->tools(function ($tools) {
                            //关闭批量删除
                            $tools->batch(function ($batch) {
                                $batch->disableDelete();
                            });
                        });*/
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Book::class, function (Form $form) {
            $form->hidden('id');
            $form->hidden('school_id');
            $form->tab('必填项', function ($form) {

                //$form->display('id', '编号');
                $form->text('name', '图书名称')->rules('required', ['required' => '必填项']);
                $form->text('isbn', 'isbn编号')->rules('required', ['required' => '必填项']);
                $book_type_list = BookCategoryRespository::getBookCategory();
                $form->select('book_category_id', '类型')->options($book_type_list)->rules(
                    'required', ['required' => '必填项']);
                $form->text('shelves_no', '书架位置')->rules('required', ['required' => '必填项']);
                $form->text('first_letter', '首字母')->rules('required|min:1')->help('图书名字首字母');

            })->tab('选填项', function ($form) {
                $form->text('author', '作者');
                $form->text('publisher', '出版社');
                $form->date('publish_at', '出版时间')->format('YYYY-MM-DD');
                $form->date('purchase_at', '购买时间')->format('YYYY-MM-DD');
                $form->number('purchase_num', '购买数量')->default(1);
                $form->number('stocks', '库存数');
                $form->number('words_count', '字数');
                $form->number('page_num', '页数');
                $form->decimal('price', '价格');
                $form->text('keywords', '关键词');
                $form->textarea('abstract', '摘要')->rows(3);
                //表单输入HTML editor编辑器
                //$form->editor('detail', '详细介绍');

            });

            //禁止重置按钮
            $form->disableReset();

            //设置宽度
            //$form->setWidth(10, 2);

            //设置表单提交的action
            //$form->setAction('Book/save');

            //保存前判断操作
            $form->saving(function(Form $form) {
                if(!isset($form->model()->id)){
                    $user = Admin::user();
                    $form->school_id=$user->school_id;
                    if(Book::where('isbn',$form->isbn)->value('id')){
                        //错误信息提示
                        $error = new MessageBag(['title'=>'提示','message'=>'ISBN已存在!'.$form->id]);
                        return back()->withInput()->with(compact('error'));
                    }
                }
                //错误提示
                if($form->purchase_at>$form->publish_at){
                    $error = new MessageBag(['title' => '操作提示', 'message' => '购买时间不能大于出版时间',]);
                    return back()->with(compact('error'));
                }
            });
            // 模型表单回调
            $form->saved(function (Form $form) {
                /*                //#指定值为固定1
                                $form->is_in = 1;
                                //验证值是够有重复
                                if($from->nick_name !== $form->model()->email && User::where('email',$form->email)->value('id')){
                                    //错误信息提示
                                    $error = new MessageBag(['title'=>'提示','message'=>'邮箱已存在!']);
                                    return back()->withInput()->with(compact('error'));
                                }*/
            });

            $form->tools(function (Form\Tools $tools) {
                // 去掉返回按钮
                //$tools->disableBackButton();

                // 去掉跳转列表按钮
                //$tools->disableListButton();

                // 添加一个按钮, 参数可以是字符串, 或者实现了Renderable或Htmlable接口的对象实例
                //$tools->add('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
            });
        });
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
/*    public function store(Request $request)
    {
        $qty = $request->input('stocks');
        $discount=0.3;
        $total = $this->bookService->getTotal($qty, $discount);
        //echo($total);
        $model = new $this->model;
        $model->fill(Input::all());
        $model->save();
        return Redirect::to(action($this->controller . '@index'));
    }*/
}
