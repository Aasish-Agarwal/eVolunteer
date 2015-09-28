<?php
//session_start();

//	$incfile = $_SERVER["DOCUMENT_ROOT"] . "/dv3/modules/devotee/classes/Devotee_new.php";
$incfile = "/home/krish933/public_html" . "/dv3/modules/devotee/classes/Devotee_new.php";


echo "<br><br>INCLUDING $incfile<br><br>";
require_once $incfile;
DebugMessageProcessor::setDebugLevel(1000);

function getDevExport() {
	//
	//Generate csv
	//
	$centre_id = 5;
	$prog_id = 10;
	$export_type = 'basic';
	$period =  12;

	$cname=Centres::getName($centre_id);
	$pname=Programs::getName($prog_id);

	$csvOutput = Devotee::exportCentreProgDev($centre_id, $prog_id,$period);
	$file_name = "GC_Saturday_basic.csv";
	$file_pointer = fopen($file_name, "w");
	fwrite($file_pointer,$csvOutput);
	fclose($file_pointer);


	return 0;
}



class mail {

    public static function prepareAttachment($path) {
        $rn = "\r\n";

        if (file_exists($path)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $ftype = finfo_file($finfo, $path);
            $file = fopen($path, "r");
            $attachment = fread($file, filesize($path));
            $attachment = chunk_split(base64_encode($attachment));
            fclose($file);

            $msg = 'Content-Type: \'' . $ftype . '\'; name="' . basename($path) . '"' . $rn;
            $msg .= "Content-Transfer-Encoding: base64" . $rn;
            $msg .= 'Content-ID: <' . basename($path) . '>' . $rn;
//            $msg .= 'X-Attachment-Id: ebf7a33f5a2ffca7_0.1' . $rn;
            $msg .= $rn . $attachment . $rn . $rn;
            return $msg;
        } else {
            return false;
        }
    }

    public static function sendMail($to, $subject, $content, $path = '', $cc = '', $bcc = '', $_headers = false) {

        $rn = "\r\n";
        $boundary = md5(rand());
        $boundary_content = md5(rand());

// Headers
        $headers = 'From: krishna-seva.com mailer <krish933@srv56.hosting24.com>' . $rn;
        $headers .= 'Reply-To: krishna-seva.com admin <ashexp1@gmail>' . $rn;
        $headers .= 'Mime-Version: 1.0' . $rn;
        $headers .= 'Content-Type: multipart/related;boundary=' . $boundary . $rn;

        //adresses cc and ci
        if ($cc != '') {
            $headers .= 'Cc: ' . $cc . $rn;
        }
        if ($bcc != '') {
            $headers .= 'Bcc: ' . $cc . $rn;
        }
        $headers .= $rn;

// Message Body
        $msg = $rn . '--' . $boundary . $rn;
        $msg.= "Content-Type: multipart/alternative;" . $rn;
        $msg.= " boundary=\"$boundary_content\"" . $rn;

//Body Mode text
        $msg.= $rn . "--" . $boundary_content . $rn;
        $msg .= 'Content-Type: text/plain; charset=ISO-8859-1' . $rn;
        $msg .= strip_tags($content) . $rn;

//Body Mode Html
        $msg.= $rn . "--" . $boundary_content . $rn;
        $msg .= 'Content-Type: text/html; charset=ISO-8859-1' . $rn;
        $msg .= 'Content-Transfer-Encoding: quoted-printable' . $rn;
        if ($_headers) {
            $msg .= $rn . '<img src=3D"cid:template-H.PNG" />' . $rn;
        }
        //equal sign are email special characters. =3D is the = sign
        $msg .= $rn . '<div>' . nl2br(str_replace("=", "=3D", $content)) . '</div>' . $rn;
        if ($_headers) {
            $msg .= $rn . '<img src=3D"cid:template-F.PNG" />' . $rn;
        }
        $msg .= $rn . '--' . $boundary_content . '--' . $rn;

//if attachement
        if ($path != '' && file_exists($path)) {
            $conAttached = self::prepareAttachment($path);
            if ($conAttached !== false) {
                $msg .= $rn . '--' . $boundary . $rn;
                $msg .= $conAttached;
            }
        }

//other attachement : here used on HTML body for picture headers/footers
        if ($_headers) {
            $imgHead = dirname(__FILE__) . '/../../../../modules/notification/ressources/img/template-H.PNG';
            $conAttached = self::prepareAttachment($imgHead);
            if ($conAttached !== false) {
                $msg .= $rn . '--' . $boundary . $rn;
                $msg .= $conAttached;
            }
            $imgFoot = dirname(__FILE__) . '/../../../../modules/notification/ressources/img/template-F.PNG';
            $conAttached = self::prepareAttachment($imgFoot);
            if ($conAttached !== false) {
                $msg .= $rn . '--' . $boundary . $rn;
                $msg .= $conAttached;
            }
        }

// Fin
        $msg .= $rn . '--' . $boundary . '--' . $rn;

// Function mail()
        mail($to, $subject, $msg, $headers);
    }

}

echo date("D-M-Y H:i:s");
getDevExport();

//    sendMail($to, $subject, $content, $path = '', $cc = '', $bcc = '', $_headers = false)


$tcontent = "Hare Krishna\n\n";
$tcontent .= "Please find attached devotee snapshot for Geeta Colony, Saturday Class\n";
$tcontent .= "Note that the data is for last 1 year\n\n";
$tcontent .= "Hare Krishna\nAashish";
$tcontent .= "\n\nNOTE:- This is an automatic delivery scheduled weekly on sunday morning";


echo mail::sendMail("vichitrakrishna.gkg@gmail.com,info.centergc@gmail.com,ashexp1@gmail.com", "GC - Saturday Class Snapshot" , $tcontent, "GC_Saturday_basic.csv", '','' , true);
unlink('GC_Saturday_basic.csv');

?>
