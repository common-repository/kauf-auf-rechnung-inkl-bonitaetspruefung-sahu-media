<?php
/*
 * Kauf auf Rechnung inkl. Bonitätsprüfung | SAHU MEDIA ® 
 *
 * @package bonitaet-sahu
 * @copyright Copyright (c) 2020, SAHU MEDIA®
*/

function sahu_karb_wc_register_post_statuses() {
	register_post_status( 'wc-karb-versendet', array(
	'label' => _x( 'Versendet', 'Versendet', 'text_domain' ),
	'public' => true,
	'exclude_from_search' => false,
	'show_in_admin_all_list' => true,
	'show_in_admin_status_list' => true,
	'label_count' => _n_noop( 'Versendet (%s)', 'Versendet (%s)', 'text_domain' )
	) );
	register_post_status( 'wc-karb-mahnung', array(
	'label' => _x( 'Mahnung (Rechnung)', 'Mahnung (Rechnung)', 'text_domain' ),
	'public' => true,
	'exclude_from_search' => false,
	'show_in_admin_all_list' => true,
	'show_in_admin_status_list' => true,
	'label_count' => _n_noop( 'Rechnung (%s)', 'Rechnung (%s)', 'text_domain' )
	) );
	register_post_status( 'wc-karb-export', array(
	'label' => _x( 'Export (Rechnung)', 'Export (Rechnung)', 'text_domain' ),
	'public' => true,
	'exclude_from_search' => false,
	'show_in_admin_all_list' => true,
	'show_in_admin_status_list' => true,
	'label_count' => _n_noop( 'Export (Rechnung) (%s)', 'Export (Rechnung) (%s)', 'text_domain' )
	) );
}
add_filter( 'init', 'sahu_karb_wc_register_post_statuses' );

// Packe den Status in die Bestell Singel-Übersicht 

function sahu_karb_wc_add_order_statuses( $order_statuses ) {
	$order_statuses['wc-karb-versendet'] = _x( 'Versendet', 'Versendet', 'text_domain' );
	$order_statuses['wc-karb-mahnung'] = _x( 'Mahnung', 'Mahnung', 'text_domain' );
	$order_statuses['wc-karb-export'] = _x( 'Export (Rechnung)', 'Export', 'text_domain' );
	return $order_statuses;
}
add_filter( 'wc_order_statuses', 'sahu_karb_wc_add_order_statuses' );

// Packe den Status in die Allgemeine Übersicht (BULK)

function sahu_karb_register_bulk_action( $bulk_actions ) {

    $bulk_actions['mark_karb-versendet'] = 'Status auf "Versendet" setzten';
    return $bulk_actions;

}
add_filter( 'bulk_actions-edit-shop_order', 'sahu_karb_register_bulk_action' );