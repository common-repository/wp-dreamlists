<?php
/**
 *   functions that take care about actions of the WP administrator
 *      
 *   */
 
 function getDHSoptions() {
      $DHS_optipons = get_option('DHS_widget_settings');
      if(!is_array($DHS_optipons)){
            $DHS_optipons['frontend_heading'] = 'My Dreamhost Subscribes';

      }

      if ($DHS_optipons['subs_perpage'] < 1){
              $DHS_optipons['subs_perpage'] = 5;
      }

     return $DHS_optipons;
 }

  function DHS_manage(){

  // get personal settings     
      $DHS_optipons = getDHSoptions();

// instal checking
      if ($DHS_optipons['owner_account_key']){
            $api = new Api_DH($DHS_optipons['owner_account_name'], $DHS_optipons['owner_account_key']);

            if ($_GET['bulk_mail'] == 'true')
                  DHS_send_bulk_mail($_POST, $bulkmail_error, $api);

            DHS_backend_addSubscriber($api, $DHS_optipons['anouncement_list'], $_POST, $error, $api_responce);
            $posted_changes = $_POST['post'];

            if ($posted_changes && ($_POST['action'] == 'delete' || $_POST['action2'] == 'delete')){
                  foreach ($posted_changes as $key=>$value) {
                        $api->__cmd('announcement_list-remove_subscriber');
                        $api->__params(array('listname' => $_POST['listname'][$key],
                                             'domain' =>   $_POST['domain'][$key],
                                             'email' =>    $_POST['email'][$key]));
                        $api_responce = $api->__request()->getAll();

                  }
            }

      //BOF get subscribers list
            if (!$_SESSION['responce_subscribers'] || $_GET['refresh_list'] == '1' || ($posted_changes && ($_POST['action'] == 'delete' || $_POST['action2'] == 'delete'))){
                  $responce_subscribers_listnames = array();
                  unset($_SESSION['responce_subscribers']);

                  $api->__cmd('announcement_list-list_lists');
                  if(is_array($api_responce_lists = $api->__request()->getAll())){
                        foreach ($api_responce_lists as $key=>$value) {
                              $api->__cmd('announcement_list-list_subscribers');
                              $api->__params(array('listname' => $value->listname, 'domain' => $value->domain));

                              $responce_subscribers_listnames[$value->listname.'@'.$value->domain] = $api->__request()->getAll();
                              if ($key == 0){
                                    $_SESSION['default_subscribers_list'] = $responce_subscribers_listnames[$value->listname.'@'.$value->domain];
                                    $_SESSION['default_listname'] = $value->listname.'@'.$value->domain;
                              }
                        }

                        $_SESSION['responce_subscribers'] = $responce_subscribers_listnames;
                  }
            } else {
                  $responce_subscribers_listnames = $_SESSION['responce_subscribers'];
            }
      //EOF get subscribers list

            if ($_GET['listname']){
                  $subscribers_list_full = $responce_subscribers_listnames[$_GET['listname']];
                  $temp_array_listname = explode('@', $_GET['listname']);

                  $listname = $temp_array_listname[0];
                  $domain   = $temp_array_listname[1];
            } else {
                  $subscribers_list_full = $_SESSION['default_subscribers_list'];
                  $temp_array_listname = explode('@', $_SESSION['default_listname']);

                  $listname = $temp_array_listname[0];
                  $domain   = $temp_array_listname[1];
            }

            if (is_array($subscribers_list_full)){
                  $subscribers_list_pages = array_chunk($subscribers_list_full, $DHS_optipons['subs_perpage']);
                  $count_pages = count($subscribers_list_pages);

            //BOF pagination
                  $page = (int)$_GET['listing_page'];
                  if ($page < 1 || !$page)
                        $page = 1;

                  if ($count_pages < $page)
                        $page = $count_pages;

                  if ($page){
                       $subscribers_list = $subscribers_list_pages[($page-1)];
                  }

                  foreach ($subscribers_list_pages as $key=>$value) {
                        if ($page == ($key+1))
                              $selected = 'style="color: #D54E21;"';
                        else
                              $selected = '';

                        $links .= '<a '.$selected.' href="'.$_SERVER['PHP_SELF'].'?page=dreamhost_subscribers_list&listing_page='.($key+1).'">'.($key+1).'</a>&nbsp;&nbsp;';
                  }
            }
      //EOF pagination

            if ($_SESSION['responce_subscribers'])
                  foreach ($_SESSION['responce_subscribers'] as $key=>$value){
                        $drop_down_info_array[] = $key;
                  }

            require('templates/subscribers_list.tpl.php');
      } else {
            require('templates/subscribers_install.tpl.php');
      }

  } 

  function DHS_mails_control(){
      global $wpdb, $wp_query;
      require('split_page_results.php');
  // get personal settings
      $DHS_optipons = getDHSoptions();

  // instal checking
      if ($DHS_optipons['owner_account_key']){
      // delet selected mails
            $selected_mails = $_POST['mail'];
            if (is_array($selected_mails) && ($_POST['action'] == 'delete' || $_POST['action2'] == 'delete')){
                  foreach ($selected_mails as $key=>$value) {
                  	      $keys_to_del .= $key.',';
                  }
                  $keys_to_del = trim($keys_to_del, ',');
                  $wpdb->query("DELETE FROM ".$wpdb->prefix."dhs_mails WHERE id IN(".$keys_to_del.")");
            }
      // get stored mails
            $sql_get_mails = "SELECT * FROM ".$wpdb->prefix."dhs_mails order by created_at desc";
            $spliter = new splitResults($sql_get_mails, 15, $_GET);

            $stored_mails = $wpdb->get_results($spliter->sql_query);

            require('templates/subscribers_mails_control.tpl.php');
      } else {
            require('templates/subscribers_install.tpl.php');
      }
  }

  function DHS_subscribers() {
      if (isset($_POST['init']) && $_POST['init'] == 1) {
          if (isset($_SESSION['mails'])) {

          } else {
              $DHS_optipons = getDHSoptions();
              $api = new Api_DH($DHS_optipons['owner_account_name'], $DHS_optipons['owner_account_key']);
              $responce_subscribers = array();
              $api->__cmd('announcement_list-list_lists');
              $api_responce_lists = $api->__request()->getAll();
              foreach ($api_responce_lists as $key=>$value) {
                  $api->__cmd('announcement_list-list_subscribers');
                  $api->__params(array('listname' => $value->listname, 'domain' => $value->domain));
                  $responce_subscribers[$value->listname.'@'.$value->domain] = $api->__request()->getAll();
              }

              $emails_array = array();
              foreach ($responce_subscribers as $domain) {
                  foreach ($domain as $subscriber) {
                      if (!in_array($subscriber->email, $emails_array)){
                          $emails .= $subscriber->email.', ';
                          $emails_array[] = $subscriber->email;
                      }
                  }
              }

              $emails = trim($emails, ', ');

          }
          exit;
      }
  }

   function DHS_send_bulk_mail($post, &$bulkmail_error){
//        if ($post['bulk_emails']) {
            global $wpdb;

            $mail_subject = $post['letter_subject'];
            $mail_content = $post['content'];

            ob_start();
            get_header();
            $msg_header = ob_get_contents();
            ob_end_clean();
            ob_start();
            get_footer();
            $msg_footer = ob_get_contents();
            ob_end_clean();

            $message = stripslashes($message);
            $message = $msg_header . '<div id="content" class="narrowcolumn">' . $mail_content . '</div><div id="sidebar"></div><hr />' . $msg_footer;
//            $message = generate_mht($message);
//            echo $message; exit;
            $site_url = get_option('siteurl');
            $res = preg_match_all('/<link.*?rel="stylesheet".*?href="([^"]*?)"[^<>]*?>/i',$message,$matches);
            $site_url = get_option('siteurl');
            $css_url = '';
            if ($res != false) {
                $css_url = str_replace($site_url.'/','',$matches[1][0]);
                $css_url = substr($css_url,0,strrpos($css_url,'/')+1);
            }
            $message = preg_replace('/<link.*?rel="stylesheet".*?href="([^"]*?)"[^<>]*>/ie',"'<style type=text/css>'.file_get_contents('\\1').'</style>'",$message);
            $message = preg_replace('/url\(["|\']+/i',"\\0{$site_url}/{$css_url}",$message);
            $message = urlencode($message);
//            echo '<pre>';
//            echo $message;
//            echo '</pre>';
//            exit;
            $DHS_optipons = getDHSoptions();

            if ($post['send_mail'] != 'Update'){
                  $api = new Api_DH($DHS_optipons['owner_account_name'], $DHS_optipons['owner_account_key']);
                  $api->__cmd('announcement_list-list_lists');
                  $api_responce_lists = $api->__request()->getAll();
                  $domain = $api_responce_lists[0]->domain;
                  $api->__cmd('announcement_list-post_announcement');
                  $api->__params(array(
                          'listname'      => urlencode($DHS_optipons['anouncement_list']),
                          'domain'        => $domain,
                          'subject'       => urlencode($mail_subject),
                          'message'       => $message,
                          'charset'       => 'UTF-8',
                          'type'          => 'html'
                  ));
                  $result = $api->__request_post()->getAll();

                  if ($result == 'success' || $result == 'posted') {
                      $bulkmail_error = 'no_error';
                  } else {
                      $bulkmail_error = $result;
                  }
            }
          /*
            echo '<pre>';
            print_r($result);
            echo '</pre>';
            echo $bulkmail_error; die;
        */
            if ($bulkmail_error == 'no_error'){
                  $mail_status = 'sent';
            } else {
                  $mail_status = 'unsent';
            }

            if (!$post['mail_id']){
                  $mail_query  = "INSERT INTO " . $wpdb->prefix . "dhs_mails
                            (html_source, mail_subject, mail_content, status, created_at) " .
                            "VALUES ('" . $wpdb->escape($message) . "', '" . $wpdb->escape($mail_subject) . "', '" . $wpdb->escape($mail_content) . "','" . $mail_status . "', now())";
            } else {
                  $mail_query = "UPDATE " . $wpdb->prefix . "dhs_mails SET html_source='".$wpdb->escape($message)."', mail_subject='".$wpdb->escape($mail_subject)."', mail_content='".$wpdb->escape($mail_content)."', status='".$mail_status."', modified_at=now() WHERE id=".$wpdb->escape($post['mail_id']);
            }
            $results = $wpdb->query( $mail_query );


/*            require('mailer/htmlMimeMail.php');
              //$to = explode(',', $post['bulk_emails']);
              $to = $post['bulk_emails'];
              $subject = $post['letter_subject'];
              $message = $post['content'];

            ob_start();
            get_header();
            $msg_header = ob_get_contents();
            ob_end_clean();

            ob_start();
            get_footer();
            $msg_footer = ob_get_contents();
            ob_end_clean();

            $message = $msg_header . '<div id="content" class="narrowcolumn">' . $message . '</div>' . $msg_footer;

            //  $headers = 'From: '.$post['from']."\r\n".'Reply-To: '.$post['from'];

            $mail_1 = new htmlMimeMail();
            $mail_1->setFrom($post['from']);
            $mail_1->setSubject($subject);
            $mail_1->setHTMLCharset('UTF-8');
            $mail_1->setHeadCharset('UTF-8');
            $mail_1->setHTML($message, strip_tags($message));

            foreach($to as $adress) {
                $adress = array($adress);
                if (!$mail_1->send($adress)) {
                    $bulkmail_error = 'error_sent';
                } else {
                    $bulkmail_error = 'no_error';
                }
            }

              
/*              if (@mail($to,$subject,$message,$headers)){
                    $bulkmail_error = 'no_error';
              } else {
                    $bulkmail_error = 'error_sent';
              }
*/
/*        } else {
              $bulkmail_error = 'no_address';    
        }
*/
   }
 
   function DHS_backend_addSubscriber($api, $listname, $_POST, &$error, &$api_responce) {
  //subscribe request
        $email    = $_POST['email'];
        $subscriber_name = 'SubscriberName';
        
        if (ereg('.*@.*\..*', $email) && $subscriber_name){ 
              $api->__cmd('announcement_list-list_lists');
              $api_responce_lists = $api->__request()->getAll();
              
              foreach ($api_responce_lists as $key=>$value) {
                    if ($value->listname == $listname)
                          $request_domain = $value->domain; 	
              }
        
              if ($request_domain){
                    $api->__cmd('announcement_list-add_subscriber');
                    $api->__params(array('listname' => $listname,
                              				   'domain'   => $request_domain,
                              				   'email'    => $email,
                              				   'name'     => $subscriber_name));
                   $api_responce = $api->__request()->getAll();
             } else {
                   $error = 'no_such_domain';
             }

        } else {
              $error = 'bad_email_entry';
        }
  
  } 
 
  function DHS_backend_widget_control(){
        $DHS_optipons = get_option('DHS_widget_settings');
        if(!is_array($DHS_optipons)){
              $DHS_optipons['frontend_heading']   = 'Dreamhost Subscribes';
              $DHS_optipons['owner_account_name'] = 'apitest@dreamhost.com';
              $DHS_optipons['owner_account_key']  = '6SHU5P2HLDAYECUM';
              $DHS_optipons['anouncement_list']   = 'list_name';      
        } 

        if($_POST['DHSW-Submit']){
              $DHS_optipons['frontend_heading']   = htmlspecialchars($_POST['DHSW-Title']);
              
              update_option("DHS_widget_settings", $DHS_optipons);
        }
      
      ?>
      <p>
      <label for="WPGL-Title">Heading caption:</label><br />
      <input type="text" id="DHSW-Title" name="DHSW-Title" value="<?php echo $DHS_optipons['frontend_heading'];?>" /><br />
      <input type="hidden" id="DHSW-Submit" name="DHSW-Submit" value="1" /><br />
      </p>
      <?php
        /*
        $DHS_optipons = get_option('DHS_widget_settings');
        if(!is_array($DHS_optipons)){
              $DHS_optipons['frontend_heading']   = 'Dreamhost Subscribes';
              $DHS_optipons['owner_account_name'] = 'apitest@dreamhost.com';
              $DHS_optipons['owner_account_key']  = '6SHU5P2HLDAYECUM';
              $DHS_optipons['anouncement_list']   = 'list_name';      
        } 

        if($_POST['DHSW-Submit']){
              $DHS_optipons['frontend_heading']   = htmlspecialchars($_POST['DHSW-Title']);
              $DHS_optipons['owner_account_name'] = htmlspecialchars($_POST['DHSW-account-name']);
              $DHS_optipons['owner_account_key']  = htmlspecialchars($_POST['DHSW-account-key']);
              $DHS_optipons['anouncement_list']   = htmlspecialchars($_POST['DHSW-list-name']);;
              
              update_option("DHS_widget_settings", $DHS_optipons);
        }
        
?>
        <p>
      		<label for="WPGL-Title">Heading caption:</label><br />
      		<input type="text" id="DHSW-Title" name="DHSW-Title" value="<?php echo $DHS_optipons['frontend_heading'];?>" /><br />
      		<label for="DHSW-account-name">Dreamhost account name:</label><br />
      		<input type="text" id="DHSW-account-name" name="DHSW-account-name" value="<?php echo $DHS_optipons['owner_account_name'];?>" /><br />
      		<label for="DHSW-account-key">Dreamhost account key:</label><br />
      		<input type="text" id="DHSW-account-key" name="DHSW-account-key" value="<?php echo $DHS_optipons['owner_account_key'];?>" /><br />      		
      		<label for="DHSW-list-name">Account listname:</label><br />      		
            <select name="DHSW-list-name" style="width: 150px;">
            <?php
            $api = new Api_DH($DHS_optipons['owner_account_name'], $DHS_optipons['owner_account_key']); 
            $api->__cmd('announcement_list-list_lists');
            $api_responce_lists = $api->__request()->getAll();
            foreach ($api_responce_lists as $key=>$value) {
                $select = ($value->listname == $DHS_optipons['anouncement_list'])?' selected="selected"':'';
                echo '<option value="'.$value->listname.'">'.$value->listname.'</option>'."\n";
            }
            ?>
            </select><br />
            
            
		      <input type="hidden" id="DHSW-Submit" name="DHSW-Submit" value="1" />
        </p>
<?php        */
  }

  
  
