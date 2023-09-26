<?php

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($conn)) {
    include "connection.php";
}

function use_cart_n_wishlist($item_id, $save_type, $remove_if_exists = false, $decrease_quantity = false, $setMultiQty = -1): string|bool
{

    global $conn, $curr_date;

    if (isset($_SESSION['user_id'])) {

        $prod_id = realEscape($item_id);
        $user_id = $_SESSION['user_id'];

        $qry = "SELECT * FROM cart_items WHERE user_id='$user_id' AND save_type='$save_type' AND status=1 AND item_id='$prod_id'";

        if (!$res = mysqli_query($conn, $qry)) {
            errlog(mysqli_error($conn), $qry);
            return false;
        }
        $rows = mysqli_num_rows($res);
        if ($rows > 0) {

            if ($remove_if_exists) {     // Remove Product

                $qry = "UPDATE cart_items set `status` = '0', qty = '0' where item_id = '$prod_id' AND user_id = '$user_id' AND save_type = '$save_type'";

                if (!mysqli_query($conn, $qry)) {
                    errlog(mysqli_error($conn), $qry);
                    return false;
                }
            } else if ($decrease_quantity && $save_type == 'CART') {   // Decrease Quantity

                $qry = "SELECT * FROM cart_items WHERE user_id='$user_id' AND save_type='$save_type' AND status=1 AND item_id='$prod_id'";

                if (!$res = mysqli_query($conn, $qry)) {
                    errlog(mysqli_error($conn), $qry);
                    return false;
                }
                $res = mysqli_fetch_assoc($res);
                if ($res['qty'] > 1) {
                    $qry = "UPDATE cart_items set qty = qty-1 where item_id = '$prod_id' AND user_id = '$user_id' AND save_type = '$save_type' AND status=1";

                    if (!mysqli_query($conn, $qry)) {
                        errlog(mysqli_error($conn), $qry);
                        return false;
                    }
                }
            } else if ($setMultiQty > 0 && $save_type == 'CART') {   // Set Multiple Quantity

                $qry = "UPDATE cart_items set qty = '$setMultiQty', `status`='1' where item_id = '$prod_id' AND user_id = '$user_id' AND save_type = '$save_type'";

                if (!mysqli_query($conn, $qry)) {
                    errlog(mysqli_error($conn), $qry);
                    return false;
                }
            } else if ($save_type == 'CART') {  // Increase Quantity

                $qry = "UPDATE cart_items set `status` = '1', qty = qty+1 where item_id = '$prod_id' AND user_id = '$user_id' and save_type = '$save_type' and status=1";

                if (!mysqli_query($conn, $qry)) {
                    errlog(mysqli_error($conn), $qry);
                    return false;
                }
                return 'increamented';
            }

            return true;
        } else {

            $qty = 1;
            if ($setMultiQty > 0 && strtoupper($save_type) == 'CART') {
                $qty = $setMultiQty;
            }
            // Adding new product
            $qry = "SELECT count(*) from cart_items where `status` = '0' AND item_id = '$prod_id' AND user_id = '$user_id' AND save_type = '$save_type'";

            if (!$res = mysqli_query($conn, $qry)) {
                errlog(mysqli_error($conn), $qry);
                return false;
            }
            $res = mysqli_fetch_assoc($res);
            if ($res  &&  $res['count(*)'] != 0) {
                // updating previous entry
                $qry = "UPDATE cart_items set `status` = '1', qty = '$qty' where item_id = '$prod_id' AND user_id = '$user_id' AND save_type = '$save_type'";

                if (!mysqli_query($conn, $qry)) {
                    errlog(mysqli_error($conn), $qry);
                    return false;
                } else {
                    return true;
                }
            } else {
                // creating new entry 
                $qry = "INSERT INTO cart_items (user_id, save_type, item_id, status, qty, created_date) VALUES ('$user_id', '$save_type', '$prod_id', '1', '$qty', '$curr_date') ";
                if (!mysqli_query($conn, $qry)) {
                    errlog(mysqli_error($conn), $qry);
                    return false;
                } else {
                    return true;
                }
            }
        }
    } else {           // When User is not Login

        $wishId = realEscape($item_id);

        if (strtoupper($save_type) == 'WISHLIST') {

            if (isset($_SESSION['guestWishlist'])) {
                $prods = $_SESSION['guestWishlist'];
                $data = $_SESSION['guestWishlist'];

                $prods = explode(',', $prods);

                if (array_search($wishId, $prods) !== false) {

                    $new_wish = "";
                    if ($remove_if_exists) {
                        foreach ($prods as $prod) {
                            if ($prod == '') {
                                continue;
                            }
                            if ($prod == $wishId) {
                                continue;
                            }
                            if ($new_wish == '') {
                                $new_wish = $prod;
                            } else {
                                $new_wish .= "," . $prod;
                            }
                        }
                    } else {
                        $new_wish = $_SESSION['guestWishlist'];
                    }
                } else {
                    if ($_SESSION['guestWishlist'] == '') {
                        $new_wish =  $wishId;
                    } else {
                        $new_wish = $_SESSION['guestWishlist'] . "," . $wishId;
                    }
                }
                $_SESSION['guestWishlist'] = $new_wish;
                setcookie('guestWishlist', serialize($_SESSION['guestWishlist']), time() + 86400 * 28, '/');
            } else {

                $data = '';
                if (isset($_COOKIE['guestWishlist'])) {
                    $data = unserialize($_COOKIE['guestWishlist']);
                }

                if (array_search($wishId, explode(',', $data)) !== false) {
                    $new_wish = "";
                    if ($remove_if_exists) {
                        foreach (explode(',', $data) as $item) {
                            if ($item == '')   continue;
                            if ($item == $wishId)   continue;
                            if ($new_wish == '') {
                                $new_wish = $item;
                            } else {
                                $new_wish .= ',' . $item;
                            }
                        }
                    } else {
                        $new_wish = $data;
                    }

                    $data = $new_wish;
                    $_SESSION['guestWishlist'] = $data;
                    setcookie('guestWishlist', serialize($data), time() + 86400 * 28, '/');
                    return true;
                } else {
                    if ($data == '') {
                        $new_wish = $wishId;
                    } else {
                        $new_wish = $data . ',' . $wishId;
                    }

                    $data = $new_wish;
                    $_SESSION['guestWishlist'] = $data;
                    setcookie('guestWishlist', serialize($data), time() + 86400 * 28, '/');
                    return true;
                }
            }
        } else if (strtoupper($save_type) == 'CART') {

            if ($decrease_quantity) {

                if (isset($_COOKIE['guestCart'])) {

                    $data = unserialize($_COOKIE['guestCart']);
                    $quantity = unserialize($_COOKIE['guestCartQuantity']);

                    if ($quantity[$wishId] > 1) {
                        $quantity[$wishId] = $quantity[$wishId] - 1;
                    }

                    $_SESSION['guestCart'] = $data;
                    $_SESSION['guestCartQuantity'] = $quantity;

                    setcookie('guestCartQuantity', serialize($quantity), time() + 86400 * 28, '/');
                    setcookie('guestCart', serialize($data), time() + 86400 * 28, '/');
                    return true;
                }
            }

            if (true) {

                $data = '';
                $quantity = array();

                if (isset($_COOKIE['guestCart'])) {
                    $data = unserialize($_COOKIE['guestCart']);
                    $quantity = unserialize($_COOKIE['guestCartQuantity']);
                }

                if (array_search($wishId, explode(',', $data)) !== false) {
                    $new_wish = "";
                    $flag = '';
                    if ($remove_if_exists) {
                        foreach (explode(',', $data) as $item) {
                            if ($item == '')   continue;
                            if ($item == $wishId)   continue;
                            if ($new_wish == '') {
                                $new_wish = $item;
                            } else {
                                $new_wish .= ',' . $item;
                            }
                        }
                        $flag = 'rem';
                        $quantity[$wishId] = 0;
                    } else {
                        $new_wish = $data;

                        if ($setMultiQty > 0) {
                            $quantity[$wishId] = $setMultiQty;
                        } else {
                            $flag = 'inc';
                            $quantity[$wishId] = ((int) ($quantity[$wishId])) + 1;
                        }
                    }

                    $data = $new_wish;
                    $_SESSION['guestCart'] = $data;
                    setcookie('guestCartQuantity', serialize($quantity), time() + 86400 * 28, '/');
                    setcookie('guestCart', serialize($data), time() + 86400 * 28, '/');
                    if ($flag == 'inc') {
                        return 'increamented';
                    }
                    return true;
                } else {
                    if ($data == '') {
                        $new_wish = $wishId;
                    } else {
                        $new_wish = $data . ',' . $wishId;
                    }

                    $data = $new_wish;
                    $_SESSION['guestCart'] = $data;

                    if ($setMultiQty > 0) {
                        $quantity[$wishId] = $setMultiQty;
                    } else {
                        $quantity[$wishId] = 1;
                    }
                    setcookie('guestCartQuantity', serialize($quantity), time() + 86400 * 28, '/');
                    setcookie('guestCart', serialize($data), time() + 86400 * 28, '/');
                    return true;
                }
            }
        }
        return true;
    }
}
