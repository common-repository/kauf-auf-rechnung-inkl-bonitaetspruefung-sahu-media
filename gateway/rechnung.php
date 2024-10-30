<?php
/*
 * Kauf auf Rechnung inkl. Bonitätsprüfung | SAHU MEDIA ® 
 *
 * @package bonitaet-sahu
 * @copyright Copyright (c) 2020, SAHU MEDIA®
*/

/**
 * Add the gateway to WC Available Gateways
 * 
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
function sahu_karb_wc_add_to_gateways( $gateways ) {
	$gateways[] = 'WC_Gateway_Sahu_KAR_boni';
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'sahu_karb_wc_add_to_gateways' );


/**
 * Adds plugin page links
 * 
 * @since 1.0.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function sahu_karb_wc_gateway_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=sahu_karb_gateway' ) . '">' . __( 'Configure', 'wc-gateway-sahu_karb' ) . '</a>'
	);

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'sahu_karb_wc_gateway_plugin_links' );


/**
 * Offline Payment Gateway
 *
 * Provides an Offline Payment Gateway; mainly for testing purposes.
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class 		WC_Gateway_Sahu_KAR_boni
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 * @author 		SkyVerge
 */
 
add_action( 'plugins_loaded', 'sahu_karb_wc_gateway_init', 11 );

