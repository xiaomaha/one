<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 23:07
 */

namespace App\Repositories;

use App\Models\System\Dictionary;

class DictionaryRespository extends BaseRepository
{
    /**
     * Create a new BlogRepository instance.
     *
     * @param  App\Models\Dictionary $dictionary
     * @return void
     */
    public function __construct(Dictionary $dictionary)
    {
        $this->model = $dictionary;
    }

    /**
     * 取得字典列表
     * @return array
     */
    public  static function getDictionary(){
        $dictionarylist = Dictionary::all();
        $return = [0=>'Root'];
        foreach ($dictionarylist as $value){
            $return[$value['id']] = $value['name'];
        }
        return $return;
    }

    /**
     * 取得某一字典的所有项
     * @return array
     */
    public  static function getDictionaryByName($name){
        $dictionarylist = Dictionary::where('dic_name', $name)->where('is_active','=',1)->where('is_leaf','=',0)->first();
        $return = [0=>''];
        if($dictionarylist!=null){
            $list = Dictionary::where('parent_id', $dictionarylist->id)->get();
            foreach ($list as $value){
                $return[$value['id']] = $value['name'];
            }
        }
        return $return;
    }

    /**
     * 取得某一字典的对应的名字
     * @return array
     */
    public  static function getDictionaryValueByName($name,$value){
        $dictionarylist = Dictionary::where('dic_name', $name)->where('is_active','=',1)->where('is_leaf','=',0)->first();
        $return = '';
        if($dictionarylist!=null){
            $list = Dictionary::where('parent_id', $dictionarylist->id)->where('value',$value)->first();
            $return = $list['name'];
        }
        return $return;
    }
}