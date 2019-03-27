<?php 
class saasappoint_schedule{
	public $conn;
	public $id;
	public $business_id;
	public $week_id;
	public $weekday_id;
	public $starttime;
	public $endtime;
	public $offday;
	public $saasappoint_schedule = 'saasappoint_schedule';
	
	/* Function to get schedule */
	public function get_schedule(){
		$query = "select * from `".$this->saasappoint_schedule."` where business_id='".$this->business_id."'";
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
	
	/* Function to change offday status */
	public function change_offday_status(){
		$query = "update `".$this->saasappoint_schedule."` set `offday`='".$this->offday."' where `id`='".$this->id."'";
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
	
	/* Function to change schedule start time */
	public function update_schedule_starttime(){
		$query = "update `".$this->saasappoint_schedule."` set `starttime`='".$this->starttime."' where `id`='".$this->id."'";
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
	
	/* Function to change schedule end time */
	public function update_schedule_endtime(){
		$query = "update `".$this->saasappoint_schedule."` set `endtime`='".$this->endtime."' where `id`='".$this->id."'";
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
	
	/* Function to generate slot dropdown options */
	public function generate_slot_dropdown_options($time_interval, $time_format, $schedule_time){
		$options = '';
		$min = 0;
		$t = 1;
		$i = 1;
		while ($min < 1440) {
			if ($min == 1440) {
				$timeValue = date('G:i', mktime(0, $min - 1, 0, 1, 1, 2015));
			} else {
				$timeValue = date('G:i', mktime(0, $min, 0, 1, 1, 2015));
			}
			$timetoprint = date('G:i', mktime(0, $min, 0, 1, 1, 2014));

			$selected = '';
			if ($schedule_time == date("H:i:s", strtotime($timeValue))) {
				$t= 10;
				$selected = "selected";
			}
			if($t==1) {
				if ("10:00:00" == date("H:i:s", strtotime($timeValue))) {
					$selected = "selected";
				}
			}
			if ($time_format == 24) {
				$slot = date("H:i", strtotime($timetoprint));
			} else {
				$slot = date("h:i A", strtotime($timetoprint));
			}
			$slotvalue = date("H:i:s", strtotime($timeValue));
			$options .= '<option '.$selected.' data-position="'.$i.'" value="'.$slotvalue.'">'.$slot.'</option>';
			$min = $min + $time_interval;
			$i++;
		}
		return $options;
	}
	
	/** Function to add default schedule while registering as administrator **/
	public function add_default_schedule(){
		$query = "INSERT INTO `".$this->saasappoint_schedule."` (`id`, `business_id`, `week_id`, `weekday_id`, `starttime`, `endtime`, `offday`) VALUES
		(NULL, '".$this->business_id."', 1, 1, '10:00:00', '20:00:00', 'N'),
		(NULL, '".$this->business_id."', 1, 2, '10:00:00', '20:00:00', 'N'),
		(NULL, '".$this->business_id."', 1, 3, '10:00:00', '20:00:00', 'N'),
		(NULL, '".$this->business_id."', 1, 4, '10:00:00', '20:00:00', 'N'),
		(NULL, '".$this->business_id."', 1, 5, '10:00:00', '20:00:00', 'N'),
		(NULL, '".$this->business_id."', 1, 6, '10:00:00', '20:00:00', 'Y'),
		(NULL, '".$this->business_id."', 1, 7, '10:00:00', '20:00:00', 'Y')";
		$result=mysqli_query($this->conn,$query);
		return $result;
	}
}
?>