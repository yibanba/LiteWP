<?php
/**
 * About This Version administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! wp_is_mobile() ) {
	wp_enqueue_style( 'wp-mediaelement' );
	wp_enqueue_script( 'wp-mediaelement' );
	wp_localize_script( 'mediaelement', '_wpmejsSettings', array(
		'pluginPath'        => includes_url( 'js/mediaelement/', 'relative' ),
		'pauseOtherPlayers' => '',
	) );
}

$video_url = 'https://videopress.com/embed/AHz0Ca46?hd=true';
$lang_code = str_replace( '_', '-', get_user_locale() );
list( $lang_code ) = explode( '-', $lang_code );
if ( 'en' !== $lang_code ) {
	$video_url = add_query_arg( 'defaultLangCode', $lang_code, $video_url );
}

$title = __( 'About' );

list( $display_version ) = explode( '-', get_bloginfo( 'version' ) );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
	<div class="wrap about-wrap">
		<h1><?php printf( __( 'Welcome to Mix Financial System &nbsp;%s' ), $display_version ); ?></h1>

		<p class="about-text"><?php printf( 'www.mixfs.com' ); ?></p>
		<div class="wp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
		</h2>

		<div class="changelog point-releases">
			<h3><?php _e( 'Maintenance and Security Releases' ); ?></h3>
			<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), $display_version ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.8.5' ); ?>
			</p>
			<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
					'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 1 ), '4.8.4', number_format_i18n( 1 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.8.4' ); ?>
			</p>
			<p><?php printf( __( '<strong>Version %s</strong> addressed one security issue.' ), '4.8.3' ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.8.3' ); ?>
			</p>
			<p><?php printf( _n( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
					'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.', 5 ), '4.8.2', number_format_i18n( 5 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.8.2' ); ?>
			</p>
			<p><?php printf( _n( '<strong>Version %1$s</strong> addressed %2$s bug.',
					'<strong>Version %1$s</strong> addressed %2$s bugs.', 29 ), '4.8.1', number_format_i18n( 29 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.8.1' ); ?>
			</p>
		</div>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'An Update with You in Mind' ); ?></h2>
				<p class="lead-description"><?php _e( 'WordPress 4.8 adds some great new features. Gear up for a more intuitive WordPress!' ); ?></p>
				<p><?php _e( 'Though some updates seem minor, they&#8217;ve been built by hundreds of contributors with <em>you</em> in mind. Get ready for new features you&#8217;ll welcome like an old friend: link improvements, <em>three</em> new media widgets covering images, audio, and video, an updated text widget that supports visual editing, and an upgraded news section in your dashboard which brings in nearby and upcoming WordPress events.' ); ?></p>
			</div>
		</div>

		<hr />

		<div class="changelog">
			<h2><?php
				printf(
					/* translators: %s: smiling face with smiling eyes emoji */
					__( 'Even More Developer Happiness %s' ),
					'&#x1F60A'
				);
			?></h2>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/17/cleaner-headings-in-the-admin-screens/"><?php _e( 'More Accessible Admin Panel Headings' ); ?></a></h3>
					<p><?php _e( 'New CSS rules mean extraneous content (like &ldquo;Add New&rdquo; links) no longer need to be included in admin-area headings. These panel headings improve the experience for people using assistive technologies.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/22/removal-of-core-embedding-support-for-wmv-and-wma-file-formats/"><?php _e( 'Removal of Core Support for WMV and WMA Files' ); ?></a></h3>
					<p><?php _e( 'As fewer and fewer browsers support Silverlight, file formats which require the presence of the Silverlight plugin are being removed from core support. Files will still display as a download link, but will no longer be embedded automatically.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/22/multisite-focused-changes-in-4-8/"><?php _e( 'Multisite Updates' ); ?></a></h3>
					<p><?php _e( 'New capabilities have been introduced to 4.8 with an eye towards removing calls to <code>is_super_admin()</code>. Additionally, new hooks and tweaks to more granularly control site and user counts per network have been added.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/23/addition-of-tinymce-to-the-text-widget/"><?php _e( 'Text-Editor JavaScript API' ); ?></a></h3>
					<p><?php _e( 'With the addition of TinyMCE to the text widget in 4.8 comes a new JavaScript API for instantiating the editor after page load. This can be used to add an editor instance to any text area, and customize it with buttons and functions. Great for plugin authors!' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/26/media-widgets-for-images-video-and-audio/"><?php _e( 'Media Widgets API' ); ?></a></h3>
					<p><?php _e( 'The introduction of a new base media widget REST API schema to 4.8 opens up possibilities for even more media widgets (like galleries or playlists) in the future. The three new media widgets are powered by a shared base class that covers most of the interactions with the media modal. That class also makes it easier to create new media widgets and paves the way for more to come.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/16/customizer-sidebar-width-is-now-variable/"><?php _e( 'Customizer Width Variable' ); ?></a></h3>
					<p><?php _e( 'Rejoice! New responsive breakpoints have been added to the customizer sidebar to make it wider on high-resolution screens. Customizer controls should use percentage-based widths instead of pixels.' ); ?></p>
				</div>
			</div>
		</div>

		<hr />

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
		</div>

	</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

__( 'Maintenance Release' );
__( 'Maintenance Releases' );

__( 'Security Release' );
__( 'Security Releases' );

__( 'Maintenance and Security Release' );
__( 'Maintenance and Security Releases' );

/* translators: %s: WordPress version number */
__( '<strong>Version %s</strong> addressed one security issue.' );
/* translators: %s: WordPress version number */
__( '<strong>Version %s</strong> addressed some security issues.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. */
_n_noop( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. Singular security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.' );

/* translators: 1: WordPress version number, 2: plural number of bugs. More than one security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.' );

/* translators: %s: Codex URL */
__( 'For more information, see <a href="%s">the release notes</a>.' );
