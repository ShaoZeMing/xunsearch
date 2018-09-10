<?php
/**
 * Created by PhpStorm.
 * User: 4d4k
 * Date: 2018/6/11
 * Time: 17:41
 */

namespace ShaoZeMing\Xunsearch;

/**
 * Interface XunsearchInterface
 * User: ZeMing Shao
 * Email: szm19920426@gmail.com
 * @package ShaoZeMing\Xunsearch
 */
interface XunsearchInterface
{

    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $string
     * @return mixed
     */
    public function search($string);
}