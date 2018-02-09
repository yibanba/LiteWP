<?php
/**
 * WordPress User Page
 *
 * Handles authentication, registering, resetting passwords, forgot password,
 * and other user handling.
 *
 * @package WordPress
 */

/** Make sure that the WordPress bootstrap has run before continuing. */
require( dirname(__FILE__) . '/wp-load.php' );

// Redirect to https login if forced to use SSL
if ( force_ssl_admin() && ! is_ssl() ) {
	if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
		wp_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
		exit();
	} else {
		wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		exit();
	}
}

/**
 * Output the login page header.
 *
 * @param string   $title    Optional. WordPress login Page title to display in the `<title>` element.
 *                           Default 'Log In'.
 * @param string   $message  Optional. Message to display in header. Default empty.
 * @param WP_Error $wp_error Optional. The error to pass. Default empty.
 */
function login_header( $title = 'Log In', $message = '', $wp_error = '' ) {
	global $error, $interim_login, $action;

	// Don't index any of these forms
	add_action( 'login_head', 'wp_no_robots' );

	add_action( 'login_head', 'wp_login_viewport_meta' );

	if ( empty($wp_error) )
		$wp_error = new WP_Error();

	// Shake it!
	$shake_error_codes = array( 'empty_password', 'empty_email', 'invalid_email', 'invalidcombo', 'empty_username', 'invalid_username', 'incorrect_password' );
	/**
	 * Filters the error codes array for shaking the login form.
	 *
	 * @since 3.0.0
	 *
	 * @param array $shake_error_codes Error codes that shake the login form.
	 */
	$shake_error_codes = apply_filters( 'shake_error_codes', $shake_error_codes );

	if ( $shake_error_codes && $wp_error->get_error_code() && in_array( $wp_error->get_error_code(), $shake_error_codes ) )
		add_action( 'login_head', 'wp_shake_js', 12 );

	$separator = is_rtl() ? ' &rsaquo; ' : ' &lsaquo; ';

	?><!DOCTYPE html>
	<!--[if IE 8]>
		<html xmlns="http://www.w3.org/1999/xhtml" class="ie8" <?php language_attributes(); ?>>
	<![endif]-->
	<!--[if !(IE 8) ]><!-->
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<!--<![endif]-->
	<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php echo get_bloginfo( 'name', 'display' ) . $separator . $title; ?></title>
	<?php

	wp_enqueue_style( 'login' );

	/*
	 * Remove all stored post data on logging out.
	 * This could be added by add_action('login_head'...) like wp_shake_js(),
	 * but maybe better if it's not removable by plugins
	 */
	if ( 'loggedout' == $wp_error->get_error_code() ) {
		?>
		<script>if("sessionStorage" in window){try{for(var key in sessionStorage){if(key.indexOf("wp-autosave-")!=-1){sessionStorage.removeItem(key)}}}catch(e){}};</script>
		<?php
	}

	/**
	 * Enqueue scripts and styles for the login page.
	 *
	 * @since 3.1.0
	 */
	do_action( 'login_enqueue_scripts' );

	/**
	 * Fires in the login page header after scripts are enqueued.
	 *
	 * @since 2.1.0
	 */
	do_action( 'login_head' );

	if ( is_multisite() ) {
		$login_header_url   = network_home_url();
		$login_header_title = get_network()->site_name;
	} else {
		$login_header_url   = __( 'https://wordpress.org/' );
		$login_header_title = __( 'Powered by WordPress' );
	}

	/**
	 * Filters link URL of the header logo above login form.
	 *
	 * @since 2.1.0
	 *
	 * @param string $login_header_url Login header logo URL.
	 */
	$login_header_url = apply_filters( 'login_headerurl', $login_header_url );

	/**
	 * Filters the title attribute of the header logo above login form.
	 *
	 * @since 2.1.0
	 *
	 * @param string $login_header_title Login header logo title attribute.
	 */
	$login_header_title = apply_filters( 'login_headertitle', $login_header_title );

	$classes = array( 'login-action-' . $action, 'wp-core-ui' );
	if ( is_rtl() )
		$classes[] = 'rtl';
	if ( $interim_login ) {
		$classes[] = 'interim-login';
		?>
		<style type="text/css">html{background-color: transparent;}</style>
		<?php

		if ( 'success' ===  $interim_login )
			$classes[] = 'interim-login-success';
	}
	$classes[] =' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

	/**
	 * Filters the login page body classes.
	 *
	 * @since 3.5.0
	 *
	 * @param array  $classes An array of body classes.
	 * @param string $action  The action that brought the visitor to the login page.
	 */
	$classes = apply_filters( 'login_body_class', $classes, $action );

	?>
	</head>
	<body class="login <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php
	/**
	 * Fires in the login page header after the body tag is opened.
	 *
	 * @since 4.6.0
	 */
	do_action( 'login_header' );
	?>
	<div id="login">
	<?php

	unset( $login_header_url, $login_header_title );

	/**
	 * Filters the message to display above the login form.
	 *
	 * @since 2.1.0
	 *
	 * @param string $message Login message text.
	 */
	$message = apply_filters( 'login_message', $message );
	if ( !empty( $message ) )
		echo $message . "\n";

	// In case a plugin uses $error rather than the $wp_errors object
	if ( !empty( $error ) ) {
		$wp_error->add('error', $error);
		unset($error);
	}

	if ( $wp_error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data( $code );
			foreach ( $wp_error->get_error_messages( $code ) as $error_message ) {
				if ( 'message' == $severity )
					$messages .= '	' . $error_message . "<br />\n";
				else
					$errors .= '	' . $error_message . "<br />\n";
			}
		}
		if ( ! empty( $errors ) ) {
			/**
			 * Filters the error messages displayed above the login form.
			 *
			 * @since 2.1.0
			 *
			 * @param string $errors Login error message.
			 */
			echo '<div id="login_error">' . apply_filters( 'login_errors', $errors ) . "</div>\n";
		}
		if ( ! empty( $messages ) ) {
			/**
			 * Filters instructional messages displayed above the login form.
			 *
			 * @since 2.5.0
			 *
			 * @param string $messages Login messages.
			 */
			echo '<p class="message">' . apply_filters( 'login_messages', $messages ) . "</p>\n";
		}
	}
} // End of login_header()

