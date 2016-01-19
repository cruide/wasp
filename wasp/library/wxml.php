<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

final class WXml
{
    private static $dom;
// ------------------------------------------------------------------------------
    /**
    * Initialize DOMDocument
    * 
    * @param string $xml
    */
    private static function _init($xml)
    {
        if( is_string($xml) ) {
            self::$dom = new \DOMDocument();
            self::$dom->loadXml($xml);
            
            return true;
        }
 
        return false;
    }
// ------------------------------------------------------------------------------
    /**
    * Process function
    * 
    * @param obj $node
    */
    private static function _process($node) 
    { 
        $occurance = [];
        $result    = [];
        
        if( $node->childNodes != null ) {
            foreach($node->childNodes as $key=>$child) {
                if( array_key_isset($child->nodeName, $occurance) ) {
                    $occurance[ $child->nodeName ]++;
                } else {
                    $occurance[ $child->nodeName ] = 1;
                }
            }
        }             
        
        if( $node->nodeType == XML_TEXT_NODE ) { 
            $result = html_entity_decode( htmlentities($node->nodeValue, ENT_COMPAT, 'UTF-8'),
                                          ENT_COMPAT,'UTF-8');
        } else if( $node->nodeType == XML_CDATA_SECTION_NODE ) { 
            $result = html_entity_decode( htmlentities($node->nodeValue, ENT_COMPAT, 'UTF-8'),
                                          ENT_COMPAT,'UTF-8');
        } else {
            if( $node->hasChildNodes() ) {
                $children = $node->childNodes;
 
                for($i=0; $i<$children->length; $i++) {
                    $child = $children->item($i);

                    if( $child->nodeName != '#text' && $child->nodeName != '#cdata-section' ) {
                        if($occurance[$child->nodeName] > 1) {
                            $result[$child->nodeName][] = self::_process($child);
                        } else {
                            $result[$child->nodeName] = self::_process($child);
                        }
                    } else if ($child->nodeName == '#cdata-section') {
                        $text = self::_process($child);
 
                        if( trim($text) != '' ) {
//                                $result[$child->nodeName] = self::_process($child);
                            $result['#text'] = (string)$text;
                        }
                        
                    } else if( $child->nodeName == '#text' ) {
                        $text = self::_process($child);
 
                        if( trim($text) != '' ) {
//                                $result[$child->nodeName] = self::_process($child);
                            $result[ $child->nodeName ] = (string)$text;
                        }
                    }
                }
            } 
 
            if( $node->hasAttributes() ) { 
                $attributes = $node->attributes;
 
                if( !is_null($attributes) ) {
                    foreach ($attributes as $key => $attr) {
                        $result[ '@' . $attr->name ] = $attr->value;
                    }
                }
            }
        }
 
        return $result;
    }
// ------------------------------------------------------------------------------
    /**
    * Public function
    * Load xml data from strinf
    * 
    * @param string $_xml
    */
    public static function FromString($_xml) 
    {
        if( !empty($_xml) && self::_init($_xml) ) {
            $_result   = self::_normalize_data_in_array( self::_process(self::$dom) );
            self::$dom = null;
            
            return $_result;
        } else {
            return [];
        }
    }
// ------------------------------------------------------------------------------
    /**
    * Public function
    * Load xml data from file
    * 
    * @param string $_file
    */
    public static function FromFile($_file) 
    {
        if( !is_file($_file) ) {
            return false;
        }
        
        $_xml = file_get_contents($_file);
        
        if( self::_init($_xml) ) {
            $_result   = self::_normalize_data_in_array( self::_process(self::$dom) );
            self::$dom = null;
            
            return $_result;
        } else {
            return [];
        }
    }
// ------------------------------------------------------------------------------
    /**
    * Normalize data into array
    * 
    * @param array $_array
    * @return array
    */
    private static function _normalize_data_in_array($_array)
    {
        if( !empty($_array) && is_array($_array) ) {
            $_ret = [];
            
            foreach($_array as $_key=>$_val) {
                if( is_numeric($_val) ) {
                    if( preg_match('/[.]/is',$_val) ) {
                        $_ret[ $_key ] = (float)$_val;
                    } else {
                        $_ret[ $_key ] = (int)$_val;
                    }
                    
                } else if( is_array($_val) ) {
                    $_ret[ $_key ] = self::_normalize_data_in_array($_val);
                    
                } else if( strtolower($_val) == 'true' ) {
                    $_ret[ $_key ] = true;
                    
                } else if( strtolower($_val) == 'false' ) {
                    $_ret[ $_key ] = false;
                    
                } else {
                    $_ret[ $_key ] = $_val;
                }
            }
             
            return $_ret;
        }
          
        return false;
    }
}
