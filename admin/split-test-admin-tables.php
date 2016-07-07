<?php
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Easy_SplitTest_Tab_Table extends WP_List_Table {
	var $flag = 'all';

	function _construct() {
		global $status, $page;
		parent::__construct( array(
			'singular'  => 'splittest',
			'plural'    => 'splittests',
			'ajax'      => false
		));
	}
	
	function column_default($item, $column_name){
		return print_r($item,true); //Show the whole array for troubleshooting purposes
	}
	function column_id($item){
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&splittest=%s">Edit</a>',$_REQUEST['page'],'pending',$item->id),
			'delete' => sprintf('<a href="?page=%s&action=%s&splittest=%s">Delete</a>',$_REQUEST['page'],'delete',$item->id),
		);
		return sprintf('%1$s%2$s',$item->post_id,$this->row_actions($actions));
	}
	function column_date_time($item) {
		$date= $item->date_time;
		return $date;
	}
	function column_splittest_name($item) {
		if($item->splittest_status):			
			$status = "pending";
			$name_status = __('Pending Experiment',EASY_SPLITTEST_TAB_DOMAIN);
		else :	
			$status = "approve";
			$name_status = __('Approve Experiment',EASY_SPLITTEST_TAB_DOMAIN);
		endif;
		$actions = array(			
			'delete' => sprintf('<a href="?page=%s&action=%s&splittest=%s">Delete</a>',$_REQUEST['page'],'delete',$item->id)
		);
		
		return sprintf('%1$s%2$s',$item->splittest_name,$this->row_actions($actions));
	}
	
	function column_splittest_title($item) {
			
			$title_page = get_the_title($item->post_id); 
			$before="<span>";
			$after="</span>";
			$title = edit_post_link( $title_page, $before, $after, $item->post_id );
		
		$permalink = get_permalink($item->post_id);
		
		return $title . '<br >' .$permalink;
	}
	
	function column_splittest_code($item) {
		//$output = Easy_SplitTest_Tab::splittest_nice_output($item->splittest_code);
		$output =  stripslashes($item->splittest_code);
		$output = '<textarea readonly>' . $output . '</textarea>';
		return $output;
	}
	
	
	function column_splittest_status($item) {			
		if($item->splittest_status):			
			$status_action = "pending";
			$status_name ='<img src="'.EASY_SPLITTEST_TAB_URL.'/img/active.png" width="16" /> '.__(' Active',EASY_SPLITTEST_TAB_DOMAIN);
			$name_status = __(' Pending Experiment',EASY_SPLITTEST_TAB_DOMAIN);
		else :	
			$status_action = "approve";
			$status_name ='<img src="'.EASY_SPLITTEST_TAB_URL.'/img/inactive.png" width="16" /> '.__(' Inactive',EASY_SPLITTEST_TAB_DOMAIN);
			$name_status = __(' Approve Experiment',EASY_SPLITTEST_TAB_DOMAIN);
		endif;
		$actions = array(
			'edit' => sprintf('<a href="?page=%s&action=%s&splittest=%s">%s</a>',$_REQUEST['page'],$status_action,$item->id,$name_status)
			
		);
		
		return sprintf('%1$s%2$s',$status_name,$this->row_actions($actions));
	}
	
	
	
	function column_variant_id($item) {				
		$data = $item->variant_id;		
		$data = maybe_unserialize($data);
		
		$return = "";

		if($data):
		$i = 1;
			foreach ($data as $id):
			$title_page = get_the_title($id); 
			$before="$i: <span>";
			$after="</span><br /><code>".get_permalink($id)."</code><hr>";
			
			
			$title = edit_post_link( $title_page, $before, $after, $id );
			
			
			
				$i++;
			endforeach;		
		endif;
		
		
	} 
	
	function column_cb($item){
		return sprintf('<input type="checkbox" name="splittest[]" value="%1$s" />',$item->id);
	}

	function get_columns() {
		 $columns = array(
			'cb'        		  => '<input type="checkbox" />',			
			'splittest_title'    => __('Original page to improve',EASY_SPLITTEST_TAB_DOMAIN),
			'splittest_status'    => __('Status',EASY_SPLITTEST_TAB_DOMAIN),			
			'splittest_name'   => __('Experiment Name',EASY_SPLITTEST_TAB_DOMAIN),			
			'splittest_code'     => __('Experiment Code',EASY_SPLITTEST_TAB_DOMAIN),
			'variant_id'         => __('Variation page ',EASY_SPLITTEST_TAB_DOMAIN),			
			'date_time'       => 'Date',	
		);
		
		
		
	return	$columns;
	}
	
	function get_sortable_columns() {
		return $sortable = array(
			//'id'              => array('id',false),
			'date_time'       => array('date_time',false),
			//'splittest_name'   => array('splittest_name',false),
			//'splittest_email'  => array('splittest_email',false),
			//'splittest_title'    => array('splittest_title',false),
			//'splittest_rating'   => array('splittest_rating',false),
			//'splittest_text'     => array('splittest_text',false),
			'splittest_status'   => array('splittest_status',false),
			//'splittest_ip'     => array('splittest_ip',false),
			'post_id'         => array('post_id',false),
			//'splittest_category' => array('splittest_category',false)
		);
	}
	
	function get_bulk_actions() {
		$actions = array();
		
		$actions['pending'] =  __('Pending Experiments',EASY_SPLITTEST_TAB_DOMAIN);
		$actions['approve'] = __('Approve Experiments',EASY_SPLITTEST_TAB_DOMAIN);
		$actions['delete'] = 'Delete';
		return $actions;
	}
	
	function process_bulk_action() {
		global $wpdb, $Easy_SplitTest_Tab;
		$output = '';
		if (isset($_REQUEST['splittest'])) {
			$ids = is_array($_REQUEST['splittest']) ? $_REQUEST['splittest'] : array($_REQUEST['splittest']);
			$this_action = '';
			if ('approve' === $this->current_action()) {
				$this_action = 'approve';
				$action_alert_type = 'approved';
			} else if ('pending' === $this->current_action()) {
				$this_action = 'pending';
				$action_alert_type = 'set to pending';
			} else if ('delete' === $this->current_action()) {
				$this_action = 'delete';
				$action_alert_type = 'deleted';
			}
			if (!empty($ids)) {
				foreach ($ids as $id) {
					$output .= $id . ' ';
					switch ($this_action) {
						case 'approve':
							$wpdb->update($Easy_SplitTest_Tab->sqltable, array('splittest_status' => '1'), array('id' => $id));
							break;
						case 'pending':
							$wpdb->update($Easy_SplitTest_Tab->sqltable, array('splittest_status' => '0'), array('id' => $id));
							break;
						case 'delete':
							$wpdb->query("DELETE FROM $Easy_SplitTest_Tab->sqltable WHERE id=\"$id\"");
							break;
					}
				}
				if (count($ids) == 1) {
					$action_alert = '1 splittest has been successfully ' . $action_alert_type . '.';
				} else {
					$action_alert = count($ids) . ' splittests have been successfully ' . $action_alert_type . '.';
				}
				echo '<div class="updated" style="padding: 10px;">' . $action_alert . '</div>';
			}
		}
		if (isset($_REQUEST['submit_split_test'])) {
			if ($_POST['submit_split_test'] == 'OK') {
			$Easy_SplitTest_Tab->split_test_insert_db();
			}
		}
		
	}
	
	function prepare_items($flag = 'pending') {
		$this->flag = $flag;
		global $wpdb, $Easy_SplitTest_Tab;
		//$page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
		$per_page = 10;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();
		//$whereStatement = ($this->flag == 'approved') ? ' WHERE splittest_status="1"' : ' WHERE splittest_status="0"';
		$whereStatement = '';
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
		$orderStatement = ' ORDER BY ' . $orderby . ' ' . $order;
		$data = $wpdb->get_results("SELECT * FROM $Easy_SplitTest_Tab->sqltable" . $whereStatement . $orderStatement);
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
		$this->items = $data;
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items/$per_page)
		));
	}
}
?>