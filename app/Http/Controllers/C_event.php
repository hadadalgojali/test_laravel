<?php

namespace App\Http\Controllers;

use RSAHelp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Ixudra\Curl\Facades\Curl;

class C_event extends Controller
{
    //
    public function index(){
    	$response 			= array();
    	$response['code']	= 200;
    	$response['data']	= array();

    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/sport-events?page=0')
        ->get();
        if (count(json_decode($resp)) > 0) {
			$response['data']	= json_decode($resp);        	
        }
    	return view('pages/event/index', $response);
    }

    public function create(Request $request){
        DB::beginTransaction();
        $response   = array();
        $parameter  = array();
        parse_str(RSAHelp::decrypte()['parameter'], $parameter);
    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers/'.$parameter['organizer'])
        ->get();
        $params = json_decode($resp);
        $parameter['id_organizer'] 	= "";
        $parameter['organizerName'] = "";
        $parameter['imageLocation'] = "";

        if (isset($params->id)) {
        	$parameter['id_organizer'] 	= $params->id;
        	$parameter['organizerName'] = $params->organizerName;
        	$parameter['imageLocation'] = $params->imageLocation;
        }

        $response['id']      	= 0;
        $response['code']       = 200;
        $response['message']    = $parameter['eventname']." - Add Successs";
        $response['id'] 		= $this->get_last_id();

        $resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/sport-events')
        ->withContentType('application/json')
        ->withData( array( 
        	'id' 			=> (int)$response['id'] ,
        	'eventDateTime' => $parameter['eventdatetime'],
        	'eventName' 	=> $parameter['eventname'],
        	'eventType' 	=> $parameter['eventtype'],
        	'organizer' 	=> array(
        		'id' 			=> (int)$parameter['id_organizer'],
        		'organizerName' => $parameter['organizerName'],
        		'imageLocation' => $parameter['imageLocation'],
        	),
        ) )
        ->asJson()
        ->post();
        
        if (isset($resp->message)) {
        	$response['code']       = 401;
        	$response['message']    = $resp->message;
        }
        if ($response['code']==200) {
            DB::commit();
        }else{
            DB::rollBack();
        }

        echo json_encode($response);
    }

    public function delete(Request $request){
        DB::beginTransaction();
        $response   = array();
        $parameter  = array();
        $response['code']       = 200;
        $response['message']    = "Deleted success";
        parse_str(RSAHelp::decrypte()['parameter'], $parameter);
        $response['id']         = $parameter['id'];
        $resp = Curl::to("http://tester.t4.voxteneo.com/sport_events_voxteneo/api/sport-events/".$parameter['id'])
        ->delete();
        // echo json_encode($resp);die;
        if (isset($resp->message)) {
        	$response['code']       = 401;
        	$response['message']    = $resp->message;
        }
        if ($response['code']==200) {
            DB::commit();
        }else{
            DB::rollBack();
        }

        echo json_encode($response);
    }


    public function update(Request $request){
        DB::beginTransaction();
        $response   = array();
        $parameter  = array();
        parse_str(RSAHelp::decrypte()['parameter'], $parameter);

        $response['code']       = 200;
        $response['id']       	= $parameter['id'];
        $response['message']    = $parameter['eventname']." - update Successs";

    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers/'.$parameter['organizer'])
        ->get();
        $params = json_decode($resp);
        $parameter['id_organizer'] 	= "";
        $parameter['organizerName'] = "";
        $parameter['imageLocation'] = "";

        if (isset($params->id)) {
        	$parameter['id_organizer'] 	= $params->id;
        	$parameter['organizerName'] = $params->organizerName;
        	$parameter['imageLocation'] = $params->imageLocation;
        }

        $resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/sport-events')
        ->withContentType('application/json')
        ->withData( array( 
        	'id' 			=> (int)$parameter['id'] ,
        	'eventDateTime' => $parameter['eventdatetime'],
        	'eventName' 	=> $parameter['eventname'],
        	'eventType' 	=> $parameter['eventtype'],
        	'organizer' 	=> array(
        		'id' 			=> (int)$parameter['id_organizer'],
        		'organizerName' => $parameter['organizerName'],
        		'imageLocation' => $parameter['imageLocation'],
        	),
        ) )
        ->asJson()
        ->put();

        if (isset($resp->message)) {
        	$response['code']       = 401;
        	$response['message']    = $resp->message;
        }
        if ($response['code']==200) {
            DB::commit();
        }else{
            DB::rollBack();
        }

        echo json_encode($response);
    }

    public function get_form($id = null){
		$response                  = array();
		$response['organizer']     = array();
		$response['id']            = "";
		$response['eventname']     = "";
		$response['eventtype']     = "";
		$response['eventdatetime'] = date('Y-m-d');

    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers')
        ->get();
        if (count(json_decode($resp)) > 0) {
			$response['organizer']	= json_decode($resp);        	
        }

    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/sport-events')
        ->get();

        if (strlen($id) > 0) {
	        if (count(json_decode($resp)) > 0) {
	        	foreach (json_decode($resp) as $key => $value) {
	        		if ($id == $value->id) {
						$response['id']            = $value->id;
						$response['eventname']     = $value->eventName;
						$response['eventtype']     = $value->eventType;
						$response['eventdatetime'] = $value->eventDateTime;
	        		}
				}
	        }
	    }
    	return view('pages/event/form', $response);
    }


    private function get_last_id(){
    	$last_id = 0;
    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/sport-events?page=0&size=1000')
        ->get();

	    if (count(json_decode($resp)) > 0) {
	      	foreach (json_decode($resp) as $key => $value) {
	      		$last_id = $value->id;
			}
	    }

	    return $last_id + 1;
    }

}
