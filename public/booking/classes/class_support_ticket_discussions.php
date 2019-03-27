<?php 
class saasappoint_support_ticket_discussions{
	public $conn;
	public $id;
	public $ticket_id;
	public $replied_by_id;
	public $reply;
	public $replied_on;
	public $replied_by;
	public $read_status;
	public $saasappoint_support_ticket_discussions = 'saasappoint_support_ticket_discussions';
	public $saasappoint_superadmin_settings = 'saasappoint_superadmin_settings';
	public $saasappoint_customers = 'saasappoint_customers';
	
	/* Function to get all support tickets */
	public function get_all_support_ticket_replies(){
		$query = "select * from `".$this->saasappoint_support_ticket_discussions."` where `ticket_id`='".$this->ticket_id."' order by `id` ASC";
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
		
	/* Function to add support tickets discussion reply */
	public function add_ticket_discussion_reply(){
		$query = "INSERT INTO `".$this->saasappoint_support_ticket_discussions."`(`id`, `ticket_id`, `replied_by_id`, `reply`, `replied_on`, `replied_by`, `read_status`) VALUES (NULL, '".$this->ticket_id."', '".$this->replied_by_id."', '".$this->reply."', '".$this->replied_on."', '".$this->replied_by."', '".$this->read_status."')";
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
		
	/* Function to delete support tickets discussion reply */
	public function delete_ticket_discussion_reply(){
		$query = "delete from `".$this->saasappoint_support_ticket_discussions."` where `ticket_id`='".$this->ticket_id."'";
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
		
	/* Function to mark as read all support tickets discussion reply */
	public function markasread_all_support_ticket_reply(){
		if($this->replied_by == "admin"){
			$query = "update `".$this->saasappoint_support_ticket_discussions."` set `read_status`='R' where (`replied_by` = 'superadmin' or `replied_by` = 'customer') and `ticket_id`='".$this->ticket_id."'";
		}else{
			$query = "update `".$this->saasappoint_support_ticket_discussions."` set `read_status`='R' where `replied_by` = 'admin' and `ticket_id`='".$this->ticket_id."'";
		}
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
		
	/* Function to count all support tickets discussion reply */
	public function count_all_ticket_discussion_reply(){
		$query = "select `id` from `".$this->saasappoint_support_ticket_discussions."` where `ticket_id`='".$this->ticket_id."'";
		$result=mysqli_query($this->conn,$query);
		$value=mysqli_num_rows($result);
		return $value;
	}
		
	/* Function to count all support tickets discussion reply */
	public function count_all_unread_ticket_discussion_reply(){
		if($this->replied_by == "admin"){
			$query = "select `id` from `".$this->saasappoint_support_ticket_discussions."` where `read_status` = 'U' and (`replied_by` = 'superadmin' or `replied_by` = 'customer') and `ticket_id` = '".$this->ticket_id."'";
		}else{
			$query = "select `id` from `".$this->saasappoint_support_ticket_discussions."` where `read_status` = 'U' and `replied_by` = 'admin' and `ticket_id` = '".$this->ticket_id."'";
		}
		$result=mysqli_query($this->conn,$query);
		$value=mysqli_num_rows($result);
		return $value;
	}
		
	/* Function to get support ticket generated by name */
	public function get_support_ticket_replied_by_name(){
		if($this->replied_by == "customer"){
			$query = "select `firstname`, `lastname` from `".$this->saasappoint_customers."` where `id`='".$this->replied_by_id."'";
			$result=mysqli_query($this->conn,$query);
			$value=mysqli_fetch_assoc($result);
			return ucwords($value['firstname']." ".$value['lastname']);
		}else if($this->replied_by == "admin"){
			$query = "select `firstname`, `lastname` from `".$this->saasappoint_customers."` where `id`='".$this->replied_by_id."'";
			$result=mysqli_query($this->conn,$query);
			$value=mysqli_fetch_assoc($result);
			return ucwords($value['firstname']." ".$value['lastname']);
		}else{
			$query = "select `option_value` from `".$this->saasappoint_superadmin_settings."` where `option_name`='saasappoint_company_name'";
			$result=mysqli_query($this->conn,$query);
			$value=mysqli_fetch_assoc($result);
			return ucwords($value['option_value']);
		}
	}
	
}
?>