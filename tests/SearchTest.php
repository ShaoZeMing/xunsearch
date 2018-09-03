<?php
/**
 *  TestSms.php
 *
 * @author szm19920426@gmail.com
 * $Id: TestSms.php 2017-08-17 上午10:08 $
 */

namespace ShaoZeMing\Xunsearch\Test;

use PHPUnit\Framework\TestCase;
use ShaoZeMing\Xunsearch\XunsearchService;


class SearchTest extends TestCase
{
    protected $instance;

    public function setUp()
    {

        $file = dirname(__DIR__) . '/config/demo.ini';
        $this->instance = new XunsearchService($file);
    }


    public function testPushManager()
    {
        $this->assertInstanceOf(XunsearchService::class, $this->instance);
    }


    public function testSearch()
    {
        echo PHP_EOL . "添加索引中...." . PHP_EOL;
        try {

            $data = $this->gieData();
//            print_r($data);

//            $result =  $this->instance->addIndex($data);
            $result = $this->instance->search('主持');
//            $result =  $this->instance->delIndex('3');
//            $result =  $this->instance->cleanIndex();

            print_r($result);
            return $result;
        } catch (\Exception $e) {
            $err = "Error : 错误：" . $e->getMessage();
            echo $err . PHP_EOL;

        }
    }


    public function gieData()
    {
        $data = [
            ['id' => 1, 'email' => '928240096@qq.com', 'name' => 'Shao ZeMing ZeMing Shao 邵泽明 泽明邵 邵澤明 澤明邵', 'lesson' => '朗诵主持,Reciting Hosting,朗誦主持，'],
            ['id' => 2, 'email' => '12315@qq.com', 'name' => 'Chris Dong 董胜君  董勝君', 'lesson' => '朗诵主持,Reciting Hosting,朗誦主持，演講辯論，speech debate，演讲辩论'],
            ['id' => 3, 'email' => 'shao-ze-ming@outlook.com', 'name' => 'Shao ZeMing ZeMing Shao 邵泽明 泽明邵 邵澤明 澤明邵', 'lesson' => '朗诵主持,Reciting Hosting,朗誦主持，'],
            ['id' => 4, 'email' => 'szm19920426@qq.com', 'name' => '明明 mingming', 'lesson' => '写作批改,writing correction,寫作批改,国学经典,National Classics,國學經典'],
            ['id' => 5, 'email' => '1270912585@qq.com', 'name' => '齐亚敏，qi ya min 齊亞敏', 'lesson' => '朗诵主持,Reciting Hosting,朗誦主持，演講辯論，speech debate，演讲辩论，国学经典,National Classics,國學經典'],
        ];
        return $data;
    }
}
