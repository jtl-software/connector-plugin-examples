<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 */

namespace jtl\SubshopCompanies\Loader;

class CompanyLoader
{
    /**
     * @var string[][]|null
     */
    protected static $data;
    
    /**
     * @param int $shop_id
     * @return string[]|null
     * @throws \InvalidArgumentException
     */
    public static function get($shop_id)
    {
        if (!is_int($shop_id) || $shop_id <= 0) {
            throw new \InvalidArgumentException('Parameter shop_id must be from type int and > 0');
        }
        
        self::init();
        
        if (!isset(self::$data[$shop_id])) {
            return null;
        }
        
        return self::$data[$shop_id];
    }
    
    /**
     * @throws \Exception
     */
    protected static function init()
    {
        if (!is_null(self::$data)) {
            return;
        }
        
        $results = Shopware()->Db()->fetchAll('SELECT e.name, e.label, e.type, v.shop_id, v.value
                                            FROM s_core_config_forms f
                                            JOIN s_core_config_elements e ON e.form_id = f.id
                                            JOIN s_core_config_values v ON v.element_id = e.id
                                            WHERE f.name = \'MasterData\'');
    
        if (count($results) == 0) {
            throw new \Exception('Could not get the master data');
        }
    
        self::$data = [];
        foreach ($results as $result) {
            self::$data[(int) $result['shop_id']][$result['name']] = unserialize($result['value']);
        }
    }
}