function sahu_karb_wc_gateway_init() {

	class WC_Gateway_Sahu_KAR_boni extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
	  
			$this->id                 = 'sahu_karb_gateway';
			$this->icon               = apply_filters('woocommerce_offline_icon', '');
			$this->has_fields         = false;
			$this->method_title       = __( 'Kauf auf Rechnung', 'wc-gateway-sahu_karb' );
			$this->method_description = __( 'Kunden können bei dir Kauf auf Rechnung tätigen, es findet ebenfalls vorher eine Bonitätsprüfung statt.', 'wc-gateway-sahu_karb' );
		  
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
		  
			// Define user set variables
			$this->title          = $this->get_option( 'title' );
			$this->description    = $this->get_option( 'description' );
			$this->instructions   = $this->get_option( 'instructions', $this->description );
			$this->bscore         = $this->get_option( 'bscore' );
			$this->clientid       = $this->get_option( 'clientid' );
			$this->clientlicence  = $this->get_option( 'clientlicence' );
			$this->mahndatum    = $this->get_option( 'mahndatum' );
			$this->export    = $this->get_option( 'export' );
			
			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		  
			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}
	
	
		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {
	  
			///////////////////////////
			// STEFFEN MORGEN ZEIGEN //
			///////////////////////////
			
			$dateschiebe1 = array(
		  		
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'wc-gateway-sahu_karb' ),
					'type'    => 'checkbox',
					'label'   => __( 'Kauf auf Rechnung aktivieren', 'wc-gateway-sahu_karb' ),
					'default' => 'no'
				),
				
				'title' => array(
					'title'       => __( 'Title', 'wc-gateway-sahu_karb' ),
					'type'        => 'text',
					'description' => __( 'Dies steuert den Titel für die Zahlungsmethode, die der Kunde beim Auschecken sieht.', 'wc-gateway-sahu_karb' ),
					'default'     => __( 'Kauf auf Rechnung', 'wc-gateway-sahu_karb' ),
					'desc_tip'    => true,
				),
				
				'description' => array(
					'title'       => __( 'Description', 'wc-gateway-sahu_karb' ),
					'type'        => 'textarea',
					'description' => __( 'Beschreibung der Zahlungsmethode, die der Kunde an Ihrer Kasse sieht.', 'wc-gateway-sahu_karb' ),
					'default'     => __( 'Erhalte erst das Produkt und zahle nach 14 Tagen. Bitte beachte das wir eine Bonitätsprüfung durchführen.', 'wc-gateway-sahu_karb' ),
					'desc_tip'    => true,
				),
				
				'instructions' => array(
					'title'       => __( 'Instructions', 'wc-gateway-sahu_karb' ),
					'type'        => 'textarea',
					'description' => __( 'Anweisungen, die der Dankesseite und den E-Mails hinzugefügt werden.', 'wc-gateway-sahu_karb' ),
					'default'     => '',
					'desc_tip'    => true,
				),		

						'mahndatum' => array(
							'title'       => __( 'Mahndatum / Erinnerung', 'wc-gateway-sahu_karb' ),
							'type'        => 'text',
							'description' => __( 'Nach wie vielen Tagen, soll der Kunde erinnert / gemahnt werden? Wir empfehlen dir 10!', 'wc-gateway-sahu_karb' ),
							'default'     => __( '10', 'wc-gateway-sahu_karb' ),
							'desc_tip'    => true,
						),
						
						
						'export' => array(
							'title'       => __( 'Export Status', 'wc-gateway-sahu_karb' ),
							'type'        => 'text',
							'description' => __( 'Nach wie vielen Tagen, soll die Bestellung auf den Status "Export" gestellt werden? Wir empfehlen dir 25!', 'wc-gateway-sahu_karb' ),
							'default'     => __( '25', 'wc-gateway-sahu_karb' ),
							'desc_tip'    => true,
						),				
				
			) ;
			
			if(!empty(get_option( 'sahu_karb_product_options_license' ))):
				if((lizenzpruefung() == 2) || (lizenzpruefung() == 3)): 
					$this->form_fields = apply_filters( 'sahu_karb_wc_form_fields', $dateschiebe1);
					
				else:
				
					$dateschiebe2 = array(
						
						'bscore' => array(
							'title'       => __( 'Max. Score', 'wc-gateway-sahu_karb' ),
							'type'        => 'text',
							'description' => __( 'Es gibt einen Score von 1 - 6 (Schulnoten). Ab wann soll Kauf auf Rechnung genehmigt werden? ', 'wc-gateway-sahu_karb' ),
							'default'     => __( '3.0', 'wc-gateway-sahu_karb' ),
							'desc_tip'    => true,
						),	
						
					) ;
					
					$ergebnis = array_merge($dateschiebe1, $dateschiebe2);
					$this->form_fields = apply_filters( 'sahu_karb_wc_form_fields', $ergebnis);
					
				endif;
			else:
				
				$this->form_fields = apply_filters( 'sahu_karb_wc_form_fields', $dateschiebe1);
			
			endif;
							
		}
	
		/**
		 * Output for the order received page.
		 */
		public function thankyou_page() {
			if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) );
			}
		}
	
	
		/**
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order
		 * @param bool $sent_to_admin
		 * @param bool $plain_text
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		
			if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'processing' ) ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
		}
	
		public function payment_fields(){

            if ( $description = $this->get_description() ) {
                echo wpautop( wptexturize( $description ) );
            }

            ?>
            <div id="custom_input">
                <p class="form-row form-row-wide">
                    <label for="sahubday" class=""><?php _e('Dein Geburtsdatum', ''); ?></label>
                    <input type="date" class="" name="sahubday" id="sahubday" placeholder="z.B. 22.03.1988" value="" pattern="\d{4}-\d{2}-\d{2}">
                </p>
            </div>
            <?php
        }
		
		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {
			
			$order = wc_get_order( $order_id );
			
			if(!empty(get_option( 'sahu_karb_product_options_license' ))){
						
				$order_data = $order->get_data(); // The Order data
				
				$data = $order_id;
				$count = 1;
				WC()->session->set( 'sahuorderid' , $data.'-'.$count );
				
				$retrive_data = WC()->session->get( 'sahuorderid' );
				
				$order_billing_first_name = $order_data['billing']['first_name'];
				$order_billing_last_name = $order_data['billing']['last_name'];
				$order_billing_company = $order_data['billing']['company'];
				$order_billing_address_1 = $order_data['billing']['address_1'];
				$order_billing_address_2 = $order_data['billing']['address_2'];
				$order_billing_city = $order_data['billing']['city'];
				$order_billing_state = $order_data['billing']['state'];
				$order_billing_postcode = $order_data['billing']['postcode'];
				$order_billing_country = $order_data['billing']['country'];
				$order_billing_email = $order_data['billing']['email'];
				$order_billing_phone = $order_data['billing']['phone'];
				$order_bday_date = $order->get_meta('_sahubday');				
				
				$data = new stdClass();
				$data->typ = "bonitaet";
				
				$kunde = new stdClass();
				
				$kunde->firstname = $order_billing_first_name;
				$kunde->lastname = $order_billing_last_name;
				$kunde->street = $order_billing_address_1;
				$kunde->houseNumber = '1';
				$kunde->postcode = $order_billing_postcode;
				$kunde->city = $order_billing_city;
              	$kunde->country = $order_billing_country;
				
				
				
				$sahubday = preg_replace("([^0-9-])", "", $_POST['sahubday']); // (1) sanitize date (remove all non-date-characters)
				$date = DateTime::createFromFormat('Y-m-d', $sahubday);  // (2) to validate: create DateTime from String ..
				$validDate = $date && $date->format('Y-m-d') === $sahubday; // .. then create new String from DateTime and compare against original
				
				// output message on validation error
				if (!$validDate) {
					wc_add_notice( __('Fehler: Ungültiges Geburtsdatum angegeben.', 'wc-gateway-sahu_karb'), 'error' );
					return;
				}
				
				$kunde->dateOfBirth = $sahubday;
				
				$data->daten = $kunde;
				
				file_put_contents("log.txt", print_r($data, TRUE) . PHP_EOL, FILE_APPEND);
				
				// replaced curl request with wp_remote_post method
				try {
					$options = [
						'body'        => wp_json_encode( $data ),
						'headers'     => [
							'Content-Type' => 'application/json',
							'License' => base64_encode(get_option( 'sahu_karb_product_options_license' ))
						],
						'httpversion' => '1.0',
						'data_format' => 'body',
					];
					
					$result = wp_remote_post( 'https://boni.sahu.media/api.php', $options );
					
					$bonitaet = json_decode($result['body']);
					$errorcode = $bonitaet->fehler->code;
					$boniscore = $bonitaet->daten->bonitaet;
					
					file_put_contents("log.txt", print_r($result['body'], TRUE) . PHP_EOL, FILE_APPEND);
					
					if ($boniscore < 1) {
						$order->update_status( 'failed', 'Prüfen der Bonität fehlgeschlagen.' );
					} else {
						// liquide ist, wenn bonität über 0, aber unter der grenze
						$liquide = ($boniscore < $this->bscore ? 1 : 0 );
					}
				} catch (Exception $e) {
					wc_add_notice( $e->getMessage(), 'error' );
					$order->update_status( 'failed', $e->getMessage() );
				}
				
				if(($errorcode == 2) || ($errorcode == 3)) {
							
					// Return zur Fehlerseite + parameter
					return array(
						'result' 	=> 'success',
						'redirect'	=> '?session=' . hash('sha512', date('H:i:s')) . md5(date('H:i:s')) . "&dt5W8", // Falscher Lizenzkey
					);
										
				}else{
					
					if($liquide == 1){
						
						// Makiere das ganze als Erfolg!
						$order->update_status( 'processing', __( 'Kauf auf Rechnung wurde genehmigt! Score liegt bei: '.$boniscore.'!', 'wc-gateway-sahu_karb' ) );
							
						// Reduziere Lagerbestand!
						$order->reduce_order_stock();
							
						// Lösche den Warenkorb
						WC()->cart->empty_cart();
							
						// Return zur ThankYou Seite
						return array(
							'result' 	=> 'success',
							'redirect'	=> $this->get_return_url( $order )
						);
					
					}else{
						
						// Makiere das ganze als Niederlage!
						$order->update_status( 'failed', __( 'Kauf auf Rechnung wurde <strong>nicht genehmigt</strong>!', 'wc-gateway-sahu_karb' ) );
							
						// Return zur Fehlerseite + parameter
						return array(
							'result' 	=> 'success',
							'redirect'	=> '?session=' . hash('sha512', date('H:i:s')) . md5(date('H:i:s')) . "&dt5W9",
						);
						
					}
					
				}	
				
			}else{
				
				// Makiere das ganze als Erfolg!
				$order->update_status( 'processing', __( 'Kauf auf Rechnung!', 'wc-gateway-sahu_karb' ) );
					
				// Reduziere Lagerbestand!
				$order->reduce_order_stock();
					
				// Lösche den Warenkorb
				WC()->cart->empty_cart();
					
				// Return zur ThankYou Seite
				return array(
					'result' 	=> 'success',
					'redirect'	=> $this->get_return_url( $order )
				);
				
			}
	}
	
  } // end \WC_Gateway_Offline class
}


/**
 * Prüfe ob Lizenz gültig, wenn nein disable plugin.
 */
