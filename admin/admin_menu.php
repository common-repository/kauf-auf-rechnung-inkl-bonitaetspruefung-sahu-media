<?php

// Registriere Adminmenü

add_action( 'admin_menu', 'sahu_karb_admin_menu' );
function sahu_karb_admin_menu() {
    add_menu_page(
        __( 'Bonitätsprüfung', 'sahu_karb' ),
        __( 'Bonitätsprüfung', 'sahu_karb' ),
        'manage_options',
        'sahu-boni',
        'sahu_karb_admin_menu_admin_page_contents',
        'dashicons-schedule',
        3
    );
}

// Lade Seiteninhalt + Einstellungen

function sahu_karb_admin_menu_admin_page_contents() {
    ?>
	<style>
		button {
			background: green;
			color: white;
			border: 0 solid;
			padding: 10px;
			cursor: pointer;
		}
		
		button:hover {
			background: gray;
		}
		
		.sahuboni {
		  list-style: none;
		}

		.sahuboni li:before {
		  content: '✓';
		}
		
		.sahuboni dd, li {
			margin-bottom: 1px !important;
		}
		
		#sahubonitable {
			width: 50%;
			border: 1px sol;
		}	

		.sahubonitabletd {
			background: white;
			padding: 10px;
			text-align: center;
		}

		#sahubonitable td {
			border: 1px solid;
			text-align: center;
		}		
		
		.codestyleboni {
			width: 30%;
			border: 1px solid;
			padding: 10px;
			margin-top: 20px;
		}		
		
		.sahuerror {
			background: red !important;
		}		
	</style>
	<div class="wrap">
    <h1> <?php esc_html_e( 'SAHU Bonitätsprüfung - SAHU MEDIA ®', 'sahu_karb' ); ?> </h1>
	<form method="POST" action="options.php">
    <?php
		settings_fields( 'sahu_karb-options-app-page' );
		do_settings_sections( 'sahu_karb-options-app-page' );
		submit_button();
    ?>
    </form>
	<a target="_blank" href="https://sahu.media/kontakt/"><img src="https://sahu.media/wp-content/uploads/2021/12/Design-ohne-Titel.gif"></a>
	<?php
	
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

		update_option( 'sahu_karb_textreminder', $sahu_karb_reminderdefaulttext);
	
	endif;

	//Get the active tab from the $_GET param
	$default_tab = null;
	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

?>
  <!-- Our admin page content should all be inside .wrap -->
  
    <!-- Here are our tabs -->
    <nav class="nav-tab-wrapper">
		<a href="?page=sahu-boni" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>"><?php echo __( 'Info', 'sahu_karb' );?></a>
		<a href="?page=sahu-boni&tab=log" class="nav-tab <?php if($tab==='log'):?>nav-tab-active<?php endif;?>" ><?php echo __( 'Log', 'sahu_karb' );?></a>
		<a href="?page=sahu-boni&tab=rechnung" class="nav-tab <?php if($tab==='rechnung'):?>nav-tab-active<?php endif;?>" ><?php echo __( 'Meine Rechnung', 'sahu_karb' );?></a>
		<a href="?page=sahu-boni&tab=remindermail" class="nav-tab <?php if($tab==='remindermail'):?>nav-tab-active<?php endif;?>" ><?php echo __( 'Text für Mahnung (E-Mail)', 'sahu_karb' );?></a>
		<a href="?page=sahu-boni&tab=video" class="nav-tab <?php if($tab==='video'):?>nav-tab-active<?php endif;?>" ><?php echo __( 'Video / Dokumentation', 'sahu_karb' );?></a>
		<a href="?page=sahu-boni&tab=disclamer" class="nav-tab <?php if($tab==='disclamer'):?>nav-tab-active<?php endif;?>" ><?php echo __( 'Disclamer / Datenschutzerklärung', 'sahu_karb' );?></a>
		<a href="?page=sahu-boni&tab=inkasso" class="nav-tab <?php if($tab==='inkasso'):?>nav-tab-active<?php endif;?>" ><?php echo __( 'Kostenloses Inkasso', 'sahu_karb' );?></a>
    </nav>


    <div class="tab-content">
