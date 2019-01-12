<?php


class AdminProductManage
{
  
    public function __construct()
    {
        
        add_action( 'admin_enqueue_scripts', array( $this,'load_custom_wp_admin_style') );
        add_menu_page('Activity title', 'Activity', 'manage_options', 'activity', array( $this,'test'));
    }

    public function load_custom_wp_admin_style() {
        wp_register_style( 'admin-style', plugins_url( '/css/admin-style.css', __FILE__ ), array(), null, 'all' );
        wp_enqueue_style( 'admin-style' );
        wp_register_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css',  array(), null, 'all');
        wp_enqueue_style( 'fontawesome' );
    }

    public function test(){
        ?>
        <div class="wrap wp-appointment-plugin-admin">"
            <h1>Product of your activity</h1>  
            <p>La liste des prestations que proposées</p>
        <?php
        if ( isset( $_GET['edit'] ) ) {
            $id = $this->updateData($_GET['edit']);
            $this->printForm($id);
        }else{
            $this->doElementAction();
            $this->printList();
        }
        ?>
        </div>
        <?php
    }

    public function updateData($id){
        if ( isset( $_POST['name'] ) ) {
            $data = array( 
                'name' => stripcslashes ($_POST['name']),
                'describe' => stripcslashes ($_POST['describe']),
                'price' => $_POST['price'],
                'during' => $_POST['during'],
                'categorie' =>  stripcslashes ($_POST['categorie'])
            );
    
            $productDao = new ProductDao();
            if($id == 0){
                print( "insertion ok");
                $id= $productDao->insert($data);
                $actual_link = $this->getCurrentUlr();
                $realLink = str_replace('edit=0', 'edit='.$id, $actual_link);
                if ( wp_redirect( $realLink ) ) {
                    exit;
                }
            }else{
                print( "sauvegarde ok ");
                $productDao->update($id, $data);
            }
        }
        return $id;
    }
    
    public function doElementAction(){
    
        //remove data
        if ( isset( $_GET['remove'] ) ) {
            $productDao = new ProductDao();
            $productDao->delete($_GET['remove']);
        }
    }
    
    public function printForm($id){
        
    
        $productDao = new ProductDao();
        $result = $productDao->get($id);
        ?>
        <form method="post" >
            <label for="name" >name :</label><input type="text" id="name" name="name" value="<?php echo $result->name ?>" /> <br />
            <label for="price" >price :</label><input type="text" id="price" name="price" value="<?php echo $result->price ?>" />€<br />
            <label for="during" >during :</label><input type="text" id="during" name="during" value="<?php echo $result->during ?>" />min<br />
            <label for="categorie" >categorie :</label><input type="text" id="categorie" name="categorie" value="<?php echo $result->categorie ?>" /><br />
            <label for="describe" >describe :</label><textarea id="describe" name="describe"><?php echo $result->describe ?></textarea>
    
            <?php
                submit_button(); 
            ?>
        </form>
        <?php
    }
    

    private function getCurrentUlr(){
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $actual_link;
    }
    public function printList(){
    
        $productDao = new ProductDao();
        $results = $productDao->getAll();
        $actual_link = $this->getCurrentUlr();
        ?>
        <table>
        <tr>
                <th>name</th>
                <th>price (€)</th>
                <th>during (min)</th>
                <th>describe</th>
                <th>categorie</th>
                <th></th>
            
            </tr>
        <?php
        foreach($results as $result){		
            ?>
                <tr>
                    <td><a href="<?php echo $actual_link ?>&edit=<?php echo $result->id ?>"><?php echo $result->name ?></a></td>
                    <td><?php echo $result->price ?></td>
                    <td><?php echo $result->during ?></td>
                    <td><pre><?php echo $result->describe ?></pre></td>
                    <td><?php echo $result->categorie ?></td>
                    <td><a href="<?php echo $actual_link ?>&remove=<?php echo $result->id ?>"><i class="fas fa-trash"></i>delete</a></td>
                
                </tr>
            <?php 
        }
        ?>
        </table>
        <a href="<?php echo $actual_link ?>&edit=0" class="add button button-primary"><i class="fas fa-plus"></i>Ajouter un produit</a>
        <?php
    }
}
    