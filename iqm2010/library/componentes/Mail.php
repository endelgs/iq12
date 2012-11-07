<?php

include_once('MailClass/class.phpmailer.php');
include_once('MailClass/class.pop3.php');
include_once('MailClass/class.smtp.php');

//configurações SMTP  
DEFINE("SMTPHOST","smtp.gmail.com");
DEFINE("SMTPPORT","587");//se porta vazia, utiliza padrao 25
DEFINE("SMTPUSERNAME","contato@eccen.com.br");
DEFINE("SMTPUSERSENHA","contatocontato2011");
DEFINE("SMTPAUTH",true);
DEFINE("SMTPFROM","contato@eccen.com.br");
DEFINE("SMTPFROMNAME","ECCEN");

class Mail {

    public static function verificar_email($email) {
        $mail_correcto = 0;
        //verifico umas coisas
        if ((strlen($email) >= 6) && (substr_count($email, "@") == 1) && (substr($email, 0, 1) != "@") && (substr($email, strlen($email) - 1, 1) != "@")) {
            if ((!strstr($email, "'")) && (!strstr($email, "\"")) && (!strstr($email, "\\")) && (!strstr($email, "\$")) && (!strstr($email, " "))) {
                //vejo se tem caracter .
                if (substr_count($email, ".") >= 1) {
                    //obtenho a termina��o do dominio
                    $term_dom = substr(strrchr($email, '.'), 1);
                    //verifico que a termina��o do dominio seja correcta
                    if (strlen($term_dom) > 1 && strlen($term_dom) < 5 && (!strstr($term_dom, "@"))) {
                        //verifico que o de antes do dominio seja correcto
                        $antes_dom = substr($email, 0, strlen($email) - strlen($term_dom) - 1);
                        $caracter_ult = substr($antes_dom, strlen($antes_dom) - 1, 1);
                        if ($caracter_ult != "@" && $caracter_ult != ".") {
                            $mail_correcto = 1;
                        }
                    }
                }
            }
        }

        if ($mail_correcto)
            return true;
        else
            return false;
    }

    public static function enviaEmail($email, $corpo, $assunto="Stricto Center") {
            
        $assunto = utf8_decode($assunto);
        $mail = new PHPMailer();
        $mail->IsSMTP(); // send via SMTP
        //configura��o SMTP Gmail
        $mail->SetLanguage("br", "libs/");
        $mail->SMTPSecure = "tls";

        $mail->Host = SMTPHOST;
        $mail->Port = SMTPPORT;
        $mail->Username = SMTPUSERNAME;
        $mail->Password = SMTPUSERSENHA;
        $mail->From = SMTPFROM;
        $mail->FromName = SMTPFROMNAME;
        $mail->SMTPAuth = SMTPAUTH;


        $mail->AddAddress($email);

        $mail->WordWrap = 50; // Defini��o de quebra de linha
        $mail->IsHTML(true); // envio como HTML se 'true'
        $mail->Subject = $assunto;
        $mail->Body = $corpo;

        $mail->AddAddress($email);



        if (!$mail->Send()) {
            echo "Mensagem não enviada<br />";
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Mensagem enviada";
        }
    }

}

?>
