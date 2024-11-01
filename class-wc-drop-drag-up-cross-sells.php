<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @class      WC_Product_Drop_Drag_Up_Cross_Sells
 * @version    1.0.1
 * @category   Class
 * @author     Huu Ha
 */

if(!class_exists('WC_Product_Drop_Drag_Up_Cross_Sells')){

  class WC_Product_Drop_Drag_Up_Cross_Sells {

         public static $plugin_url;
         public static $plugin_path;
         public static $plugin_basefile;

         private $options_data = false;

         /**
          * Constructor
          */
         public function __construct(){
            WC_Product_Drop_Drag_Up_Cross_Sells::$plugin_basefile = plugin_basename(__FILE__);
            WC_Product_Drop_Drag_Up_Cross_Sells::$plugin_url = plugin_dir_url(WC_Product_Drop_Drag_Up_Cross_Sells::$plugin_basefile);
            WC_Product_Drop_Drag_Up_Cross_Sells::$plugin_path = trailingslashit(dirname(__FILE__));
            add_action('woocommerce_init', array(&$this, 'init'));
         }


      /**
       * enqueue_scripts function.
       *
       * @access public
       * @return void
       */
      public function enqueue_scripts() {
         wp_enqueue_script( 'jquery' );
         wp_enqueue_script( 'jquery-ui-tabs' );

         // wp_enqueue_style( 'sortable', plugins_url( 'assets/css/sortable.css' , __FILE__ ), array(), '1.0.0' );
         
         wp_enqueue_script( 'sortable', plugins_url( 'assets/js/Sortable.js' , __FILE__ ), array(), '', true );

         wp_enqueue_style( 'mhc-main-css', plugins_url( 'assets/css/style.css' , __FILE__ ), array(), '1.0.0' );
         
         wp_enqueue_script( 'mhc-script-js', plugins_url( 'assets/js/custom.js' , __FILE__ ), array(), '', true );

      }

      /**
       * Gets saved data
       * It is used for displaying the data value in template file
       * @return array
       */
      public function get_value($post_id, $field_id){

         $meta_value = get_post_meta($post_id, 'wc_productdata_options', true);
         $meta_value = $meta_value[0];

        return (isset($meta_value[$field_id])) ? $meta_value[$field_id] : '';

      }



      /**
      * Init the extension once we know WooCommerce is active
      *
      * @return void
      */
      public function init(){

         add_filter('woocommerce_product_data_tabs', array($this, 'change_linked_product_target'));

         add_action('woocommerce_product_data_panels', array($this, 'drop_drag_panel'));

         //Used WC Product Data Store  to save for upsell_ids and cross_sell_ids 
         // select name
         // Save Fields for other fields 
         // add_action('woocommerce_process_product_meta', array($this, 'product_save_data'), 10, 2);

         add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts') );

      }


      /**
      * Change linked product target to #drop_drap_linked_product_data
      *
      * @return string
      */
      public function change_linked_product_target($tabs){
         $tabs['linked_product']['label'] = 'Linked';
         $tabs['linked_product']['target'] = 'drop_drap_linked_product_data';
         return $tabs;
      }


      /**
      * Adds the panel to the Product Data postbox in the product interface
      *
      * @return string
      */
      public function drop_drag_panel(){
         global $post, $thepostid, $product_object;
         $upsell_ids = $product_object->get_upsell_ids( 'edit' );
         $crosssell_ids = $product_object->get_cross_sell_ids( 'edit' );
         $grouped_products = $product_object->get_children( 'edit' );
         $args = array(
             'taxonomy'   => "product_cat",
             'number'     => $number,
             'orderby'    => $orderby,
             'order'      => $order,
             'hide_empty' => $hide_empty,
             'include'    => $ids
         );
         $product_categories = get_terms($args);
         $c = 0;
         ?>
            <div id="drop_drap_linked_product_data" class="panel woocommerce_options_panel hidden">
               <div id="drop_drap_product_options" class="drop-drag-wrap">

                  <ul class="drop_drap_data_tabs hh-tabs">
                     <?php foreach ($product_categories as $cat) { ?>
                        <li><a href="#<?php echo $cat->slug ?>"><?php echo $cat->name; ?></a></li>
                     <?php } ?>
                  </ul>
                  <div id="categories_sort" class="categories_sort_wrap">
                     <?php foreach ($product_categories as $cat) { ?>

                        <ul id="<?php echo $cat->slug; ?>" class="hh_panel hh_list">
                           <?php  
                           $args = array( 
                              'post_type' => 'product', 
                              'posts_per_page' => -1, 
                              'product_cat' =>  $cat->slug
                           );
                           $products_query = new WP_Query( $args );

                           while ( $products_query->have_posts() ) : $products_query->the_post(); 
                              global $product; 
                              echo '<li data-id="' . get_the_ID() . '" class="from_cat">'.$product->get_image( array(70,70) ).'<i class="js-remove">×</i><input type="hidden" name="product_ids[]" value="' . esc_attr( get_the_ID() ) . '"></li>';
                           endwhile; 
                           wp_reset_query();
                           
                           ?>
                        </ul>
                     <?php } ?>
                  </div>
               </div>
               <div id="up-cross-sells-wrap">
                  <div class="up-title"><?php _e( 'Upsells', 'woocommerce' ); ?></div>
                  <ul id="upsells-area" class="hh_list">
                     <?php
                        foreach ( $upsell_ids as $product_id ) {
                           $pro = wc_get_product( $product_id );
                           if ( is_object( $pro ) ) {
                              echo '<li data-id="' . esc_attr( $product_id ) . '">'.$pro->get_image( array(70,70) ) . '<i class="js-remove">×</i><input type="hidden" name="upsell_ids[]" value="' . esc_attr( $product_id ) . '"></li>';
                           }
                        }
                     ?>
                  </ul>
                  <div class="cross-title cross-sells"><?php _e( 'Cross-sells', 'woocommerce' ); ?></div>
                  <ul id="cross-sells-area" class="hh_list">
                     <?php
                        foreach ( $crosssell_ids as $product_id ) {
                           $pro = wc_get_product( $product_id );
                           if ( is_object( $pro ) ) {
                              echo '<li data-id="' . esc_attr( $product_id ) . '">'.$pro->get_image( array(70,70) ) . '<i class="js-remove">×</i><input type="hidden" name="crosssell_ids[]" value="' . esc_attr( $product_id ) . '"></li>';
                           }
                        }
                     ?>
                  </ul>
                  <div class="cross-title grouped_products"><?php _e( 'Grouped products', 'woocommerce' ); ?></div>
                  <ul id="grouped-products-area" class="hh_list">
                     <?php
                        foreach ( $grouped_products as $product_id ) {
                           $pro = wc_get_product( $product_id );
                           if ( is_object( $pro ) ) {
                              echo '<li data-id="' . esc_attr( $product_id ) . '">'.$pro->get_image( array(70,70) ) . '<i class="js-remove">×</i><input type="hidden" name="grouped_products[]" value="' . esc_attr( $product_id ) . '"></li>';
                           }
                        }
                     ?>
                  </ul>
               </div>
            </div>
         <?php
      }


      /**
      * Saves the data inputed into the product boxes
      *
      * @param int $post_id the post (product) identifier
      * @param stdClass $post the post (product)
      * @return void
      */
      public function product_save_data($post_id, $post){

         $_upsell_ids =  maybe_unserialize($_POST['upsell_ids']);
         update_post_meta( $post_id, '_upsell_ids', $_upsell_ids );

         $_cross_sell_ids =  maybe_unserialize($_POST['cross_sell_ids']);
         update_post_meta( $post_id, '_cross_sell_ids', $_cross_sell_ids );

      }


  }

}

/**
* Instantiate Class
*/

$wc_mhc = new WC_Product_Drop_Drag_Up_Cross_Sells();
