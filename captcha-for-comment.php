<?php 
/*
 * Plugin Name:   Captcha For Comment
 * Version:       3.0
 * Plugin URI:    http://wordpress.org/extend/plugins/captcha-for-comment/
 * Description:   A simple captcha system for comment system to prevent spam <a href="options-general.php?page=captcha-for-comment">here</a>.
 * Author:        MaxBlogPress
 * Author URI:    http://www.maxblogpress.com
 *
 * License:       GNU General Public License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 * Copyright (C) 2007 www.maxblogpress.com
 *
 */
 
$mbpcaptcha_path     = preg_replace('/^.*wp-content[\\\\\/]plugins[\\\\\/]/', '', __FILE__);
$mbpcaptcha_path     = str_replace('\\','/',$mbpcaptcha_path);
$mbpcaptcha_dir      = substr($mban_path,0,strrpos($mbpcaptcha_path,'/'));
$mbpcaptcha_siteurl  = get_bloginfo('wpurl');
$mbpcaptcha_siteurl  = (strpos($mbpcaptcha_siteurl,'http://') === false) ? get_bloginfo('siteurl') : $mbpcaptcha_siteurl;
$mbpcaptcha_fullpath = $mbpcaptcha_siteurl.'/wp-content/plugins/'.$mbpcaptcha_dir.'';
$mbpcaptcha_fullpath  = $mbpcaptcha_fullpath.'captcha-for-comment/';
$mbpcaptcha_abspath  = str_replace("\\","/",ABSPATH); 

define('CAPTCHA_LIBPATH', $mbpcaptcha_fullpath);
define('CAPTCHA_PATH', $mbpcaptcha_path);
define('CAPTCHA_NAME', 'Captcha For Comment');
define('CAPTCHA_VERSION', '3.0');  

class MBPCommentSparmSecurity {

	function MBPCommentSparmSecurity() {
	add_action('comment_form', array("MBPCommentSparmSecurity", "display_captcha"), 9999);
	add_action('comment_post', array("MBPCommentSparmSecurity", "post_sparmfree_comment"));
	add_action('admin_menu', array("MBPCommentSparmSecurity", "captcha_option_pg"));
	}
	
	function captcha_option_pg(){
	// Add a new submenu under Options:
add_options_page('Captcha for Comment', 'Captcha for Comment', 8, 'captcha-for-comment', array("MBPCommentSparmSecurity", "option_pg_captcha"));
	}
	
