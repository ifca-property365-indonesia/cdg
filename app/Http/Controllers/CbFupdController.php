<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Mail\CbFupdMail;

class CbFupdController extends Controller
{
    public function sendMail(Request $request)
    {
        $new_doc_no = str_replace("/","-",$request->doc_no);

        if (isset($request->band_hd_descs)) {
            $band_hd_descs = str_replace('\n', '(', $request->band_hd_descs) . ')';
        } else {
            $band_hd_descs = $request->band_hd_descs;
        }

        $dataArray = array(
            'band_hd_descs' => $band_hd_descs,
            'sender'        => $request->sender,
            'url1'          => $url1,
            'url2'          => $url2,
            'file_name1'    => $file_name1,
            'file_name2'    => $file_name2,
            'entity_name'   => $request->entity_name,
            'email_address' => $request->email_addr,
            'user_name'     => $request->user_name,
            'reason'        => $request->reason,
            'link'          => 'cbfupd',
        );

        $data2Encrypt = array(
            'entity_cd'     => $request->entity_cd,
            'project_no'    => $request->project_no,
            'doc_no'        => $new_doc_no,
            'old_doc_no'    => $request->doc_no,
            'trx_type'      => $request->trx_type,
            'email_address' => $request->email_addr,
            'level_no'      => $request->level_no,
            'usergroup'     => $request->usergroup,
            'user_id'       => $request->user_id,
            'supervisor'    => $request->supervisor
        );

        // Melakukan enkripsi pada $dataArray
        $encryptedData = Crypt::encrypt($data2Encrypt);
    
        try {
            $emailAddresses = $request->email_addr;
        
            // Check if email addresses are provided and not empty
            if (!empty($emailAddresses)) {
                $emails = is_array($emailAddresses) ? $emailAddresses : [$emailAddresses];
                
                foreach ($emails as $email) {
                    Mail::to($email)->send(new CbFupdMail($encryptedData, $dataArray));
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
