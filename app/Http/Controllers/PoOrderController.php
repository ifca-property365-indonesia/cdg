<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoOrderController extends Controller
{
    public function processModule($data) 
    {

        $new_doc_no = str_replace("/","-",$data["doc_no"]);

        $dataArray = array(
            'doc_no'        => $new_doc_no,
            'sender'        => $data["sender"],
            'url_file'      => $data["url_file"],
            'file_name'     => $data["file_name"],
            'entity_name'   => $data["entity_name"],
            'email_address' => $data["email_addr"],
            'user_name'     => $data["user_name"],
            'module'        => "PoOrder",
            'subject'       => "Need Approval for Purchase Order No ".$data['doc_no'],
        );

        $data2Encrypt = array(
            'entity_cd'     => $data["entity_cd"],
            'project_no'    => $data["project_no"],
            'email_address' => $data["email_addr"],
            'level_no'      => $data["level_no"],
            'trx_type'      => $data["trx_type"],
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

    public function update($status, $encrypt, $reason)
    {
        return "READY TO UPDATE";
    }
}
