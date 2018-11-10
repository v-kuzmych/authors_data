<?php

/* Функція для обробки даних авторів вибраних з бази даних */
function getAuthorsData() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'posts';
	$author_rows = $wpdb->get_results( "SELECT post_author  FROM $table_name where post_type='arenda' GROUP BY post_author " );

	$url = get_admin_url(null, 'admin.php?page=customer-profile-page&id=');

	/* Присвоюємо нашій табличці значення */
	foreach ( $author_rows as $author_row ) {
			$author_row->author_url      = "<a href=" . $url . $author_row->post_author . " target=\"_blank\" >" . get_the_author_meta( "login", $author_row->post_author) . "</a>";
			$author_row->author_name     = get_the_author_meta( "display_name", $author_row->post_author);
			$author_row->author_phone    = get_the_author_meta( "phone", $author_row->post_author);
			$author_row->count_all_posts = count_user_posts_by_post_type ('arenda', $author_row->post_author);
			$author_row->count_posts	 = count_user_posts ( $author_row->post_author, 'arenda', true );
}
	wp_send_json( $author_rows);
}

add_action( 'wp_ajax_get_authors_data', "getAuthorsData" );

/* Функція для обробки даних постів вибраних з бази даних */
function getAuthorsPostData() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'posts';
	$customerID  = $_POST['customerID'];
	$post_rows = $wpdb->get_results( "SELECT * FROM $table_name where post_type = 'arenda' and post_author='$customerID' " );

	$post_status = array(
		"draft" => array(
			"label" => "Приховано",
			"color" => "secondary"
		),
		"publish" => array(
			"label" => "Опубліковано",
			"color" => "success"
		),
	);

	/* Присвоюємо нашій табличці значення */
	foreach ( $post_rows as $post_row ) {
		$current_post_status         = $post_status[$post_row->post_status];
		$color                       = $current_post_status['color'];
		$label                       = $current_post_status['label'];
		$post_row->post_id           = $post_row -> ID;
		$post_row->post_url          = "<a href=" . get_edit_post_link($post_row -> ID) . " target=\"_blank\" >" . get_the_title($post_row -> ID) . "</a>";
		$post_row->post_address      = get_post_meta($post_row -> ID, 'cc_address');
		$post_row->date              = get_post_field( 'post_date', $post_row -> ID );
		$post_row->post_status_badge = "<span class=\"badge badge-" . $color . "\">" . $label . "</span>";
}
	wp_send_json( $post_rows);
}

add_action( 'wp_ajax_get_authors_posts_data', "getAuthorsPostData" );

//Функція для видалення оголошення з бази даних
function deleteAuthorsPost() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'posts';
	$post_id  = $_POST['post_id'];
	$wpdb->delete( $table_name, array( 'ID' => $post_id ) );
}

add_action( 'wp_ajax_delete_authors_post', "deleteAuthorsPost" );

//Функція для оновлення статусу оголошення
function updatePostStatusCode() {
	global $wpdb;
	$table_post     	  = $wpdb->prefix . 'posts';
	$table_postmeta     	  = $wpdb->prefix . 'postmeta';
	$post_id        	  = $_POST['post_id'];

	$new_post_status_code = $_POST['new_post_status_code'];
	$wpdb->update( $table_postmeta, array( 'meta_value' => current_time( 'mysql' ) ), array( 'meta_key' => 'cc_listing_duration' ) );
	$wpdb->update( $table_post, array( 'post_status' => $new_post_status_code ), array( 'ID' => $post_id ) );
}

add_action( 'wp_ajax_update_post_status_code', "updatePostStatusCode" );

//Функція для підрахунку всіх постів автора
function count_user_posts_by_post_type($post_type = 'arenda',$user_id = 0){
	global $wpdb;
	$count = $wpdb->get_var(
		$wpdb->prepare(" SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = %s AND post_author = %d", $post_type, $user_id)
	);
	return ($count) ? $count : 0;
}

       