	function option_pg_captcha(){
	
	$captcha_activate = get_option('captcha_activate');
	$reg_msg = '';
	$captcha_msg = '';
	$form_1 = 'captcha_reg_form_1';
	$form_2 = 'captcha_reg_form_2';
		// Activate the plugin if email already on list
	if ( trim($_GET['mbp_onlist']) == 1 ) {
		$captcha_activate = 2;
		update_option('captcha_activate', $captcha_activate);
		$reg_msg = 'Thank you for registering the plugin. It has been activated'; 
	} 
	// If registration form is successfully submitted
	if ( ((trim($_GET['submit']) != '' && trim($_GET['from']) != '') || trim($_GET['submit_again']) != '') && $captcha_activate != 2 ) { 
		update_option('captcha_name', $_GET['name']);
		update_option('captcha_email', $_GET['from']);
		$captcha_activate = 1;
		update_option('captcha_activate', $captcha_activate);
	}
	if ( intval($captcha_activate) == 0 ) { // First step of plugin registration
		global $userdata;
		captchaRegisterStep1($form_1,$userdata);
	} else if ( intval($captcha_activate) == 1 ) { // Second step of plugin registration
		$name  = get_option('captcha_name');
		$email = get_option('captcha_email');
		captchaRegisterStep2($form_2,$name,$email);
	} else if ( intval($captcha_activate) == 2 ) { // Options page
		if ( trim($reg_msg) != '' ) {
			echo '<div id="message" class="updated fade"><p><strong>'.$reg_msg.'</strong></p></div>';
		}


	if($_POST['submit'] == "Remove"){
		update_option('captcha_image_options', array( $_POST['remove_noise'], $_POST['pwdby'] ));
	}
	
		$captcha_msg = "We request you to have the powered by link as this would be visible to your blog visitors and they would be benefited by this plugin as well.<br/><br/>If you want to remove the powered by link, we will appreciate a review post for this plugin in your blog. This will help lots of other people know about the plugin and get benefited by it. By the way, if for any reason you do not want to write a review post then its ok as well. No obligation. We will be much happy if you find out some other ways to spread the word for this plugin ";

	$captcha_img_option = get_option('captcha_image_options');
	if( $captcha_img_option[0] == 'remove_noise' )  $noise = 'checked'; 
	if( $captcha_img_option[1] == 'pwdby' )  $pwdby_option = 'checked'; 
	?>
<script type="text/javascript" src="<?php echo CAPTCHA_LIBPATH;?>tooltip.js"></script>
<link href="<?php echo CAPTCHA_LIBPATH;?>tooltip.css" rel="stylesheet" type="text/css">
	
<div class="wrap">
<h2><?php echo CAPTCHA_NAME.' '.CAPTCHA_VERSION; ?></h2>
<br>
<strong><img src="<?php echo CAPTCHA_LIBPATH;?>img/howimg.gif" border="0" align="absmiddle" /> <a href="http://wordpress.org/extend/plugins/captcha-for-comment/other_notes/" target="_blank">How to use it</a>&nbsp;&nbsp;&nbsp;
		<img src="<?php echo CAPTCHA_LIBPATH;?>img/commentimg.gif" border="0" align="absmiddle" /> <a href="http://www.maxblogpress.com/forum/forumdisplay.php?f=28" target="_blank">Community</a></strong>
<br><br>			
	<form action="" method="post">
    <table border="0" width="100%" bgcolor="#f1f1f1" style="border:1px solid #e5e5e5">

     <tr >
		<td style="padding:3px 3px 3px 3px; background-color:#fff">
<input name="pwdby" type="checkbox" value="pwdby" <?php echo $pwdby_option; ?>  /> &nbsp;Remove "powered by <?php echo CAPTCHA_NAME; ?>"&nbsp;  <a href="" onMouseover="tooltip('<?php echo $captcha_msg; ?>',480)" onMouseout="hidetooltip()" style="border-bottom:none;"><img src="<?php echo CAPTCHA_LIBPATH."img/helpimg.gif"; ?>" border="0"></a><br>
		</td>
	</tr>
	
	
<tr>
<td style="padding:3px 3px 3px 3px; background-color:#f1f1f1"><input name="submit" type="Submit" value="Remove"  class="button" /></td>
</tr>
	</table>
</form>

<br>

<div align="center" style="background-color:#f1f1f1; padding:5px 0px 5px 0px" >
<p align="center"><strong><?php echo CAPTCHA_NAME.' '.CAPTCHA_VERSION; ?> by <a href="http://www.maxblogpress.com" target="_blank">MaxBlogPress</a></strong></p>
<p align="center">This plugin is the result of <a href="http://www.maxblogpress.com/blog/219/maxblogpress-revived/" target="_blank">MaxBlogPress Revived</a> project.</p>
</div>
</div>


</div>

	<?php
	}
	
}	
	
	# ----------------------------------------------------------------
	# Check GD extension is loaded or not
	# ----------------------------------------------------------------
	function GDCheck() {
		if (!extension_loaded('gd')) {
		    return false;
		}
		return true;
	}

