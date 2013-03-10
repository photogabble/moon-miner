<?php

/**
* Creates a data object in ADOdb's datadict format
*
* This class stores information about table data, and is called
* when we need to load field data into a table.
*
* @package axmls
* @access private
*/
class dbData3 extends dbObject3 {
    
    var $data = array();
    
    var $row;
    
    // Initializes the new dbData object.
    // @param object $parent Parent object
    // @param array $attributes Attributes
    // @internal
    function dbData3( &$parent, $attributes = NULL )
    {
        $this->parent = $parent;
    }
    
    // XML Callback to process start elements
    // Processes XML opening tags. 
    // Elements currently processed are: ROW and F (field). 
    // @access private
    function _tag_open( &$parser, $tag, $attributes )
    {
        $this->currentElement = strtoupper( $tag );
        
        switch( $this->currentElement )
        {
            case 'ROW':
                $this->row = count( $this->data );
                $this->data[$this->row] = array();
                break;
            case 'F':
                $this->addField($attributes);
            default:
                // print_r( array( $tag, $attributes ) );
        }
    }
    
    // XML Callback to process CDATA elements
    // Processes XML cdata.
    // @access private
    function _tag_cdata( &$parser, $cdata )
    {
        switch( $this->currentElement )
        {
            // Index field name
            case 'F':
                $this->addData( $cdata );
                break;
            default:
                
        }
    }
    
    // XML Callback to process end elements
    // @access private
    function _tag_close( &$parser, $tag )
    {
        $this->currentElement = '';
        
        switch( strtoupper( $tag ) )
        {
            case 'DATA':
                xml_set_object( $parser, $this->parent );
                break;
        }
    }
    
    // Adds a field to the insert
    // @param string $name Field name
    // @return string Field list
    function addField( $attributes )
    {
        // check we're in a valid row
        if( !isset( $this->row ) || !isset( $this->data[$this->row] ) )
        {
            return;
        }
        
        // Set the field index so we know where we are
        if( isset( $attributes['NAME'] ) )
        {
            $this->current_field = $this->FieldID( $attributes['NAME'] );
        }
        else
        {
            $this->current_field = count( $this->data[$this->row] );
        }
        
        // initialise data
        if( !isset( $this->data[$this->row][$this->current_field] ) )
        {
            $this->data[$this->row][$this->current_field] = '';
        }
    }
    
    // Adds options to the index
    // @param string $opt Comma-separated list of index options.
    // @return string Option list
    function addData( $cdata )
    {
        // check we're in a valid field
        if ( isset( $this->data[$this->row][$this->current_field] ) )
        {
            // add data to field
            $this->data[$this->row][$this->current_field] .= $cdata;
        }
    }
    
    // Generates the SQL that will add/update the data in the database
    // @param object $xmls adoSchema object
    // @return array Array containing index creation SQL
    function create( &$xmls )
    {
        $table = $xmls->dict->TableName($this->parent->name);
        $table_field_count = count($this->parent->fields);
        $tables = $xmls->db->MetaTables(); 
        $sql = array();
        
        $ukeys = $xmls->db->MetaPrimaryKeys( $table );
        if( !empty( $this->parent->indexes ) and !empty( $ukeys ) )
        {
            foreach( $this->parent->indexes as $indexObj )
            {
                if( !in_array( $indexObj->name, $ukeys ) ) $ukeys[] = $indexObj->name;
            }
        }
        
        // eliminate any columns that aren't in the table
        foreach( $this->data as $row )
        {
            $table_fields = $this->parent->fields;
            $fields = array();
            $rawfields = array(); // Need to keep some of the unprocessed data on hand.
            
            foreach( $row as $field_id => $field_data )
            {
                if( !array_key_exists( $field_id, $table_fields ) )
                {
                    if( is_numeric( $field_id ) )
                    {
                        $field_id = reset( array_keys( $table_fields ) );
                    }
                    else
                    {
                        continue;
                    }
                }
                
                $name = $table_fields[$field_id]['NAME'];
                
                switch( $table_fields[$field_id]['TYPE'] )
                {
                    case 'I':
                    case 'I1':
                    case 'I2':
                    case 'I4':
                    case 'I8':
                        $fields[$name] = intval($field_data);
                        break;
                    case 'C':
                    case 'C2':
                    case 'X':
                    case 'X2':
                    default:
                        $fields[$name] = $xmls->db->qstr( $field_data );
                        $rawfields[$name] = $field_data;
                }
                
                unset($table_fields[$field_id]);
                
            }
            
            // check that at least 1 column is specified
            if( empty( $fields ) )
            {
                continue;
            }
            
            // check that no required columns are missing
            if( count( $fields ) < $table_field_count )
            {
                foreach( $table_fields as $field )
                {
                    if( isset( $field['OPTS'] ) and ( in_array( 'NOTNULL', $field['OPTS'] ) || in_array( 'KEY', $field['OPTS'] ) ) && !in_array( 'AUTOINCREMENT', $field['OPTS'] ) )
                    {
                        continue(2);
                    }
                }
            }
            
            // The rest of this method deals with updating existing data records.
            
            if( !in_array( $table, $tables ) or ( $mode = $xmls->existingData() ) == XMLS_MODE_INSERT )
            {
                // Table doesn't yet exist, so it's safe to insert.
                logMsg( "$table doesn't exist, inserting or mode is INSERT" );
                $sql[] = 'INSERT INTO '. $table .' ('. implode( ',', array_keys( $fields ) ) .') VALUES ('. implode( ',', $fields ) .')';
                continue;
            }
        
            // Prepare to test for potential violations. Get primary keys and unique indexes
            $mfields = array_merge( $fields, $rawfields );
            $keyFields = array_intersect( $ukeys, array_keys( $mfields ) );
            
            if( empty( $ukeys ) or count( $keyFields ) == 0 )
            {
                // No unique keys in schema, so safe to insert
                logMsg( "Either schema or data has no unique keys, so safe to insert" );
                $sql[] = 'INSERT INTO '. $table .' ('. implode( ',', array_keys( $fields ) ) .') VALUES ('. implode( ',', $fields ) .')';
                continue;
            }
            
            // Select record containing matching unique keys.
            $where = '';
            foreach( $ukeys as $key )
            {
                if( isset( $mfields[$key] ) and $mfields[$key] )
                {
                    if( $where ) $where .= ' AND ';
                    $where .= $key . ' = ' . $xmls->db->qstr( $mfields[$key] );
                }
            }
            $records = $xmls->db->Execute( 'SELECT * FROM ' . $table . ' WHERE ' . $where );
            switch( $records->RecordCount() )
            {
                case 0:
                    // No matching record, so safe to insert.
                    logMsg( "No matching records. Inserting new row with unique data" );
                    $sql[] = $xmls->db->GetInsertSQL( $records, $mfields );
                    break;
                case 1:
                    // Exactly one matching record, so we can update if the mode permits.
                    logMsg( "One matching record..." );
                    if( $mode == XMLS_MODE_UPDATE )
                    {
                        logMsg( "...Updating existing row from unique data" );
                        $sql[] = $xmls->db->GetUpdateSQL( $records, $mfields );
                    }
                    break;
                default:
                    // More than one matching record; the result is ambiguous, so we must ignore the row.
                    logMsg( "More than one matching record. Ignoring row." );
            }
        }
        return $sql;
    }
}

?>
