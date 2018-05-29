<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * WordPress API manager
 * 
 * @package AAM
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class AAM_Backend_Feature_Main_Route extends AAM_Backend_Feature_Abstract {
    
    /**
     * 
     * @return type
     */
    public function getTable() {
        $response = array('data' => $this->retrieveAllRoutes());

        return json_encode($response);
    }

    /**
     * 
     * @return type
     */
    public function save() {
       $type   = filter_input(INPUT_POST, 'type');
       $route  = filter_input(INPUT_POST, 'route');
       $method = filter_input(INPUT_POST, 'method');
       $value  = filter_input(INPUT_POST, 'value');

       $object = AAM_Backend_Subject::getInstance()->getObject('route');

       $object->save($type, $route, $method, $value);

       return json_encode(array('status' => 'success'));
    }

    /**
     * @inheritdoc
     */
    public static function getTemplate() {
        return 'main/route.phtml';
    }
    
    /**
     * 
     * @return type
     */
    protected function retrieveAllRoutes() {
        $response = array();
        $object   = AAM_Backend_Subject::getInstance()->getObject('route');
	$routes   = rest_get_server()->get_routes();
        
        //build all RESTful routes
        foreach ($routes as $route => $handlers) {
            $methods = array();
            foreach($handlers as $handler) {
                $methods = array_merge($methods, array_keys($handler['methods']));
            }
        
            foreach(array_unique($methods) as $method) {
                $response[] = array(
                    'restful',
                    $method,
                    htmlspecialchars($route),
                    $object->has('restful', $route, $method) ? 'checked' : 'unchecked'
                );
            }
        }
        
        return $response;
    }

    /**
     * Check inheritance status
     * 
     * Check if menu settings are overwritten
     * 
     * @return boolean
     * 
     * @access protected
     */
    protected function isOverwritten() {
        $object = AAM_Backend_Subject::getInstance()->getObject('route');
        
        return $object->isOverwritten();
    }

    /**
     * Register Menu feature
     * 
     * @return void
     * 
     * @access public
     */
    public static function register() {
        AAM_Backend_Feature::registerFeature((object) array(
            'uid'        => 'route',
            'position'   => 50,
            'title'      => __('API Routes', AAM_KEY) . ' <span class="badge">NEW</span>',
            'capability' => 'aam_manage_api_routes',
            'type'       => 'main',
            'subjects'   => array(
                AAM_Core_Subject_Role::UID, 
                AAM_Core_Subject_User::UID,
                AAM_Core_Subject_Visitor::UID,
                AAM_Core_Subject_Default::UID
            ),
            'option'     => 'core.settings.restful',
            'view'       => __CLASS__
        ));
    }

}