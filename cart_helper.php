<?php
session_start();
include_once 'connection.php';
include_once 'use_cart_and_wishlist.php';

$user_id = -1;

if (isset($_SESSION['user_id']))
    $user_id = $_SESSION['user_id'];

function checkCoupon(string $coupon_code, Int $user_id = -1): array
{
    global $conn, $curr_date;
    if ($user_id == -1)
        $user_id = getUserID();
    $coupon_code = realEscape($coupon_code);
    $qry = "SELECT * FROM available_coupons WHERE coupon_code = '$coupon_code' AND user_id = '$user_id' AND (validity = -1 OR created_date + INTERVAL validity day >= '$curr_date') AND redeemed = 0";
    $res = mysqli_query($conn, $qry);

    if (!$res) {
        errlog(mysqli_error($conn), $qry);
    }
    $data = mysqli_fetch_assoc($res);
    if (isset($data['id'])) {
        return $data;
    }
    return array();
}

if (isset($_POST['clearCart'])) {
    if (isset($_SESSION['user_id'])) {
        $qry = "UPDATE cart_items set qty = 0, `status` = 0 WHERE user_id = '$user_id' AND save_type = 'CART' ";
        if (!mysqli_query($conn, $qry)) {
            errlog(mysqli_error($conn), $qry);
            echo 0;
        } else {
            setcookie("guestCart", "", time() - 1, '/');
            setcookie("guestCartQuantity", "", time() - 1, '/');
            unset($_SESSION['guestCart']);
            echo 1;
        }
    } else {
        setcookie("guestCart", "", time() - 1, '/');
        setcookie("guestCartQuantity", "", time() - 1, '/');
        unset($_SESSION['guestCart']);
        echo 1;
    }
} else if (isset($_POST['updateQuantity'])) {
    $update = realEscape($_POST['updateQuantity']);
    $id = realEscape($_POST['item']);

    $curr = isset($_POST['curr']) ? realEscape($_POST['curr']) : '';

    $qry = "";
    if ($update == 'increase') {
        if (use_cart_n_wishlist($id, 'CART', false)) {
            echo 1;
        } else {
            echo 0;
        }
    } else if ($update == 'decrease') {
        if (use_cart_n_wishlist($id, 'CART', false, true)) {
            echo 1;
        } else {
            echo 0;
        }
    } else {
        echo '
        <script>
        console.log(' . $update . ');
        </script>
        ';
        die;
    }
}


//  remove item  

else if (isset($_POST['removeItem'])) {
    if (use_cart_n_wishlist((int)($_POST['removeItem']), 'CART', true)) {
        echo "1";
    } else {
        echo "0";
    }
} else if (isset($_POST['removeWish'])) {
    if (use_cart_n_wishlist((int)($_POST['removeWish']), 'WISHLIST', true)) {
        echo "1";
    } else {
        echo "0";
    }
} else if (isset($_POST['applyCouponCode'])) {
    $code = realEscape($_POST['applyCouponCode']);
    $data = checkCoupon($code);
    $price = (float)($_POST['price']);

    $response = array();
    if (isset($data['id'])) {
        $discount = 0;
        $data['discount'] = (float)($data['discount']);
        if ($data['discount_type'] == '%') {
            $discount = round($price * $data['discount'] / 100, 2);
        } else {
            $discount = $data['discount'];
        }

        $_SESSION['APPLIED_COUPON'] = $data;

        $response = array("success" => true, "discount" => $discount);
    } else {
        if (isset($_SESSION['APPLIED_COUPON'])) {
            unset($_SESSION['APPLIED_COUPON']);
        }
        $response = array("error" => true);
    }
    die(json_encode($response));
}


//  checkout_helper

else if (isset($_POST['removeAddress'])) {

    $id = realEscape($_POST['removeAddress']);

    $qry = "SELECT * from address  where id = '$id' and user_id = '$user_id'";
    $res = mysqli_query($conn, $qry);
    if (!$res) {
        errlog(mysqli_error($conn), $qry);
    }
    $res = mysqli_fetch_assoc($res);
    if ($res  &&  isset($res['id'])) {
        $qry = "DELETE from address where id = '$id' ";
        if (mysqli_query($conn, $qry)) {
            echo 1;
        } else {
            errlog(mysqli_error($conn), $qry);
            echo 0;
        }
    } else {
        echo -1;
    }
}


//  set parameters for checkout 

else if (isset($_POST['address'])) {

    $_SESSION['use_address'] = realEscape($_POST['address']);
    $_SESSION['payment_mode'] = realEscape($_POST['payment_mode']);

    echo 1;
}