add_filter( 'woocommerce_available_payment_gateways', 'sahu_karb_lizdisable' );
  
function sahu_karb_lizdisable( $available_gateways ) {
	if(!empty(get_option( 'sahu_karb_product_options_license' ))):
		if((lizenzpruefung() == 2) || (lizenzpruefung() == 3)):
		   if ( isset( $available_gateways['sahu_karb_gateway'] )) {
			  unset( $available_gateways['sahu_karb_gateway'] );
		   } 
		endif;   
	endif;
	
	return $available_gateways;
}

/**
 * Prüfe ob Guthaben vorhanden...wenn nein Disable.
 */
add_filter( 'woocommerce_available_payment_gateways', 'sahu_karb_guthabendisable' );
  
function sahu_karb_guthabendisable( $available_gateways ) {
	if(getUserData("kontostand") < getUserData("abfrage_kosten")):
		if ( isset( $available_gateways['sahu_karb_gateway'] )) {
		  unset( $available_gateways['sahu_karb_gateway'] );
		}   
	endif;
	
	return $available_gateways;
}

/**
 * Prüfe Land, unset Payment, wenn nicht DE,AT,CH
 
add_filter( 'woocommerce_available_payment_gateways', 'sahu_karb_disableforcountry' );
  
function sahu_karb_disableforcountry( $available_gateways ) {
	
	if(isset(WC()->customer->get_billing_country())){
		if( WC()->customer->get_billing_country() != 'DE' ):
			if( WC()->customer->get_billing_country() != 'AT' ):
				if( WC()->customer->get_billing_country() != 'CH' ):
					if ( isset( $available_gateways['sahu_karb_gateway'] )) {
					  unset( $available_gateways['sahu_karb_gateway'] );
					} 
				endif;	
			endif;			
		endif;
	}
	
	return $available_gateways;
}*/

