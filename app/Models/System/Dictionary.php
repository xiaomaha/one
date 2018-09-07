<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

class Dictionary extends Model
{
    use ModelTree, AdminBuilder;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'dictionaries';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('parent_id');
        $this->setOrderColumn('order');
        $this->setTitleColumn('name');
    }
}