<?php switch($tab) :

	  case 'log':
		echo __( '<h2>Aktivitäten-Log</h2>', 'sahu_karb' );
		if(!empty(get_option( 'sahu_karb_product_options_license' ))):
			if((lizenzpruefung() == 2) || (lizenzpruefung() == 3)):
				print '<p>';
				echo __( 'Deine Lizenz ist nicht gültig. Bitte hole dir unverbindlich die PRO-Version! Bitte beachte das bei einer ungültigen Lizenz die Zahlungsmethode nicht aktiv ist!', 'sahu_karb' );
				print '</p>';
				print '<p>';
				print '<a target="_blank" href="https://boni.sahu.media/register.php?domain='.$_SERVER['SERVER_NAME'].'"><button>'; echo __( 'Kostenlos zur Pro-Version!', 'sahu_karb' ); print '</button></a>';
				print '</p>';
			else:
			
				print '<p>Hier findest du die letzten 20 Aktivitäten. Bitte beachte das aus Datenschutzgründen, keine Personbezogenden Daten hinterlegt sind.</p>';
				
				// Zahlungen
				$data = new stdClass();	
				$data->typ = "zahlungen";


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
					
					$antwort = json_decode($result['body']);
					
					echo '<table id="sahubonitable">';
						echo '<tr>';
						
						echo '<td  class="sahubonitabletd">';
						echo 'Datum';
						echo '<td>';
						
						echo '<td  class="sahubonitabletd">';
						echo 'Betrag';
						echo '<td>';
						
						echo '<td  class="sahubonitabletd">';
						echo 'Grund';
						echo '<td>';
						
						echo '</tr>';
						
					foreach($antwort->daten->zahlungen as $zahlung) {
						echo '<tr>';
						
						echo '<td>';
						echo $zahlung->datum;
						echo '<td>';
						
						echo '<td>';
						echo $zahlung->betrag;
						echo '<td>';
						
						echo '<td>';
						echo $zahlung->grund;
						echo '<td>';
						
						echo '</tr>';
					}
					echo '</table>';
					
				} catch (Exception $e) {
					wc_add_notice( $e->getMessage(), 'error' );					
				}			
					
			endif;
		else:
			print '<p>';
			print 'Jetzt unverbindlich zur PRO-Version wechseln, bedingt das es auf Guthabenbasis ist, hast du die Kosten im blick. Erfahre mehr im Video! <a href="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=sahu_karb_gateway"><b>Zu den Plugin-Einstellungen.</b></a>';
			print '<ul class="sahuboni">';
			print '<li>Bonitätsprüfung</li>';
			print '<li>Keine Vertragslaufzeit</li>';
			print '<li>Keine Grundgebühr</li>';
			print '<li>Basierend auf Guthabenbasis</li>';
			print '<li>Score skalierung</li>';
			print '<li>Einstellungen des Mahnwesen</li>';
			print '</ul>';
			print '</p>';
			print '<p>';
			print '<a target="_blank" href="https://boni.sahu.media/register.php?domain='.$_SERVER['SERVER_NAME'].'"><button>'; echo __( 'Kostenlos zur Pro-Version wechseln!', 'sahu_karb' ); print '</button></a>';
			print '</p>';
		endif;
      break;
	  
	  
	  case 'rechnung':
		echo __( '<h2>Meine Rechnungen</h2>', 'sahu_karb' );
		if(!empty(get_option( 'sahu_karb_product_options_license' ))):
			if((lizenzpruefung() == 2) || (lizenzpruefung() == 3)):
				print '<p>';
				echo __( 'Deine Lizenz ist nicht gültig. Bitte hole dir unverbindlich die PRO-Version! Bitte beachte das bei einer ungültigen Lizenz die Zahlungsmethode nicht aktiv ist!', 'sahu_karb' );
				print '</p>';
				print '<p>';
				print '<a target="_blank" href="https://boni.sahu.media/register.php?domain='.$_SERVER['SERVER_NAME'].'"><button>'; echo __( 'Kostenlos zur Pro-Version!', 'sahu_karb' ); print '</button></a>';
				print '</p>';
			else:
			
				print '<p>Hier findest du dein Rechnungen.</p>';
				
				// Zahlungen
				$data = new stdClass();	
				$data->typ = "rechnungen";


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
					
					$antwort = json_decode($result['body']);
					
					echo '<table id="sahubonitable">';
						echo '<tr>';
						
						echo '<td  class="sahubonitabletd">';
						echo 'Datum';
						echo '<td>';
						
						echo '<td  class="sahubonitabletd">';
						echo 'Link zur Rechnung';
						echo '<td>';
						
						echo '</tr>';
						
					foreach($antwort->daten->rechnungen as $zahlung) {
						echo '<tr>';
						
						echo '<td>';
						echo $zahlung->datum;
						echo '<td>';
						
						echo '<td>';
						echo '<a href="'.$zahlung->link.'" target="_blank">Lade deine Rechnung herunter</a>';
						echo '<td>';
						
						echo '</tr>';
					}
					echo '</table>';
					
				} catch (Exception $e) {
					wc_add_notice( $e->getMessage(), 'error' );					
				}			
					
			endif;
		else:
			print '<p>';
			print 'Jetzt unverbindlich zur PRO-Version wechseln, bedingt das es auf Guthabenbasis ist, hast du die Kosten im blick. Erfahre mehr im Video! <a href="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=sahu_karb_gateway"><b>Zu den Plugin-Einstellungen.</b></a>';
			print '<ul class="sahuboni">';
			print '<li>Bonitätsprüfung</li>';
			print '<li>Keine Vertragslaufzeit</li>';
			print '<li>Keine Grundgebühr</li>';
			print '<li>Basierend auf Guthabenbasis</li>';
			print '<li>Score skalierung</li>';
			print '<li>Einstellungen des Mahnwesen</li>';
			print '</ul>';
			print '</p>';
			print '<p>';
			print '<a target="_blank" href="https://boni.sahu.media/register.php?domain='.$_SERVER['SERVER_NAME'].'"><button>'; echo __( 'Kostenlos zur Pro-Version wechseln!', 'sahu_karb' ); print '</button></a>';
			print '</p>';
		endif;
      break;
	  
	   case 'remindermail':
		echo __( '<h2>Mahnungs E-Mail</h2>', 'sahu_karb' );
			print '<p>';
			print 'Bearbeite hier den Text für die Mahn E-Mail, benutze folgene Parameter (Shortcodes) als Platzhalter.';
			print '<ul class="sahuboni">';
			print '<li>[sahu_karb_shopname] = Zeigt die Shop-URL z.B. deinedomain.de</li>';
			print '<li>[sahu_karb_amount] = Zeigt den Betrag der Bestellung an z.B. 59,90</li>';
			print '<li>[sahu_karb_currency] = Zeigt die aktuelle WooCommerce Währung z.B. €</li>';
			print '<li>[sahu_karb_ordernumber] = Gibt die aktuelle Bestellnummer aus z.B. 1001</li>';
			print '<li>[sahu_karb_bankname] = Gibt den Bankname aus, welcher bei WooCommerce hinterlegt ist z.B. Volksbank</li>';
			print '<li>[sahu_karb_bankiban] = Gibt die Bankiban aus, welcher bei WooCommerce hinterlegt ist z.B. DE XX XX XX XX</li>';
			print '<li>[sahu_karb_bankbic] = Gibt die Bic aus, welcher bei WooCommerce hinterlegt ist z.B. BSXA44A</li>';
			print '</ul>';
			print 'Sollte die Textarea unten Leer sein, so wird ein Standard-E-Mail Text verwendet.</p>';
			print '</p>';
			print '<p>';		
			print '<div class="wrap">
						<form method="post" action="options.php">'; 
							wp_nonce_field('update-options')?>
							<?php
								 $settings = array(
								'teeny' => true,
								'textarea_rows' => 10,
								'tabindex' => 1,
								'media_buttons' => false
								 );
								 wp_editor( __(get_option('sahu_karb_textreminder', '' )), 'sahu_karb_textreminder', $settings);
							 ?>
							<p><input type="submit" name="Submit" value="Option Speichern" /></p>
							<input type="hidden" name="action" value="update" />
							<input type="hidden" name="page_options" value="sahu_karb_textreminder" />
						</form>
					</div>
				<?php
			print '</p>';
			print '<p><h3>Vorschau</h3>';
							
						global $woocommerce;	
						global $wpdb;
						
						$last_order_id = wc_get_orders(array('limit' => 1, 'return' => 'ids')); // Get last Order ID (array)
						$reallastorder = (string) reset($last_order_id); // Displaying last order ID
						$order = new WC_Order( $reallastorder );	

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
						
						print $message_body;
			print '</p>';
      break;

      case 'video':
        
			echo '<iframe width="560" height="315" style="margin-top:10px;" style="margin-top:10px;" src="https://www.youtube.com/embed/nc_iBfeZPTI" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		
      break;
	  
	  case 'disclamer':

			echo __( '<h2>Wichtig für PRO-Nutzer</h2>', 'sahu_karb' );
			print 'Du bist Pro-Nutzer? Dann ist es wichtig, dass du deine Datenschutzerklärung und AGB aktualisierst. Bitte beachte, dass wir keine Haftung übernehmen. Solltest du das ganze nicht in deine Datenschutzerklärung ergänzen, kann es zu Problemen kommen mit Kunden. Daher erneut die bitte, dass du dies auf deine Webseite integrierst.';
			print '<div class="codestyleboni">';
			print '<h4>Datenverarbeitung zur Bestellabwicklung</h4>';
			print '<h3><b>Bonitätsprüfung - Crifbürgel</b></h3>
				   <p>Bei Auswahl der Zahlungsmethode „Kauf auf Rechnung“, findet die Zahlungsabwicklung direkt über den Shop Betreiber statt. Um die Abwicklung der Zahlung zu ermöglichen, werden Ihre persönlichen Daten (Vor- und Nachname, Straße, Hausnummer, Postleitzahl, Ort und Geburtsdatum) sowie Daten, welche im Zusammenhang mit der Bestellung stehen (z. B. Rechnungsbetrag) zum Zweck der Identitäts- und Bonitätsprüfung an mediaFinanz weitergegeben, welche diese an Bürgel übermittelt, sofern Sie hierin gemäß Art. 6 Abs. 1 lit. a DSGVO im Rahmen des Bestellprozesses ausdrücklich eingewilligt haben. An welche Auskunfteien Ihre Daten hierbei weitergeleitet werden können, können Sie hier einsehen:</p>
				   <p>https://www.crifbuergel.de/datenschutz/</p>
				   <p>Die Bonitätsauskunft kann Wahrscheinlichkeitswerte enthalten (sog. Score-Werte). Soweit Score-Werte in das Ergebnis der Bonitätsauskunft einfließen, haben sie ihre Grundlage in einem wissenschaftlich anerkannten mathematisch-statistischen Verfahren. In die Berechnung der Score-Werte fließen unter anderem, aber nicht ausschließlich, Anschriftendaten ein. Die erhaltenen Informationen über die statistische Wahrscheinlichkeit eines Zahlungsausfalls verwendet Bürgel für eine abgewogene Entscheidung über die Begründung, Durchführung oder Beendigung des Vertragsverhältnisses.</p>
				   <p>Sie können Ihre Einwilligung jederzeit durch eine Nachricht an den für die Datenverarbeitung Verantwortlichen oder gegenüber mediaFinanz widerrufen. Jedoch bleibt mediaFinanz ggf. weiterhin berechtigt, Ihre personenbezogenen Daten zu verarbeiten, sofern dies zur vertragsgemäßen Zahlungsabwicklung erforderlich ist.</p>';
			print '</div>';
			
      break;
	  
	  case 'inkasso':

			echo __( '<h2>Billingportal.de</h2>', 'sahu_karb' );
			print 'Als <strong>offizieller Partner von Crifbürgel</strong>, haben wir nebenher eine Tochterfirma gegründet, welche kostenlos Bestellungen an das Inkasso übergibt.<br>Dabei liegt es unserer Tochterfirma, der <a target="_blank" href="https://billingportal.de/">S&H Collect UG & Co. KG</a> am Herzen, dass deine <strong>Reputation</strong> geschützt ist.';
			print '<h3>Was bedeutet das?</h3>';
			print '<a target="_blank" href="https://billingportal.de/">Billingportal</a> ist eine Vorstufe vor dem Inkasso und gewährt dem Kunden zusätzlich 16 Tage zu den regulären 14 Tagen bis die Forderung an ein Inkasso abgetreten wird.<br>
					Somit hat der Kunde <strong>effektiv 30 Tage Zeit</strong>, die offene Rechnung bei deinem Onlineshop zu begleichen. Zusätzlich hat der Kunde die Möglichkeit beim Billingportal Ratenzahlungen<br>
					zu vereinbaren, was deinen Shop so vor schlechter Reputation schützt. Dank 70 % Erfolgsquote mit dem Billingportal, ergibt sich so ein <strong>netter Cashflow</strong>.';
			print '<h3>Wie funktioniert das System?</h3>';
			print '<a target="_blank" href="https://billingportal.de/">Billingportal</a> bietet <strong>Schnittstellen für Shopsysteme wie Wordpress, Shopify und Prestashop und automatisiert so das Mahnwesen</strong>. Bei WooCommerce gibt es 4 neue Staten - Mahnung 1, Mahnung 2,<br>
					Mahnung 3 und Inkasso. Das System versendet automatisiert E-Mails und macht den Kunden darauf, aufmerksam, die Bestellung zu bezahlen. In der Regel ist sogar nur eine Mahnung nötig! Somit<br>
					wird hier dem Kunden bereits Zeit gegeben, das Geld bei dir zu begleichen.';
			print '<p>Die Zeitlichen abstände der Mahnungen kann in z. B. WooCommerce hinterlegt werden, sodass man den Kunden sogar mehr Zeit einräumen könnte. Sollte die Frist abgelaufen sein, wird das ganze automatisch zu<br>
					<a target="_blank" href="https://billingportal.de/">billingportal.de</a> geleitet. Billingportal selbst versendet dann eine E-Mail an den Kunden mit Zahlungsaufforderung, optional auch postalisch. Billingportal räumt den Kunden dann erneut 16 Tage ein, bis das<br>
					Ganze an das Inkasso weitergeleitet wird.</p>';
			print '<p>Du erhältst später dann eine Gutschrift von den Zahlungen, welche eingegangen sind. Sollte eine Zahlung an das Inkasso oder das Billingportal stattfinden, setzt unser System die Bestellung bei dir automatisch in Bearbeitung.<br>
					Du hast natürlich ebenfalls und vor allem jederzeit die Möglichkeit, den Status deiner Fälle einzusehen. Dies gibt dir Transparenz, ebenfalls steht dir der Support per E-Mail und WhatsApp zur Verfügung. Den, selbst wenn du einen<br>
					Fall stornieren musst, so kostet dich das ganze nichts!</p>';
			print '<h3>Somit ist Billingportal.de, die kostenlose automatisierte Lösung</h3>';
			print 'Wie verdient Billingportal? Eine wichtige Frage! Den <a target="_blank" href="https://billingportal.de/">Billingportal</a> selbst verdient durch deinen Kunden an Mahngebühren, was für dich den Vorteil hat, dass du nichts bezahlst. <a target="_blank" href="https://billingportal.de/">Jetzt kostenlos kontaktieren</a>.';
			
      break;
	  
      default:
		
		echo __( '<h2>Bonitätsprüfung</h2>', 'sahu_karb' );
		if(!empty(get_option( 'sahu_karb_product_options_license' ))):
			if((lizenzpruefung() == 2) || (lizenzpruefung() == 3)):
				print '<p>';
				echo __( 'Deine Lizenz ist nicht gültig. Bitte hole dir unverbindlich die PRO-Version! Bitte beachte das bei einer ungültigen Lizenz die Zahlungsmethode nicht aktiv ist!', 'sahu_karb' );
				print '</p>';
				print '<p>';
				print '<a target="_blank" href="https://boni.sahu.media/register.php?domain='.$_SERVER['SERVER_NAME'].'"><button>'; echo __( 'Kostenlos zur Pro-Version!', 'sahu_karb' ); print '</button></a>';
				print '</p>';
			else:
			
				print '<p>';
				echo __( 'Vielen Dank für dein Vertrauen!', 'sahu_karb' );
				print '</p>';
				
				print '<p>';
				echo __( 'Dein Kontostand beträgt: ', 'sahu_karb' ); print getUserData("kontostand"); print ' €';
				print '<br>';
				echo __( 'Kosten pro Abfrage : ', 'sahu_karb' ); print getUserData("abfrage_kosten"); print ' €';
				print '</p>';
				
				if(getUserData("kontostand") < getUserData("abfrage_kosten")):
					print '<p><a target="_blank" href="https://boni.sahu.media/payment.php?domain='.$_SERVER['SERVER_NAME'].'"><button class="sahuerror">'; echo __( 'Kauf auf Rechnung wird nicht angezeigt! Lade dein Konto auf.', 'sahu_karb' ); print '</button></a></p>';
				endif;
				
				
				print '<p><a target="_blank" href="https://boni.sahu.media/payment.php?domain='.$_SERVER['SERVER_NAME'].'"><button>'; echo __( 'Jetzt Konto aufladen!', 'sahu_karb' ); print '</button></a></p>';
				
				
				print '<p>Du hast ein hohes Abfragevolumen? Profitiere von besseren Konditionen pro Abfrage - Kontaktiere uns unverbinderlich unter kontakt@sahu.media</p>';
				print '<ul class="sahuboni">';
				print '<li>Standard - 1,49 € pro Abfrage (AKTION!)</li>';
				print '<li>ab 100 Abfragen pro Monat - 1,45 € pro Abfrage</li>';
				print '<li>ab 200 Abfragen pro Monat - 1,40 € pro Abfrage</li>';
				print '<li>ab 300 Abfragen pro Monat - 1,35 € pro Abfrage</li>';
				print '<li>ab 400 Abfragen pro Monat - 1,30 € pro Abfrage</li>';
				print '<li>ab 500 Abfragen pro Monat - 1,25 € pro Abfrage</li>';
				print '</ul>';
				print '<p>Es findet keine Automatische Änderung der Abfragepreise statt. Bitte beachte, dass du dafür mit uns in Kontakt treten musst!</p>';
			endif;
		else:
			print '<p>';
			print 'Jetzt unverbindlich zur PRO-Version wechseln, bedingt das es auf Guthabenbasis ist, hast du die Kosten im blick. Erfahre mehr im Video! <a href="/wp-admin/admin.php?page=wc-settings&tab=checkout&section=sahu_karb_gateway"><b>Zu den Plugin-Einstellungen.</b></a>';
			print '<ul class="sahuboni">';
			print '<li>Bonitätsprüfung</li>';
			print '<li>Keine Vertragslaufzeit</li>';
			print '<li>Keine Grundgebühr</li>';
			print '<li>Basierend auf Guthabenbasis</li>';
			print '<li>Score skalierung</li>';
			print '<li>Einstellungen des Mahnwesen</li>';
			print '</ul>';
			print '</p>';
			print '<p>';
			print '<a target="_blank" href="https://boni.sahu.media/register.php?domain='.$_SERVER['SERVER_NAME'].'"><button>'; echo __( 'Kostenlos zur Pro-Version wechseln!', 'sahu_karb' ); print '</button></a>';
			print '</p>';
		endif;
        break;
		
    endswitch; 