/**
 * Prüfe ob Mindestbestellwert vorhanden...wenn nein Disable.
 */
add_filter( 'woocommerce_available_payment_gateways', 'sahu_karb_mindestbestellwertdisable' );
  
function sahu_karb_mindestbestellwertdisable( $available_gateways ) {

	$minimum_amount	= get_option( 'sahu_karb_product_options_mindestebestllwert' );
	if($minimum_amount){
		$cart_total     = (float) WC()->cart->subtotal; // Total cart amount
		if( $cart_total < $minimum_amount ):
			if ( isset( $available_gateways['sahu_karb_gateway'] )) {
			  unset( $available_gateways['sahu_karb_gateway'] );
			}   
		endif;
	}
	
	return $available_gateways;
	
}

/**
 * Prüfe ob Maximalerbestellwert vorhanden...wenn nein Disable.
 */
if(get_option( 'sahu_karb_product_options_maxbestllwert' ) !== 0 && !empty(get_option( 'sahu_karb_product_options_maxbestllwert' ))):
 
add_filter( 'woocommerce_available_payment_gateways', 'sahu_karb_maxbestellwertdisable' );
  
function sahu_karb_maxbestellwertdisable( $available_gateways ) {
	
	$minimum_amount	= get_option( 'sahu_karb_product_options_maxbestllwert' );
	$cart_total     = (float) WC()->cart->subtotal; // Total cart amount
	if( $cart_total > $minimum_amount ):
		if ( isset( $available_gateways['sahu_karb_gateway'] )) {
		  unset( $available_gateways['sahu_karb_gateway'] );
		}   
	endif;
	
	return $available_gateways;
}

