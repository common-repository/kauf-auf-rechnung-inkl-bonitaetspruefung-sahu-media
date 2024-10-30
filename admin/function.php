<?php

	
	function getUserData($daten){
		if(!empty(get_option( 'sahu_karb_product_options_license' ))):
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
					'sslverify' => FALSE
				];
				
				$result = wp_remote_post( 'https://boni.sahu.media/api.php', $options );
				
				$antwort = json_decode($result['body']);

				return $antwort->daten->{$daten};
				
			} catch (Exception $e) {
				wc_add_notice( $e->getMessage(), 'error' );					
			}	
		endif;
	}

	function lizenzpruefung(){
		if(!empty(get_option( 'sahu_karb_product_options_license' ))):
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
					'sslverify' => FALSE
				];
				
				$result = wp_remote_post( 'https://boni.sahu.media/api.php', $options );
				
				$antwort = json_decode($result['body']);

				return $antwort->fehler->code;
				
			} catch (Exception $e) {
				wc_add_notice( $e->getMessage(), 'error' );					
			}	
		endif;
	}


?>