function DHS_sendmessage () {
    global $wpdb;

    $bulkmail_error = '';
    $DHS_optipons = getDHSoptions();

// install checking
    if ($DHS_optipons['owner_account_key']){
          if (isset($_POST['send_mail'])) {
              DHS_send_bulk_mail($_POST, $bulkmail_error);
          }

// get mail contents if selected
    $mail_id = $wpdb->escape($_GET['mail_id']);
    if ($mail_id){
          $mail_contents = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."dhs_mails WHERE id = ".$mail_id);
    }

          require('templates/subscribers_send_message.tpl.php');
    } else {
          require('templates/subscribers_install.tpl.php');
    }
}

function DHS_configure() {
        $DHS_optipons = get_option('DHS_widget_settings');
        if(!is_array($DHS_optipons)){
              $DHS_optipons['frontend_heading']   = 'Dreamhost Subscribes';
              $DHS_optipons['owner_account_name'] = 'apitest@dreamhost.com';
              $DHS_optipons['owner_account_key']  = '6SHU5P2HLDAYECUM';
              $DHS_optipons['anouncement_list']   = 'list_name';
              $DHS_optipons['subs_perpage']       = 10;
        } 

        if($_POST['DHSW-Submit']){
              $DHS_optipons['frontend_heading']   = htmlspecialchars($_POST['DHSW-Title']);
              $DHS_optipons['owner_account_name'] = htmlspecialchars($_POST['DHSW-account-name']);
              $DHS_optipons['owner_account_key']  = htmlspecialchars($_POST['DHSW-account-key']);
              $DHS_optipons['anouncement_list']   = htmlspecialchars($_POST['DHSW-list-name']);
              $DHS_optipons['subs_perpage']       = intval($_POST['DHSW-subs_perpage']);
              
              update_option("DHS_widget_settings", $DHS_optipons);
        }
        
?>
 <div class="wrap">
 <h2>Dreamhost subscribers Configure.</h2>
 <form action="<?php echo $_SERVER['PHP_SELF'] ?>?page=dreamhost_configure" method="post">
         <p>
              <label for="WPGL-Title">Heading caption:</label><br />
              <input type="text" id="DHSW-Title" name="DHSW-Title" value="<?php echo $DHS_optipons['frontend_heading'];?>" /><br />
              <label for="DHSW-account-name">Dreamhost account name:</label><br />
              <input type="text" id="DHSW-account-name" name="DHSW-account-name" value="<?php echo $DHS_optipons['owner_account_name'];?>" /><br />
              <label for="DHSW-account-key">Dreamhost account key:</label><br />
              <input type="text" id="DHSW-account-key" name="DHSW-account-key" value="<?php echo $DHS_optipons['owner_account_key'];?>" /><br />              
              <label for="DHSW-list-name">Account listname:</label><br />              
            <select name="DHSW-list-name" style="width: 150px;">
            <?php
            $api = new Api_DH($DHS_optipons['owner_account_name'], $DHS_optipons['owner_account_key']); 
            $api->__cmd('announcement_list-list_lists');
            $api_responce_lists = $api->__request()->getAll();
            foreach ($api_responce_lists as $key=>$value) {
                $select = ($value->listname == $DHS_optipons['anouncement_list'])?' selected="selected"':'';
                echo '<option value="'.$value->listname.'">'.$value->listname.'</option>'."\n";
            }
            ?>
            </select><br />
              <label for="DHSW-account-key">Subscribers per page:</label><br />
              <input type="text" id="DHSW-subs_perpage" name="DHSW-subs_perpage" value="<?php echo $DHS_optipons['subs_perpage'];?>" /><br /><br />
            
            
              <input type="submit" id="DHSW-Submit" name="DHSW-Submit" value="Save" class="button-secondary action" />
        </p>
 </form>
 </div>
<?php    
}

