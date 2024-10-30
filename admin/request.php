<?php

////////////////////	
		// Request Types //
		///////////////////
		/*
			 
			$request = [
				name ..
				strasse ..
			]

			// Bonität

			$request = [
				'typ' => 'bonitaet',
				'daten' => [
					name ..
					strasse ..
				]
			]

			// Bonität - Antwort:

			$response = [
				'fehler' => [
					'code' => 0,
					'text' => 'keine fehler'
				],
				'daten' => [
					'bonitaet' => 2.6
				]
			]
			
			// Zahlungen

			$request = [
				'typ' => 'zahlungen',
				'daten' => [
					name ..
					strasse ..
				]
			]

			// Zahlungen - Antwort:

			$response = [
				'fehler' => [
					'code' => 0,
					'text' => 'keine fehler'
				],
				'daten' => [
					'zahlungen' => ...
				]
			]

			// Kontostand

			$request = [
				'typ' => 'kontostand'
			]

			// Kontostand - Antwort:

			$response = [
				'fehler' => [
					'code' => 0,
					'text' => 'keine fehler'
				],
				'daten' => [
					'konto_aktiv' => 1,
					'kontostand' => "30,40",
					'abfrage_kosten' => "0,80"
				]
			]				
				
		*/
			
		/*
		
			Fehlerliste:

			0 = keine Fehler
			1 = Ausnahmefehler
			2 = konnte Lizenzdaten nicht laden
			3 = Lizenz inaktiv
			4 = Fehler bei der Bonitätsprüfung
			
			
			// Kontostand
			$data = new stdClass();	
			$data->typ = "kontostand";
			
			
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

				echo '<pre>';
				//print_r($result);
				print_r($antwort);
				echo '</pre>';
				print $antwort->daten->kontostand;
			} catch (Exception $e) {
				wc_add_notice( $e->getMessage(), 'error' );
			}
			
		*/
		
?>