<?php
/*
 * Kauf auf Rechnung inkl. Bonitätsprüfung | SAHU MEDIA ® 
 *
 * @package bonitaet-sahu
 * @copyright Copyright (c) 2020, SAHU MEDIA®
*/


// Setze Status Farben

function sahu_karb_admin_style() {
?>
  <style>
	.order-status.status-karb-export.tips {
		background: #f43d3d;
		color: #4a0404;
	} 
	
	.order-status.status-karb-mahnung.tips {
		background-color: #ee9f0f;
		color: #795615;
	}	
	
	.order-status.status-karb-versendet.tips {
		background: #c6e1c6;
		color: #5b841b;
	}	
  </style>
<?php	  
}
add_action('admin_head', 'sahu_karb_admin_style');