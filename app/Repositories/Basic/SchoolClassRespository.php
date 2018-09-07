<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 23:37
 */

namespace App\Repositories\Basic;

use App\Models\Basic\SchoolClass;
use App\Repositories\BaseRepository;

class SchoolClassRespository extends BaseRepository
{
    /**
     * Create a new BlogRepository instance.
     *
     * @param  App\Models\Basic\SchoolClass $schoolClass
     * @return void
     */
    public function __construct(SchoolClass $schoolClass)
    {
        $this->model = $schoolClass;
    }

    public  static function getList(){
        $list = SchoolClass::all()->sortBy('name',SORT_ASC,true);
        $return = [0=>'Root'];
        foreach ($list as $value){
            $return[$value['id']] = $value['full_name'];
        }
        return $return;
    }
}