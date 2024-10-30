<?php
/*
 * Plugin Name: Kauf auf Rechnung inkl. Bonitätsprüfung
 * Description: Mit diesem Plugin, wird in WooCommerce eine weitere Zahlungsart aktiviert "Kauf auf Rechnung" zzl. bietet das Plugin eine Bonitätsprüfung.
 * Version: 2.3
 * Author: SAHU MEDIA ®
 * Author URI: https://sahu.media
 *
 * @package bonitaet-sahu
 * @copyright Copyright (c) 2021-2023, SAHU MEDIA®
*/

/*
 * Prüfe ob WooCommerce aktiv ist.
 */
 
function sahu_karb_checkwoo_init () {
  if (class_exists( 'WooCommerce' )) {
  }else{
    add_action( 'admin_notices', 'sahu_karb_missing_wc_notice' );
  }
}
add_action( 'init', 'sahu_karb_checkwoo_init' );

// Admin Error Messages

function sahu_karb_missing_wc_notice() {
  ?>
  <div class="error notice">
      <p><?php _e( 'Du musst WooCommerce aktivieren, damit das Plugin "Kauf auf Rechnung inkl. Bonitätsprüfung" funktioniert.', 'bonitaet-sahu' ); ?></p>
  </div>
  <?php
}
 
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

/*
 * Lade Funktionen
 */
 
include( plugin_dir_path( __FILE__ ) . 'admin/function.php');

/*
 * Lade Admin-Einstellungen
 */
 
include( plugin_dir_path( __FILE__ ) . 'assets/admin.php');

/*
 * Lade WooCommerce Extra-Zahlungsarten.
 */
 
include( plugin_dir_path( __FILE__ ) . 'gateway/rechnung.php');

/*
 * Lade WooCommerce Reminder
 */
 
include( plugin_dir_path( __FILE__ ) . 'reminder/reminder.php');

// Lade Menü

include( plugin_dir_path( __FILE__ ) . 'admin/admin_menu.php');

// Prüfe ReminderText Inhalt bei Aktivierung / Update

// Activation
function sahu_karb_activation_check(){
	if(empty(get_option('sahu_karb_textreminder'))):
	
		$sahu_karb_reminderdefaulttext= "
			Hallo,

			wir danken dir für deine Bestellung bei [sahu_karb_shopname] mit der Bestellnummer [sahu_karb_ordernumber]. Deine Bestellung ist eingegangen, aber leider konnte die Zahlung mit der gewünschten Zahlungsmethode nicht ausgeführt werden. Wir bitten dich daher die Zahlung per Bank-Überweisung zu tätigen, damit wir deine Bestellung verschicken können. Nutze dazu folgende Daten:

			Bankname: [sahu_karb_bankname]
			IBAN: [sahu_karb_bankiban]
			BIC: [sahu_karb_bankbic]
			Verwendungszweck: [sahu_karb_ordernumber]
			Betrag: [sahu_karb_amount] [sahu_karb_currency]

			Solltest du natürlich bereits eine Zahlung vorgenommen haben, brauchst du diese Nachricht nicht weiter beachten. Oder sollen wir die Bestellung stornieren? Wir bitten um kurze Rückmeldung wie wir dir helfen können und wie du vorgehen möchtest. Bei Fragen stehen wir dir gerne telefonisch oder per E-Mail zur Verfügung.

			Beste Grüße,
			Dein Team von [sahu_karb_shopname]
		";
		
		add_option( 'sahu_karb_textreminder', $sahu_karb_reminderdefaulttext, '', 'yes');
	
	endif;
}
register_activation_hook( __FILE__, 'sahu_karb_activation_check' );
