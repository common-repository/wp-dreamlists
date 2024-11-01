<?php
/**
 *   functions that take care about displaying frontend widget
 *
 *   */
  require('api/dreamhost_api.class.php');
  
  function DHS_frontend_widget($args) {
  // First we grab the Wordpress theme args, which we
  // use to display the widget
        extract($args);
  
  // get personal settings     
        $DHS_optipons = get_option('DHS_widget_settings');
        if(!is_array($DHS_optipons)){
              $DHS_optipons['frontend_heading'] = 'Dreamhost Subscribers';
              $DHS_optipons['owner_account_name'] = 'email@address.com';
              $DHS_optipons['owner_account_key']  = '16_DIGIT_APIKEY';
              $DHS_optipons['anouncement_list']   = 'list_name'; 
        } 
	      
        $api = new Api_DH($DHS_optipons['owner_account_name'], $DHS_optipons['owner_account_key']);                
	
  //subscribe request

        $email    = $_POST['email'];
        $listname = $DHS_optipons['anouncement_list'];
        $subscriber_name = /*$_POST['subscriber_name']*/'SubscriberName';
        
        if (ereg('.*@.*\..*', $email) && $subscriber_name){
              $api->__cmd('announcement_list-list_lists');
              
              if (is_array($api_responce_lists = $api->__request()->getAll())){
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
             }
        } else if($email && !ereg('.*@.*\..*', $email)) {
              $error = 'bad_email_entry';
        }
  
  
  // template prepare    announcement_list-list_lists
        $heading = $DHS_optipons['frontend_heading'];
    //    echo htmlspecialchars($api_responce);
        if (!is_array($api_responce)){
                 if(ereg('already_subscribed', $api_responce)) {
                  	$api_responce_msg = '<font color="green">You are already subscribed!</font>'; 
              } else if(ereg('sent_opt_in_email', $api_responce)){
                    $api_responce_msg = '<font color="green">You have been succesfully subscribed</font>';
              } else if (ereg('email_requested_to_be_added_in_last_two_days_already', $api_responce)){
                    $api_responce_msg = '<font color="gray">Your request is in process. Please check your e-mail.</font>';
              }    	
        }
        
        require('templates/frontend.tpl.php');
  } 
 
?>