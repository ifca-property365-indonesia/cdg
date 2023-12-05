<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\CbFupdMail;
use Illuminate\Support\Facades\DB;

class CbFupdController extends Controller
{
    public function sendMail(Request $request)
    {
        $callback = array(
            'Error' => false,
            'Pesan' => '',
            'Status' => 200
        );

        $new_doc_no = str_replace("/","-",$request->doc_no);

        $pieces_url = explode(",", $request->url_file);
        $url1 = $pieces_url[0];
        $url2 = $pieces_url[1];

        $pieces_file = explode(",", $request->file_name);
        $file_name1 = $pieces_file[0];
        $file_name2 = $pieces_file[1];

        // var_dump($mail1);
        // var_dump($mail2);

        $dataArray = array(
            'trx_type'      => $request->trx_type,
            'band_hd_descs' => $request->band_hd_descs,
            'sender'        => $request->sender,
            'url1'          => $url1,
            'url2'          => $url2,
            'file_name1'    => $file_name1,
            'file_name2'    => $file_name2,
            'entity_name'   => $request->entity_name,
            'user_name'     => $request->user_name,
            'link'          => 'cbfupd',
        );

        $data2Encrypt = array(
            'entity_cd'     => $request->entity_cd,
            'project_no'    => $request->project_no,
            'old_doc_no'    => $request->doc_no,
            'doc_no'        => $new_doc_no,
            'email_address' => $request->email_addr,
            'level_no'      => $request->level_no,
            'usergroup'     => $request->usergroup,
            'user_id'       => $request->user_id,
            'supervisor'    => $request->supervisor
        );

        $encryptedData = Crypt::encrypt($data2Encrypt);

        try {
            $emails = is_array($request->email_addr) ? $request->email_addr : [$request->email_addr];
        
            foreach ($emails as $email) {
                // Mengirim email dengan data yang telah dienkripsi
                Mail::to($email)->send(new CbFupdMail($encryptedData, $dataArray));
            }
            
            // Jika berhasil mengirim semua email
            $sentTo = is_array($request->email_addr) ? implode(', ', $request->email_addr) : $request->email_addr;
            Log::info('Email berhasil dikirim ke: ' . $sentTo);
            return "Email berhasil dikirim";
        } catch (\Exception $e) {
            // Tangani kesalahan jika pengiriman email gagal
            Log::error('Gagal mengirim email: ' . $e->getMessage());
            return "Gagal mengirim email. Cek log untuk detailnya.";
        }
    }
}
