<?php
session_start();
include 'connection.php';

function isMobile($mobile)

{

    if ($mobile[0] == '+') {

        if (strlen($mobile) != 13) {

            return false;
        }

        if ((int)($mobile[3]) < 6) {

            return false;
        }
    } else {

        if ((int)($mobile[0]) < 6) {

            return false;
        }

        if (strlen($mobile) != 10) {

            return false;
        }
    }

    return true;
}

if (isset($_POST['SignUp'])) {

    $name = realEscape($_POST['user_name']);
    $email = realEscape($_POST['user_email']);
    $mobile = realEscape($_POST['user_mobile']);
    $pass = realEscape($_POST['user_pass']);

    $pass = password_hash($pass, PASSWORD_DEFAULT);

    $qry = "SELECT * FROM users WHERE email='$email' AND status > -1";

    $res = mysqli_query($conn, $qry);
    if (!$res) {
        errlog($res, $qry);
    }

    if (mysqli_num_rows($res) > 0) {
        echo 2;
        return;
    }

    $otp = rand(100000, 999999);

    if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
        echo 3;
        die;
    }

    $_SESSION['temp_otp'] = $otp;
    $_SESSION['otp_create_time'] = date('Y-m-d H:i:s', strtotime($curr_date . " + 5 min"));
    $_SESSION['temp_email'] = $email;
    $_SESSION['temp_mobile'] = $mobile;
    $_SESSION['temp_name'] = $name;
    $_SESSION['temp_password'] = $pass;

    $body = '<table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8" style=" @import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: ' . "'Open Sans'" . ', sans-serif; ">
    <tr>
        <td>
            <table style="background-color: #f2f3f8; max-width: 670px; margin: 0 auto" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="height: 80px">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: center">
                        <a href="" title="logo" target="_blank">
                            <img width="60" src="' . $site_logo . '" title="logo" alt="logo" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="height: 20px">&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style=" max-width: 670px; background: #fff; border-radius: 3px; text-align: center; -webkit-box-shadow: 0 6px 18px 0 rgba(0, 0, 0, 0.06); -moz-box-shadow: 0 6px 18px 0 rgba(0, 0, 0, 0.06); box-shadow: 0 6px 18px 0 rgba(0, 0, 0, 0.06); ">
                            <tr>
                                <td style="height: 40px">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="padding: 0 35px">
                                    <h1 style="color: #1e1e2d; font-weight: 500; margin: 0; font-size: 32px; font-family: ' . "'Rubik'" . ', sans-serif; "> New One Time Password(OTP) </h1> <span style=" display: inline-block; vertical-align: middle; margin: 29px 0 26px; border-bottom: 1px solid #cecece; width: 100px; "></span>
                                    <p style="color: #455056; font-size: 15px; line-height: 24px; margin: 0; "> Thank you for choosing ' . $site_name . '. Use the following OTP to verify your email. OTP is valid for 5 minutes </p> <a href="javascript:void(0);" style=" background: #20e277; text-decoration: none !important; font-weight: 700; margin-top: 35px; color: #fff; text-transform: uppercase; font-size: 22px; padding: 10px 24px; display: inline-block; border-radius: 50px; ">' . $otp . '</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="height: 40px">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="height: 20px">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: center">
                        <p style=" font-size: 14px; color: rgba(69, 80, 86, 0.7411764705882353); line-height: 18px; margin: 0 0 0; "> &copy; <strong>' . $site_name . '</strong> </p>
                    </td>
                </tr>
                <tr>
                    <td style="height: 80px">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>';

    $admin = 'rajatagrawal9394@gmail.com';
    $headers = array(
        'From' => $admin,
        'Reply-To' => $admin,
        'X-Mailer' => 'PHP/' . phpversion()
    );

    if (mail($email, "Verification OTP", $body, $headers)) {
        echo 1;
    } else {
        echo 5;
    }
} else if (isset($_POST['verifyOtp'])) {             // verify otp
    $otp = realEscape($_POST['verifyOtp']);
    $email = realEscape($_SESSION['temp_email']);


    if ($otp == $_SESSION['temp_otp']  &&  $curr_date <= $_SESSION['otp_create_time']) {

        $qry = "INSERT INTO users (name, email, mobile, password, status, created_date) VALUES ('" . realEscape($_SESSION['temp_name']) . "', '" . realEscape($_SESSION['temp_email']) . "', '" . realEscape($_SESSION['temp_mobile']) . "',  '" . realEscape($_SESSION['temp_password']) . "','1', '$curr_date')";
        if (!mysqli_query($conn, $qry)) {
            errlog(mysqli_error($conn), $qry);
            echo 0;
        } else {

            $id = $_SESSION['user_id'] = $_SESSION['temp_user_id'] = mysqli_insert_id($conn);

            $body = '
            <div style="color: white; text-align: center; padding: 15px; background-color: green;">
                Thanks for showing interest. <br>
                Your account has been created successfully.<br>
                Your Account is under review process, we will notify you when it get activated.<br>
			
				<small>Reach to us for any queries  at onlineshop-info@gmail.com</small>
            </div>
            ';
            $admin = 'rajatagrawal9394@gmail.com';
            $headers = array(
                'From' => $admin,
                'Reply-To' => $admin,
                'X-Mailer' => 'PHP/' . phpversion()
            );

            if (!mail($_SESSION['temp_email'], "Account Created", $body, $headers)) {
                echo "Mail not sent";
            }


            unset($_SESSION['temp_otp']);
            unset($_SESSION['temp_name']);
            unset($_SESSION['temp_email']);
            unset($_SESSION['temp_mobile']);
            unset($_SESSION['temp_password']);
            unset($_SESSION['otp_create_time']);

            echo 1;
        }
    } else {
        echo 2;
    }
} elseif (isset($_POST['userLogin'])) {

    $email = realEscape($_POST['userLogin']);
    $pass = realEscape($_POST['pass']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)  &&  !isMobile($email)) {

        die("4");
    }

    $qry = "SELECT * FROM users where (email = '$email' OR (mobile = '$email' AND mobile_verified = 1)) AND status = 1";

    $res = mysqli_query($conn, $qry);
    if (!$res) {

        errlog(mysqli_error($conn), $qry);
    } else {

        $result = mysqli_fetch_assoc($res);

        if (isset($result['id'])) {

            if (password_verify($pass, $result['password'])) {

                $_SESSION['user_id'] = $result['id'];

                // $verify_code = password_hash($result['id'], PASSWORD_DEFAULT);

                // $qry = "UPDATE vendor SET verify_code = '" . realEscape($verify_code) . "' WHERE id = '" . $result['id'] . "' ";

                // if (!mysqli_query($conn, $qry)) {

                //     errlog(mysqli_error($conn), $qry);
                // }

                // if (isset($_POST['rememberMe'])) {

                //     setcookie('rememberUser', $verify_code, time() + 60 * 60 * 24 * 7, '/');

                //     setcookie('verificationFlagUser1', password_hash($result['password'], PASSWORD_DEFAULT), time() + 60 * 60 * 24 * 7, '/');

                //     setcookie('verificationFlagUser2', password_hash($result['email'], PASSWORD_DEFAULT), time() + 60 * 60 * 24 * 7, '/');
                // }

                echo '1';
            } else {

                echo "2";
            }
        } else {

            $qry = "SELECT * FROM users where (email = '$email' OR (mobile = '$email' AND mobile_verified = 1))";

            $res = mysqli_query($conn, $qry);
            if (!$res) {

                errlog(mysqli_error($conn), $qry);
            } else {

                $result = mysqli_fetch_assoc($res);
                if (isset($result['id'])) {
                    echo "-1";
                } else {
                    echo "3";
                }
            }
        }
    }
} elseif (isset($_POST['adminLogin'])) {
} else if (isset($_POST['Logout'])) {

    unset($_SESSION['user_id']);

    echo 1;
}