	# ----------------------------------------------------------------
	# Display Captcha Security 
	# ----------------------------------------------------------------
	function display_captcha($id) {
	
		global $obj_commentcaptcha;
		global $user_ID;
		
		$pwdbyoption = get_option('captcha_image_options');
		if( $pwdbyoption[0] == 'remove_noise' )  $removenoise = '1'; 

		
		# If GD not enabled, disable 
		if( !$obj_commentcaptcha->GDCheck() ) {
			# ---- Start HTML code ----
			?>
			<div style="background-color:#FFFBCC; border:solid 1px #E4F2FD; color: #000; width:50%; font-size:11px; font-weight:bold;  padding: 3px;" align="center">
              You need to enable GD extension in order to use Captcha For Comment.
			</div>
			<?php
			# ---- End HTML code ----
			return $id;
		}
		
		$captcha_activate = get_option('captcha_activate');
		if( $captcha_activate[0] < 2 ){
		?>
        <div align="center" style="background-color:#FFFBCC; border:solid 1px #E4F2FD; width:50%; font-size:11px; font-weight:bold; color: #000; padding: 3px;"><font color="#FF3300">Please register the plugin to activate it.</font></div><br>
		<?php
		 return $id;
		
		}else{	
		# If its registered user, hide CAPTCHA
		if( $user_ID ) {
		?>
        <div align="center" style="background-color:#FFFBCC; border:solid 1px #E4F2FD; width:50%; font-size:11px; font-weight:bold; color: #000; padding: 3px;">
You Can Post Comment Without CAPTCHA test.</div><br>
        <?php
			return $id;
		}	
		
		}
		
		# ---- Start HTML code ----
		
	///$captcha_img_option = get_option('captcha_image_options');
	?>
<br>
<div id="js_captcha_check">
<div align="left" style="background-color:#FFFBCC; border:solid 1px #E4F2FD; padding:10px 5px 10px 20px; width:50%; font-size:11px; font-weight:bold; color: #000;">
<table width="100%" border="0"><tr>
<td width="28%" align="left"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/captcha-for-comment/captcha.php?width=100&height=40&characters=5&noise=<?php echo $removenoise; ?>" /></td>
<td width="72%" align="center"><small>Security Code:</small><br>
  <input id="comment_security_code" name="comment_security_code" type="text" style="width:80px; height:20px" /></td>
</tr>
<?php if( !$pwdbyoption[1] == 'pwdby' ){  ?>
<tr>
<td colspan="2" align="center"><small style="color:#0066CC"><a href="http://wordpress.org/extend/plugins/captcha-for-comment/" style="font-size:12px; font:Arial, Helvetica, sans-serif;" target="_blank">Powered by Captcha for Comment</a></small></td>
</tr>
<?php } ?>
</table>
</div>
</div>
<!--Insert form above message box-->
<script type="text/javascript">
for( i = 0; i < document.forms.length; i++ ) {
	if( typeof(document.forms[i].comment_security_code) != 'undefined' ) {
		commentForm = document.forms[i].comment.parentNode;
		break;
	}
}
var commentArea = commentForm.parentNode;
var captchafrm = document.getElementById("js_captcha_check");
commentArea.insertBefore(captchafrm, commentForm);
commentArea.publicKey.size = commentArea.author.size;
commentArea.publicKey.className = commentArea.author.className;
</script>

<?php 

	# Display the alert box if wrong security code is entered
	if( isset($_POST['captcha_error']) ) {
		$obj_commentcaptcha->alertjsErrormsg();
	}
		
}// eof function
	
	# ----------------------------------------------------------------
	# Show error message
	# ----------------------------------------------------------------
	function alertjsErrormsg() {
		# ---- Start HTML code ----
		?>
<script type="text/javascript">
//<![CDATA[
	// Copy back the data into the form
	ff = document.getElementById("commentform");
	ff.author.value = "<?php echo htmlspecialchars($_POST['author1']); ?>";
	ff.email.value = "<?php echo htmlspecialchars($_POST['email1']); ?>";
	ff.url.value = "<?php echo htmlspecialchars($_POST['url1']); ?>";
	ff.comment.value = "<?php $trans = array("\r" => '\r', "\n" => '\n');
	echo strtr(htmlspecialchars($_POST['comment1']), $trans); ?>";
	alert("Invalid secutiry code! Please try again.");
//]]>
</script>
 <?php 	

	}//Eof function
	