/**
 * @since 3.0.0
 */
function wp_shake_js() {
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
function s(id,pos){g(id).left=pos+'px';}
function g(id){return document.getElementById(id).style;}
function shake(id,a,d){c=a.shift();s(id,c);if(a.length>0){setTimeout(function(){shake(id,a,d);},d);}else{try{g(id).position='static';wp_attempt_focus();}catch(e){}}}
addLoadEvent(function(){ var p=new Array(15,30,15,0,-15,-30,-15,0);p=p.concat(p.concat(p));var i=document.forms[0].id;g(i).position='relative';shake(i,p,20);});
</script>
<?php
}

/**
 * @since 3.7.0
 */
function wp_login_viewport_meta() {
	?>
	<meta name="viewport" content="width=device-width" />
	<?php
}

//
// Main
//

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
$errors = new WP_Error();

if ( isset($_GET['key']) )
	$action = 'resetpass';

// validate action so as to default to the login screen
if ( !in_array( $action, array( 'postpass', 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'login' ), true ) && false === has_filter( 'login_form_' . $action ) )
	$action = 'login';

nocache_headers();

header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

if ( defined( 'RELOCATE' ) && RELOCATE ) { // Move flag is set
	if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
		$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );

	$url = dirname( set_url_scheme( 'http://' .  $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ) );
	if ( $url != get_option( 'siteurl' ) )
		update_option( 'siteurl', $url );
}

//Set a cookie now to see if they are supported by the browser.
$secure = ( 'https' === parse_url( wp_login_url(), PHP_URL_SCHEME ) );
setcookie( TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN, $secure );
if ( SITECOOKIEPATH != COOKIEPATH )
	setcookie( TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );

/**
 * Fires when the login form is initialized.
 *
 * @since 3.2.0
 */
do_action( 'login_init' );
/**
 * Fires before a specified login form action.
 *
 * The dynamic portion of the hook name, `$action`, refers to the action
 * that brought the visitor to the login form. Actions include 'postpass',
 * 'logout', 'lostpassword', etc.
 *
 * @since 2.8.0
 */
do_action( "login_form_{$action}" );

$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
$interim_login = isset($_REQUEST['interim-login']);

