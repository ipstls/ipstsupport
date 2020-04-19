<?php

require_once('PHPMailer/PHPMailerAutoload.php');
    $mail = new PHPMailer();

    $mail->From = "learningspace.ipst@gmail.com";
    $mail->FromName = "IPST Support";
    
    $mail->addAddress("cartoon_toon29@hotmail.com", "Recipient Name");
    
    //Provide file path and name of the attachments     
    $mail->addAttachment("files/1.jpg");
    
    $mail->isHTML(true);
    
    $mail->Subject = "Subject Text";
    $mail->Body = "<i>Mail body in HTML</i>";
    $mail->AltBody = "This is the plain text version of the email content";
    
    if(!$mail->send()) 
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } 
    else 
    {
        echo "Message has been sent successfully";
    }



    $MailTo = 'cartoon_toon29@hotmail.com' ;
$MailFrom = 'learningspace.ipst@gmail.com' ;
$MailSubject = 'ทดสอบ' ;
$MailMessage = 'รายละเอียด' ;

    $Headers = "MIME-Version: 1.0\r\n" ;
$Headers .= "Content-type: text/html; charset=windows-874\r\n" ;
// ส่งข้อความเป็นภาษาไทย ใช้ "windows-874"
$Headers .= "From: ".$MailFrom." <".$MailFrom.">\r\n" ;
$Headers .= "Reply-to: ".$MailFrom." <".$MailFrom.">\r\n" ;
$Headers .= "X-Priority: 3\r\n" ;
$Headers .= "X-Mailer: PHP mailer\r\n" ;

if(mail($MailTo, $MailSubject , $MailMessage, $Headers, $MailFrom))
{
echo "Send Mail True" ; //ส่งเรียบร้อย
}else{
echo "Send Mail False" ; //ไม่สามารถส่งเมล์ได้
}

?>