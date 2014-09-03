<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RTWPIdeasAutoProductSynchronization
 *
 * @author kishore
 */
if( !class_exists( 'RTWPIdeasAutoProductSynchronization' ) ){
	class RTWPIdeasAutoProductSynchronization {

		public function __construct() {
			$taxonomy_metadata = new Rt_Wp_Ideas_Taxonomy_Metadata\Taxonomy_Metadata();
			$taxonomy_metadata->activate();
			$this->hooks();
		}
		
		
		function hooks() {
			if ( get_option( 'wpideas_auto_product_synchronizationenabled' ) == 1 ){
				add_action( 'save_post', array( $this, 'insert_products' ) );
				add_action( 'wp_untrash_post', array( $this, 'insert_products' ) );
				add_action( 'delete_post', array( $this, 'delete_products' ) );
				add_action( 'trashed_post', array( $this, 'delete_products' ) );
			}
		}
		
		function old_product_synchronization_enabled() {
			if ( get_option( 'wpideas_old_product_synchronizationenabled' ) == 1 ){
				// $this->insert_products();
				$this->delete_products();
			}
		}

		/**
		 * insert_products function.
		 *
		 * @access public
		 * @return void
		 */
		public function insert_products( $post_id ) {
		  	
		   
		  // If this is just a revision, don't.
		  if ( wp_is_post_revision( $post_id ) || empty( $_POST['post_type'] ) ){
			return;
		  }
		  
		  // If this isn't a 'product' post, don't update it.
    	  if ( 'product' != $_POST['post_type'] ){
        	return;
    	  }
		  
		  $args = array( 'posts_per_page' => -1, 'post_type' => 'product' );
		  $products_array = get_posts( $args ); // Get Woo Commerce post object
		  $product_names = wp_list_pluck( $products_array, 'post_title' ); // Get Woo Commerce post_title
		  $product_ids = wp_list_pluck( $products_array, 'ID' ); // Get Woo Commerce Post ID
		
		  $taxonomies = array(
		    'product' => $product_names,
		    'product_id' => $product_ids
		  );
		  
		  /*foreach ( $taxonomies as $taxonomy => $terms ) {
		    foreach ( $terms as $term ) {
		      if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) && $taxonomy == "product" && ! empty( $_POST['ID'] ) ){
		      	$term = wp_insert_term( $term, $taxonomy );
			  	$term_id = $term["term_id"];
				Rt_Wp_Ideas_Taxonomy_Metadata\add_term_meta($term_id, "_product_id", $_POST['ID'], true); // todo: need to fetch product_id
			  }
		    }
		  }*/
		  
		  $taxonomy = "product";
		  $term = sanitize_title( $_POST['post_title'] );
		  
		  if ( $taxonomy == "product" && ! empty( $_POST['ID'] ) ){
				$post = get_post( $_POST['ID'] );
				$slug = $post->post_name;
		      	$term = wp_insert_term(
				  $term, // the term 
				  'product', // the taxonomy
				  array(
				    'slug' => $slug
				  )
				);
				if (is_array($term)){
					$term_id = $term["term_id"];
					Rt_Wp_Ideas_Taxonomy_Metadata\add_term_meta($term_id, "_product_id", $_POST['ID'], true); // todo: need to fetch product_id
				}
		  }
		  
		  
		
		}
		
		/**
		 * delete_products function.
		 *
		 * @access public
		 * @return void
		 */
		public function delete_products() {
			$args = array( 'posts_per_page' => -1, 'post_type' => 'product' ); // get all woo commerce product
			$products_array = get_posts( $args );
			$product_names = wp_list_pluck( $products_array, 'post_name' );
			
			$product_taxonomies = get_terms( 'product', 'hide_empty=0' ); // Get all the product list from product taxonomy under Ideas
			$product_taxonomy_names = wp_list_pluck( $product_taxonomies, 'slug' );
			
			$product_taxonomies_to_delete = array_diff($product_taxonomy_names, $product_names); // Do a array diff
			
			foreach ( $product_taxonomies_to_delete as $product_taxonomy_to_delete ) {
				$product_taxonomies_obj = get_term_by('slug', $product_taxonomy_to_delete, 'product');
				wp_delete_term( $product_taxonomies_obj->term_id, 'product' ); // Now Delete those products which are not present in woo-commerce product section.
				Rt_Wp_Ideas_Taxonomy_Metadata\delete_term_meta($product_taxonomies_obj->term_id, '_product_id');
			}
		}
		
		
				
	}
}