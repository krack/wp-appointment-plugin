<?php


class ProductDao
{
    private $table_name;
    public function __construct()
    {

	    global $wpdb;
        $this->$table_name = $wpdb->prefix . 'products';
    }

    public function createTable(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $sql = "CREATE TABLE {$this->$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            describe_product text,
            price mediumint(9) NOT NULL,
            during mediumint(9) NOT NULL,
            categorie tinytext NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";



        dbDelta( $sql );
    }

    public function delete($id){
        global $wpdb;
		$wpdb->delete( "{$this->$table_name}", array( 'id' => $id ) );
    }

    public function insert($data){
        global $wpdb;
        $data["describe_product"] = $data["describe"];
		unset($data["describe"]);
        $wpdb->insert( "{$this->$table_name}",$data );
       return $wpdb->insert_id;;
    }
    
    public function update($id, $data){
        global $wpdb;
        $data["describe_product"] = $data["describe"];
		unset($data["describe"]);
        $wpdb->update( 
            "{$this->$table_name}", 
            $data, 
            array( 'id' => $id )
        );
    }
   

    public function get($id){
        if($id==0){
            return null;
        }
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM {$this->$table_name} where id={$id}", OBJECT );
        $result = $results[0];
        $result->describe = $result->describe_product;
        unset($result->describe_product);
        return $result;
    }


    public function getAll(){
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM {$this->$table_name}", OBJECT );
        foreach( $results  as $result){
            $result->describe = $result->describe_product;
            unset($result->describe_product);
        }
        return $results;
    }
}