?>
    </div>
  </div>	
    <?php
}

//////////////////////////////////////////////////////
// Lade Formular für Lizenz + Max / Min-Bestellwert //
//////////////////////////////////////////////////////

add_action( 'admin_init', 'sahu_karb_options_settings_init' );
function sahu_karb_options_settings_init() {

    add_settings_section(
        'sahu_karb_product_options_setting_section',
        __( 'Einstellungen', 'sahu_karb' ),
        'sahu_karb_options_section_callback_function',
        'sahu_karb-options-app-page'
    );

		add_settings_field(
		   'sahu_karb_product_options_license',
		   __( 'Lizenzkey', 'sahu_karb' ),
		   'sahu_karb_product_options_license',
		   'sahu_karb-options-app-page',
		   'sahu_karb_product_options_setting_section'
		);
		
		register_setting( 'sahu_karb-options-app-page', 'sahu_karb_product_options_license' );
		
		if(!empty(get_option( 'sahu_karb_product_options_license' ))):
			if((lizenzpruefung() == 2) OR (lizenzpruefung() == 3)): 
				// FALSCHE LIZENZ!
			else:
				add_settings_field(
				   'sahu_karb_product_options_mindestebestllwert',
				   __( 'Mindest-Bestellwert', 'sahu_karb' ),
				   'sahu_karb_product_options_mindestebestllwert',
				   'sahu_karb-options-app-page',
				   'sahu_karb_product_options_setting_section'
				);
				
				register_setting( 'sahu_karb-options-app-page', 'sahu_karb_product_options_mindestebestllwert' );
				
				add_settings_field(
				   'sahu_karb_product_options_maxbestllwert',
				   __( 'Maximaler-Bestellwert', 'sahu_karb' ),
				   'sahu_karb_product_options_maxbestllwert',
				   'sahu_karb-options-app-page',
				   'sahu_karb_product_options_setting_section'
				);
				
				register_setting( 'sahu_karb-options-app-page', 'sahu_karb_product_options_maxbestllwert' );
				
			endif;
		endif;
}

