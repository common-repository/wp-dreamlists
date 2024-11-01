<?php
  /**
   *  PHP 5 required
   *
   *        */
  class splitResults {

        public $page_name = 'split_page';
        public $get_data  = NULL;
        
        function __construct($sql_query, $max_rows, $get, $count_key = '*') {
              global $wpdb;

              $page_name = $this->page_name;
        
              $this->sql_query = $sql_query;
              $this->get_data  = $get;
              $this->current_page = $get[$page_name];
          
              if ($this->current_page){
                    $this->current_page_number = $this->current_page;
              } else {
                    $this->current_page_number = 1;
              }
               
              $this->number_of_rows_per_page = $max_rows;
          
              $count_query = mysql_query($sql_query) or die('Slitter was stoped:'.mysql_error().'<br />'.$sql_query);
              $this->number_of_rows = mysql_num_rows($count_query);
            
              $this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);
          
              if ($this->current_page_number > $this->number_of_pages) {
                    $this->current_page_number = $this->number_of_pages;
              }
          
              $offset = $this->number_of_rows_per_page * ($this->current_page_number - 1);

              if ($offset >= 0){
                    $this->sql_query .= " LIMIT " . $offset . ", " . $this->number_of_rows_per_page;
              }
              //  LIMIT 14, 4 - то есть показывать результат с 15-той по 19-ю запись. 
        }

        function display_links($prev_string, $next_string) {
              $page_name = $this->page;
              $request_string = ereg_replace('\&'.$this->page_name.'\=(.*)$', '',  $_SERVER['REQUEST_URI']);

              if ($this->number_of_rows > $this->number_of_rows_per_page){

                    $prev_link = $next_link = $pages = $result_string = NULL;
                    
                    if (!$this->current_page) $this->current_page = 1; 
                    
                // prev & nex6t links  

                    if ($this->current_page > 1){
                          $prev_pagenum = $this->current_page - 1;
                          $prev_link = '<a class="splitPrev" style="margin-right: 10px;" href="'.$request_string.'&'.$this->page_name.'='.$prev_pagenum.'">'.$prev_string.'</a>';
                    }
                    
                    if ($this->current_page < $this->number_of_pages){    
                          $next_pagenum = $this->current_page + 1;
                          $next_link = '<a class="splitNext" style="margin-left: 10px;" href="'.$request_string.'&'.$this->page_name.'='.$next_pagenum.'">'.$next_string.'</a>';
                    }      
                    
                // links page numbers
                    $links_count = '10';
                    
                    $start_pagelink = ($links_count * (int)(($this->current_page-1)/$links_count));                    
                    if (($start_pagelink - $links_count) >= 0)
                          $dots_before = '...';
                                        
                    if (($this->number_of_pages - $start_pagelink) >= $links_count){
                          $iterator_orientir = $links_count+$start_pagelink;
                          $dots_after = '...';
                    } else {
                          $iterator_orientir = $this->number_of_pages-1;
                    } 
                    (($start_pagelink) > 0)?$first_link_dislaying = $start_pagelink:$first_link_dislaying = $start_pagelink;
                    
                    for ($i=$start_pagelink-1; $i <= $iterator_orientir; $i++) {
                         if ($i >= 0 && ($this->current_page != ($i+1)))
                              $pages .= '<a class="page-numbers" href="'.$request_string.'&'.$this->page_name.'='.($i+1).'">'.($i+1).'</a>';
                         else if($i >= 0)
                              $pages .= '<span class="page-numbers current">'.($i+1).'</span>';

                    }  
      
                    
                    $result_string = $prev_link.$dots_before.$pages.$dots_after.$next_link;
                      
                    return $result_string;
              } else {
                    return false;
              }
        }
  }
?>
