<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoOrderController extends Controller
{
    public function processModule($data) 
    {

        $new_doc_no = str_replace("/","-",$data["doc_no"]);

        if (isset($data["req_hd_descs"])) {
            $req_hd_descs = str_replace('\n', '(', $data["req_hd_descs"]) . ')';
        } else {
            $req_hd_descs = $data["req_hd_descs"];
        }

        $dataArray = array(
            'sender'        => $data["sender"],
            'entity_name'   => $data["entity_name"],
            'descs'         => $data["descs"],
            'doc_no'        => $new_doc_no,
            'req_hd_descs'  => $req_hd_descs,
            'req_hd_no'     => $data["req_hd_no"],
            'user_name'     => $data["user_name"],
            'url_file'      => $data["url_file"],
            'file_name'     => $data["file_name"],
            'module'        => "PoRequest",
            'subject'       => "Need Approval for Purchase Requisition No. ".$data['req_hd_no'],
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

        // Melakukan enkripsi pada $dataArray
        $encryptedData = Crypt::encrypt($data2Encrypt);
    
        try {
            $emailAddresses = $data["email_addr"];
        
            // Check if email addresses are provided and not empty
            if (!empty($emailAddresses)) {
                $emails = is_array($emailAddresses) ? $emailAddresses : [$emailAddresses];
                
                foreach ($emails as $email) {
                    Mail::to($email)->send(new SendMail($encryptedData, $dataArray));
                }
                
                $sentTo = is_array($emailAddresses) ? implode(', ', $emailAddresses) : $emailAddresses;
                Log::channel('sendmail')->info('Email berhasil dikirim ke: ' . $sentTo);
                return "Email berhasil dikirim ke: " . $sentTo;
            } else {
                Log::channel('sendmail')->warning('Tidak ada alamat email yang diberikan.');
                return "Tidak ada alamat email yang diberikan.";
            }
        } catch (\Exception $e) {
            Log::channel('sendmail')->error('Gagal mengirim email: ' . $e->getMessage());
            return "Gagal mengirim email: " . $e->getMessage();
        }
    }
}
