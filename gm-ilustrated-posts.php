<?php
/*
Plugin Name: Ilustrated Posts
Plugin URI: http://www.guiawp.com.br/plugins-wordpress/ilustrated-posts/
Description: Posts list's with thumbnails
Version: 1.5.1
Author: Guia WordPress
Author URI: http://www.guiawp.com.br
*/

if ( !class_exists( 'gm_ilustrated_posts' ) ) {

	class gm_ilustrated_posts {
	
		private static $options;

		public static function install() {
			
			self::$options = array(
				'count' 	=> 5,
				'order'		=> 'date',
				'title'		=> '',
				'category'	=> 0,
				'excerpt'	=> true
			);
			
			add_option( 'gm_ilustrated_posts', self::$options );
		}
		
		public static function uninstall() {
		
			delete_option( 'gm_ilustrated_posts' );
		
		}
		
		public static function activate() {
		
			$opt = array(
				'description' => __( 'Displays a list of posts and their thumbnails according to the chosen options.', 'ilustrated-posts' )
			);

			wp_register_sidebar_widget( 'wdg_ilustrated', __( 'Ilustrated Posts', 'ilustrated-posts' ), array( 'gm_ilustrated_posts', 'show' ), $opt );
			wp_register_widget_control( 'wdg_ilustrated', __( 'Ilustrated Posts', 'ilustrated-posts' ), array( 'gm_ilustrated_posts', 'options' ) );
		
		}
		
		public static function show( $args ) {
		
			self::get_options();
		
			$params = array(
				'posts_per_page' 		=> self::$options[ 'count' ],
				'orderby' 				=> self::$options[ 'order' ],
				'post_type' 			=> 'post',
				'ignore_sticky_posts' 	=> 1
			);
			
			if ( self::$options[ 'category' ] > 0 )
				$params[ 'category__in' ] = self::$options[ 'category' ];
			
			query_posts( $params );

			echo $args[ 'before_widget' ] . $args[ 'before_title' ] . self::$options[ 'title' ] . $args[ 'after_title' ];
			
			echo '<ul id="ilustrated-posts">';

			while ( have_posts() ) :
			
				the_post();
				global $post;
				
				if ( has_post_thumbnail() )
					$thumb = get_the_post_thumbnail( $post->ID, 'thumbnail' );
				else
					$thumb = '<img src="' . plugins_url( '/thumb-not-found.png', __FILE__ ) . '" alt="' . __( 'Image not found!', 'ilustrated-posts' ) . '" />';
				
				$link = '<a href="' . get_permalink() . '" title="' . esc_attr( get_the_title() ) . '">';
				
				echo '<li>' . $link . $thumb . '</a>' . $link . get_the_title() . '</a>';
				
				if ( self::$options[ 'excerpt' ] )
					echo '<p>' . get_the_excerpt() . '</p>';
				
				echo '</li>';
					
			endwhile;

			wp_reset_query();

			print "</ul>";
			print $args[ 'after_widget' ];
			
		}
		
		public static function options() {
		
			self::save();
			self::get_options(); ?>
		
			<input type="hidden" name="submit" value="1" />
			<ul>
				<li>
					<label><?php _e( 'Title', 'ilustrated-posts' ); ?>:
					<input type="text" name="title" maxlength="30" value="<?php echo self::$options[ 'title' ]; ?>" class="widefat" /></label>
				</li>
				<li>
					<label><?php _e( 'Post count', 'ilustrated-posts' ); ?>:
					<input type="text" name="count" maxlength="2" value="<?php echo self::$options[ 'count' ]; ?>" class="widefat" /></label>
				</li>
				<li>
					<label>
					<input type="checkbox" value="1" name="excerpt" <?php if ( self::$options[ 'excerpt' ] ) echo 'checked="true" ' ?>/>
					<?php _e( 'Show excerpts', 'ilustrated-posts' ); ?>
					</label>
				</li>
				<li>
					<label><?php _e( 'Order by', 'ilustrated-posts' ); ?>:
					<select name="order">
					<?php
					$params = array( 
						array( 'value' => 'date', 			'label' => __( 'Date', 'ilustrated-posts' ) ),
						array( 'value' => 'rand', 			'label' => __( 'Random', 'ilustrated-posts' ) ),
						array( 'value' => 'comment_count', 	'label' => __( 'Comment count', 'ilustrated-posts' ) )
					);
					
					foreach ( $params as $param ) {
					
						echo '<option value="' . $param[ 'value' ] . '"';
						
						if ( self::$options[ 'order' ] == $param[ 'value' ] )
							echo ' selected="true"';
						
						echo '>' . $param[ 'label' ] . '</option>';
					}
					?>
					</select></label>
				</li>
				<li>
					<label><?php _e( 'Category', 'ilustrated-posts' ); ?>:
					<?php
					$args = array(
						'hide_empty'=> true,
						'orderby'	=> 'name',
						'name' 		=> 'category',
						'selected' 	=> self::$options[ 'category' ]
					);
					wp_dropdown_categories( $args );
					?>
				</li>
			</ul>
			
			<?php
		}
		
		private static function get_options() {
		
			if ( !self::$options )
				self::$options = get_option( 'gm_ilustrated_posts' );
				
		}
		
		private static function save() {
		
			if ( isset( $_POST[ 'submit' ] ) ) {
			
				self::$options[ 'title' ] = $_POST[ 'title' ];
				self::$options[ 'excerpt' ] = ( $_POST[ 'excerpt' ] ) ? true : false;

				if( !empty( $_POST[ 'count' ] ) ) self::$options[ 'count' ] = (int) $_POST[ 'count' ];
				if( !empty( $_POST[ 'order' ] ) ) self::$options[ 'order' ] = $_POST[ 'order' ];
				
				if( !empty( $_POST[ 'category' ] ) ) self::$options[ 'category' ] = (int) $_POST[ 'category' ];

				update_option( 'gm_ilustrated_posts', self::$options );
			}
			
		}	
		
	}	
}

add_action( 'widgets_init', array( 'gm_ilustrated_posts', 'activate' ) );

register_activation_hook( __FILE__, array( 'gm_ilustrated_posts', 'install' ) );

register_deactivation_hook( __FILE__, array( 'gm_ilustrated_posts', 'uninstall' ) );

load_plugin_textdomain( 'ilustrated-posts', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
?>