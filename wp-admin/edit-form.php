
<div class="wrap">
<?php
$form_action = 'post';
$form_extra = '';

$form_pingback = '<input type="hidden" name="post_pingback" value="1" id="post_pingback" />';

$form_trackback = '<p><label for="trackback">' . sprintf(__('<a href="%s" title="Help on trackbacks"><strong>TrackBack</strong> a <abbr title="Universal Resource Identifier">URI</abbr></a>:</label> (Separate multiple <abbr title="Universal Resource Identifier">URI</abbr>s with spaces.)<br />'), 'http://wordpress.org/docs/reference/post/#trackback') .
	'<input type="text" name="trackback_url" style="width: 360px" id="trackback" tabindex="7" /></p>';


$saveasdraft = '';


?>

<form name="post" action="post.php" method="post" id="simple">

<?php
if (isset($mode) && 'bookmarklet' == $mode) {
    echo '<input type="hidden" name="mode" value="bookmarklet" />';
}
?>
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
<!--
function focusit() {
	// focus on first input field
	document.getElementById('title').focus();
}
window.onload = focusit;
//-->
</script>

<div id="poststuff">
    <fieldset id="titlediv">
      <legend><a href="http://wordpress.org/docs/reference/post/#title" title="<?php _e('Help on titles') ?>"><?php _e('Title') ?></a></legend> 
	  <div><input type="text" name="post_title" size="30" tabindex="1" value="<?php echo $edited_post_title; ?>" id="title" /></div>
    </fieldset>

    <fieldset id="categorydiv">
      <legend><a href="http://wordpress.org/docs/reference/post/#category" title="<?php _e('Help on categories') ?>"><?php _e('Categories') ?></a></legend> 
	  <div><?php dropdown_categories($default_post_cat); ?></div>
    </fieldset>

<br />
<fieldset id="postdiv">
    <legend><a href="http://wordpress.org/docs/reference/post/#post" title="<?php _e('Help with post field') ?>"><?php _e('Post') ?></a></legend>
<?php the_quicktags(); ?>
<?php
 $rows = get_settings('default_post_edit_rows');
 if (($rows < 3) || ($rows > 100)) {
     $rows = 10;
 }
?>
<div><textarea rows="<?php echo $rows; ?>" cols="40" name="content" tabindex="4" id="content"><?php echo $content ?></textarea></div>
</fieldset>


<script type="text/javascript">
<!--
edCanvas = document.getElementById('content');
//-->
</script>

<?php echo $form_pingback ?>
<?php echo $form_trackback; ?>

<p class="submit"><input name="saveasdraft" type="submit" id="saveasdraft" tabindex="9" value="<?php _e('Save as Draft') ?>" /> 
  <input name="saveasprivate" type="submit" id="saveasprivate" tabindex="10" value="<?php _e('Save as Private') ?>" /> 
  <input name="publish" type="submit" id="publish" tabindex="6" style="font-weight: bold;" value="<?php _e('Publish') ?>" /> 
  <?php if ('bookmarklet' != $mode) {
      echo '<input name="advanced" type="submit" id="advancededit" tabindex="7" value="' .  __('Advanced Editing &raquo;') . '" />';
  } ?>
  <input name="referredby" type="hidden" id="referredby" value="<?php if (isset($_SERVER['HTTP_REFERER'])) echo urlencode($_SERVER['HTTP_REFERER']); ?>" />
</p>
<?php do_action('edit_form', ''); ?>
</div>
</form>

</div>
