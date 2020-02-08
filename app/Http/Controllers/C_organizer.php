<?php

namespace App\Http\Controllers;

use RSAHelp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Ixudra\Curl\Facades\Curl;

use App\M_log;

class C_organizer extends Controller
{
    //
    public function index(){
    	$response 			= array();
    	$response['code']	= 200;
    	$response['data']	= array();

    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers?page=0')
        ->get();
        if (count(json_decode($resp)) > 0) {
			$response['data']	= json_decode($resp);        	
        }
    	return view('pages/organizer/index', $response);
    }

    public function create(Request $request){
        DB::beginTransaction();
        $response   = array();
        $parameter  = array();
        parse_str(RSAHelp::decrypte()['parameter'], $parameter);

        $response['id']      	= 0;
        $response['code']       = 200;
        $response['message']    = $parameter['organizer']." - Add Successs";
        $response['id']         = $this->get_last_id();
        $resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers')
        ->withContentType('application/json')
        ->withData( array( 
        	'id' 			=> $response['id'] ,
        	'organizerName' => $parameter['organizer'],
        	'imageLocation' => $parameter['image'],
        ) )
        ->asJson()
        ->post();
        
        $query = new M_log;
        if (isset($resp->message)) {
            $response['code']       = 401;
            $response['message']    = $resp->message;
            $query->response        = $resp->message;
        }

        $query->id          = $this->get_last_id_log();
        $query->url         = "http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers";
        $query->parameter   = "";
        $query->save();
        
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
        $response['message']    = $parameter['organizer']." - update Successs";

        $resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers')
        ->withContentType('application/json')
        ->withData( array( 
        	'id' 			=> $parameter['id'],
        	'organizerName' => $parameter['organizer'],
        	'imageLocation' => $parameter['image'],
        ) )
        ->asJson()
        ->put();

        $query = new M_log;
        if (isset($resp->message)) {
            $response['code']       = 401;
            $response['message']    = $resp->message;
            $query->response        = $resp->message;
        }

        $query->id          = $this->get_last_id_log();
        $query->url         = "http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers";
        $query->parameter   = "";
        $query->save();
        
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
        // http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers/59
        $resp = Curl::to("http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers/".$parameter['id'])
        ->delete();
        // echo json_encode($resp);die;
        $query = new M_log;
        if (isset($resp->message)) {
        	$response['code']       = 401;
        	$response['message']    = $resp->message;
            $query->response        = $resp->message;
        }

        $query->id          = $this->get_last_id_log();
        $query->url         = "http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers";
        $query->parameter   = "";
        $query->save();
        
        if ($response['code']==200) {
            DB::commit();
        }else{
            DB::rollBack();
        }

        echo json_encode($response);
    }


    public function get_form($id = null){
    	$response 				= array();
    	$response['id'] 		= "";
    	$response['organizer'] 	= "";
    	$response['image']		= "";

    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers?page=0')
        ->get();

        if (strlen($id) > 0) {
	        if (count(json_decode($resp)) > 0) {
	        	foreach (json_decode($resp) as $key => $value) {
	        		if ($id == $value->id) {
				    	$response['id'] 		= $value->id;
				    	$response['organizer'] 	= $value->organizerName;
				    	$response['image']		= $value->imageLocation;
	        		}
				}
	        }
	    }
    	return view('pages/organizer/form', $response);
    }


    private function get_last_id(){
    	$last_id = 0;
    	$resp = Curl::to('http://tester.t4.voxteneo.com/sport_events_voxteneo/api/organizers?page=0&size=500')
        ->get();

	    if (count(json_decode($resp)) > 0) {
	      	foreach (json_decode($resp) as $key => $value) {
	      		$last_id = $value->id;
			}
	    }

	    return $last_id + 1;
    }


    private function get_last_id_log(){
        $last_id    = M_log::find(M_log::max('id'));

        if ($last_id !== null) {
            $last_id = $last_id->id + 1;
        }else{
            $last_id = 1;
        }
        return $last_id;
    }

}
