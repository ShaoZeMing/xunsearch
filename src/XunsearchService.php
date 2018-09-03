<?php

namespace ShaoZeMing\Xunsearch;


/**
 * Class XunsearchService
 * User: ZeMing Shao
 * Email: szm19920426@gmail.com
 * @package ShaoZeMing\Xunsearch
 */
class XunsearchService extends \XS implements XunsearchInterface
{


    protected $flush_index = true;//立即刷新索引
    protected $set_fuzzy = true;//开启模糊搜索
    protected $auto_synonyms = true; //开启自动同义词搜索功能

    /**
     * XunsearchService constructor.
     * @param $file
     */
    public function __construct($file)
    {
        defined('XS_APP_ROOT') || define('XS_APP_ROOT', __DIR__ . '../config');
        parent::__construct($file);
    }


    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $string
     * @return array|mixed
     * @throws \XSException
     */
    public function search($string)
    {
        if (!$string) {
            throw new \Exception('请输入搜索字符');
        }
        $count = $total = $search_cost = 0;
        $doc = $related = $corrected = $hot = [];
        $total_begin = microtime(true);
        $search = $this->getSearch();

        //热门词汇
        $hot = $search->getHotQuery();

        // fuzzy search 模糊搜索
        $search->setFuzzy($this->set_fuzzy);

        // synonym search
        $search->setAutoSynonyms($this->auto_synonyms);

        // set query
        $search->setQuery($string);
        // get the result
        $search_begin = microtime(true);
        $doc = $search->search();
        $search_cost = microtime(true) - $search_begin;

        // get other result
        $count = $search->getLastCount();    //最近一次搜索结果数
        $total = $search->getDbTotal();      //数据库总数

//            $corrected = $this->getSearch()->getCorrectedQuery();      //模糊词搜索
//            if (count($doc) < 10) {
//                foreach ($corrected as $v) {
//                    $doc = array_merge($doc, $this->getSearch()->search($v));
//                }

        // try to corrected, if resul too few
        if ($count < 1 || $count < ceil(0.001 * $total)) {
            $corrected = $search->getCorrectedQuery();
        }
        // get related query
        $related = $search->getRelatedQuery();
        $total_cost = microtime(true) - $total_begin;

        return [
            'doc' => $doc,                    //搜索数据结果文档
            'hot' => $hot,                    //热门词汇
            'count' => $count,                  //搜索结果统计
            'total' => $total,                  //数据库总数据
            'corrected' => $corrected,             //搜索提示
            'related' => $related,               //相关搜索
            'search_cost' => $search_cost,          //搜索所用时间
            'total_cost' => $total_cost,           //页面所用时间
        ];
    }


    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $data
     * @return \XSIndex
     * @throws \XSException
     */
    public function addIndex(array $data)
    {
        if (!is_array($data)) {
            throw new \Exception('你的索引参数不是一个数组');
        }
        if (!is_array($data[0])) {
            // 一维数组
            $this->getIndex()->add(new \XSDocument($data));
        } else {
            // 多维数组
            foreach ($data as $v) {
                $this->getIndex()->add(new \XSDocument($v));
            }
        }
        //索引是否立即生效
        if ($this->flush_index) {
            $this->getIndex()->flushIndex();
        }
        return $this->getIndex();
    }


    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $data
     * @return \XSIndex
     * @throws \XSException
     */
    public function updateIndexOne(array $data)
    {
        if (!is_array($data)) {
            throw new \Exception('你的索引参数不是一个数组');
        }

        if (count($data) == count($data, 1)) {
            // 一维数组
            $this->getIndex()->update(new \XSDocument($data));
        }

        //索引是否立即生效
        if ($this->flush_index) {
            $this->getIndex()->flushIndex();
        }

        return $this->getIndex();
    }


    /**
     * 删除索引文档
     * User: shaozeming
     * @param array|string $ids 删除主键值为 $pids 的记录
     * @return \XSIndex
     * @throws \XSException
     */
    public function delIndex($ids)
    {

        if (!$ids) {
            throw new \Exception('索引主键不能为空');
        }

        $this->getIndex()->del($ids);
        if ($this->config['flushIndex']) {
            $this->getIndex()->flushIndex();
        }
        return $this->getIndex();
    }


    /**
     * 清空索引数据
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @return \XSIndex
     */
    public function cleanIndex()
    {

        return $this->getIndex()->clean();

    }


    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $attr
     * @param $value
     * @return $this
     */
    public function __set($attr, $value)
    {
        $this->$attr = $value;
        return $this;
    }


    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $attr
     * @return mixed
     */
    public function __get($attr)
    {
        return $this->$attr;
    }


}
