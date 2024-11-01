<script type='text/javascript' src='./js/editor.js?ver='<?=mce_version()?>></script>
<div class="wrap">
 <h2>Dreamhost subscribers send message.</h2>
   <?php
      if ($_GET['mail_id']){
            $form_url = $_SERVER['PHP_SELF'].'?page=dreamhost_sendmessage&mail_id='.$_GET['mail_id'];
      } else {
            $mail_param = $_SERVER['PHP_SELF'].'?page=dreamhost_mails_control';
      }
   ?>
   <form action="<?php echo $mail_param ?>" method="post">
<?php if($_POST['send_mail'] && $bulkmail_error == 'no_error'): ?>
     <h3 style="color: green;">Mail sent</h4> 
<?php elseif ($_POST['send_mail'] && $bulkmail_error != 'no_error'): ?>
    <h3 style="color: red;"><?=$bulkmail_error?></h4> 
<?php endif ?>     
     <div id="email_bulk_form">
        <br />Subject:<br />
        <input type="text" name="letter_subject" value="<?php echo $mail_contents->mail_subject ?>" size="40" style="border: 1px solid #DDDDDD;" />
        <br />From:<br />
        <input type="text" name="from" size="40" style="border: 1px solid #DDDDDD;" value="<?=$DHS_optipons['owner_account_name']?>" />
        <br />
      <div id="poststuff" class="metabox-holder">
      <div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : ''; ?>">
      <div id="post-body-content" class="has-sidebar-content">
      <div id="titlediv">

<div class="inside">
</div>
</div>

<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">

<?php the_editor($mail_contents->mail_content, 'content','title',false); ?>

<div id="post-status-info">
    <span id="wp-word-count" class="alignleft"></span>
    <span class="alignright">
    <span id="autosave">&nbsp;</span>
    </span>
    <br class="clear" />
</div>
</div>
</div>
</div><!-- /poststuff -->
</div>

        <br /><input type="submit" name="send_mail" class="button-secondary action" value="Send"/>
<?php if($_GET['mail_id']): ?>
      &nbsp;&nbsp;&nbsp;<input type="submit" name="send_mail" class="button-secondary action" value="Update"/>
      <input type="hidden" name="mail_id" value="<?php echo $_GET['mail_id'] ?>" />
<?php endif ?>
   </form>
</div>