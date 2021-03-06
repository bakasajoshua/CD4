<?php
class pima_model extends MY_Model{
	public function expected_reporting_devices_pie($user_group_id,$user_filter_used,$from,$to){

		$user_delimiter =$this->sql_user_delimiter($user_group_id,$user_filter_used);

		$beg_year = $this->config->item("starting_year");
		$beg_date = "$beg_year-1-1";

		$sql_expected = "SELECT 
								COUNT(DISTINCT `facility_equipment_id`) AS `expected`
							FROM `v_facility_equipment_details`
							WHERE 1 
							AND `date_added` BETWEEN '$beg_date' AND '$to' 
							AND ((`date_removed` IS NULL) OR (`date_removed` IS NOT NULL AND `date_removed` > '$to') ) 
							$user_delimiter 
						";

		$sql_reported = "SELECT 
								COUNT(DISTINCT `facility_equipment_id`) AS `reported`
							FROM `v_tests_details`
							WHERE 1
							AND `result_date` BETWEEN '$from' AND '$to' 
							$user_delimiter 
						";
		$expected_res  	= R::getAll($sql_expected);			
		$reported_res  	= R::getAll($sql_reported);	

		$data["chart"][0]["name"] 		= "Devices Not Reported";
		$data["chart"][0]["y"] 			= (int) $expected_res[0]["expected"] - (int) $reported_res[0]["reported"];
		$data["chart"][0]["color"] 		= "#aa1919";
		$data["chart"][0]["sliced"] 	= false;
		$data["chart"][0]["selected"] 	= false;

		$data["chart"][1]["name"] 		= "Devices Reported";
		$data["chart"][1]["y"] 			= (int) $reported_res[0]["reported"];
		$data["chart"][1]["color"] 		= "#a4d53a";
		$data["chart"][1]["sliced"] 	= true;
		$data["chart"][1]["selected"] 	= true;

		return $data;

	}
	public function tests_table($from,$to,$user_group_id,$user_filter_used){

		$user_delimiter =$this->sql_user_delimiter($user_group_id,$user_filter_used);

		$sql 	= 	"SELECT 
							COUNT(*) AS `total`,
							SUM(CASE WHEN `patient_age_group_id`='3' AND `valid`= '1' AND `cd4_count` < 350 THEN 1 ELSE 0 END ) AS `failed`,
							SUM(CASE WHEN `patient_age_group_id`='3' AND `valid`= '1' AND`cd4_count` >= 350 THEN 1 ELSE 0 END ) AS `passed`,
							SUM(CASE WHEN `valid`= '0'    THEN 1 ELSE 0 END) AS `errors`,	
							SUM(CASE WHEN `valid`= '1'    THEN 1 ELSE 0 END) AS `valid`				
						FROM `v_tests_details`

						WHERE `result_date` BETWEEN '$from' AND '$to'
						AND `equipment_id` = '4' 
						$user_delimiter

					";
		$tests 	=	R::getAll($sql);

		$tests[0]["title"]= 'Tests';

		$failed =	$tests[0]["failed"];
		$passed =	$tests[0]["passed"];
		$total =	$tests[0]["total"];
		$errors =	$tests[0]["errors"];
		$valid =	$tests[0]["valid"];

		if($total>0){

			$row["title"]	= 'Percentages';
			$row["total"]	= null;
			$row["passed"]	= round(($passed/$total)*100,2)."%";
			$row["failed"]	= round(($failed/$total)*100,2)."%";	
			$row["errors"]	= round(($errors/$total)*100,2)."%";	
			$row["valid"]	= round(($valid/$total)*100,2)."%";	
		}else{
			$row["title"]	= 'Percentages';
			$row["total"]	= null;
			$row["passed"]	= "0 %";
			$row["failed"]	= "0 %";	
			$row["errors"]	= "0 %";	
			$row["valid"]	= "0 %";
		}

		$tests[1]	=	$row;

		return $tests;
	}
	public function errors_pie($user_group_id,$user_filter_used,$from,$to){

		$user_delimiter = $this->sql_user_delimiter($user_group_id,$user_filter_used);

		$sql 	=	 	"SELECT 
								COUNT(`error_id`) AS `num`,
								`error_code`,
								`error_detail`,
								`error_type_description`
							FROM `v_pima_error_details`

							WHERE 1
							AND `result_date` BETWEEN '$from' AND '$to' 
							$user_delimiter 

							GROUP BY `error_id`
						";
		$res  = R::getAll($sql);

		$data = array();

		foreach ($res as $key => $value) {
			
			$data["chart"][$key]["name"] 		=  	$value["error_detail"]."(".$value["error_code"].")";
			$data["chart"][$key]["y"] 			=  	(int) $value["num"];
			$data["chart"][$key]["sliced"]		=	false;
			$data["chart"][$key]["selected"]	=	false;

		}

		$data["chart"][0]["sliced"]		=	true;
		$data["chart"][0]["selected"]	=	true;

		return $data;

	}

