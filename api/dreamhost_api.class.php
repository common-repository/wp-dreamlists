<?php
 /** PHP 5   require 
  *  This class take cares about api request building
  *  
  *
  *     */
  class Api_DH
  {
        protected static $api_url = 'https://api.dreamhost.com/';
        private $user;
        private $key;
        
        public function __construct($username, $api_key){
              $this->username = $username;
              $this->api_key  = $api_key;
              
              $this->user = $username;
              $this->key = $api_key;
              $this->request_url = self::$api_url.'?username='.$username.'&key='.$api_key.'&format=json';
        }
        
        public function __cmd($command){
              $this->cmd = $command;
        }
        
        public function __request(&$api_responce = NULL){
              if ($this->cmd){ 
                    $this->url = $this->request_url.'&cmd='.$this->cmd.$this->params.'&unique_id='.uniqid();

                    $this->__parse_request();
                    
                    $this->params = NULL;
                    $this->cmd    = NULL;
                  
                    return $this;
              }
        }

        public function __request_post(&$api_responce = NULL) {
            if ($this->cmd) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, self::$api_url);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, 'username='.$this->user.'&key='.$this->key.'&format=json&cmd='.$this->cmd.$this->params.'&unique_id='.uniqid());
                $res = curl_exec($curl);
                curl_close($curl);
                $res = json_decode($res);
                $this->data = $res->data;
                $this->params = NULL;
                $this->cmd    = NULL;
                return $this;
            }
        }

        public function getKey($key_name){
              if ($this->data){
                    return $this->data[0]->$key_name;
              }
        }

        public function getResult(){
              return $this->result;
        }

        public function getAll(){
              return $this->data;
        }

        private function __parse_request()
        {
              $api_request = fopen($this->url, "rb");
              $api_responce = '';
              while (!feof($api_request)) {
                    $api_responce .= fread($api_request, 8192);
              }
              fclose($api_request);
              
              $parsed_responce = json_decode($api_responce);
              $this->data = $parsed_responce->data;
        }
        
        public function __params($params_array){
              if (count($params_array) > 0){
                    foreach ($params_array as $key=>$value){
                          $this->params .= '&'.$key.'='.$value; 
                    }
              }
        }
  }
 
?>