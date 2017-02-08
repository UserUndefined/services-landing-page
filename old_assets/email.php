<?php

if(isset($_POST['email'])) {

    $name    = $_POST['name'];
    $email   = $_POST['email'];
    $message = $_POST['message'];

    $to      = "cristov.igor2@gmail.com";
    $subject = "New message";

    $headers = 'From: '.$email."\r\n".
    'Reply-To: '.$email."\r\n" .
    'X-Mailer: PHP/' . phpversion();

    //if( mail($to,$subject,$message,$headers) )
    if(mail("cristov.igor2@gmail.com","test email","this is a message",$headers) )
    {
        echo "<h2>Thank you for your comment</h2>";
    }
    else
    {
        echo "<h2>Sorry, there has been an error</h2>";
    }
}
?>