	# ----------------------------------------------------------------
	# Post Comment
	# ----------------------------------------------------------------
	function post_sparmfree_comment($id) {
	
		global $obj_commentcaptcha;
		global $user_ID;
		
		# Is user, no need validate secret key, If GD not enabled, disable Simple CAPTCHA
		if( $user_ID || !$obj_commentcaptcha->GDCheck() ) {
			return $id;
		}
		
		session_start();
		$publicKey = $_POST['comment_security_code'];
		$secretKey = $_SESSION['captcha_comment_security_code'];
		
		$captcha_activate = get_option('captcha_activate');
		# Check if the public and private key match
		if( $obj_commentcaptcha->validateKey($publicKey, $secretKey) || $captcha_activate[0] < 2  ) {
			return $id;
		}
		
		wp_set_comment_status($id, 'delete');
		
		?><html>
		    <head><title>Invalid Security Code:</title></head>
			<body>
				<form name="data" action="<?php echo $_SERVER['HTTP_REFERER']; ?>#error" method="post">
					<input type="hidden" name="captcha_error" value="1" />
					<input type="hidden" name="author1" value="<?php echo htmlspecialchars($_POST['author']); ?>" />
					<input type="hidden" name="email1" value="<?php echo htmlspecialchars($_POST['email']); ?>" />
					<input type="hidden" name="url1" value="<?php echo htmlspecialchars($_POST['url']); ?>" />
					<textarea style="display:none;" name="comment1"><?php echo htmlspecialchars($_POST['comment']); ?></textarea>
				</form>
				<script type="text/javascript">
				<!--
				document.forms[0].submit();
				//-->
				</script>					
			</body>
		</html>
		<?php
		exit();
	}
	
	# ----------------------------------------------------------------
	# Validate pub key and secret key
	# ----------------------------------------------------------------
	function validateKey($public, $private) {
		
		if( $public == $private ) {
			return true;
		}
		return false;
	}
	
}// Eof class

$obj_commentcaptcha = new MBPCommentSparmSecurity();

	

// Srart Registration.

/**
 * Plugin registration form
 */
function captchaRegistrationForm($form_name, $submit_btn_txt='Register', $name, $email, $hide=0, $submit_again='') {
	$wp_url = get_bloginfo('wpurl');
	$wp_url = (strpos($wp_url,'http://') === false) ? get_bloginfo('siteurl') : $wp_url;
	$plugin_pg    = 'options-general.php';
	$thankyou_url = $wp_url.'/wp-admin/'.$plugin_pg.'?page='.$_GET['page'];
	$onlist_url   = $wp_url.'/wp-admin/'.$plugin_pg.'?page='.$_GET['page'].'&amp;mbp_onlist=1';
	if ( $hide == 1 ) $align_tbl = 'left';
	else $align_tbl = 'center';
	?>
	
	<?php if ( $submit_again != 1 ) { ?>
	<script><!--
	function trim(str){
		var n = str;
		while ( n.length>0 && n.charAt(0)==' ' ) 
			n = n.substring(1,n.length);
		while( n.length>0 && n.charAt(n.length-1)==' ' )	
			n = n.substring(0,n.length-1);
		return n;
	}
	function captchaValidateForm_0() {
		var name = document.<?php echo $form_name;?>.name;
		var email = document.<?php echo $form_name;?>.from;
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var err = ''
		if ( trim(name.value) == '' )
			err += '- Name Required\n';
		if ( reg.test(email.value) == false )
			err += '- Valid Email Required\n';
		if ( err != '' ) {
			alert(err);
			return false;
		}
		return true;
	}
	//-->
	</script>
	<?php } ?>
	<table align="<?php echo $align_tbl;?>">
	<form name="<?php echo $form_name;?>" method="post" action="http://www.aweber.com/scripts/addlead.pl" <?php if($submit_again!=1){;?>onsubmit="return captchaValidateForm_0()"<?php }?>>
	 <input type="hidden" name="unit" value="maxbp-activate">
	 <input type="hidden" name="redirect" value="<?php echo $thankyou_url;?>">
	 <input type="hidden" name="meta_redirect_onlist" value="<?php echo $onlist_url;?>">
	 <input type="hidden" name="meta_adtracking" value="mr-captcha-for-comment">
	 <input type="hidden" name="meta_message" value="1">
	 <input type="hidden" name="meta_required" value="from,name">
	 <input type="hidden" name="meta_forward_vars" value="1">	
	 <?php if ( $submit_again == 1 ) { ?> 	
	 <input type="hidden" name="submit_again" value="1">
	 <?php } ?>		 
	 <?php if ( $hide == 1 ) { ?> 
	 <input type="hidden" name="name" value="<?php echo $name;?>">
	 <input type="hidden" name="from" value="<?php echo $email;?>">
	 <?php } else { ?>
	 <tr><td>Name: </td><td><input type="text" name="name" value="<?php echo $name;?>" size="25" maxlength="150" /></td></tr>
	 <tr><td>Email: </td><td><input type="text" name="from" value="<?php echo $email;?>" size="25" maxlength="150" /></td></tr>
	 <?php } ?>
	 <tr><td>&nbsp;</td><td><input type="submit" name="submit" value="<?php echo $submit_btn_txt;?>" class="button" /></td></tr>
	 </form>
	</table>
	<?php
}

