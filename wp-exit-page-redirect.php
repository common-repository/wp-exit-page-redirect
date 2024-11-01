<?php
/*
Plugin Name: WP Exit Page Redirect
Plugin URI: http://www.lgr.ca/wp-exit-page-redirect/
Description: Create landing pages on your WordPress website that will automatically redirect to another website after a set period of time. Great for exit pages before sending people to another website. 
Version: 1.2.2
Author: Lee Robertson
Author Email: lee@lgr.ca
License:

  Copyright 2011 Lee Robertson (lee@lgr.ca)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

add_action('admin_init', 'lgr_wpexitpageoptions_init' );
add_action('admin_menu', 'lgr_wpexitpageoptions_add_page');

// Init plugin options
function lgr_wpexitpageoptions_init(){
	register_setting( 'lgr_exitpage_options', 'lgr_exitpage_options', 'lgr_exitpageoptions_validate' );
}

// Add menu page
function lgr_wpexitpageoptions_add_page() {
	add_options_page('Exit Page Redirect Options', 'Exit Page Options', 'manage_options', 'wp-exit-page-redirect.php', 'lgr_exitpage_do_page');
}

function lgr_wpexitpagedefault_options_init() {
//add some default values
			$lgrexit_defaultoptions = array(
			'seconds' => '5',
			'url' => 'http://www.lgr.ca/',
			'message' => 'Please wait while you are redirected.',
			'clickhere' => 'Click here to go now.',
			'showtimer' => 'off',
			'timermessage'  => 'Redirecting in',
		);
	update_option( 'lgr_exitpage_options', $lgrexit_defaultoptions );
}
register_activation_hook( __FILE__, 'lgr_wpexitpagedefault_options_init' );


// Draw the menu page
function lgr_exitpage_do_page() {
	?>
	<div class="wrap">
		<h2>Exit Page Redirect</h2>
		<p>Enter the defaults you would like for the shortcode below.</p>
		<form method="post" action="options.php">
			<?php settings_fields('lgr_exitpage_options'); ?>
			<?php $options = get_option('lgr_exitpage_options'); ?>
			<table class="form-table">
				<tr valign="top"><th scope="row">Message</th>
					<td><input type="text" name="lgr_exitpage_options[message]" value="<?php echo $options['message']; ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">Link Text</th>
					<td><input type="text" name="lgr_exitpage_options[clickhere]" value="<?php echo $options['clickhere']; ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">URL</th>
					<td><input type="text" name="lgr_exitpage_options[url]" value="<?php echo $options['url']; ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">Seconds</th>
					<td><input type="text" name="lgr_exitpage_options[seconds]" value="<?php echo $options['seconds']; ?>" /></td>
				<tr valign="top"><th scope="row">Show Timer Countdown</th>
					<td><select name="lgr_exitpage_options[showtimer]" size="1">
					<option value="on" label="On"<?php if ($options['showtimer']=='on') echo ' selected="selected"'; ?>></option>
					<option value="off" label="Off"<?php if ($options['showtimer']=='off') echo ' selected="selected"'; ?>></option>
					</select></td>
				</tr>
				<tr valign="top"><th scope="row">Timer Message</th>
					<td><input type="text" name="lgr_exitpage_options[timermessage]" value="<?php echo $options['timermessage']; ?>" /></td>
				</tr>

			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
<h2>How to Use</h2>
<p>Insert the shortcode [wpexitpage]. Only one per page or you might get unpredictable results.</p>
<p>Options:
<ul>
<li><strong>Message</strong><br />
To modify the message that is used on the page where the shortcode is used simply add the option [wpexitpage message="Your new message"] and it will replace the default text you set above.
</li>
<li><strong>Link Text</strong><br />
To modify the link text that is used on the page where the shortcode is used simply add the option [wpexitpage link="Go Here Now!"] and it will replace the default text you set above.
</li>
<li><strong>URL</strong><br />
To modify the URL that is used on the page and where the reader will be redirected to add the option [wpexitpage url="http://www.lgr.ca/"] and it will replace the default URL you set above. This needs to be correctly formatted with http:// or https://.</li> 
<li><strong>Seconds</strong><br />
To modify the number of seconds before your reader is redirected add the option [wpexitpage seconds="10"] and it will replace the default redirect time you set above.</li>
<li><strong>Show Countdown Timer</strong><br />
You can control whether the countdown timer shows in the shortcode by adding the option [wpexitpage showtimer="on"] or  [wpexitpage showtimer="off"]. All other values will be ignored. This can also be set as a global default if you want to always show the timer. <strong>Note:</strong>The countdown timer that shows is independent of the actual meta tag and may take less or more time depending on how fast the exit page loads.</li>
<li><strong>Timer Message</strong><br />
To modify the text that is shown before the seconds countdown timer add the option [wpexitpage timermessage="Making you leave in"] and it will replace the default text you set above.
</li>
</ul>
</p>

	</div>
	<?php	
}

function lgr_exitpageoptions_validate($input) {
	$input['message'] =  wp_filter_nohtml_kses($input['message']);
	$input['clickhere'] =  wp_filter_nohtml_kses($input['clickhere']);
	$input['seconds'] =  wp_filter_nohtml_kses($input['seconds']);
	$input['showtimer'] =  wp_filter_nohtml_kses($input['showtimer']);
	$input['timermessage'] =  wp_filter_nohtml_kses($input['timermessage']);
	$input['url'] =  esc_url($input['url']);
	return $input;
}

function lgrexitpage_embed_display($atts) {
	$options = get_option('lgr_exitpage_options');

	extract( shortcode_atts( array(
		'message' => $options['message'],
		'link' => $options['clickhere'],
		'seconds' => $options['seconds'],
		'url' => $options['url'],
		'showtimer' => $options['showtimer'],
		'timermessage' => $options['timermessage'],
	), $atts, 'wpexitpage' ) );

	$jsseconds = $seconds+1;

	if ($showtimer=='on') {
		$timerjs ='<script type="text/javascript">//<![CDATA[ 
//Javascript thanks to Stack Overflow. With a couple small changes.
//http://stackoverflow.com/questions/1191865/code-for-a-simple-javascript-countdown-timer
var count='.$jsseconds.';
var counter=setInterval(timer, 1000);
function timer() {
  count=count-1;
  if (count < 0) {
     clearInterval(counter);
     return;
  }
 document.getElementById("lgrtimer").innerHTML=count; 
}
//]]>
</script>
';
		$tmessage = $timermessage.' <span id="lgrtimer">'.$seconds.'</span> seconds.';
	}
	else {
		$timerjs ='';
		$tmessage ='';
	}
	$lgrwpexitpage_display = $timerjs.'
<div class="wpexitlink">'.$message.' <a href="'.$url.'" rel="nofollow">'.$link.'</a> '.$tmessage.'</div>';

	return $lgrwpexitpage_display;
}
add_shortcode('wpexitpage', 'lgrexitpage_embed_display'); 


function lgr_add_meta($atts) {
global $post;
	if( has_shortcode( $post->post_content, 'wpexitpage') ) {
		$options = get_option('lgr_exitpage_options');
		$pattern = get_shortcode_regex();
		if (preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) && array_key_exists( 2, $matches )	&& in_array( 'wpexitpage', $matches[2] ) ) { 
			$atts = str_replace(" ", "&", $matches[3]);
			$atts = str_replace('"', '', $atts);
			$atts = implode('', $atts);
			$attributes = wp_parse_args($atts, $options);
		}	
		echo '<meta http-equiv="refresh" content="'.$attributes['seconds'].'; url='.$attributes['url'].'" />';
	}
}	
add_action('wp_head', 'lgr_add_meta');
?>