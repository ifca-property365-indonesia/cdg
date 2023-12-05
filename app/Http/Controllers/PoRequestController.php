<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Mail\PoRequestMail;
use Illuminate\Support\Facades\DB;

class PoRequestController extends Controller
{
    public function sendMail(Request $request)
    {
        $new_doc_no = str_replace("/","-",$request->doc_no);

        $dataArray = array(
            'sender'        => $request->sender,
            'entity_name'   => $request->entity_name,
            'email_address' => $request->email_addr,
            'descs'         => $request->descs,
            'doc_no'        => $new_doc_no,
            'req_hd_descs'  => $request->req_hd_descs,
            'req_hd_no'     => $request->req_hd_no,
            'user_name'     => $request->user_name,
            'url_file'      => $request->url_file,
            'file_name'     => $request->file_name,
            'link'          => 'porequest',
        );

        $data2Encrypt = array(
            'entity_cd'     => $request->entity_cd,
            'project_no'    => $request->project_no,
            'doc_no'        => $new_doc_no,
            'email_address' => $request->email_addr,
            'old_doc_no'    => $request->doc_no,
            'level_no'      => $request->level_no,
            'usergroup'     => $request->usergroup,
            'user_id'       => $request->user_id,
            'supervisor'    => $request->supervisor
        );

        // Melakukan enkripsi pada $dataArray
        $encryptedData = Crypt::encrypt($data2Encrypt);
    
        try {
            $emails = is_array($request->email_addr) ? $request->email_addr : [$request->email_addr];
        
            foreach ($emails as $email) {
                // Mengirim email dengan data yang telah dienkripsi
                Mail::to($email)->send(new PoRequestMail($encryptedData, $dataArray));
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

    public function reqpass($status ='', $doc_no = '', $encrypt='') {
        if ($status == 'A') {
            $name   = 'Approval';
            $bgcolor = '#40de1d';
            $valuebt  = 'Approve';
        } else if ($status == 'R') {
            $name   = 'Revision';
            $bgcolor = '#f4bd0e';
            $valuebt  = 'Revise';
        } else {
            $name   = 'Cancellation';
            $bgcolor = '#e85347';
            $valuebt  = 'Cancel';
        }
        $dataArray = Crypt::decrypt($encrypt);
        $data = array(
            "status"    => $status,
            "doc_no"    => $doc_no,
            "email"     => $dataArray["email_address"],
            "link"      => "porequest",
            "encrypt"   => $encrypt,
            "name"      => $name,
            "bgcolor"   => $bgcolor,
            "valuebt"   => $valuebt
        );
        if ($status == "A"){
            return view('email/passcheck', $data);
        } else {
            return view('email/passcheckwithremark', $data);
        }
    }

    public function updateStatus(Request $request)
    {
        $doc_no = $request->doc_no;
        $status = $request->status;
        $encrypt= $request->encrypt;
        $password=$request->password;
        $email=$request->email;
        $reason=$request->reason;
        if ($reason == NULL) {
            $reason="0";
        }

        $where = array('email' => $email);
        $data = DB::connection('BTID')
                    ->table('mgr.security_users')
                    ->where($where)
                    ->select('name')
                    ->first();

        $servername = getenv('DB_HOST3');
        $port = getenv('DB_PORT3');
        $dbname = getenv('DB_DATABASE3');
        $username = $data->name;
        $password = $password;

        try {
            // Attempt to connect to the database
            $connection = new \PDO("sqlsrv:Server=$servername,$port;Database=$dbname", $username, $password);
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $result = $this->update($status, $encrypt, $reason);
            return $result;
        } catch (\Exception $e) {
            if ($e->getCode() == 2002 || $e->getCode() == 50000) {
                echo "Server not found or connection failed.";
            } elseif ($e->getCode() == 28000) {
                echo "Authentication failed: Invalid username or password.";
            } else {
                echo "Connection failed: " . $e->getMessage();
            }
        }
    }

    public function update($status, $encrypt, $reason)
    {
        $data = Crypt::decrypt($encrypt);
        $entity_cd = $data["entity_cd"];
        $project_no = $data["project_no"];
        $level_no = $data["level_no"];
        $usergroup = $data["usergroup"];
        $user_id = $data["user_id"];
        $supervisor = $data["supervisor"];

        $new_doc_no = str_replace("-","/",$data["doc_no"]);
        $where = array(
            'doc_no'        => $new_doc_no,
            'status'        => array("A",'R', 'C'),
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'Q',
            'module'        => 'PO',
        );

        $query = DB::connection('BTID')
        ->table('mgr.cb_cash_request_appr')
        ->where($where)
        ->get();

        $where2 = array(
            'doc_no'        => $new_doc_no,
            'status'        => 'P',
            'entity_cd'     => $entity_cd,
            'level_no'      => $level_no,
            'type'          => 'Q',
            'module'        => 'PO',
        );

        $query2 = DB::connection('BTID')
        ->table('mgr.cb_cash_request_appr')
        ->where($where2)
        ->get();

        if (count($query)>0) {
            $msg = 'You Have Already Made a Request to Purchase Requisition No. '.$new_doc_no ;
            $notif = 'Restricted !';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "notif" => $notif,
                "image" => $image
            );
        } else if (count($query2) == 0){
            $msg = 'There is no Purchase Requisition with No. '.$new_doc_no ;
            $notif = 'Restricted !';
            $st  = 'OK';
            $image = "double_approve.png";
            $msg1 = array(
                "Pesan" => $msg,
                "St" => $st,
                "notif" => $notif,
                "image" => $image
            );
        }

        if ($status == "A") {
            $descstatus = "Approved";
            $imagestatus = "approved.png";
        } else if ($status == "R") {
            $descstatus = "Revised";
            $imagestatus = "revise.png";
        } else {
            $descstatus = "Cancelled";
            $imagestatus = "reject.png";
        }
        $pdo = DB::connection('BTID')->getPdo();
        $sth = $pdo->prepare("SET NOCOUNT ON; EXEC mgr.x_send_mail_approval_po_request ?, ?, ?, ?, ?, ?, ?, ?, ?;");
        $sth->bindParam(1, $entity_cd);
        $sth->bindParam(2, $project_no);
        $sth->bindParam(3, $new_doc_no);
        $sth->bindParam(4, $status);
        $sth->bindParam(5, $level_no);
        $sth->bindParam(6, $usergroup);
        $sth->bindParam(7, $userid);
        $sth->bindParam(8, $supervisor);
        $sth->bindParam(9, $reason);
        $sth->execute();
        if ($sth == true) {
            $msg = "You Have Successfully ".$descstatus." the Purchase Requisition No. ".$new_doc_no;
            $notif = $descstatus." !";
            $st = 'OK';
            $image = $imagestatus;
        } else {
            $msg = "You Failed to ".$descstatus." the Purchase Requisition No.".$new_doc_no;
            $notif = 'Fail to '.$descstatus.' !';
            $st = 'OK';
            $image = "reject.png";
        }
        $msg1 = array(
            "Pesan" => $msg,
            "St" => $st,
            "notif" => $notif,
            "image" => $image
        );
        return view("email.after", $msg1);
    }

}