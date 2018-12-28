<?php

namespace ShaoZeMing\Xunsearch;


/**
 * Class XunsearchService
 * User: ZeMing Shao
 * Email: szm19920426@gmail.com
 * @package ShaoZeMing\Xunsearch
 */
include_once __DIR__ . '/vendor/lib/XS.php';

class XunsearchService extends \XS implements XunsearchInterface
{

    protected $flush_index = true;//立即刷新索引
    protected $fuzzy = true;//开启模糊搜索
    protected $auto_synonyms = true; //开启自动同义词搜索功能
    protected $limit = 10; //每页搜索条数
    protected $offset = 0; //每页搜索条数
    protected $sort_field = null; //排序字段
    protected $sort_state = false; //排序规则:false 倒叙，true 正序
    protected $database = null; //连接服务器

    /**
     * XunsearchService constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        defined('XS_APP_ROOT') || define('XS_APP_ROOT', dirname(__DIR__) . '/config');


        $file = XS_APP_ROOT.'/xunsearch.php';
        if(!file_exists($file)){
            throw new \Exception('配置文件'.$file.'不存在');
        }
        $config = include $file;

        if(!isset($config['default'])){
            throw new \Exception('配置文件'.$file.'缺少default配置');
        }
        $this->database = $config['default'];

        $config = $config['databases'][$config['default']];

        parent::__construct($config);

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
        $search->setFuzzy($this->fuzzy);

        // synonym search
        $search->setAutoSynonyms($this->auto_synonyms);

        // set query
        $search->setQuery($string);


        if($this->sort_field){
            $search->setSort($this->sort_field,$this->sort_state);
        }
        // get the result
        $search_begin = microtime(true);
        $doc = $search->setLimit($this->limit, $this->offset)->search();
        $search_cost = microtime(true) - $search_begin;

        // get other result
        $count = $search->getLastCount();    //最近一次搜索结果数
        $total = $search->getDbTotal();      //数据库总数



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
        if (!is_array($this->array_first($data))) {
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
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function setLimit($limit = 10, $offset = 0)
    {

        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * 模糊搜索
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param bool $state
     * @return $this
     */
    public function setFuzzy($state = true)
    {
        $this->fuzzy = $state;
        return $this;
    }

    /**
     * 自动匹配同义词
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param bool $state
     * @return $this
     */
    public function setAutoSynonyms($state = true)
    {
        $this->auto_synonyms = $state;
        return $this;
    }

    /**
     * 排序
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $field
     * @param bool $sort
     * @return $this
     */
    public function setSort($field,$sort = false)
    {
        $this->sort_field = $field;
        $this->sort_state = $sort;
        return $this;
    }


    /**
     * 设置配置
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $config
     * @return $this
     * @throws \XSException
     */
    public function setConfig(array $config)
    {

        $this->loadIniFile($config);
        self::$_lastXS = $this;
        return $this;
    }


    /**
     * 设置bug
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $database
     * @return XunsearchService
     * @throws \XSException
     */
    public function setDatabase($database){

        $file = XS_APP_ROOT.'/xunsearch.php';
        if(!file_exists($file)){
            throw new \Exception('配置文件'.$file.'不存在');
        }
        $config = include $file;
        if(!isset($config['databases'][$database])){
            throw new \Exception('配置文件'.$file.'缺少这个'.$database.'配置');
        }

        $this->database = $database;

        $config = $config['databases'][$database];

        return $this->setConfig($config);

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



   private function array_first(array $array)
    {
        if (count($array)) {
            reset($array);
            $array[key($array)];
        }
        return null;
    }

}
