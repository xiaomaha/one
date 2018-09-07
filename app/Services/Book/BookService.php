<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/26
 * Time: 21:08
 */

namespace APP\Services\Book;


class BookService
{
    /**
     *
     * @param integer $qty
     * @param float $discount
     * @return float
     */
    public function getTotal($qty, $discount)
    {
        return 500 * $qty * $discount;
    }
}