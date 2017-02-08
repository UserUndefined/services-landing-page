<?php

session_start();

if(isset($_POST['email'])) {

    $email_to = "your@email.com";
    $email_subject = "New 3 Cubed Message";

    function died($error) {
        header('HTTP/1.1 400 Bad Request');
        echo $error;
        die();
    }

    if(!isset($_POST['name'])) {
        died('Please supply your name.');
    }

    if(!isset($_POST['email'])) {
        died('Please supply your email address.');
    }

    if(!isset($_POST['message'])) {
        died('Please supply a message.');
    }

    if(!isset($_POST['captcha_code'])) {
        died('Please supply a security code.');
    }

    //check the fields are in the payload
    $name = $_POST['name'];
    $email_from = $_POST['email'];
    $message = $_POST['message'];
    $captcha_code = $_POST['captcha_code'];

    //sanitize the email. NOTE: this method seems to still leave a lot of junk in the string!
    $email_from = filter_var($email_from, FILTER_SANITIZE_EMAIL);

    //sanitize the name
    $name = strip_tags(trim($name));
	$name = str_replace(array("\r","\n"),array(" "," "),$name);

    //sanitize the message
    $message = trim($message);

    //sanitize the security code
    $captcha_code = trim($captcha_code);

    //validate the email address using PHP filter, NOTE: this method is pretty lame!
    $email_from = filter_var($email_from, FILTER_VALIDATE_EMAIL);
    if (!$email_from){
        died('The email address you entered does not appear to be valid2.');
    }

    //validate the supplied text using regex
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if(!preg_match($email_exp,$email_from)) {
        died('The email address you entered does not appear to be valid.');
    }
    $string_exp = "/^[A-Za-z .'-]+$/";
    if(!preg_match($string_exp,$name)) {
        died('The name you entered does not appear to be valid.');
    }
    $string_exp = "/^[A-Za-z0-9]+$/";
    if(!preg_match($string_exp,$captcha_code)) {
        died('The security code does not appear to be valid.');
    }

    //check the strings length
    if(strlen($email_from) < 6) {
        died('The email address you entered does not appear to be valid.');
    }
    if(strlen($name) < 3) {
        died('The name you entered does not appear to be valid.');
    }
    if(strlen($message) < 2) {
        died('The message you entered does not appear to be valid.');
    }
    if(strlen($captcha_code) < 6) {
        died('The security code you entered does not appear to be valid.');
    }

    //remove any in-appropriate strings
    function clean_string($string) {
        $bad = array("content-type","bcc:","to:","cc:","href");
        return str_replace($bad,"",$string);
    }

    // check the captcha code
    include_once $_SERVER['DOCUMENT_ROOT'] . '/imagecode/securimage.php';
    $securimage = new Securimage();
    if ($securimage->check($captcha_code) == false) {
        died('The security code entered was incorrect.');
    }

    $email_message .= "Name: ".clean_string($name)."\n";
    $email_message .= "Email: ".clean_string($email_from)."\n";
    $email_message .= "Message: ".clean_string($message)."\n";

    // Message lines should not exceed 70 characters (PHP rule), so wrap it.
    $email_message = wordwrap($email_message, 70);

    // create email headers
    $headers = 'From: ' . $email . "\r\n".
        'Reply-To: ' . $email . "\r\n" .
        'X-Mailer: PHP/' . phpversion() .
        'MIME-Version: 1.0' . "\r\n" .
        'Content-type: text/html; charset=iso-8859-1';

    //send the email
    mail($email_to, $email_subject, $email_message, $headers);

    //return a response
    echo "Thank You! We'll be in touch";
}

?>