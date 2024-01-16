<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="application/pdf">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    
    <style>
        body {
            font-family: Arial;
        }
    </style>
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #ffffff;font-family: Arial;">
	<div style="width: 100%; background-color: #ffffff; text-align: center;">
        <table width="80%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="margin-left: auto;margin-right: auto;" >
            <tr>
               <td style="padding: 40px 0;">
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding-bottom:25px">
                                    <img width = "120" src="{{ url('public/images/btid_logo.bmp') }}" alt="logo">
                                        <p style="font-size: 16px; color: #026735; padding-top: 0px;">{{ $dataArray['entity_name'] }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;background-color:#e0e0e0;">
                        <tbody>
                            <tr>
                                <td style="padding: 30px 30px">
                                    <h5 style="text-align:left;margin-bottom: 24px; color: #000000; font-size: 20px; font-weight: 400; line-height: 28px;">Dear {{ $dataArray['user_name'] }}, </h5>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;">Below is a request to purchase that requires your approval :</p><br>

                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;"><b>{{ $dataArray['req_hd_descs'] }}</b><br>
                                        With total estimated amount of Rp {{ $dataArray['total_price'] }}<br>
                                        RF no : {{ $dataArray['req_hd_no'] }}</p><br>

                                    <a href="{{ url('api') }}/processdata/{{ $dataArray['module'] }}/A/{{ $encryptedData }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #1ee0ac; border-radius: 4px; color: #ffffff;">Approve</a>
                                    <a href="{{ url('api') }}/processdata/{{ $dataArray['module'] }}/R/{{ $encryptedData }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #f4bd0e; border-radius: 4px; color: #ffffff;">Revise</a>
                                    <a href="{{ url('api') }}/processdata/{{ $dataArray['module'] }}/C/{{ $encryptedData }}" style="display: inline-block; font-size: 13px; font-weight: 600; line-height: 20px; text-align: center; text-decoration: none; text-transform: uppercase; padding: 10px 40px; background-color: #e85347; border-radius: 4px; color: #ffffff;">Cancel</a>
                                    <br>
                                    <p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;">
                                        In case you need some clarification, kindly approach<br>
                                        <table style="width:100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 8px; text-align: left; font-weight: bold; font-size: 16px;">Name of department head</td>
                                                <td style="padding: 8px; text-align: left; font-size: 16px;">{{ $dataArray['user_name'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px; text-align: left; font-weight: bold; font-size: 16px;">Email</td>
                                                <td style="padding: 8px; text-align: left; font-size: 16px;">{{ $dataArray['email_address'] }}</td>
                                            </tr>
                                        </table>                                                                               
                                    </p>
                                        
                                        <br><p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;">
                                        <b>Thank you,</b><br>
                                        {{ $dataArray['sender'] }}
                                    </p><br>
                                    @php
                                        $hasAttachment = false;
                                    @endphp

                                    @foreach($dataArray['url_file'] as $key => $url_file)
                                        @if($url_file !== '' && $dataArray['file_name'][$key] !== '' && $url_file !== 'EMPTY' && $dataArray['file_name'][$key] !== 'EMPTY')
                                            @if(!$hasAttachment)
                                                @php
                                                    $hasAttachment = true;
                                                @endphp
                                                <p style="text-align:left; margin-bottom: 15px; color: #000000; font-size: 16px;">
                                                    <span>To view a detailed product list, description, and estimate price per item, please click on the link below :</span><br>
                                            @endif
                                            <a href="{{ $url_file }}" target="_blank">{{ $dataArray['file_name'][$key] }}</a><br><br>
                                        @endif
                                    @endforeach

                                    @if($hasAttachment)
                                        </p>
                                    @endif

                                    @php
                                        $hasAttachment = false;
                                    @endphp

                                    @foreach($dataArray['doc_link'] as $key => $doc_link)
                                        @if($doc_link !== '' && $doc_link !== 'EMPTY')
                                            @if(!$hasAttachment)
                                                @php
                                                    $hasAttachment = true;
                                                @endphp
                                                <p style="text-align:left; margin-bottom: 15px; color: #000000; font-size: 16px;">
                                                    <span>This request to purchase comes with additional supporting documents, such as detailed specifications, that you can access from the link below :</span><br>
                                            @endif
                                            <a href="{{ $doc_link }}" target="_blank">{{ $doc_link }}</a><br><br>
                                        @endif
                                    @endforeach

                                    @if($hasAttachment)
                                        </p>
                                    @endif

                                    <br><p style="text-align:left;margin-bottom: 15px; color: #000000; font-size: 16px;">
                                        <b>Please do not reply, as this is an automated-generated email.</b><br>
                                    </p><br>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;max-width:620px;margin:0 auto;">
                        <tbody>
                            <tr>
                                <td style="text-align: center; padding:25px 20px 0;">
                                    <p style="font-size: 13px;">Copyright © 2023 IFCA Software. All rights reserved.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
               </td>
            </tr>
        </table>
        </div>
</body>
</html>