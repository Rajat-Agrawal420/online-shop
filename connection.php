<?php

date_default_timezone_set('Asia/Kolkata');
$curr_date = date('Y-m-d H:i:s');

$site_name = 'Online Shop';

$this_site_url = 'http://localhost/online-shop';
$this_site_id = 2;
$site_logo = $this_site_url . '/img/dizital.png';
$avatar = $this_site_url . '/img/avatar.png';
$avatar2 = $this_site_url . '/img/avatar2.png';


$conn = mysqli_connect('localhost', 'root', '', 'onlineshop');

if (!$conn) {
    echo "Connection Error.";
    die;
}


function realEscape($val)
{
    global $conn;
    return mysqli_escape_string($conn, $val);
}

function getUserID()
{
    $user_id = -1;
     if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    return $user_id;
}

function getUserInfo(Int $userID = -1)
{
    global $conn;

    if ($userID == -1) {
        $userID = getUserID();
    }

    $qry = "SELECT * FROM users WHERE id = '$userID' ";
    $res = mysqli_query($conn, $qry);
    $data = mysqli_fetch_assoc($res);

    return $data;
}

function errlog($error, $qry)
{
    global $curr_date;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $url = "https://";
    else
        $url = "http://";
    $url .= $_SERVER['HTTP_HOST'];
    $url .= $_SERVER['REQUEST_URI'];

    $handle = fopen('error.txt', 'a');
    $txt = $curr_date . " ERROR : [URL:" . $url . "] " . $error . " [SQL:" . $qry . "]\r\n";
    fwrite($handle, $txt);
    fclose($handle);
    echo '<script> location.replace("error.html"); </script>';
}

function getProductRating($item_id): array
{
    global $conn;
    $p_rating = 0;
    $review=0;
    $sql = "SELECT avg(rating) as num, count(id) as reviews from product_rating where item_id=$item_id and status=1";

    $res2 = mysqli_query($conn, $sql);
    if (!$res2) {
        errlog(mysqli_error($conn), $sql);
    } else {
        $res = mysqli_fetch_assoc($res2);
        $p_rating = round($res['num']);
        $review = $res['reviews'];
    }

    return array($p_rating, $review);
}


function getCartItems($save_type = 'CART'): int|bool
{
    global $conn;

    $total_cart_items = 0;
    if (isset($_SESSION['user_id'])) {
        $user_id = getUserID();
        $qry = "SELECT count(*) from cart_items where `status` = '1' AND user_id = '$user_id' and save_type = '$save_type'";

        if (!$res = mysqli_query($conn, $qry)) {
            errlog(mysqli_error($conn), $qry);
            return false;
        }
        $wishes = mysqli_fetch_assoc($res);
        $total_cart_items = $wishes['count(*)'];
        return ((int)($total_cart_items));
    } else {

        if (strtoupper($save_type) == 'CART') {
            $data = '';
            if (isset($_COOKIE['guestCart'])) {
                $data = unserialize($_COOKIE['guestCart']);
            }
            if ($data == '') {
                return 0;
            }
            $arr = explode(',', $data);
            $total_cart_items = count($arr);

            return ((int)($total_cart_items));
        } else if (strtoupper($save_type) == 'WISHLIST') {

            $data = '';
            if (isset($_COOKIE['guestWishlist'])) {
                $data = unserialize($_COOKIE['guestWishlist']);
            }
            if ($data == '') {
                return 0;
            }

            $arr = explode(',', $data);
            $total_cart_items = count($arr);

            return ((int)($total_cart_items));
        }
    }
}


function agoTime($time, $chat_time = false, $show_online = false)
{
    global $curr_date;

    if ($chat_time) {
        $chat_date = new DateTime(date('Y-m-d H:i:s', strtotime($time)));
        $curr_time = new DateTime($curr_date);

        $diff = $curr_time->diff($chat_date);
        if ($diff->y > 0  ||  $diff->m > 0   ||  $diff->d > 14) {
            return (date('D-M-y', strtotime($time)));
        }

        if ($diff->d == 1) {
            return ($diff->d . " Day ago");
        }

        if ($diff->d > 0) {
            return ($diff->d . " Days ago");
        }


        if ($diff->h == 1) {
            return ($diff->h . " hr ago");
        }

        if ($diff->h > 0) {
            return ($diff->h . " hrs ago");
        }

        if ($diff->i > 0) {
            return ($diff->i . " min ago");
        }

        if ($show_online) {
            return "Online";
        }

        return "Just Now";
    }

    $time = strtotime($time);
    $time_difference = time() - $time;

    if ($time_difference < 1) {
        return 'less than 1 second ago';
    }

    $condition = array(
        12 * 30 * 24 * 60 * 60  =>  'year',
        30 * 24 * 60 * 60       =>  'month',
        24 * 60 * 60            =>  'day',
        60 * 60                 =>  'hour',
        60                      =>  'minute',
        1                       =>  'second'
    );

    foreach ($condition as $secs => $str) {
        $d = $time_difference / $secs;

        if ($d >= 1) {
            $t = round($d);
            return 'about ' . $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
        }
    }
}

function encoder($str)
{
    $str = str_replace("'", "'+" . '"' . "'" . '"' . "+'", $str);
    return $str;
}

function decoder($str)
{
    $str = str_replace("'+" . '"' . "'" . '"' . "+'", "'", $str);
    $str = str_replace("<script>", htmlspecialchars("<script>"), $str);
    $str = str_replace("</script>", htmlspecialchars("</script>"), $str);
    return $str;
}
