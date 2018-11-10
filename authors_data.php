<?php
/*
Plugin Name: Data About Authors
Description: Plugin with the data of the authors
Author: Vasilisa Vaiman
Version: 1.0
Author URI: http://v-vaiman.zzz.com.ua/
*/

/* Підключаємо наш файл з функціями*/
require_once (dirname(__FILE__).'/functions.php');

add_action( 'admin_init', 'authors_admin_init' );
add_action( 'admin_menu', 'authors_admin_menu' );


function authors_admin_init() {
	/* Реєструємо скрипти. */
	wp_register_style( 'datatables-bundle-css', '//cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/sl-1.2.5/datatables.min.css');
	wp_register_style( 'pnotify-css', '//cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.css');
	wp_deregister_script( 'jquery' );
	wp_register_script( 'popper-js', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js');
	wp_register_script( 'pnotify-js', '//cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js');
	wp_register_script( 'pnotify-js', '//cdnjs.cloudflare.com/ajax/libs/pnotify/3.2.1/pnotify.js');
	wp_register_script( 'datatables-bundle-js', '//cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/sl-1.2.5/datatables.min.js',  array('popper-js'));
	wp_register_script( 'authors-plugin-script-js', plugin_dir_url(__FILE__) . '/functions.js');
}

function authors_admin_menu() {
	/* Реєструємо і додаємо сторінки плагіну в меню адміністратора*/
	$plgn_MainPage = add_menu_page( 'Authors', 'Authors', 'manage_options', 'authors_setting_page', 'authors_admin_page');
	$plgn_CustomerProfilePage = add_submenu_page('null', 'Профіль клієнта', 'Профіль клієнта', 'manage_options', 'customer-profile-page', 'customer_profile_page_callback');
	/* Використовуємо зареєстрований плагін для завантаження скрипта */
	add_action( 'admin_print_scripts-' . $plgn_MainPage, 'authors_admin_scripts' );
	add_action( 'admin_print_scripts-' . $plgn_CustomerProfilePage, 'authors_admin_scripts' );
}

function authors_admin_scripts() {
	/* Підключаємо скрипти до сторінки плагіна */
	wp_enqueue_style( 'pnotify-css' );
	wp_enqueue_style( 'datatables-bundle-css' );
	wp_enqueue_script( 'popper-js' );
	wp_enqueue_script( 'pnotify-js' );
	wp_enqueue_script( 'datatables-bundle-js' );
	wp_enqueue_script( 'authors-plugin-script-js' );
}


/* Наповнюємо головну сторінку плагіна */
function authors_admin_page() {
	?>
	<div class="container-fluid" style="padding-top: 24px; padding-right: 35px;">
        <h3>Список авторів</h3>
        <hr class="my-4">
		<div class="row">
			<!-- Використовуючи  стилі datatables малюємо табличку з авторами-->
			<table id="authors-table" class="display table" style="width:100%;  border-collapse: collapse !important;"></table>
		</div>
	</div>
<?php

}

/* Наповнюємо сторінку плагіна з профілями авторів*/
function customer_profile_page_callback() {
		$customerID = $_GET['id'];
		$user_info = get_userdata($customerID);
		echo '<h1>Профіль клієнта ' . get_the_author_meta( "display_name",$customerID) . ' (id: ' . $customerID . ')</h1>';
		echo "<script>var customerID =" . $customerID . "</script>"; ?>

		<div class="authors-info" style="padding-top: 20px; padding-left: 20px;">
            <div class="authors-avatar" style="float: left; margin-right: 10px">
                <?php  echo get_avatar( $customerID , 70 ); ?>
            </div>
            <div class="authors-data" style="">
                <?php
                    echo '<h8><b>Емейл: </b>' . $user_info->user_email . '</h8><br>';
                    if (empty (get_the_author_meta( "phone",$customerID))) {
	                    echo '<h8><b>Телефон: </b> не вказаний</h8><br>';
                    } else {
	                    echo '<h8><b>Телефон: </b>' . get_the_author_meta( "phone", $customerID ) . '</h8><br>';
                    }
                    echo '<h8><b>Дата реєстрації: </b>' . $user_info->user_registered . '</h8>';
                ?>
            </div>
        </div>

	<div class="container-fluid" style="padding-top: 24px; padding-right: 35px;">
        <h3>Список оголошень</h3>
        <hr class="my-4">
		<div class="row">
			<!-- Використовуючи  стилі datatables малюємо табличку з оголошеннями-->
			<table id="authors-posts-table" class="display table" style="width:100%;  border-collapse: collapse !important;"></table>
		</div>
	</div>

<?php
	
}