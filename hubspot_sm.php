<?php
$api_array = array("Alpharetta"=>"285a992d46c942d68619ecb21d39264f","Athens"=>"5af4053edb98471eb3eb2a55498c92f3","Atlanta"=>"285a992d46c942d68619ecb21d39264f","Aurora"=>"5c69454f881f48439f2efbf412107b14","Austin"=>"40bba154a4d845cd8fda94d172dc0f9b","Bensalem"=>"a27bddd04af3459291eb38be8f04f663","Centennial"=>"5c69454f881f48439f2efbf412107b14","Charlotte"=>"26c73c28baa54a26aede6d44ad0fb646","Charleston"=>"42a42a8ef0fd49758591f52034b943d4","Chicago"=>"f8e3c61de721418d961ef95afd6b6605","Columbia"=>"7ad8730b1d494ef9acd732806d94f7e6","Dalton"=>"41904f07c48b44ef8eda1dcc81dacdef","Fairfield"=>"8b2892b2e0454726af62f84697baad05","Flower Mound"=>"9aa3fdd02e044992a62e79a255bbe5ce","Fort Myers"=>"94270d8d3a024be08402cf2a28eff0b0","Galveston"=>"c218331658b644b284eba7d353d76c36","Greenville"=>"c919cf7642fd43e48705223593ed5d2e","Inglewood"=>"cdce010090ee4e529cff6b6eea3fac58","Indianapolis"=>"c72f05c4f2d94ca2b36c4b926667508f","Kansas City"=>"ba6f1c3ba3a44ab090bee9ed372a18da","Kissemmee"=>"ce83cc8928aa45a3a6ae9cece2f902f9","La Grange"=>"f8e3c61de721418d961ef95afd6b6605","Myrtle Beach"=>"blank","Montville"=>"bea279bddf064d149874d57e668b6159","Naples"=>"94270d8d3a024be08402cf2a28eff0b0","Nashville"=>"0ce158d61a46414dbbf1b65bf5aed717","Orlando"=>"ce83cc8928aa45a3a6ae9cece2f902f9","Owing Mills"=>"blank","Philadelphia"=>"2951eb6fe36642f498595373b2310c77","Plano"=>"11ffc63aef524cc68301d8338e135d51","Raliegh"=>"93219e48a69946ce8abcc230f4cea9ec","Portland"=>"a9794d421d284bd593df5ef98cf1b83a","San Antonio"=>"0b6bd790984a430b92750212cdba680b","San Francisco"=>"41904f07c48b44ef8eda1dcc81dacdef","Scottsdale"=>"88dd4e05cd814bfd86c8ce7bf11ce625","Tampa"=>"fb005b79e0f04439b2cf2a1dfb54740b","Van Nuys"=>"blank","Winter Garden"=>"b04f2460c62b4c3ca1c2249b3ea686cb","Walnut Creek"=>"41904f07c48b44ef8eda1dcc81dacdef","West Palm Beach"=>"f4e5e9747ee4435284d79fa5b890bcbe","Hilton Head"=>"42a42a8ef0fd49758591f52034b943d4","Louisville"=>"1e9a7261b6ea46a19e7f8d14d15fcf59","Detroit"=>"blank","Memphis"=>"bc6da3b8ef0c4f4080de2cc88ef7d26f");

$hapikey = 'd669925b-e437-4bf8-8054-7c0aaafdfcdc';

$properties=array();
/***first api to get contacts***/
$get_all_contacts = 'https://api.hubapi.com/contacts/v1/lists/all/contacts/all?hapikey=' . $hapikey;
/***get contacts from hubspot****/
$get_data = curl_get_request($get_all_contacts,$properties);
/****get contacts from hubspot end*****/
$firstname = $lastname = $phone = $city = $zip = $state = $company = $address = $email = $franchise_location  = '' ;
$response_contacts=json_decode($get_data);

/****get data from hubspot and loop start****/
if(!empty($response_contacts)){
	if(isset($response_contacts->contacts)){
		$contact_ids=array();
		foreach($response_contacts->contacts as $all_contacts){
			$contact_ids[] = "vid=".$all_contacts->vid;
		}
		/***second api***/
		if(!empty($contact_ids)){
			$contacts = implode("&",$contact_ids);
			
			$contact_batch_url = "https://api.hubapi.com/contacts/v1/contact/vids/batch/?$contacts&hapikey=$hapikey";
			$get_data_batch = curl_get_request($contact_batch_url,$properties);
			$response_batch=json_decode($get_data_batch);
			/* echo"<pre>";
			print_r($response_batch);
			die; */
			if(!empty($response_batch)){
			
				foreach($response_batch as $all_new){
					/***get hubspot properties****/
					if(isset($all_new->properties)){
						if(isset($all_new->properties->firstname)){
							$firstname = $all_new->properties->firstname->value;
						}
						if(isset($all_new->properties->lastname)){
							$lastname = $all_new->properties->lastname->value;
						}
						if(isset($all_new->properties->phone)){
							$phone = $all_new->properties->phone->value;
						}
						if(isset($all_new->properties->city)){
							$city = $all_new->properties->city->value;
						}
						if(isset($all_new->properties->zip)){
							$zip = $all_new->properties->zip->value;									
						}
						if(isset($all_new->properties->state)){
							$state = $all_new->properties->state->value;									
						}
						if(isset($all_new->properties->company)){
							$company = $all_new->properties->company->value;									
						}
						if(isset($all_new->properties->address)){
							$address = $all_new->properties->address->value;									
						}
						if(isset($all_new->properties->email)){
							$email = $all_new->properties->email->value;									
						}
						if(isset($all_new->properties->franchise_location)){
							$franchise_location = $all_new->properties->franchise_location->value;									
						}
					}
					$fran_api='';
					if (array_key_exists($franchise_location,$api_array)) {
						$fran_api = $api_array[$franchise_location];
					}
					$sm_url='';
					if($fran_api!=''){
						//$sm_url = "https://serviceminder.io/service/contact/addupdate/$fran_api";
						$sm_url = "https://serviceminder.io/service/contact/addupdate/222ec9aa37b44f5b805a31519d4a224d";
					}
					echo $sm_url.'<br/>';
					
					if($sm_url!=''){
						$data = array (
							'Name'=>$firstname.' '.$lastname,
							//'Email'=>"shri@gmail.com",
							'Address1'=>$address,
							'PostalCode'=>$zip,
							'City'=>$city,
							'State'=>$state,
							'Company'=>$company,
							'Phone1'=>$phone,
							'email'=>$email
						);
						/****insert data to serviceminder start****/
						$insert_data_sm = curl_request_post($sm_url,$data);
						echo"<pre>";
						print_r($insert_data_sm);
						/****insert data to serviceminder end****/
					
					}					
				}
			}
		}		
	}
}

function curl_get_request($url,$properties){
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$resp = curl_exec($curl);	
		curl_close($curl); // Close the connection		
		return $resp;
	}

function curl_request_post($url,$data){
	
	$ch = curl_init( $url ); 
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$response=json_decode($result);
	
	return $response;
}
?>
