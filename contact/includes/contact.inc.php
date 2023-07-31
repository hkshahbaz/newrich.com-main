<?php

session_start();

require '../../assets/setup/env.php';
require '../../assets/includes/security_functions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../assets/vendor/PHPMailer/src/Exception.php';
require '../../assets/vendor/PHPMailer/src/PHPMailer.php';
require '../../assets/vendor/PHPMailer/src/SMTP.php';

if (isset($_POST['submit'])) {

    /*
    * -------------------------------------------------------------------------------
    *   Securing against Header Injection
    * -------------------------------------------------------------------------------
    */

    foreach($_POST as $key => $value){

        $_POST[$key] = _cleaninjections(trim($value));
    }
    
    /*
    * -------------------------------------------------------------------------------
    *   Verifying CSRF token
    * -------------------------------------------------------------------------------
    */

    if (!verify_csrf_token()){

        $_SESSION['STATUS']['mailstatus'] = 'Request could not be validated';
        echo json_encode(array('status' => 'fail', 'message' => 'Request could not be validated'));
        exit();
    }

    
    	// Check if the referer is a local server.
	if (!isset($_SERVER['HTTP_REFERER']) || (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != $_SERVER['SERVER_NAME'])) {
	exit('Direct access not permitted');
	}
	
	if (isset($_SESSION['auth'])){
	
		$name = $_SESSION['username'];
		$email = $_SESSION['email'];
	}
	else {
			if (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
				$_SESSION['ERRORS']['emailerror'] = 'invalid email';
				header("Location: ../");
				exit();
			}
	
		$name = $_POST['name'];
		$email = $_POST['email'];
	}

    
	//To filter message variable
	function input_filter($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data; 
	} 

	$msg = input_filter($_POST['message']);

	if (!isset($_SESSION['auth']) && (!$name || !$msg)) {
	
		$_SESSION['ERRORS']['mailstatus'] = 'Fields cannot be empty';
		exit();
	}
    
	
    /*
    * -------------------------------------------------------------------------------
    *   Using email template
    * -------------------------------------------------------------------------------
    */

    $subject = "$name sent you a message via your contact form";

    $mail_variables = array();

    $mail_variables['APP_NAME'] = APP_NAME;
    $mail_variables['username'] = $name;
    $mail_variables['email'] = $email;
    $mail_variables['message'] = $msg;

    $message = file_get_contents("./template_contactemail.php");

    foreach($mail_variables as $key => $value) {
        
        $message = str_replace('{{ '.$key.' }}', $value, $message);
    }

    $mail = new PHPMailer(true);

    
    try {

        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_USERNAME, APP_NAME);
        $mail->addAddress(MAIL_USERNAME, APP_NAME);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
    } 
    catch (Exception $e) {

        // for public use
        $_SESSION['STATUS']['mailstatus'] = 'Message could not be sent, try again later';

        // for development use
        // $_SESSION['STATUS']['mailstatus'] = 'message could not be sent. ERROR: ' . $mail->ErrorInfo;

        echo json_encode(array('status' => 'fail', 'message' => 'Message could not be sent, try again later'));
        
        exit();
    }

    $_SESSION['STATUS']['mailstatus'] = 'Thanks for contacting! Please Allow 24 hrs for a response';
    echo json_encode(array('status' => 'success', 'message' => 'Thanks for contacting! Please Allow 24 hrs for a response'));
    exit();
}
else {

    header("Location: ../");
    exit();
}
