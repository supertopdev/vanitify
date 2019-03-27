<?php 
class saasappoint_slots{
	public $conn;
	public $business_id;
	public $saasappoint_schedule = 'saasappoint_schedule';
	public $saasappoint_bookings = 'saasappoint_bookings';
	public $saasappoint_block_off = 'saasappoint_block_off';
	
	/* Function to get already booked slots */
	public function get_already_booked_slots($selected_date,$cur_time_interval){
		$return_arr = array();
		$query="select `booking_datetime`, `booking_end_datetime` from `".$this->saasappoint_bookings."` where CAST(`booking_datetime` as date)='".$selected_date."' and (`booking_status`='pending' OR `booking_status`='confirmed' OR `booking_status`='rescheduled_by_you' OR `booking_status`='rescheduled_by_customer') and `business_id`='".$this->business_id."' group by `order_id`,`booking_datetime`, `booking_end_datetime`";
		$value=mysqli_query($this->conn,$query);
		while($row=mysqli_fetch_array($value)){
			$newarr = array();
			$newarr["start_time"] = strtotime($row['booking_datetime']);
			$newarr["end_time"] = strtotime($row['booking_end_datetime']);
			array_push($return_arr, $newarr);
		}
		return $return_arr;
	}
	
	/* Function to get block off */
	public function get_block_off($selected_date){
		$return_arr = array();
		$query="select * from `".$this->saasappoint_block_off."` where '".$selected_date."' between `from_date` and `to_date` and `business_id`='".$this->business_id."' and `status`='Y'";
		$value=mysqli_query($this->conn,$query);
		if(mysqli_num_rows($value)>0){
			while($row=mysqli_fetch_array($value)){
			    if ($row['blockoff_type'] == "fullday") {
					$arr = array();
					$arr["start_time"] = "00:00:00";
					$arr["end_time"] = "23:59:59";
				}else{
					$arr = array();
					$arr["start_time"] = $row['from_time'];
					$arr["end_time"] = $row['to_time'];
				}
				array_push($return_arr, $arr);
			}
		}
		return $return_arr;
	}
	
	/* Function to get day start time and day end time */
	public function get_time_slots($day_id, $week_id){
		$dayid=$day_id;
		$weekid=$week_id;
        $results = array();
		$query="SELECT `starttime`,`endtime` FROM `".$this->saasappoint_schedule."` WHERE `weekday_id`='" .$dayid . "' AND `offday`='N' AND `week_id`='".$weekid."' and `business_id`='".$this->business_id."'";
		$result=mysqli_query($this->conn,$query);
		$value=mysqli_fetch_row($result);
		$results['daystart_time'] = $value[0];
		$results['dayend_time']   = $value[1];
        return $results;	
    }
	
