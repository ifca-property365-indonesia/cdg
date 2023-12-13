<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Mail\SendMail;

class CbFupdController extends Controller
{
    public function processModule($data) 
    {
        $list_of_urls = explode(',', $data["url_file"]);
        $list_of_files = explode(',', $data["file_name"]);

        $url_data = [];
        $file_data = [];

        foreach ($list_of_urls as $url) {
            $url_data[] = $url;
        }

        foreach ($list_of_files as $file) {
            $file_data[] = $file;
        }

        $dataArray = array(
            'sender'        => $data["sender"],
            'url_file'      => $url_data,
            'file_name'     => $file_data,
            'doc_no'        => $data["doc_no"],
            'entity_name'   => $data["entity_name"],
            'user_name'     => $data["user_name"],
            'reason'        => $data["reason"],
            'module'        => "CbFupd",
            'subject'       => "Please approve Propose Transfer to Bank No. ".$data['doc_no']." for ".$data['band_hd_descs'],
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

        // var_dump($data2Encrypt);

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
