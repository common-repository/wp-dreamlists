<script src="../wp-content/plugins/WP-DreamLists/jquery.dataTables.js" type="text/javascript" language="JavaScript"></script>
<script type="text/javascript" language="javascript">
jQuery(document).ready( function($) {
 $("#mails_table").tablesorter( {headers: { 0: { sorter: false}, 4: {sorter: false} }} );

});
</script>
<style>
<!--
  .header{
        cursor: pointer;
  }

  .headerSortDown{
        background-image: url(../wp-content/plugins/WP-DreamLists/images/sort_asc.gif) !important;
        background-repeat: no-repeat !important;
        background-position: center left !important;
        padding-left: 20px !important;
  }

  .headerSortUp{
        background-image: url(../wp-content/plugins/WP-DreamLists/images/sort_desc.gif) !important;
        background-repeat: no-repeat !important;
        background-position: center left !important;
        padding-left: 20px !important;
  }
//-->
</style>
<div class="wrap">
  <h2>Dreamhost subscribers mails control.</h2>
   <form action="<?php echo $_SERVER['PHP_SELF'] ?>?page=dreamhost_mails_control" method="post">
    <div class="tablenav" style="float: left;">
     <div class="alignleft actions">
      <select name="action">
        <option selected="selected" value="-1">Select acion</option>
        <option value="delete">Remove</option>
      </select>
      <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Update"/>
     </div>
    </div>
    <table id="mails_table" cellspacing="0" class="widefat page fixed">
      <thead>
       <tr>
      	<th style="" class="manage-column column-cb check-column" scope="col">&nbsp;</th>
        <th style="" class="manage-column column-title" scope="col">Subject</th>
        <th style="" class="manage-column column-title" scope="col">Mail created</th>
        <th style="" class="manage-column column-date" scope="col">Status</th>
        <th style="" class="manage-column column-date" scope="col">Action</th>
       </tr>
      </thead>
      <tfoot>
       <tr>
      	<th style="" class="manage-column column-cb check-column" scope="col">&nbsp;</th>
        <th style="" class="manage-column column-title" scope="col">Subject</th>
        <th style="" class="manage-column column-title" scope="col">Mail created</th>
        <th style="" class="manage-column column-date" scope="col">Status</th>
        <th style="" class="manage-column column-date" scope="col">Action</th>
       </tr>
      </tfoot>
      <tbody>
<?php if($stored_mails): ?>
<?php foreach($stored_mails as $key=>$field): ?>
       <tr class="alternate iedit">
    		  <th class="check-column" scope="row"><input type="checkbox" name="mail[<?php echo $field->id; ?>]" /></th>
          <td><?php echo $field->mail_subject ?></td>
          <td class="post-title page-title column-title"><?php echo $field->created_at ?></td>
          <td class="author column-author"><?php echo ($field->status == 'sent')?'Mail sent':'<font color="red">Mail unsent</font>'; ?></td>
          <td class="comments column-comments"><input onclick="javascript: view_mail('<?php echo $field->id ?>'); return false;" type="submit" value="View" class="button-secondary action" name="send_mail" /></td>
       </tr>
<?php endforeach ?>
<?php else: ?>
       <tr>
         <td colspan="4"><center><strong>No mails.</strong></center></td>
       </tr>
<?php endif ?>
    	</tbody>
    </table>
    <div class="tablenav" style="text-align: center;">
     <div class="tablenav-pages" style="float: none;"><?php echo $spliter->display_links('<<', '>>') ?></div>
    </div>
    <div class="tablenav">
      <div class="alignleft actions">
        <select name="action2">
          <option selected="selected" value="-1">Select acion</option>
          <option value="delete">Remove</option>
        </select>
        <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Update"/>
      </div>
    </div>
   </form>
</div>
<script type="text/javascript">
  function view_mail(mail_id){
        document.location.href = "<?php echo $_SERVER['PHP_SELF'].'?page=dreamhost_sendmessage' ?>&mail_id="+mail_id;
  }
</script>