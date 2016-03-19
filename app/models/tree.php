<?php namespace App\Models;

abstract class Tree extends \Wasp\Model
{
    public function hasChildren()
    {
        if( self::where('parent_id', '=', $this->id)->count() > 0 ) {
            return true;
        }
        
        return false;
    }
    
    public function getChildren()
    {
        return self::where('parent_id', '=', $this->id)->get();
    }
    
    public function root()
    {
        return self::find( $this->root_id );
    }
    
    public function parent()
    {
        return self::find( $this->parent_id );
    }

    public static function makeTree($id, Callable $callback = null)
    {
        $item = self::find( $id );
        
        if( empty($item) ) {
            return false;
        }
        
        $data   = ($build == null) ? $item->getAttributes() : $callback( $item );
        $childs = self::whereBetween('lidx', [$item->lidx, $item->ridx])
                      ->where('root_id', '=', $item->root_id)
                      ->where('id', '!=', $item->id)
                      ->orderBy('lidx', 'ASC')
                      ->get();

        if( $childs->count() > 0 ) {
            if( is_object($data) ) {
                $data->children = self::makeChildren($item, $callback, $childs);
            } else {
                $data['children'] = self::makeChildren($item, $callback, $childs);
            }
        }

        return $data;
    }
    
    public static function childrenExists($parent_id, $items)
    {
        foreach($items as $item) {
            if( $parent_id == $item->parent_id ) {
                return true;
            }
        }
        
        return false;
    }
    
    public static function makeChildren($item, $callback, $items) 
    {
        if( empty($item->id) ) {
            return false;
        }
        
        $_ = [];
        
        foreach($items as $child) {
            if( $child->parent_id == $item->id ) {
                $data = ($callback == null) ? $child->getAttributes() : $callback( $child );
                
                if( self::childrenExists($child->id, $items) ) {
                    if( is_object($data) ) {
                        $data->children = self::makeChildren($child, $callback, $items);
                    } else {
                        $data['children'] = self::makeChildren($child, $callback, $items);
                    }
                }
                
                $_[] = $data;
            }
        }
        
        return $_;
    }
    
    public static function treeRecalc( $root_id = null )
    {
        $roots = self::where('parent_id', '=', 0);
        
        if( !empty($root_id) ) {
            $roots = $roots->where('root_id', '=', $root_id);
        }
        
        $roots = $roots->get();

        foreach($roots as $root) {
            $c           = 1;
            $root->lidx  = 1;
            $root->depth = 0;
            
            if( $root->hasChildren() ) {
                self::childCaclc($root, $root->id, $c, $root->depth);
            }
            
            $c++;
            $root->root_id = $root->id;
            $root->ridx    = $c;
            $root->save();
        }
    }
    
    public static function childCaclc($parent, $root_id, &$c, $depth)
    {
        $depth++;
        
        foreach($parent->getChildren() as $children) {
            $c++;
            $children->lidx    = $c;
            $children->root_id = $root_id;
            $children->depth   = $depth;
            
            if( $children->hasChildren() ) {
                self::childCaclc($children, $root_id, $c, $depth);
            }
            
            $c++;
            $children->ridx = $c;
            $children->save();
        }
    }
    
}
  

