<script src="../wp-content/plugins/WP-DreamLists/jquery.dataTables.js" type="text/javascript" language="JavaScript"></script>
<script type="text/javascript" language="javascript">
jQuery(document).ready( function($) {
 $("#subscribers_table").tablesorter( {headers: { 0: { sorter: false} }} );

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
<h2>Dreamhost subscribers manage.</h2>
   <input type="submit" class="button-secondary action" id="doaction" onclick="refresh_list();" name="doaction" value="Refresh subscribers list"/>
   <div style="hright: 1px; border:1px solid black; margin: 15px 0 15px 0;"></div>
   <form action="<?php echo $_SERVER['PHP_SELF'] ?>?page=dreamhost_subscribers_list" method="post">
    <div>        
      <input size="15" type="text" name="email" style="border: 1px solid #DDDDDD;" value="Type email here" onmousedown="javascript:if(this.value == 'Type email here')this.value=''; this.focus()" onblur="javascript:if(this.value.length == 0)this.value='Type email here'" />
      <input id="doaction" class="button-secondary action" type="submit" value="Add Subscriber" name="doaction"/>
      <input type="hidden" name="DHS_subscribe_request" value="1" />
    </div> 
   </form>       
<?php if($api_responce): ?>
   <h4 style="margin-bottom: 0;">Dreamhost api:</h4>
   <div style="background-color: #DDDCCC; padding: 15px 20px; margin: 5px; border: 1px dashed black;"><?php echo $api_responce ?></div>
   <br />
<?php endif ?>   
   <form action="<?php echo $_SERVER['PHP_SELF'] ?>?page=dreamhost_subscribers_list" method="post">
    <div class="tablenav" style="float: left;">
     <div class="alignleft actions">
      <select name="action">
        <option selected="selected" value="-1">Select acion</option>
        <option value="delete">Remove</option>
      </select>      
      <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Update"/>
     </div>
    </div>
    <div style="display: inline; height: 40px;">
     <div class="alignleft actions" style="padding-top: 8px;">Listname:
     <select name="listname_selection" onchange="select_list(this.value)">
     <?php foreach($drop_down_info_array as $name): ?>
        <option <?php if($name == $_GET['listname']): ?>selected<?php endif ?> value="<?php echo $name ?>"><?php echo $name ?></option>
     <?php endforeach ?>
     </select>
     </div>
    </div>
    <table id="subscribers_table" cellspacing="0" class="widefat page fixed">
      <thead>
       <tr>
      	<th style="" class="manage-column column-cb check-column" scope="col">&nbsp;</th>
      	<th style="" class="manage-column column-title" scope="col">E-mail</th>
        <th style="" class="manage-column column-date" scope="col">Status</th>
        <th style="" class="manage-column column-date" scope="col">Num bounces</th>
        <th style="" class="manage-column column-author" scope="col">Subscribe date</th>        	
       </tr>
      </thead>
      <tfoot>
       <tr>
      	<th style="" class="manage-column column-cb check-column" scope="col">&nbsp;</th>
      	<th style="" class="manage-column column-title" scope="col">E-mail</th>
        <th style="" class="manage-column column-date" scope="col">Status</th>
        <th style="" class="manage-column column-date" scope="col">Num bounces</th>
        <th style="" class="manage-column column-author" scope="col">Subscribe date</th>
       </tr>
      </tfoot>
      <tbody>
<?php  if($subscribers_list): ?>
<?php foreach($subscribers_list as $key=>$field): ?>
       <tr class="alternate iedit">
    		  <th class="check-column" scope="row"><input type="checkbox" onclick="switch_fields('<?php echo $listname.'|'.$key; ?>', this.checked)" name="post[<?php echo $listname.'|'.$key; ?>]"/></th>
    			<td class="post-title page-title column-title"><?php echo $field->email ?></td>
          <td class="author column-author"><?php echo ($field->confirmed == '1')?'Confirmed':'Awaiting'; ?></td>
          <td class="comments column-comments"><?php echo $field->num_bounces ?></td>
          <td class="comments column-comments"><?php echo $field->subscribe_date ?></td>
       </tr>
       <input type="hidden" disabled id="<?php echo $listname.'|'.$key; ?>_email" name="email[<?php echo $listname.'|'.$key; ?>]" value="<?php echo $field->email ?>">
       <input type="hidden" disabled id="<?php echo $listname.'|'.$key; ?>_listname" name="listname[<?php echo $listname.'|'.$key; ?>]" value="<?php echo $listname ?>">
       <input type="hidden" disabled id="<?php echo $listname.'|'.$key; ?>_domain" name="domain[<?php echo $listname.'|'.$key; ?>]" value="<?php echo $domain ?>">
<?php endforeach ?>
<?php else: ?>
       <tr>
         <td colspan="4"><center><strong>No subscribers.</strong></center></td>
       </tr>
<?php endif ?>
    	</tbody>
    </table>
    <div class="tablenav">   
      <div class="alignleft actions">
        <select name="action2">
          <option selected="selected" value="-1">Select acion</option>
          <option value="delete">Remove</option>
        </select>      
        <input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Update"/>
      </div>
    </div>
    <div style="text-align: center;"><?php echo $links ?></div>
</div>
</form>
<script type="text/javascript">

    function select_list(listname){
          document.location.href="<?php echo $_SERVER['PHP_SELF'] ?>?page=dreamhost_subscribers_list&listname="+listname;
    }

    function refresh_list(){
          document.location.href="<?php echo $_SERVER['PHP_SELF'] ?>?page=dreamhost_subscribers_list&refresh_list=1";
    }

    function switch_fields(key_row, flag){
          if (flag){
                document.getElementById(key_row+'_email').disabled = false;
                document.getElementById(key_row+'_listname').disabled = false;
                document.getElementById(key_row+'_domain').disabled = false;
          } else {
                document.getElementById(key_row+'_email').disabled = false;
                document.getElementById(key_row+'_listname').disabled = false;
                document.getElementById(key_row+'_domain').disabled = false;          
          }
         // alert(key_row);
    }
    

</script>