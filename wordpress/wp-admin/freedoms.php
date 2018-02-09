<?php
/**
 * Your Rights administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __( 'Freedoms' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap about-wrap">

        <h1><?php printf( __( 'Welcome to Mix Financial System &nbsp;%s' ), $display_version ); ?></h1>

        <p class="about-text"><?php printf( 'www.mixfs.com' ); ?></p>
        <div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

        <h2 class="nav-tab-wrapper wp-clearfix">
                <a href="about.php" class="nav-tab"><?php _e( 'What&#8217;s New' ); ?></a>
                <a href="freedoms.php" class="nav-tab nav-tab-active"><?php _e( 'Freedoms' ); ?></a>
        </h2>

<p class="about-description"><?php printf( 'Mix Financial System' ); ?></p>

<ol start="0">
	<li><p><?php _e( 'You have the freedom to run the program, for any purpose.' ); ?></p></li>
	<li><p><?php _e( 'You have access to the source code, the freedom to study how the program works, and the freedom to change it to make it do what you wish.' ); ?></p></li>
	<li><p><?php _e( 'You have the freedom to redistribute copies of the original program so you can help your neighbor.' ); ?></p></li>
	<li><p><?php _e( 'You have the freedom to distribute copies of your modified versions to others. By doing this you can give the whole community a chance to benefit from your changes.' ); ?></p></li>
</ol>

<p>
</div>
<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
