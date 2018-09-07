<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/26
 * Time: 13:23
 */
namespace App\Repositories\Basic;

use App\Models\Basic\School;
use App\Repositories\BaseRepository;

class SchoolRespository extends BaseRepository
{
    /**
     * Create a new BlogRepository instance.
     *
     * @param  App\Models\Basic\School $school
     * @return void
     */
    public function __construct(School $school)
    {
        $this->model = $school;
    }

    public  static function getSchoolList(){
/*        $categorylist = Category::all();
        $return = [0=>'Root'];
        foreach ($categorylist as $value){
            $return[$value['id']] = $value['name'];
        }
        return $return;*/
    }
}