	/* Function to generate slot dropdown options */
    public function generate_available_slots_dropdown($time_interval, $time_format, $start_date, $advance_bookingtime, $currDateTime_withTZ, $isEndTime = false){
		
        $day_slots = array();
        $week_id = 1;
    
		/* if calendar starting date is missing then it will take starting date to current date */
        if ($start_date == '') {
            $day_id = date('N', $currDateTime_withTZ);
			/*  add Date as heading of the day column */
            $day_slots['date'] = date('Y-m-d', $currDateTime_withTZ);
        } else {
            $day_id = date('N', strtotime($start_date));
			/* add Date as heading of the day column */
            $day_slots['date'] =date('Y-m-d', strtotime($start_date));
        }
        
        $available_slots = $this->get_time_slots($day_id, $week_id);
		
		/* calculating starting and end time of day into mintues */				
		if(isset($available_slots['daystart_time'],$available_slots['dayend_time'])){		
			$min_day_start_time        = (date('G', strtotime($available_slots['daystart_time'])) * 60) + date('i',strtotime($available_slots['daystart_time']));
			$min_day_end_time          = (date('G', strtotime($available_slots['dayend_time'])) * 60) + date('i',strtotime($available_slots['dayend_time']));
			
			$min_allow_advance='Y';
			$advance_minutes='N';
			if($advance_bookingtime>=1440){
				$advance_minutes='Y';
				$currdatestr = strtotime(date('Y-m-d H:i:s', $currDateTime_withTZ));					
				$withadncebooktime = strtotime("+$advance_bookingtime minutes", $currdatestr);
				$withadncebookdate = date('Y-m-d',strtotime("+$advance_bookingtime minutes", $currdatestr));
				$daystarttimeofdate = strtotime(date($withadncebookdate.' '.$available_slots['daystart_time']));
				$with_advance_time = date('H:i:s',$withadncebooktime);
				
				if(strtotime($start_date)>strtotime($withadncebookdate)){
					$with_advance_time = $available_slots['daystart_time'];
				}
				
				if(strtotime($start_date)>=strtotime($withadncebookdate)){
					if($withadncebooktime<$daystarttimeofdate){
						$min_day_start_time = (date('G', strtotime($available_slots['daystart_time'])) * 60) + date('i',strtotime($available_slots['daystart_time']));								
							$min_allow_advance='Y';					
					}else{
					
						$min_day_start_time = (date('G', strtotime($with_advance_time)) * 60) + date('i',strtotime($with_advance_time));						
						if($min_day_start_time%$time_interval!=0){
							$extraminsadd =  $time_interval-($min_day_start_time%$time_interval);
							$min_day_start_time = $min_day_start_time+$extraminsadd;
						}
					
						$min_allow_advance='Y';
					}
				}else{
					$min_allow_advance='N';
				}
			}
			
			$starting_min = $min_day_start_time;
			
			/* check if selected date is today  if yes calculate current time's min to avoid past booking */
			$today                     = false;
			$conditional_min_mins      = 0;
			
			if (strtotime($day_slots['date']) == strtotime(date('Y-m-d', $currDateTime_withTZ)) && $advance_minutes=='N') {
				$today                = true;
				/* total mins of current time */
			   $conditional_min_mins = date('G',strtotime(date('Y-m-d H:i:s', $currDateTime_withTZ))) * 60 + date('i',strtotime(date('Y-m-d H:i:s', $currDateTime_withTZ))) ;
			} else {
				$today = false;
			}
			
			
		   
		   /* add minimum advance booking mins with starting mins for slots */
			 if($advance_bookingtime<1440){
					$conditional_min_mins += $advance_bookingtime;
			}
			
					
			/* check already booked timeslots */
			$day_slots['booked'] = $this->get_already_booked_slots($start_date,$time_interval);
			$day_slots['block_off'] = $this->get_block_off($start_date);
			
			/* Converting time into slots based on given daystart time and dayend time */
			if ($available_slots['daystart_time'] != '' && $available_slots['dayend_time'] != '' && $min_allow_advance=='Y') {
				if($isEndTime){
					while ($starting_min <= $min_day_end_time) {
						if ($today) {
							if ($starting_min > $conditional_min_mins) {						
								$day_slots['slots'][] = date('G:i:s', mktime(0, $starting_min, 0, 1, 1, date('Y')));
							}
						} else {
							$day_slots['slots'][] = date('G:i:s', mktime(0, $starting_min, 0, 1, 1, date('Y')));
						}
						$starting_min = $starting_min + $time_interval;
					}
				}else{
					while ($starting_min < $min_day_end_time) {
						if ($today) {
							if ($starting_min > $conditional_min_mins) {						
								$day_slots['slots'][] = date('G:i:s', mktime(0, $starting_min, 0, 1, 1, date('Y')));
							}
						} else {
							$day_slots['slots'][] = date('G:i:s', mktime(0, $starting_min, 0, 1, 1, date('Y')));
						}
						$starting_min = $starting_min + $time_interval;
					}
				}
			} else {
				$day_slots['slots'] = array();
			}
		}
        return $day_slots;		
		
    }
}
?>