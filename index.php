<?php
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$api_array = array("Tampa"=>"9e451e7177cd43f49270cd942848add2");

$vid_offset=0;

$hapikey = '73844f79-2967-4253-9237-33a37221de3c';
//$hapikey = '60bcf069-28b6-474d-a394-0ca033ed4e78';
$properties=array();
$firstname = $lastname = $phone = $city = $zip = $state = $company = $address = $email = $franchise_location  = '' ;

$res = 1;
second_request(3838501,$hapikey,$res,$api_array);
//echo"sdsd";
function curl_get_request($url,$properties) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$resp = curl_exec($curl);	
	curl_close($curl); // Close the connection		
	return $resp;
}

function curl_request_post($url,$data) {
	$ch = curl_init( $url ); 
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$response=json_decode($result);
	
	return $response;
}

 
function second_request($vid_offset,$hapikey,$res,$api_array) { 
	$count = 200;
	$firstname = $lastname = $phone = $city = $zip = $state = $company = $address = $email = $franchise_location  = '' ;
	$properties=array();
	$get_all_contacts = "https://api.hubapi.com/contacts/v1/lists/all/contacts/all?count=$count&hapikey=" . $hapikey.'&vidOffset='.$vid_offset;
	$pdata_count = 0;
	/***get contacts from hubspot****/
	$get_data = curl_get_request($get_all_contacts,$properties);
	$response_contacts=json_decode($get_data);
	
	echo"<pre>";
	print_r($response_contacts);
	die;

	$contactids=array();
	/****get data from hubspot and loop start****/
	if(!empty($response_contacts)){
		if(isset($response_contacts->contacts)){
			$contact_ids=array();
			foreach($response_contacts->contacts as $all_contacts) {
				$contact_ids[] = "vid=".$all_contacts->vid;
				
			}
			$pdata_count+=count($contact_ids);
			/***second api***/
			if(!empty($contact_ids)){
				$contacts = implode("&",$contact_ids);
				
				$contact_batch_url = "https://api.hubapi.com/contacts/v1/contact/vids/batch/?$contacts&hapikey=$hapikey";
				$get_data_batch = curl_get_request($contact_batch_url,$properties);
				$response_batch=json_decode($get_data_batch);

			}		
		}
		//echo"sdswddddddd";
		$hs_more = "has-more";
		$vid_of = "vid-offset";
		 
		if (isset($response_contacts->$hs_more)) {
			$has_more = $response_contacts->$hs_more;
			if($has_more!=''){
				$vid_offset = $response_contacts->$vid_of;						
				second_request($vid_offset,$hapikey,$res,$api_array); 				
			}
		}
	}
	$fp = fopen('file.txt', 'w');
	fwrite($fp, print_r($contactids, TRUE));
	fclose($fp);
	$res++;
}


	// the message
    $msg = "Cron test email Hubspot to ServiceMinder";
    
    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg,70);
    
    // send email
    mail("kushal.kbizsoft@gmail.com","My subject cron",$msg);
    echo"done";
	die;
?>