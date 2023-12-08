<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CbFupdController extends Controller
{
    public function processModule($data) 
    {
        $new_doc_no = str_replace("/","-",$data["doc_no"]);

        $pieces_url = explode(",", $data["url_file"]);
        $url1 = $pieces_url[0];
        $url2 = $pieces_url[1];

        $pieces_file = explode(",", $data["file_name"]);
        $file_name1 = $pieces_file[0];
        $file_name2 = $pieces_file[1];

        $dataArray = array(
            'entity_cd'     => $request->entity_cd,
            'project_no'    => $request->project_no,
            'doc_no'        => $new_doc_no,
            'old_doc_no'    => $request->doc_no,
            'trx_type'      => $request->trx_type,
            'level_no'      => $request->level_no,
            'usergroup'     => $request->usergroup,
            'band_hd_descs' => $request->band_hd_descs,
            'user_id'       => $request->user_id,
            'sender'            => $request->sender,
            'url1'          => $url1,
            'url2'          => $url2,
            'file_name1'    => $file_name1,
            'file_name2'    => $file_name2,
            'entity_name'   => $request->entity_name,
            'email_address' => $request->email_addr,
            'user_name'     => $request->user_name,
            'reason'        => $request->reason,
            'supervisor'    => $request->supervisor,
            'link'          => 'cbfupd',
        );

        $data2Encrypt = array(
            'entity_cd'     => $data["entity_cd"],
            'project_no'    => $data["project_no"],
            'email_address' => $data["email_addr"],
            'level_no'      => $data["level_no"],
            'doc_no'        => $new_doc_no,
            'usergroup'     => $data["usergroup"],
            'user_id'       => $data["user_id"],
            'supervisor'    => $data["supervisor"]
        );
    }
}