function generate_mht($html) {
    // get the CSS
    $matches = array();
    $res = preg_match_all('/<link.*?rel="stylesheet".*?href="([^"]*?)"[^<>]*?>/i',$html,$matches);
    $css = array();
    $site_url = get_option('siteurl');
    $css_url = '';
    if ($res != false) {
        $css_url = str_replace($site_url.'/','',$matches[1][0]);
        $css_url = substr($css_url,0,strrpos($css_url,'/')+1);
        foreach ($matches[1] as $i) {
            $url = (strpos($i,'http://')!==false)? '':$site_url.'/';
            $url = $url.$i;
            $tmp = file_get_contents($url);
            $css[] = "----------e838f15a9226cee74099c5f2f85cdd22\r\nContent-Disposition: inline; filename=style.css\r\nContent-Type: text/css; charset=UTF-8; name=style.css\r\nContent-Location: {$i}\r\nContent-Transfer-Encoding: 8bit\n\n".$tmp."\r\n\r\n";
        }
    }
    //echo $css_url;exit;
    // get the images
/*    $res = preg_match_all('/<img.*?src="([^"]*?)"[^<>]*?>/i',$html,$matches);*/
    $_html = $html.implode(' ', $css);
    $res = preg_match_all('/url\(["|\']+([^"\']*?)["|\']+\)/i',$_html,$matches);
    $img = array();
    if ($res != false) {
        foreach ($matches[1] as $i) {
            $url = (strpos($i,'http://')!==false)? '':$site_url.'/';
            if (!is_http_file($url.$i)) { $url .= $css_url; }
            if (!is_http_file($url.$i)) { continue; }
            $url = $url.$i;
            //echo $url."<br /><br />\n";
            $tmp = file_get_contents($url);
            $ext = substr($i,strrpos($i,'.')+1,5);
            $file = substr($i,strrpos($i,'/')+1,strlen($i)-(strrpos($i,'/')+1));
            $img[] = "----------e838f15a9226cee74099c5f2f85cdd22\r\nContent-Disposition: inline; filename={$file}\r\nContent-Type: image/{$ext}; name={$file}\r\nContent-Location: {$i}\r\nContent-Transfer-Encoding: Base64\r\n\r\n".base64_encode($tmp)."\r\n\r\n";
        }
    }
    $res = preg_match_all('/<img.*?src="([^"]*?)"[^<>]*?>/i',$_html,$matches);
    if ($res != false) {
        foreach ($matches[1] as $i) {
            $url = (strpos($i,'http://')!==false)? '':$site_url.'/';
            if (!is_http_file($url.$i)) { $url .= $css_url; }
            if (!is_http_file($url.$i)) { continue; }
            $url = $url.$i;
            $tmp = file_get_contents($url);
            $ext = substr($i,strrpos($i,'.')+1,5);
            $file = substr($i,strrpos($i,'/')+1,strlen($i)-(strrpos($i,'/')+1));
            $img[] = "----------e838f15a9226cee74099c5f2f85cdd22\r\nContent-Disposition: inline; filename={$file}\r\nContent-Type: image/{$ext}; name={$file}\r\nContent-Location: {$i}\r\nContent-Transfer-Encoding: Base64\r\n\r\n".base64_encode($tmp)."\r\n\r\n";
        }
    }
    $ret = "Content-Type: multipart/related;\r\n boundary=\"----------e838f15a9226cee74099c5f2f85cdd22\"\r\nContent-Location: {$site_url}\r\rSubject: =?utf-8?Q?test?=\r\nMIME-Version: 1.0\r\n\r\n" . 
    "----------e838f15a9226cee74099c5f2f85cdd22\r\nContent-Disposition: inline; filename=default.htm\r\nContent-Type: text/html; charset=UTF-8; name=default.htm\r\nContent-Location: {$site_url}/\r\nContent-Transfer-Encoding: 8bit\r\n" . 
    $html . "\r\n\r\n" . implode('', $css) . implode('', $img);
    return $ret;
}

function is_http_file($file) {
    $fp = @fopen($file, 'r');
    if ($fp) {
        fclose($fp);
       return true;
    }
    return false;
}
?>