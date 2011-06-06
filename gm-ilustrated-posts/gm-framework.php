<?php
if ( !class_exists( 'gm_framework' ) ) {

	class gm_framework {

		public static function get_categories( $value='', $blank=false ) {
		
			$list = array();
			
			if ( $blank )
				$list[] = array(
					'value' => 0,
					'label' => 'Todas'
				);
			
			$cats = get_categories( 'hide_empty=0' );
			
			foreach ($cats as $cat) {
			
				$item = array(
					'value' => $cat->term_id,
					'label' => $cat->cat_name
				);

				if ( $value == $cat->term_id )
					$item[ 'selected' ] = true;
					
				$list[] = $item;
			}
			
			return $list;
			
		}
		
	}
}
?>