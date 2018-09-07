<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 20:20
 */

namespace App\Repositories\Basic;

use App\Models\Basic\Department;
use App\Repositories\BaseRepository;

class DepartmentRespository extends BaseRepository
{
    /**
     * Create a new BlogRepository instance.
     *
     * @param  App\Models\Basic\Department $department
     * @return void
     */
    public function __construct(Department $department)
    {
        $this->model = $department;
    }


    public  static function getDepartment(){
        $categorylist = Department::all();
        $return = [0=>'Root'];
        foreach ($categorylist as $value){
            $return[$value['id']] = $value['name'];
        }
        return $return;
    }
}