 <div class="wrap">
 <h2>Dreamhost subscribers Installation.</h2>
 <p>This plugin requires that you register for a  Dreamhost API Key in your <a href="http://panel.dreamhost.com">Dreamhost Control Panel</a>. Once you have the code, you may begin using this plugin.</p>
 <form action="<?php echo $_SERVER['PHP_SELF'] ?>?page=dreamhost_configure" method="post">
         <p>
              <label for="DHSW-account-name">Dreamhost account name:</label><br />
              <input type="text" id="DHSW-account-name" name="DHSW-account-name" value="<?php echo $DHS_optipons['owner_account_name'];?>" /><br />
              <label for="DHSW-account-key">Dreamhost account key:</label><br />
              <input type="text" id="DHSW-account-key" name="DHSW-account-key" value="<?php echo $DHS_optipons['owner_account_key'];?>" /><br />
              <input type="submit" id="DHSW-Submit" name="DHSW-Submit" value="Save" class="button-secondary action" />
        </p>
 </form>
 </div>