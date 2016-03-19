<?php namespace Wasp;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    protected $_columns_list;
// -------------------------------------------------------------------------------
    public function getColumns()
    {
        global $database;

        if( !is_array($this->_columns_list) ) {
            $this->_columns_list = [];
        }
        
        if( count($this->_columns_list) > 0 ) {
            return $this->_columns_list;
        }
        
        $connection = $this->getConnection()->getName();
        $schema     = $database->schema( $connection );

        return $this->_columns_list = $schema->getColumnListing( $this->getTable() );
    }
// -------------------------------------------------------------------------------
    public function hasColumn( $column )
    {
        $columns = $this->getColumns();
        
        return in_array($column, $columns);
    }
}