switch ($action) {

case 'logout' :
	check_admin_referer('log-out');

	$user = wp_get_current_user();

	wp_logout();

	if ( ! empty( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
	} else {
		$redirect_to = 'wp-login.php?loggedout=true';
		$requested_redirect_to = '';
	}

	/**
	 * Filters the log out redirect URL.
	 *
	 * @since 4.2.0
	 *
	 * @param string  $redirect_to           The redirect destination URL.
	 * @param string  $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User $user                  The WP_User object for the user that's logging out.
	 */
	$redirect_to = apply_filters( 'logout_redirect', $redirect_to, $requested_redirect_to, $user );
	wp_safe_redirect( $redirect_to );
	exit();

case 'login' :
default:
	$secure_cookie = '';
	$customize_login = isset( $_REQUEST['customize-login'] );
	if ( $customize_login )
		wp_enqueue_script( 'customize-base' );

	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
		$user_name = sanitize_user($_POST['log']);
		$user = get_user_by( 'login', $user_name );

		if ( ! $user && strpos( $user_name, '@' ) ) {
			$user = get_user_by( 'email', $user_name );
		}

		if ( $user ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	}

	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}

	$reauth = empty($_REQUEST['reauth']) ? false : true;

	$user = wp_signon( array(), $secure_cookie );

	if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
		if ( headers_sent() ) {
			/* translators: 1: Browser cookie documentation URL, 2: Support forums URL */
			$user = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.' ),
				__( 'https://codex.wordpress.org/Cookies' ), __( 'https://wordpress.org/support/' ) ) );
		} elseif ( isset( $_POST['testcookie'] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
			// If cookies are disabled we can't log in even with a valid user+pass
			/* translators: 1: Browser cookie documentation URL */
			$user = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href="%s">enable cookies</a> to use WordPress.' ),
				__( 'https://codex.wordpress.org/Cookies' ) ) );
		}
	}

	$requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
	/**
	 * Filters the login redirect URL.
	 *
	 * @since 3.0.0
	 *
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
	 */
	$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );

	if ( !is_wp_error($user) && !$reauth ) {
		if ( $interim_login ) {
			$message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
			$interim_login = 'success';
			login_header( '', $message ); ?>
			</div>
			<?php
			/** This action is documented in wp-login.php */
			do_action( 'login_footer' ); ?>
			<?php if ( $customize_login ) : ?>
				<script type="text/javascript">setTimeout( function(){ new wp.customize.Messenger({ url: '<?php echo wp_customize_url(); ?>', channel: 'login' }).send('login') }, 1000 );</script>
			<?php endif; ?>
			</body></html>
<?php		exit;
		}

		if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
			// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
			if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) )
				$redirect_to = user_admin_url();
			elseif ( is_multisite() && !$user->has_cap('read') )
				$redirect_to = get_dashboard_url( $user->ID );
			elseif ( !$user->has_cap('edit_posts') )
				$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();

			wp_redirect( $redirect_to );
			exit();
		}
		wp_safe_redirect($redirect_to);
		exit();
	}

	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) || $reauth )
		$errors = new WP_Error();

	if ( $interim_login ) {
		if ( ! $errors->get_error_code() )
			$errors->add( 'expired', __( 'Your session has expired. Please log in to continue where you left off.' ), 'message' );
	} else {
		// Some parts of this script use the main login form to display a message
		if		( isset($_GET['loggedout']) && true == $_GET['loggedout'] )
			$errors->add('loggedout', __('You are now logged out.'), 'message');
	}

	/**
	 * Filters the login page errors.
	 *
	 * @since 3.6.0
	 *
	 * @param object $errors      WP Error object.
	 * @param string $redirect_to Redirect destination URL.
	 */
	$errors = apply_filters( 'wp_login_errors', $errors, $redirect_to );

	// Clear any stale cookies.
	if ( $reauth )
		wp_clear_auth_cookie();

	login_header(__('Log In'), '', $errors);

	if ( isset($_POST['log']) )
		$user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(wp_unslash($_POST['log'])) : '';
	$rememberme = ! empty( $_POST['rememberme'] );

	if ( ! empty( $errors->errors ) ) {
		$aria_describedby_error = ' aria-describedby="login_error"';
	} else {
		$aria_describedby_error = '';
	}
?>

<form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
	<p>
		<label for="user_login"><?php _e( 'Username' ); ?><br />
		<input type="text" name="log" id="user_login"<?php echo $aria_describedby_error; ?> class="input" value="<?php echo esc_attr( $user_login ); ?>" size="20" /></label>
	</p>
	<p>
		<label for="user_pass"><?php _e( 'Password' ); ?><br />
		<input type="password" name="pwd" id="user_pass"<?php echo $aria_describedby_error; ?> class="input" value="" size="20" /></label>
	</p>
	<?php
	/**
	 * Fires following the 'Password' field in the login form.
	 *
	 * @since 2.1.0
	 */
	do_action( 'login_form' );
	?>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Log In'); ?>" />
<?php	if ( $interim_login ) { ?>
		<input type="hidden" name="interim-login" value="1" />
<?php	} else { ?>
		<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
<?php 	} ?>
<?php   if ( $customize_login ) : ?>
		<input type="hidden" name="customize-login" value="1" />
<?php   endif; ?>
		<input type="hidden" name="testcookie" value="1" />
	</p>
</form>

<script type="text/javascript">
function wp_attempt_focus(){
setTimeout( function(){ try{
<?php if ( $user_login ) { ?>
d = document.getElementById('user_pass');
d.value = '';
<?php } else { ?>
d = document.getElementById('user_login');
<?php if ( 'invalid_username' == $errors->get_error_code() ) { ?>
if( d.value != '' )
d.value = '';
<?php
}
}?>
d.focus();
d.select();
} catch(e){}
}, 200);
}

/**
 * Filters whether to print the call to `wp_attempt_focus()` on the login screen.
 *
 * @since 4.8.0
 *
 * @param bool $print Whether to print the function call. Default true.
 */
<?php if ( apply_filters( 'enable_login_autofocus', true ) && ! $error ) { ?>
wp_attempt_focus();
<?php } ?>
if(typeof wpOnload=='function')wpOnload();
<?php if ( $interim_login ) { ?>
(function(){
try {
	var i, links = document.getElementsByTagName('a');
	for ( i in links ) {
		if ( links[i].href )
			links[i].target = '_blank';
	}
} catch(e){}
}());
<?php } ?>
</script>

<?php
break;
} // end action switch