function sahu_karb_options_section_callback_function() {
    echo __( '<p>Die Lizenz wird nur benötigt, wenn du Bonitätsabfragen machen möchtenst. Du erhälst diese Kostenlos <a target="_blank" href="https://boni.sahu.media/register.php?domain='.$_SERVER['SERVER_NAME'].'">hier!</a></p>', 'sahu_karb' );
}

function sahu_karb_product_options_license() {
    ?>
    <input type="text" id="sahu_karb_product_options_license" name="sahu_karb_product_options_license" value="<?php echo get_option( 'sahu_karb_product_options_license' ); ?>" placeholder="<?php echo __( 'Lizenz der SAHU MEDIA ®', 'sahu_karb' );?>">
    <?php
}

function sahu_karb_product_options_mindestebestllwert() {
    ?>
    <input type="text" id="sahu_karb_product_options_mindestebestllwert" name="sahu_karb_product_options_mindestebestllwert" value="<?php echo get_option( 'sahu_karb_product_options_mindestebestllwert' ); ?>" placeholder="<?php echo __( 'Ab wann soll Kauf auf Rechnung aktiviert werden?', 'sahu_karb' );?>">
    <?php
}

function sahu_karb_product_options_maxbestllwert() {
    ?>
    <input type="text" id="sahu_karb_product_options_maxbestllwert" name="sahu_karb_product_options_maxbestllwert" value="<?php echo get_option( 'sahu_karb_product_options_maxbestllwert' ); ?>" placeholder="<?php echo __( 'Maximaler Bestellwert?', 'sahu_karb' );?>">
    <?php
}

////////////////////
// Ende des Laden //
///////////////////

?>