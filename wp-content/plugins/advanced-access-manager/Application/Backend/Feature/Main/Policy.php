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
class AAM_Backend_Feature_Main_Policy extends AAM_Backend_Feature_Abstract {
    
    /**
     * 
     * @return type
     */
    public function getTable() {
        return wp_json_encode($this->retrievePolicies());
    }

    /**
     * Save post properties
     * 
     * @return string
     * 
     * @access public
     */
    public function save() {
        if (defined('AAM_PLUS_PACKAGE')) {
            $subject = AAM_Backend_Subject::getInstance();
            $id      = AAM_Core_Request::post('id');
            $effect  = AAM_Core_Request::post('effect');

            //clear cache
            AAM_Core_API::clearCache();

            $result = $subject->save($id, $effect, 'policy');
        } else {
            $result = false;
        }

        return wp_json_encode(array(
            'status'  => ($result ? 'success' : 'failure')
        ));
    }
    
    /**
     * @inheritdoc
     */
    public static function getTemplate() {
        return 'main/policy.phtml';
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
        $object = AAM_Backend_Subject::getInstance()->getObject('policy');
        
        return $object->isOverwritten();
    }
    
    /**
     * 
     * @return type
     */
    protected function retrievePolicies() {
        $search = trim(AAM_Core_Request::request('search.value'));
        
        $list = get_posts(array(
            'post_type'   => 'aam_policy',
            'numberposts' => AAM_Core_Request::request('length'),
            'offset'      => AAM_Core_Request::request('start'),
            's'           => ($search ? $search . '*' : ''),
        ));
        
        $response = array(
            'recordsTotal'    => count($list),
            'recordsFiltered' => count($list),
            'draw'            => AAM_Core_Request::request('draw'),
            'data'            => array(),
        );
        
        foreach($list as $record) {
            $policy = json_decode($record->post_content);
            
            if ($policy) {
                $response['data'][] = array(
                    $record->ID,
                    $this->buildTitle($record),
                    $this->buildActionList($record),
                    get_edit_post_link($record->ID, 'link')
                );
            }
        }
        
        return $response;
    }
    
    /**
     * 
     * @global type $wpdb
     * @param type $type
     * @param type $search
     * @return type
     */
    protected function getPolicyCount($type, $search) {
        global $wpdb;
        
        $query  = "SELECT COUNT(*) AS total FROM {$wpdb->posts} ";
        $query .= "WHERE (post_type = %s) AND (post_title LIKE %s) AND (post_status = %s)";
        
        $args   = array($type, "{$search}%", 'publish');
        
        return $wpdb->get_var($wpdb->prepare($query, $args));
    }
    
    /**
     * 
     * @param type $record
     * @return string
     */
    protected function buildTitle($record) {
        $title  = (!empty($record->post_title) ? $record->post_title : __('(no title)'));
        $title .= '<br/>';
        
        if (isset($record->post_excerpt)) {
            $title .= '<small>' . esc_js($record->post_excerpt) . '</small>';
        }
        
        return $title;
    }
    
    /**
     * 
     * @param type $record
     * @return type
     */
    protected function buildActionList($record) {
        //'assign,edit,clone,delete'
        $subject = AAM_Backend_Subject::getInstance();
        $object  = $subject->getObject('policy');
        $actions = array();
        
        $actions[] = $object->has($record->ID) ? 'unassign' : 'assign';
        $actions[] = 'edit';
        $actions[] = 'delete';
        
        return implode(',', $actions);
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
            'uid'        => 'policy',
            'position'   => 2,
            'title'      => __('Access Policies', AAM_KEY) . '<span class="badge">NEW</span>',
            'capability' => 'aam_manage_policy',
            'type'       => 'main',
            'subjects'   => array(
                AAM_Core_Subject_Role::UID, 
                AAM_Core_Subject_User::UID,
                AAM_Core_Subject_Visitor::UID,
                AAM_Core_Subject_Default::UID
            ),
            'view'       => __CLASS__
        ));
    }

}