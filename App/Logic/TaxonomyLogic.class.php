<?php
// +----------------------------------------------------------------------
// | TP-Admin [ 多功能后台管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2015 http://www.hhailuo.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: XiaoYao <476552238li@gmail.com>
// +----------------------------------------------------------------------

namespace Logic;
use Lib\Log;

/**
* 类别Logic
*/
class TaxonomyLogic extends BaseLogic {
    protected $siteid;
    function __construct() {
        $this->siteid = get_siteid();
    }

    public function getTaxonomies() {
        $file = CONF_PATH . 'taxonomy-' . $this->siteid  . '.php';
        return file_exists($file) ? (require $file) : array();
    }

    public function setTaxonomies($taxonomies) {
        $taxonomies = '<?php return ' . var_export($taxonomies, true) . "; ?>";
        $file_name = 'taxonomy-' . $this->siteid  . '.php';
        file_put_contents(CONF_PATH . $file_name,   $taxonomies);
    }

    public function getTaxonomy($post_type, $taxonomy_name) {
        $taxonomies = $this->getTaxonomies();
        return isset($taxonomies[$post_type][$taxonomy_name]) ? $taxonomies[$post_type][$taxonomy_name] : false;
    }

    public function registerTaxonomy($taxonomy) {
        if (empty($taxonomy['post_type']) || empty($taxonomy) || !is_array($taxonomy) || empty($taxonomy['name'])) {
            $this->errorCode = 10001;
            $this->errorMessage = '参数不合法！';
            return false;
        }
        $taxonomy['label'] = $taxonomy['label'] ? : $taxonomy['name'];
        $taxonomy['menu_name'] = $taxonomy['menu_name'] ? : $taxonomy['name'];
        $taxonomies = $this->getTaxonomies();
        if (isset($taxonomies[$taxonomy['post_type']][$taxonomy['name']])) {
            $this->errorCode = 30001;
            $this->errorMessage = '分类已存在！';
            return false;
        }
        $taxonomies[$taxonomy['post_type']][$taxonomy['name']] = $taxonomy;

        $this->setTaxonomies($taxonomies);
        return true;
    }

    public function deleteTaxonomy($post_type, $taxonomy_name) {
        if (empty($post_type) || empty($taxonomy_name)) {
            $this->errorCode = 10001;
            $this->errorMessage = '参数不合法！';
            return false;
        }

        $taxonomies = $this->getTaxonomies();
        if (!isset($taxonomies[$post_type][$taxonomy_name])) {
            $this->errorCode = 30002;
            $this->errorMessage = '分类不已存在！';
            return false;
        }

        $taxonomy = $taxonomies[$post_type][$taxonomy_name];

        if ($this->removeTaxItems($post_type, $taxonomy_name)) {
            unset($taxonomies[$post_type][$taxonomy_name]);
        } else {
            $this->errorCode = 30003;
            $this->errorMessage = '分类移除失败！';
            return false;
        }

        $this->setTaxonomies($taxonomies);
        return true;
    }

    public function removeTaxItems($post_type, $taxonomy_name) {
        return true;
    }

}