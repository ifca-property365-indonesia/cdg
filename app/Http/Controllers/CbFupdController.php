<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CbFupdController extends Controller
{
    public function processModule($data) 
    {
        $pieces_url = explode(",", $data["url_file"]);
        $url1 = $pieces_url[0];
        $url2 = $pieces_url[1];

        $pieces_file = explode(",", $data["file_name"]);
        $file_name1 = $pieces_file[0];
        $file_name2 = $pieces_file[1];

        $dataArray = array(
            'sender'        => $data["sender"],
            'url1'          => $url1,
            'url2'          => $url2,
            'file_name1'    => $file_name1,
            'file_name2'    => $file_name2,
            'entity_name'   => $data["entity_name"],
            'user_name'     => $data["user_name"],
            'reason'        => $data["reason"],
            'module'        => "CbFupd",
            'subject'       => "Please approve Propose Transfer to Bank No.  ".$data['doc_no']." for ".$data['band_hd_descs'],
        );

        $data2Encrypt = array(
            'entity_cd'     => $data["entity_cd"],
            'project_no'    => $data["project_no"],
            'trx_type'      => $data["trx_type"],
            'email_address' => $data["email_addr"],
            'level_no'      => $data["level_no"],
            'doc_no'        => $data["doc_no"],
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
