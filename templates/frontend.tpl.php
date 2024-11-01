<h2 class="widgettitle"><?php echo $heading ?></h2>
<div class="widget">
<?php if($error == 'bad_email_entry'): ?>
  <h4 style="margin:0; color: brown;">Bad email</h4>
<?php endif ?>
<?php if($api_responce_msg): ?>
  <h4 style="margin:0;"><?php echo $api_responce_msg ?></h4>
<?php endif ?> 
  <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
     <!--  Your name is: <input size="8" type="text" name="subscriber_name" value="Type name" onmousedown="javascript:if(this.value == 'Type name')this.value=''; this.focus()" onblur="javascript:if(this.value.length == 0)this.value='Type name'"/>
      <br />
<?php// if ($api_responce_lists): ?>
      Lists: <select name="listname">
<?php //foreach ($api_responce_lists as $field): ?>       
       <option value="<?php// echo $field->listname ?>"><?php //echo $field->listname ?></option>
<?php //endforeach ?>
      </select>      
<?php// endif ?>      
      <br />   -->
      <input size="15" id="email_input" type="text" name="email" value="Type your email" onmousedown="javascript:if(this.value == 'Type your email')this.value=''; this.focus()" onblur="javascript:if(this.value.length == 0)this.value='Type your email'" />
      <input type="submit" value="ok" onclick="javascript: if (document.getElementById('email_input').value == 'Type your email'){alert('Enter your email'); return false;}"/>
      <input type="hidden" name="DHS_subscribe_request" value="1" />
  </form> 
</div>