/**
 * Register Plugin - Step 2
 */
function captchaRegisterStep2($form_name='frm2',$name,$email) {
	$msg = 'You have not clicked on the confirmation link yet. A confirmation email has been sent to you again. Please check your email and click on the confirmation link to activate the plugin.';
	if ( trim($_GET['submit_again']) != '' && $msg != '' ) {
		echo '<div id="message" class="updated fade"><p><strong>'.$msg.'</strong></p></div>';
	}
	?>
	<style type="text/css">
	table, tbody, tfoot, thead {
		padding: 8px;
	}
	tr, th, td {
		padding: 0 8px 0 8px;
	}
	</style>
	<div class="wrap"><h2> <?php echo CAPTCHA_NAME.' '.CAPTCHA_VERSION; ?></h2>
	 <center>
	 <table width="100%" cellpadding="3" cellspacing="1" style="border:1px solid #e3e3e3; padding: 8px; background-color:#f1f1f1;">
	 <tr><td align="center">
	 <table width="650" cellpadding="5" cellspacing="1" style="border:1px solid #e9e9e9; padding: 8px; background-color:#ffffff; text-align:left;">
	  <tr><td align="center"><h3>Almost Done....</h3></td></tr>
	  <tr><td><h3>Step 1:</h3></td></tr>
	  <tr><td>A confirmation email has been sent to your email "<?php echo $email;?>". You must click on the link inside the email to activate the plugin.</td></tr>
	  <tr><td><strong>The confirmation email will look like:</strong><br /><img src="http://www.maxblogpress.com/images/activate-plugin-email.jpg" vspace="4" border="0" /></td></tr>
	  <tr><td>&nbsp;</td></tr>
	  <tr><td><h3>Step 2:</h3></td></tr>
	  <tr><td>Click on the button below to Verify and Activate the plugin.</td></tr>
	  <tr><td><?php captchaRegistrationForm($form_name.'_0','Verify and Activate',$name,$email,$hide=1,$submit_again=1);?></td></tr>
	 </table>
	 </td></tr></table><br />
	 <table width="100%" cellpadding="3" cellspacing="1" style="border:1px solid #e3e3e3; padding:8px; background-color:#f1f1f1;">
	 <tr><td align="center">
	 <table width="650" cellpadding="5" cellspacing="1" style="border:1px solid #e9e9e9; padding:8px; background-color:#ffffff; text-align:left;">
	   <tr><td><h3>Troubleshooting</h3></td></tr>
	   <tr><td><strong>The confirmation email is not there in my inbox!</strong></td></tr>
	   <tr><td>Dont panic! CHECK THE JUNK, spam or bulk folder of your email.</td></tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr><td><strong>It's not there in the junk folder either.</strong></td></tr>
	   <tr><td>Sometimes the confirmation email takes time to arrive. Please be patient. WAIT FOR 6 HOURS AT MOST. The confirmation email should be there by then.</td></tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr><td><strong>6 hours and yet no sign of a confirmation email!</strong></td></tr>
	   <tr><td>Please register again from below:</td></tr>
	   <tr><td><?php captchaRegistrationForm($form_name,'Register Again',$name,$email,$hide=0,$submit_again=2);?></td></tr>
	   <tr><td><strong>Help! Still no confirmation email and I have already registered twice</strong></td></tr>
	   <tr><td>Okay, please register again from the form above using a DIFFERENT EMAIL ADDRESS this time.</td></tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr>
		 <td><strong>Why am I receiving an error similar to the one shown below?</strong><br />
			 <img src="http://www.maxblogpress.com/images/no-verification-error.jpg" border="0" vspace="8" /><br />
		   You get that kind of error when you click on &quot;Verify and Activate&quot; button or try to register again.<br />
		   <br />
		   This error means that you have already subscribed but have not yet clicked on the link inside confirmation email. In order to  avoid any spam complain we don't send repeated confirmation emails. If you have not recieved the confirmation email then you need to wait for 12 hours at least before requesting another confirmation email. </td>
	   </tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr><td><strong>But I've still got problems.</strong></td></tr>
	   <tr><td>Stay calm. <strong><a href="http://www.maxblogpress.com/contact-us/" target="_blank">Contact us</a></strong> about it and we will get to you ASAP.</td></tr>
	 </table>
	 </td></tr></table>
	 </center>		
	<p style="text-align:center;margin-top:3em;"><strong><?php echo CAPTCHA_NAME.' '.CAPTCHA_VERSION; ?> by <a href="http://www.maxblogpress.com/" target="_blank" >MaxBlogPress</a></strong></p>
	</div>
	<?php
}

