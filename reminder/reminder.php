<?php
/*
 * Kauf auf Rechnung inkl. Bonitätsprüfung | SAHU MEDIA ® 
 *
 * @package bonitaet-sahu
 * @copyright Copyright (c) 2020, SAHU MEDIA®
*/

/*
//
// CronJob in Wordpress anlegen.
// Täglich soll geprüft werden.
//
*/

if ( ! wp_next_scheduled( 'sahu_karb_check_order_status' ) ) {
	wp_schedule_event( time(), 'hourly', 'sahu_karb_check_order_status' );
}

// Hook der jede Stunde den Cron feuert.
add_action( 'sahu_karb_check_order_status', 'sahu_karb_check_order' );

// Zeit berechnen zwecks Abstand!

function dateDiffInDays($date1, $date2)  
{ 
    // Calulating the difference in timestamps 
    $diff = strtotime($date2) - strtotime($date1); 
      
    // 1 day = 24 hours 
    // 24 * 60 * 60 = 86400 seconds 
    return abs(round($diff / 86400)); 
} 

// Funktion für den Cron.
function sahu_karb_check_order() {
	global $wpdb;
	
	// Hole dir Informationen aus den Zahlungseinstellungen
	$payment_gateway_id = 'sahu_karb_gateway'; // um welches Gateway geht es?
	// Get an instance of the WC_Payment_Gateways object
	$payment_gateways   = WC_Payment_Gateways::instance();
	// Get the desired WC_Payment_Gateway object
	$payment_gateway    = $payment_gateways->payment_gateways()[$payment_gateway_id];

	// Speichere dir die Information in einer Variable
	$mahndatum_karb = $payment_gateway->mahndatum;
	$exportdatum_karb = $payment_gateway->export;
	
	$post_status = implode("','", array('wc-on-hold', 'wc-karb-versendet', 'wc-karb-mahnung', 'wc-karb-export') );
	
	
	$orders = $wpdb->get_results( "SELECT * FROM $wpdb->posts 
							WHERE post_type = 'shop_order'
							AND post_status IN ('{$post_status}')
							ORDER BY post_date DESC
				");
	
	$rt = array('wc-on-hold', 'wc-karb-versendet', 'wc-karb-mahnung', 'wc-karb-export');
	
	foreach($orders as $order){
		
		$order_id = $order->ID;
		// Get an instance of the WC_Order object
        $order = wc_get_order( $order_id );      
		$order_data = $order->get_data(); // The Order data
		$order_id = $order_data['id'];
		$order_parent_id = $order_data['parent_id'];
		$order_status = $order_data['status'];
		$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
		$date = date('Y-m-d H:i:s', time());
  
		// End date 
		$date1 = $date; 
		$date2 = $order_date_created; 
			
		// Function call to find date difference 
		$dateDiff = dateDiffInDays($date1, $date2); 
			
		// Display the result 
		$order_status = 'wc-'.$order_status; 
        if(in_array($order_status,  $rt)){
							
			if($dateDiff>$mahndatum_karb && $order_status=='wc-karb-versendet'){ // Versende nach 10 Tagen Standard eine Zahlungserinnerung!

				$order->update_status('wc-karb-mahnung'); 
				update_post_meta($order_id, 'first_state', $date);
					//note this line is different
					//because I already have the ID from the hook I am using.
					$order = wc_get_order( $order_id );

					// The text for the note
					$note = 'Kunde erhält eine Mahnung zu seiner Bestellung per E-Mail';

					// Add the note
					$order->add_order_note( $note );

					// Save the data
					$order->save();
					
					// E-Mail für Pending auf In Wartestellung
						// Create a mailer
						global $woocommerce;
						$order = new WC_Order( $order_id );			
						$mailer = $woocommerce->mailer();
						global $wpdb;
						$account_details = get_option( 'woocommerce_bacs_accounts',
									array(
										array(
											'account_name'   => get_option( 'account_name' ),
											'account_number' => get_option( 'account_number' ),
											'sort_code'      => get_option( 'sort_code' ),
											'bank_name'      => get_option( 'bank_name' ),
											'iban'           => get_option( 'iban' ),
											'bic'            => get_option( 'bic' )
										)
									)

								);


						$account_fields = array(
							'bank_name'      => array(
								'label' => 'Bank',
								'value' => $account_details[0]['bank_name']
							),
							'account_name'   => array(
								'label' => 'Account Name',
								'value' => $account_details[0]['account_name']
							),
							'account_number' => array(
								'label' => __( 'Account Number', 'woocommerce' ),
								'value' => $account_details[0]['sort_code'].' '.$account_details[0]['account_number']
							),
							'bic'            => array(
								'label' => __( 'BIC', 'woocommerce' ),
								'value' => $account_details[0]['bic']
							),
							'iban'            => array(
								'label' => __( 'IBAN', 'woocommerce' ),
								'value' => $account_details[0]['iban']
							)
						);
						
						$sahukarbgetcurrency = get_woocommerce_currency_symbol();
						$sahukarbsitename = get_site_url();
						$sahukarborderamount = $order->get_total();
						$sahukarbordernumber = $order->get_order_number();
						$sahukarbbankname = $account_details[0]['bank_name'];
						$sahukarbbankiban = $account_details[0]['iban'];
						$sahukarbbankbic = $account_details[0]['bic'];

						$message_body = nl2br(get_option('sahu_karb_textreminder'));
						 
						$message_body = str_replace('[sahu_karb_currency]', $sahukarbgetcurrency , $message_body);
						$message_body = str_replace('[sahu_karb_shopname]', $sahukarbsitename , $message_body);
						$message_body = str_replace('[sahu_karb_amount]', $sahukarborderamount , $message_body);
						$message_body = str_replace('[sahu_karb_ordernumber]', $sahukarbordernumber , $message_body);
						$message_body = str_replace('[sahu_karb_bankname]', $sahukarbbankname , $message_body);
						$message_body = str_replace('[sahu_karb_bankiban]', $sahukarbbankiban , $message_body);
						$message_body = str_replace('[sahu_karb_bankbic]', $sahukarbbankbic , $message_body);

					  $message = $mailer->wrap_message(
					  // Message head and message body.
					  sprintf( __( 'Mahnung zur Bestellung %s' ), $order->get_order_number() ), $message_body );

					  // Cliente email, email subject and message.
					  $mailer->send( $order->billing_email, sprintf( __( 'Mahnung zur Bestellung %s' ), $order->get_order_number() ), $message );
				
			}
			
			if($dateDiff>$exportdatum && $order_status=='wc-karb-mahnung'){ // Setzte Status auf Rechnung Export, nach 25 Tagen

				$order->update_status('wc-karb-export'); 
                update_post_meta($order_id, 'second_state', $date);							
				
			}
			

		}
	}
}

/*
 * Registriere die neuen Staten
 */
 
include( plugin_dir_path( __FILE__ ) . '/states.php');