endif;

/**
 * Diable Gateway wenn Cookie drin ist.
 */
add_filter( 'woocommerce_available_payment_gateways', 'sahu_karb_cookiedisable' );
  
function sahu_karb_cookiedisable( $available_gateways ) {
	if(isset($_COOKIE["sahukarbrequest"])){
		   
			  unset( $available_gateways['sahu_karb_gateway'] );

	}
	
	return $available_gateways;
}

/**
 * Prüfe ob KD abgelehnt wurde.
 */
add_action('wp_head', 'sahu_karb_disablepayment');
function sahu_karb_disablepayment(){
	if(isset($_GET['dt5W9'])){
	?>
		<style>
			.wc_payment_method.payment_method_sahu_karb_gateway {
				display: none !important;
			}
		</style>
	<?php
	
		wc_add_notice(__('Leider können wir die Anfrage mit Kauf auf Rechnung nicht genehmigen. Wir bitten dich daher eine andere Zahlungsmethode zu wählen.'), 'error');
		
		if(!isset($_COOKIE["sahukarbrequest"])) {
			 
				// set a cookie for 1 year
				setcookie('sahukarbrequest', "1", time()+86400, "/"); // 24 Stunden
			 
		}
	}
	
	if(isset($_GET['dt5W8'])){

		wc_add_notice(__('Der hinterlegte Lizenzkey ist nicht korrekt, bitte wende dich an kontakt@sahu.media'), 'error');
		
	}
}

/**
 * Wenn Kunde abgelehnt, setze cookie
 */
add_action( 'template_redirect', 'sahu_karb_define_payment_gateway' );
function sahu_karb_define_payment_gateway(){
	if(isset($_GET['dt5W9'])){
		if( is_checkout() && ! is_wc_endpoint_url() ) {
			// HERE define the default payment gateway ID
			$default_payment_id = 'bacs';

			WC()->session->set( 'chosen_payment_method', $default_payment_id );
 
			if(!isset($_COOKIE["sahu_karb_request"])) {
			 
				// set a cookie for 1 year
				setcookie('sahukarbrequest', "1", time()+86400, "/"); // 24 Stunden
			 
			}
		}
	}
}

/**
 * Prüfe ob Bday eingetragen ist.
 */
add_action('woocommerce_checkout_process', 'sahu_karb_checkout_field_process_sahubday');
function sahu_karb_checkout_field_process_sahubday() {
	if($_POST['payment_method'] != 'sahu_karb_gateway')
        return;
	if (!$_POST['sahubday']) {
		wc_add_notice(__('Bitte trage dein Geburtsdatum ein z.B. 22.03.1988'), 'error');
	}
}

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'sahu_karb_update_order_meta' );
function sahu_karb_update_order_meta( $order_id ) {

    if($_POST['payment_method'] != 'sahu_karb_gateway')
        return;
	
	$originalDate = preg_replace("([^0-9-])", "", sanitize_text_field($_POST['sahubday']));
	$newDate = date("d.m.Y", strtotime($originalDate));

    update_post_meta( $order_id, 'sahubday',  $newDate);
}

/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'sahu_karb_field_display_admin_order_meta', 10, 1 );
function sahu_karb_field_display_admin_order_meta($order){
    $method = get_post_meta( $order->id, '_payment_method', true );
    if($method != 'sahu_karb_gateway')
        return;

    $sahubday = get_post_meta( $order->id, 'sahubday', true );
	$karid = get_post_meta( $order->id, 'karid', true );

    echo '<p><strong>'.__( 'Geburtsdatum' ).':</strong> ' . $sahubday . '</p>';
}

?>