/**
 * Register Plugin - Step 1
 */
function captchaRegisterStep1($form_name='frm1',$userdata) {
	$name  = trim($userdata->first_name.' '.$userdata->last_name);
	$email = trim($userdata->user_email);
	?>
	<style type="text/css">
	tabled , tbody, tfoot, thead {
		padding: 8px;
	}
	tr, th, td {
		padding: 0 8px 0 8px;
	}
	</style>
	<div class="wrap"><h2> <?php echo CAPTCHA_NAME.' '.CAPTCHA_VERSION; ?></h2>
	 <center>
	 <table width="100%" cellpadding="3" cellspacing="1" style="border:2px solid #e3e3e3; padding: 8px; background-color:#f1f1f1;">
	  <tr><td align="center">
		<table width="548" align="center" cellpadding="3" cellspacing="1" style="border:1px solid #e9e9e9; padding: 8px; background-color:#ffffff;">
		  <tr><td align="center"><h3>Please register the plugin to activate it. (Registration is free)</h3></td></tr>
		  <tr><td align="left">In addition you'll receive complimentary subscription to MaxBlogPress Newsletter which will give you many tips and tricks to attract lots of visitors to your blog.</td></tr>
		  <tr><td align="center"><strong>Fill the form below to register the plugin:</strong></td></tr>
		  <tr><td align="center"><?php captchaRegistrationForm($form_name,'Register',$name,$email);?></td></tr>
		  <tr><td align="center"><font size="1">[ Your contact information will be handled with the strictest confidence <br />and will never be sold or shared with third parties ]</font></td></tr>
		</table>
	  </td></tr></table>
	 </center>
	<p style="text-align:center;margin-top:3em;"><strong><?php echo CAPTCHA_NAME.' '.CAPTCHA_VERSION; ?> by <a href="http://www.maxblogpress.com/" target="_blank" >MaxBlogPress</a></strong></p>
	</div>
	<?php
}
		
?>