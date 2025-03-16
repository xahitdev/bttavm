<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/PHPMailer/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/PHPMailer/src/SMTP.php';

ob_start();
session_start();

$phpm = new PHPMailer();

function RandomSubIDGenerator() : string {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password_length = 30;
    $password = "";

    for ($i = 0; $i < $password_length; $i++) {
        $random_character = $chars[rand(0, strlen($chars) - 1)];
        $password .= $random_character;
    }

    return $password;
}

    if (isset($_POST["mail_input"]) && isset($_POST["name_surname_input"]) && isset($_POST["password_input"])) {
        include "../esclot-panel/dbcon.php";

        $name_surname = htmlspecialchars($_POST["name_surname_input"]);
        $mail = htmlspecialchars($_POST["mail_input"]);
        $pass = htmlspecialchars($_POST["password_input"]);
        $sha_pass = hash('sha512',$pass);
        $verified = "false";
        $subid = htmlspecialchars(RandomSubIDGenerator());

        $mailQuery = $connect -> prepare("SELECT * FROM customers WHERE customers_mail = :cm");
        $mailQuery -> bindParam(":cm", $mail, PDO::PARAM_STR);
        $mailQuery -> fetchAll(PDO::FETCH_ASSOC);
        $mailQuery -> execute();
        if($mailQuery -> rowCount()){ // have an email in db
            echo "0";
            exit();
        }

        $insertQuery = $connect -> prepare("INSERT INTO customers(customers_subid, customers_mail, customers_name_surname, customers_password, customers_verified) VALUES (:csid, :cm, :cns, :cp, :cv)");
        $insertQuery -> bindParam(":csid", $subid, PDO::PARAM_STR);
        $insertQuery -> bindParam(":cm", $mail, PDO::PARAM_STR);
        $insertQuery -> bindParam(":cns", $name_surname, PDO::PARAM_STR);
        $insertQuery -> bindParam(":cp", $sha_pass, PDO::PARAM_STR);
        $insertQuery -> bindParam(":cv", $verified, PDO::PARAM_STR);

        if($insertQuery -> execute()){
            $idQuery = $connect -> prepare("SELECT * FROM customers WHERE customers_mail = :cm");
            $idQuery -> bindParam(":cm", $mail, PDO::PARAM_STR);
            $idQuery -> fetchAll(PDO::FETCH_ASSOC);
            $idQuery -> execute();

            $targetid = "";
            if($idQuery -> rowCount()){
                foreach($idQuery as $user){
                    $targetid = $user["customers_id"];
                }
            }

            $verification_url = "localhost/esclot/verification-action.php?cid=".$targetid."&sid=".$subid;

            $mailcontent = '
            <div dir="ltr" style="background-color:#f7f7f7;margin:0;padding:70px 0;width:100%" bgcolor="#f7f7f7" width="100%">
            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
            <tbody>
            <tr>
            <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="800" style="background-color:#fff;border:1px solid #dedede;border-radius:25px" bgcolor="#fff">
            <tbody>
            <tr>
            <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:black;color:#fff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:25px 25px 0 0" bgcolor="#a32bbc">
            <tbody>
            <tr>
            <td style="padding:20px 40px;display:block">
            <h1 style="font-weight: bolder; text-align:center" bgcolor="inherit">
            <b>Welcome to ESCLOT LONDON</b></h1>
            </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            <tr>
            <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="600">
            <tbody>
            <tr>
            <td valign="top" style="background-color:#fff" bgcolor="#fff">

            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody>
            <tr>
            <td valign="top" style="padding:40px 32px">
            <div style="color:#636363;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left" align="left">
            <h2 style="margin:0 0 16px; color:black;">Hi '.$name_surname.',</h2>
            <p style="margin:0 0 16px; color:black;">Thanks for creating an account on esclotlondon.com,<br> your verification link is
                    <a href="'.$verification_url.'"><button style="margin:0px; padding: 0px; background-color: gray; border: 0px solid #FFF; border-radius: 8px; padding-top: 4px; padding-bottom: 4px; padding-left: 12px; padding-right: 12px; border: 1px solid black; cursor: pointer;"><b>Verify</b></button></a></p>
            </div>
            </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </tbody>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:black;color:#fff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:0px 0px 25px 25px">
            <tbody>
            <tr>
            <td style="padding:20px 40px;display:block">

            </td>
            </tr>
            </tbody>
            </table>

            </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            <tr>
            <td align="center" valign="top">
            <table border="0" cellpadding="10" cellspacing="0" width="600">
            <tbody>
            <tr>
            <td valign="top" style="padding:0;border-radius:6px">
            <table border="0" cellpadding="10" cellspacing="0" width="100%">
            <tbody>
            <tr>
            <td colspan="2" valign="middle" style="border-radius:6px;border:0;color:#8a8a8a;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:12px;line-height:150%;text-align:center;padding:24px 0" align="center">
            <p style="margin:0 0 16px">ESCLOT LONDON</p>
            </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </tbody>
            </table>
            </td>
            </tr>
            </tbody>
            </table>
            </div>';

            try{
                $phpm -> isSMTP();
                $phpm -> CharSet = 'UTF-8';
                $phpm -> Host = 'smtp.gmail.com';
                $phpm -> SMTPAuth = true;
                $phpm -> Username = 'SMTP_MAIL';
                $phpm -> Password = 'SMTP_PASSWORD';
                $phpm -> Port = 587;
                $phpm -> setFrom("SMTP_MAIL", "EsclotLondon");
                $phpm -> addAddress($mail, $mail);
                $phpm -> isHTML(true);
                $phpm -> Subject = "ESCLOTLONDON.COM | Mail Activation";
                $phpm -> Body = $mailcontent;
                $mbol = $phpm -> send();
            }catch(Exception $e){
                // Exception
            }

            $_SESSION["email"] = $mail;

            echo "1";
        }else{
            echo "0";
        }
    }

?>
