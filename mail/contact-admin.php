<?php
session_start();
include '../connection.php';
include 'mailer.php';

$secretKey = '6LdraxUnAAAAAPKKPrxOuDHWIHbmHFVzwLfSI6od';

if (isset($_POST['name'])) {

  $name = strip_tags(htmlspecialchars($_POST['name']));
  $email = strip_tags(htmlspecialchars($_POST['email']));
  $m_subject = strip_tags(htmlspecialchars($_POST['subject']));
  $message = strip_tags(htmlspecialchars($_POST['message']));

  if (isset($_POST['recaptcha_response']) && !empty($_POST['recaptcha_response'])) {

    $api_url = 'https://www.google.com/recaptcha/api/siteverify';
    $resq_data = array(
      'secret' => $secretKey,
      'response' => $_POST['recaptcha_response'],
      'remoteip' => $_SERVER['REMOTE_ADDR']
    );

    $curlConfig = array(
      CURLOPT_URL => $api_url,
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POSTFIELDS => $resq_data
    );

    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response);

    if ($responseData->success) {

      $to = "rajatagrawal9394@gmail.com";
      $subject = "$m_subject:  $name";
      $body = "You have received a new message from your website contact form.\n\n" . "Here are the details:\n\nName: $name\n\n\nEmail: $email\n\nSubject: $m_subject\n\nMessage: $message";

      $admin = 'rajatagrawal9394@gmail.com';
      $header = array(
        'From' => $admin,
        'Reply-To' => $admin,
        'X-Mailer' => 'PHP/' . phpversion()
      );

      $sql = "INSERT INTO messages(sender, receiver, user_name, mobile, message, msg_type, attachment, status) VALUES ('$email','$to','$name','','$message','message','','1')";

      $res = mysqli_query($conn, $sql);
      if (!$res) {
        errlog(mysqli_error($conn), $sql);
        echo 0;
      } else {

        if (!mail($to, $subject, $body, $header)) {
          echo 0;
        } else {
          echo 1;
        }
      }
    } else {
      echo -1;
    }
  } else {
    echo -1;
  }
} else if (isset($_POST['subscribe'], $_POST['e_mail'])) {

  $email = realEscape($_POST['e_mail']);

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo -1;
    die;
  }

  $subscriber = getUserID();
  $sql = "INSERT into subscribe(email_id,subscriber_id) values('$email','$subscriber')";
  $res = mysqli_query($conn, $sql);
  if (!$res) {
    errlog(mysqli_error($conn), $sql);
  } else {
    echo 1;
  }
}