	public function expected_reporting_devices($user_group_id,$user_filter_used,$year){

		//$categories 	=	$this->get_yearmonth_categories($from,$to);

		$user_delimiter =$this->sql_user_delimiter($user_group_id,$user_filter_used);

		$data["chart"][0]["name"] 	= 	"Expected Reporting Devices";
		$data["chart"][0]["data"] 	= 	$this->expected_reporting_dev_array($user_delimiter,$year);

		$data["chart"][1]["name"] 	= 	"Reported Devices";
		$data["chart"][1]["color"] 	= 	"#a4d53a";
		$data["chart"][1]["data"] 	= 	$this->reported_devices($user_delimiter,$year);

		return $data;

	}

	private function expected_reporting_dev_array($user_delimiter, $year){
		//error_reporting(0);

		$sql_added	=	"SELECT
								`t1`.`date_added` as `rank_date`,
								`t1`.`yearmonth`,
								`t1`.`month`, 
								`t1`.`rolledout`, 
								SUM(`t2`.`rolledout`) AS `cumulative`
							FROM
								(SELECT 	CONCAT(YEAR(`date_added`),'-',MONTH(`date_added`)) AS `yearmonth`,
                                 			`date_added`, 
											MONTH(`date_added`) AS `month`,
											COUNT(*) AS `rolledout` 			
										FROM `v_facility_reporting_view` 		 
										WHERE `date_added` <> '0000-00-00'
										AND `equipment_id` = '4' 

										$user_delimiter 

										GROUP BY `yearmonth`) AS `t1` 
							INNER JOIN 
								(SELECT 	CONCAT(YEAR(`date_added`),'-',MONTH(`date_added`)) AS `yearmonth`,
                                 			`date_added`, 
											MONTH(`date_added`) AS `month`,
											COUNT(*) AS `rolledout` 			
										FROM `v_facility_reporting_view` 		 
										WHERE `date_added` <> '0000-00-00'
										AND `equipment_id` = '4' 

										$user_delimiter 

										GROUP BY `yearmonth`) AS `t2` 
							ON `t1`.`date_added` >= `t2`.`date_added` 
							group by `t1`.`date_added`";

		$sql_removed	="SELECT 
									`t1`.`date_removed` as `rank_date`,
									`t1`.`yearmonth`,
									`t1`.`month`, 
									`t1`.`removed`, 
									SUM(`t2`.`removed`) AS `cumulative`
								FROM
									(SELECT 	CONCAT(YEAR(`date_removed`),'-',MONTH(`date_removed`)) AS `yearmonth`,
												`date_removed`, 
												MONTH(`date_removed`) AS `month`,
												COUNT(*) AS `removed` 			
											FROM `v_facility_reporting_view` 		 
											WHERE `date_removed` <> '0000-00-00'
											AND `equipment_id` = '4' 

											$user_delimiter 

											GROUP BY `yearmonth`) AS `t1` 
								INNER JOIN 
									(SELECT 	CONCAT(YEAR(`date_removed`),'-',MONTH(`date_removed`)) AS `yearmonth`,
												`date_removed`, 
												MONTH(`date_removed`) AS `month`,
												COUNT(*) AS `removed` 			
											FROM `v_facility_reporting_view` 		 
											WHERE `date_removed` <> '0000-00-00'
											AND `equipment_id` = '4' 

											$user_delimiter 

											GROUP BY `yearmonth`) AS `t2` 
								ON `t1`.`date_removed` >= `t2`.`date_removed` 
								
								group by `t1`.`date_removed`";

		$devices_added_assoc 	=	R::getAll($sql_added);
		$devices_removed_assoc 	=	R::getAll($sql_removed);

		$devices_added_array	=	array();	
		$devices_removed_array	=	array();

		$consolidated_array		=	array();

		//initialize
		$current_cummulative_added 		= 0;
		$current_cummulative_removed 	= 0;

		//assisgn $current_cummulative_added and $current_cummulative_removed to last years ending

		foreach ($devices_added_assoc as $value) {
			$curr_year = (int) Date("Y",strtotime($value["rank_date"]));
			if($curr_year< (int) $year){
				//echo $curr_year."  ".$year."  ".$value["cumulative"]."<br/>" ;				
				$current_cummulative_added 		= (int) $value["cumulative"];
			}
		}
		//echo 	$current_cummulative_added ;


		foreach ($devices_removed_assoc as $value) {
			$curr_year = (int) Date("Y",strtotime($value["rank_date"]));
			if($curr_year< (int) $year){
				//echo $curr_year."  ".$year."  ".$value["cumulative"]."<br/>" ;				
				$current_cummulative_removed 		= (int) $value["cumulative"];
			}
		}
		//echo 	$current_cummulative_removed ;

		//initialize
		$devices_added_array[0]		=	$current_cummulative_added;	
		$devices_removed_array[0]	=	$current_cummulative_removed;

		for($i=0;$i<12;$i++){

			foreach ($devices_added_assoc as $key => $added) {
				if($added['yearmonth']==($year)."-".($i+1)){
					$current_cummulative_added = (int) $added['cumulative'];
				}				
			}
			foreach ($devices_removed_assoc as $key => $removed) {
				if($removed['yearmonth']==($year)."-".($i+1)){
					$current_cummulative_removed = (int) $removed['cumulative'];
				}				
			}

			$devices_added_array[$i] = $current_cummulative_added;
			$devices_removed_array[$i] = $current_cummulative_removed;
			
		}

		for($i=0;$i<12;$i++){
			$consolidated_array[$i] = (int)$devices_added_array[$i] - (int) $devices_removed_array[$i];
		}

		// echo "<pre>";
		// print_r($consolidated_array);
		// echo "</pre>";
		

		return $consolidated_array;
	}
	private function reported_devices($user_delimiter, $year){

		$sql = 	"SELECT 
						`t1`.`month`,
						COUNT(`t1`.`facility_equipment_id`) AS `reported_devices`

					FROM (
							SELECT 
								DISTINCT `facility_equipment_id`,
								MONTH(`result_date`) AS `month`
							FROM `v_tests_details`
							WHERE 1 

							AND YEAR(`result_date`) = '$year' 

							$user_delimiter
						)AS `t1`					
					GROUP BY `t1`.`month`
				";
		$res = R::getAll($sql);

		$reported_array = array(); 
		for($i=0;$i<12;$i++){

			$reported_array[$i] = 0; 
		}

		for($i=0;$i<12;$i++){

			foreach ($res as $key => $value) {

				if($value["month"]==($i+1)){					
					
					$reported_array[$i] = (int) $value["reported_devices"]; 

				}
			}
		}

		// echo "<pre>";
		// print_r($reported_array);

		return $reported_array;
	}
}