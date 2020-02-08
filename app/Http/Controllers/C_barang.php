<?php

namespace App\Http\Controllers;

use RSAHelp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\M_barang;

class C_barang extends Controller
{
    //
    public function index(){
    	$response 			= array();
    	$response['code']	= 200;
    	$response['data']	= array();
    	$query = M_barang::get();
    	if ($query->count() > 0 ) {
    		$response['data'] = $query;
    	}
    	return view('pages/barang/index', $response);
    }

    public function create(Request $request){
        DB::beginTransaction();
        $response   = array();
        $parameter  = array();
        parse_str(RSAHelp::decrypte()['parameter'], $parameter);
        $response['code']       = 200;
        $response['message']    = $parameter['code']." - ".$parameter['barang']." berhasil di tambahkan";

        $query = M_barang::where('code', $parameter['code'])->get();
        if (strlen($parameter['code']) == 0) {
            $response['code']       = 401;
            $response['message']    = "Code yang anda masukkan kosong";
        }else if($query->count() > 0){
            $response['code']       = 401;
            $response['message']    = "Code yang anda masukkan telah terdaftar";
        }else{
            $parameter['id'] = $this->get_last_id();
            $query = new M_barang;
            $query->id          = $parameter['id'];
            $query->code        = $parameter['code'];
            $query->barang      = $parameter['barang'];
            $query->deleted_at  = null;
            $query = $query->save();

            if ($query === false || $query == 0) {
                $response['code'] = 401;
                $response['message']    = $parameter['code']." - ".$parameter['barang']." tidak berhasil di tambahkan";
            }else{
                $response['id'] = $parameter['id'];
            }
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
        $response['message']    = $parameter['code']." - ".$parameter['barang']." berhasil di perbarui";

        $query      = M_barang::where('id', $parameter['id'])->get();
        if ($query->count() == 0) {
            $response['code']       = 401;
            $response['message']    = "Data tidak di temukan";
        }else if (strlen($parameter['code']) == 0) {
            $response['code']       = 401;
            $response['message']    = "Code yang anda masukkan kosong";
        }else{
            $validate   = M_barang::
            where('code', $parameter['code'])
            ->get();
            if ($validate->count() > 0) {
                if ($validate[0]->id != $parameter['id']) {
                    $response['code']       = 401;
                    $response['message']    = "Code barang sudah digunakan";
                }
            }
            $query = M_barang::find($parameter['id']);
            $query->id          = $parameter['id'];
            $query->code        = $parameter['code'];
            $query->barang      = $parameter['barang'];
            $query->deleted_at  = null;
            $query = $query->save();

            if ($query === false || $query == 0) {
                $response['code'] = 401;
                $response['message']    = $parameter['code']." - ".$parameter['barang']." tidak berhasil di perbarui";
            }
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
        $response['id']         = 0;
        $response['message']    = "Data berhasil di hapus";

        parse_str(RSAHelp::decrypte()['parameter'], $parameter);

        $query = M_barang::where('id', $parameter['id'])->get();
        if ($query->count() > 0) {
            $response['id'] = $parameter['id'];
            $query = M_barang::find($parameter['id']);
            $query = $query->delete();
            if ($query == 0 || $query === false ) {
                $response['code']       = 401;
                $response['message']    = "Data gagal di hapus";
            }
        }else{
            $response['code']       = 401;
            $response['message']    = "Data tidak ditemukan";
        }

        if ($response['code']==200) {
            DB::commit();
        }else{
            DB::rollBack();
        }

        echo json_encode($response);
    }

    public function get_form($id = null){
    	$response 			= array();
    	$response['id'] 	= "";
    	$response['code'] 	= "";
    	$response['barang']	= "";
    	$query = M_barang::where('id', $id)->get();
    	if ($query->count() > 0) {
	    	$response['id'] 	= $query[0]->id;
	    	$response['code'] 	= $query[0]->code;
	    	$response['barang']	= $query[0]->barang;
    	}

    	return view('pages/barang/form', $response);
    }

    private function get_last_id(){
		$last_id    = M_barang::find(M_barang::max('id'));

		if ($last_id !== null) {
			$last_id = $last_id->id + 1;
		}else{
			$last_id = 1;
		}
		return $last_id;
    }
}
