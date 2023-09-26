<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['temp_user_id'])) {
    die(json_encode(array("error" => "Unknown User")));
}
require_once "connection.php";
require_once("razorpay/config.php");
require_once("razorpay/razorpay-php/Razorpay.php");

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$api = new Api(RAZORPAY_KEY, RAZORPAY_SECRET);


function sendInvoice(array $invoice)
{
    global $conn;

    $site_title = 'ONLINE SHOP';

    $target = $invoice['target'];
    $title = $invoice['title'];
    $online_payment = $invoice['online_payment'];
    $discount = $invoice['discount'];
    // $image = $invoice['image'];

    $subject = $body = '';
    $ewallet = 0;
    $ebanking = 0;

    $subject = "Purchased new Product";

    $user_name = getUserInfo((isset($_SESSION['temp_user_id'])) ? $_SESSION['temp_user_id'] : getUserID())['name'];

    $coupon_discount = 0;
    $coupon_row = '';
    if (isset($invoice['coupon_discount'])  &&  ($invoice['coupon_discount']) > 0  &&  isset($invoice['coupon_code'])) {
        $coupon_discount = $invoice['coupon_discount'];
        $coupon_row = '<tr>
                            <th>
                                Coupon Used
                            </th>
                            <td>
                                ' . $invoice['coupon_code'] . '
                            </td>
                        </tr>';

        $coupon_row .= '<tr>
                            <th>
                                Coupon Discount
                            </th>
                            <td>
                                ' . $invoice['coupon_discount'] . '
                            </td>
                        </tr>';
    }

    $body = '<!DOCTYPE html>
        <html>
          <title>W3.CSS</title>
          <meta name="viewport" content="width=device-width, initial-scale=1" />
          <style>
            /* @import url("https://fonts.googleapis.com/css2?family=Dosis:wght@400;500&display=swap"); */
        
            .credit-card {
              position: relative;
              max-width: 520px;
              min-width: 520px;
              margin: 50px auto;
              min-height: 300px;
              border-radius: 20px;
              display: flex;
              flex-direction: column;
              padding: 24px;
              box-sizing: border-box;
              background: linear-gradient(-240deg, #b30e11, #b30e11, #df0a0a);
              justify-content: space-between;
              font-family: "Dosis", sans-serif;
              overflow: hidden;
            }
            .credit-card:after {
              content: "";
              position: absolute;
              height: 100%;
              width: 100%;
              left: 0;
              top: 0;
              z-index: 0;
              color: rgb(249 249 249 / 10%);
              background: linear-gradient(310deg, currentColor 25%, transparent 25%) -100px
                  0,
                linear-gradient(146deg, currentColor 25%, transparent 25%) -100px 0,
                linear-gradient(293deg, currentColor 25%, transparent 25%),
                linear-gradient(244deg, currentColor 25%, transparent 25%);
              background-size: calc(2 * 100px) calc(2 * 100px);
            }
            .logo {
              display: flex;
              z-index: 1;
              font-size: 30px;
              color: #ede5e5;
            }
            .logo1 {
              width: 107px;
              display: flex;
              z-index: 1;
              font-size: 1rem;
              color: #ede5e5;
              margin-top: -44px;
              margin-right: -19px;
            }
            .name-and-expiry {
              display: flex;
              justify-content: space-between;
              z-index: 1;
              color: #ede5e5;
              font-size: 20px;
              letter-spacing: 3px;
              filter: drop-shadow(1px 0px 1px #555);
              text-transform: uppercase;
            }
            .numbers {
              font-size: 36px;
              letter-spacing: 7px;
              text-align: center;
              color: #ede5e5;
              z-index: 1;
            }
            .sideqr {
              height: 1px;
            }
            img.qr {
              height: 91px;
            }
            .val {
              font-size: 14px;
              color: #fff;
            }
          </style>
          <body>
          <div class="credit-card">
                    <h3 class="logo">' . strtoupper($title) . '</h3>
                    <h4 class="logo1">' . $site_title . '</h4>
                    <div class="numbers">'  . round($online_payment + $ewallet + $ebanking, 2) . '</div>
                    <div class="name-and-expiry">
                    <span>' . strtoupper($user_name) . '</span>
                    </div>
                </div>

                <table border="1">
                <thead>
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            Value
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            Title
                        </th>
                        <td>
                            ' . $title . '
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Image
                        </th>
                        <td>
                            <img style="max-height: 5rem; max-width: 5rem;" src="'  . '" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Total Cost
                        </th>
                        <td>
                            &#8377;' . round($online_payment + $ewallet + $ebanking + $discount + $coupon_discount, 2) . '
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Paid Online
                        </th>
                        <td>
                        &#8377;' . $online_payment . '
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Paid From Ewallet
                        </th>
                        <td>
                        &#8377;' . $ewallet . '
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Paid From E-Banking
                        </th>
                        <td>
                        &#8377;' . $ebanking . '
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Discount Received
                        </th>
                        <td>
                        &#8377;' . $discount . '
                        </td>
                    </tr>
                    ' . $coupon_row . '
                    <tr>
                        <th>
                            Total Paid
                        </th>
                        <td>
                        &#8377;' . round($online_payment + $ewallet + $ebanking, 2) . '
                        </td>
                    </tr>
                </tbody>
            </table>

          </body>
        </html>
        ';

    $admin = 'rajatagrawal9394@gmail.com';
    $headers = array(
        'From' => $admin,
        'Reply-To' => $admin,
        'X-Mailer' => 'PHP/' . phpversion()
    );

    return mail($target, $subject, $body, $headers);
}

if (isset($_POST['make_payment'])) {

    if (!isset($_SESSION['use_address'])  ||  !isset($_SESSION['payment_mode'])) {
        die(json_encode(array("error" => "Unknown request")));
    }

    $coups_used = isset($_SESSION['APPLIED_COUPON']) ? $_SESSION['APPLIED_COUPON']['coupon_code'] : "";
    $address_id = $_SESSION['use_address'];

    $total_price = $total_discount = $total_shipping_price = $total_coupon_discount = 0;

    $item_ids = '';
    $quantities = '';

    $item_array = array();
    $price_array = array();
    $discount_array = array();

    $addon_array = array();
    $quantity_array = array();


    $uid = -1;

    if (isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
    }


    $qry = "SELECT * from cart_items where save_type = 'CART' AND `status` = '1' AND qty > 0 AND user_id = '$uid'";
    $res = mysqli_query($conn, $qry);
    if (!$res) {
        errlog(mysqli_error($conn), $qry);
    }

    $data = mysqli_fetch_all($res, MYSQLI_ASSOC);

    foreach ($data as $datum) {
        $prod_id = realEscape($datum['item_id']);
        $addon_pr = 0;

        $qry = "SELECT *, product.id as prod_id from product where product.id = '" . $prod_id . "'";


        if ($quantities == "") {
            $quantities = ($datum['qty']);
        } else {
            $quantities .= "," . ($datum['qty']);
        }

        $res = mysqli_query($conn, $qry);
        if (!$res) {
            errlog(mysqli_error($conn), $qry);
        }

        $item_det = mysqli_fetch_assoc($res);

        // echo $item_det['price'] . "<br>" ;
        // print_r($item_det) ;
        // die;

        if ($item_ids == "") {
            $item_ids = ($datum['item_id']);
        } else {
            $item_ids .= "," . ($datum['item_id']);
        }


        $tmp_price_for_array = ((float)(realEscape($item_det['price'])) + $addon_pr) * $datum['qty'];

        $total_price += ((float)(realEscape($item_det['price'])) + $addon_pr) * $datum['qty'];

        $d = 0;

        if ($item_det['discount'] > 0) {
            $d = (int)($item_det['discount']);
        }

        $d *= $datum['qty'];

        array_push($price_array, $tmp_price_for_array - $d);
        array_push($item_array, $datum['item_id']);
        array_push($quantity_array, $datum['qty']);
        array_push($discount_array, $d);

        $total_discount += $d;
    }


    $discount = 0;
    if (isset($_SESSION['APPLIED_COUPON'])) {
        $qry = "SELECT * FROM available_coupons where id = '" . $_SESSION['APPLIED_COUPON']['id'] . "' AND redeemed = 0";
        $res = mysqli_query($conn, $qry);
        if (!$res) {
            errlog(mysqli_error($conn), $qry);
        }

        $data = mysqli_fetch_assoc($res);
        if (isset($data['id'])) {
            $price = $total_price;
            $_SESSION['APPLIED_COUPON'] = $data;
            $data['discount'] = (float)($data['discount']);
            if ($data['discount_type'] == '%') {
                $discount = round($price * $data['discount'] / 100, 2);
            } else {
                $discount = $data['discount'];
            }


            $qry = "UPDATE available_coupons set redeemed = 1 where id = '" . $_SESSION['APPLIED_COUPON']['id'] . "'";
            if (!mysqli_query($conn, $qry)) {
                errlog(mysqli_error($conn), $qry);
            }
        } else {
            unset($_SESSION['APPLIED_COUPON']);
        }
    }

    $total_coupon_discount = $discount;



    $payment_mode = $_SESSION['payment_mode'];

    $amount = ($total_price + $total_shipping_price) - ($total_coupon_discount + $total_discount);


    $coups_used = isset($_SESSION['APPLIED_COUPON']) ? $_SESSION['APPLIED_COUPON']['coupon_code'] : "";

    $qry = "INSERT INTO `orders`(`item_id`, `user_id`, `quantity`, `amount`, `coupon_id`, `coupon_discount`, `item_discount`, `payment_status`, `payment_mode`, `address`, `order_status`, `order_date`) VALUES ('$item_ids', '$uid', '$quantities', '$amount', '$coups_used', '$total_coupon_discount', '$total_discount', 'PENDING', '$payment_mode', '$address_id', 'PENDING', '$curr_date')";

    if (!mysqli_query($conn, $qry)) {
        errlog(mysqli_error($conn), $qry);
        die();
    }

    $insID = mysqli_insert_id($conn);

    for ($i = 0; $i < count($item_array); $i++) {

        $qry = "INSERT INTO `order_detail`(`order_id`, `item_id`, `quantity`, `price`, `discount`, `order_date`, user_id) VALUES ('$insID', '" . realEscape($item_array[$i]) . "',  '" . realEscape($quantity_array[$i]) . "', '" . realEscape($price_array[$i]) . "', '" . realEscape($discount_array[$i]) . "', '$curr_date', $uid)";
        if (!mysqli_query($conn, $qry)) {
            errlog(mysqli_error($conn), $qry);
        }
    }


    if ($payment_mode == 'COD') {
        $_SESSION['RECENT_ORDER_ID'] = $insID;
        if (isset($_SESSION['user_id'])) {
            $uid = $_SESSION['user_id'];
        }

        $qry = "UPDATE cart_items set `status` = 0 where save_type = 'CART' AND user_id = '" . $uid . "'";
        if (!mysqli_query($conn, $qry)) {
            errlog(mysqli_error($conn), $qry);
        }
        die(json_encode(array("cod" => true)));
    }


    if (isset($_SESSION['admin_id'])) {
        setcookie("is_admin", "yes", time() + 60 * 60, '/');
    }

    $amount = round($amount, 2) * 100;

    $_SESSION['order_in_progress'] = $insID;
    $_SESSION['amount_to_be_paid'] = $amount;

    $orderData = [
        'receipt'         => 'rcptid_11',
        'amount'          => $amount,  // in paise
        'currency'        => 'INR'
    ];

    $razorpayOrder = $api->order->create($orderData);
    $newArray = array();
    foreach ($razorpayOrder as $key => $value) {
        if (is_object($key)  ||  is_object($value))   continue;
        $newArray[$key] = $value;
    }

    echo json_encode($newArray);
} else if (isset($_POST['verifyPayment'])  &&  isset($_SESSION['order_in_progress'])) {
    // ! uncomment in live mode


    // $id = $_POST['verifyPayment']['razorpay_payment_id'];
    // $order_id = $_POST['verifyPayment']['razorpay_order_id'] ;
    // $sign = $_POST['verifyPayment']['razorpay_signature'] ;

    // $success = true;

    // $error = "Payment Failed";

    // if (empty($_POST['razorpay_payment_id']) === false) {
    //     $api = new Api(RAZORPAY_KEY, RAZORPAY_SECRET);

    //     try {
    //         // Please note that the razorpay _details ID must
    //         // come from a trusted source (session here, but
    //         // could be database or something else)
    //         $attributes = array(
    //             'razorpay_order_id' => $order_id,
    //             'razorpay_payment_id' => $id,
    //             'razorpay_signature' => $sign
    //         );

    //         $api->utility->verifyPaymentSignature($attributes);
    //     } catch (SignatureVerificationError $e) {
    //         $success = false;
    //         $error = 'Razorpay Error : ' . $e->getMessage();
    //     }
    // }

    // if ($success === true) {
    //     $html = "<p>Your payment was successful</p>
    //         <p>Payment ID: {$id}</p>";
    // } else {
    //     $html = "<p>Your payment failed</p>
    //         <p>{$id}</p>";
    // }

    // // echo $html;

    $order_id = $_SESSION['order_in_progress'];

    $_SESSION['RECENT_ORDER_ID'] = $order_id;

    $qry = "SELECT * from orders where id = '$order_id'";
    $res = mysqli_fetch_assoc(mysqli_query($conn, $qry));

    $total = $res['amount'];

    // $a = "SELECT vendor.id FROM admin INNER JOIN vendor ON admin.vendor_id = vendor.id WHERE admin.site_id = '$this_site_id'";
    // $a = mysqli_query($conn, $a);
    // $a = mysqli_fetch_assoc($a);
    // if ($total > 0)
    //     credit_ebanking($a['id'], $total, $order_id, "Item Sold");

    $qry = "SELECT product_name from product where id IN(" . $res['item_id'] . ")";
    $res1 = mysqli_fetch_all(mysqli_query($conn, $qry), MYSQLI_ASSOC);

    $invoice = array();

    $invoice['target'] = getUserInfo((isset($_SESSION['temp_user_id'])) ? $_SESSION['temp_user_id'] : getUserID())['email'];
    $invoice['ewallet'] = 0;
    $invoice['ebanking'] = 0;
    $title = '';
    foreach ($res1 as $item) {
        if ($title = '')
            $title = $item['product_name'];
        else
            $title .= ", " . $item['product_name'];
    }
    $invoice['title'] = $title;
    // $invoice['image'] = $res['banner'];

    $invoice['online_payment'] = $res['amount'];
    $invoice['discount'] = $res['item_discount'];
    $invoice['coupon_discount'] = isset($_SESSION['COUPON_DISCOUNT']) ? $_SESSION['COUPON_DISCOUNT'] : 0;
    $invoice['coupon_code'] = isset($_SESSION['COUPON_CODE']) ? $_SESSION['COUPON_CODE'] : "";


    $qry = "UPDATE orders set payment_status = 'PAID' where id = '$order_id'";
    if (!mysqli_query($conn, $qry)) {
        errlog(mysqli_error($conn), $qry);
    }

    $qry = "UPDATE cart_items set `status` = 0 where save_type = 'CART' AND user_id = '" . $_SESSION['user_id'] . "'";
    if (!mysqli_query($conn, $qry)) {
        errlog(mysqli_error($conn), $qry);
    }

    sendInvoice($invoice);

    echo 1;
}
