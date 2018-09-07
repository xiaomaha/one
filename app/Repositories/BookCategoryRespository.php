<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 14:40
 */

namespace App\Repositories;

use App\Models\Book\Category;

class BookCategoryRespository extends BaseRepository
{
    /**
     * Create a new BlogRepository instance.
     *
     * @param  App\Models\Book\Category $bookCategory
     * @return void
     */
    public function __construct(Category $bookCategory)
    {
        $this->model = $bookCategory;
    }


    public  static function getBookCategory(){
        $categorylist = Category::all();
        $return = [0=>'Root'];
        foreach ($categorylist as $value){
            $return[$value['id']] = $value['name'];
        }
        return $return;
    }

    public  static function getBookCategoryName($id){
        $categorylist = Category::all();
        $return = '';
        foreach ($categorylist as $value){
            if($id==$value['id'])
                $return= $value['name'];
        }
        return $return;
    }
}