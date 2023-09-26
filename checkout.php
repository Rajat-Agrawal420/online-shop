<?php
session_start();
// print_r($_SESSION);
include 'connection.php';
require_once("razorpay/config.php");

if (getUserID() == -1) {
    header("Location: cart.php");
}

$user_id = getUserID();

$show_address_add_msg = 0;

/// Insert new address

if (isset($_POST['pincode'])) {
    $pincode = realEscape($_POST['pincode']);
    $address = realEscape($_POST['address1']) . "?" . "" . "?" . realEscape($_POST['address2']);
    $name = realEscape($_POST['firstName']) . "?" . realEscape($_POST['lastName']);
    $mobile = realEscape($_POST['mobileNumber']);
    $additional_mobile = '';
    $address_type = realEscape($_POST['addressType']);
    $availability = "";
    if (isset($_POST['availability'])) {
        foreach ($_POST['availability'] as $ava) {
            if ($availability == "") {
                $availability = realEscape($ava);
            } else {
                $availability .= "?" . realEscape($ava);
            }
        }
    }
    $landmark = realEscape($_POST['landmark']);

    $qry = "INSERT INTO `address`(`user_id`, `name`, `address`, `pincode`, `mobile`, `address_type`, `availability`, `created_date`, `landmark`) VALUES ('$user_id', '$name', '$address', '$pincode', '$mobile', '$address_type', '$availability', '$curr_date', '$landmark')";

    if (!mysqli_query($conn, $qry)) {
        errlog(mysqli_error($conn), $qry);
    } else {
        $show_address_add_msg = 1;
        echo 1;
    }

    die;
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include 'site_head.php'; ?>
<style>
    #paymentLoader {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        width: 100%;
        background: rgba(0, 0, 0, 0.75) no-repeat center center;
        z-index: 10000;
        color: white;
    }
</style>

<body>
    <?php include 'header.php'; ?>

    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <span class="breadcrumb-item active">Checkout</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->


    <!-- Checkout Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-8">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">SELECT Address</span></h5>
                <div class="bg-light p-30 mb-5">

                    <div class="row mb-3">
                        <?php
                        $sql = "SELECT * from address where user_id='$user_id'";
                        $res = mysqli_query($conn, $sql);
                        if (!$res) {
                            errlog(mysqli_error($conn), $sql);
                        }
                        $counter = 0;
                        $addresses = mysqli_fetch_all($res, MYSQLI_ASSOC);
                        foreach ($addresses as $address) {
                            // echo 1;
                            $counter++;
                        ?>
                            <div class="col-lg-3">
                                <div class="card client-card">
                                    <div class="float-end">
                                        <input type="radio" data-target-block="#addressPreviewBlock<?php echo $address['id'] ?>" class="addressSelection" name="addressSelection" value="<?php echo $address['id'] ?>" data-id="<?= $address['id']; ?>" style="height: 15px; width: 15px;" <?php echo ($counter == 1) ? "checked" : ""; ?>>
                                    </div>
                                    <div class="card-body text-center">

                                        <h5 class=" client-name fw-bold" style="margin-bottom:6px;"> <?php $str = $address['name'];
                                                                                                        $str = str_replace('?', " ", $str);
                                                                                                        echo htmlspecialchars($str); ?></h5>

                                        <span class="text-muted fw-semibold"><i class="la la-home me-2 text-secondary"></i>Type: <?= htmlspecialchars($address['address_type']); ?>

                                            <p class="text-muted text-center mb-2 fw-semibold"><?php $str = $address['address'];
                                                                                                $str = str_replace('?', " ", $str);
                                                                                                echo htmlspecialchars($str); ?></p>

                                            <span class="text-muted fw-semibold"><i class="la la-phone me-2 text-secondary"></i><?= htmlspecialchars($address['mobile']); ?></span>

                                        </span><br>

                                        <div class="col-sm-12 text-end mt-1">
                                            <span class="btn btn-danger btn-sm px-2 py-2 removeAddress" data-id="<?php echo $address['id'] ?>" style="margin-top: 4px;">Remove</span>
                                        </div>
                                    </div><!--end card-body-->
                                </div><!--end card-->
                            </div><!--end col-->
                        <?php } ?>
                    </div>
                    <form method="post" id="tempForm">
                        <div class="row">

                            <div class="col-md-6 form-group">
                                <label>First Name</label>
                                <input class="form-control" id="firstName" name="firstName" type="text" placeholder="John">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Last Name</label>
                                <input class="form-control" id="lastname" name="lastName" type="text" placeholder="Doe">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>E-mail</label>
                                <input class="form-control" id="email" name="email" type="text" placeholder="example@email.com">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Mobile No</label>
                                <input class="form-control" name="mobileNumber" id="mobileNumber" type="text" placeholder="+123 456 789">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Address Line 1</label>
                                <input class="form-control" id="address1" name="address1" type="text" placeholder="123 Street">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Address Line 2</label>
                                <input class="form-control" id="address2" name="address2" type="text" placeholder="123 Street">
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Land Mark</label>
                                <input class="form-control" id="landmark" name="landmark" type="text" placeholder="Land Mark">
                            </div>

                            <div class="col-md-6 form-group">
                                <label>ZIP Code</label>
                                <input class="form-control" id="pincode" name="pincode" type="text" placeholder="123">
                            </div>
                            <!-- <div class="col-md-12 form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="newaccount">
                                <label class="custom-control-label" for="newaccount">Create an account</label>
                            </div>
                        </div> -->
                            <!-- <div class="col-md-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="shipto">
                                <label class="custom-control-label" for="shipto" data-toggle="collapse" data-target="#shipping-address">Ship to different address</label>
                            </div>
                        </div> -->
                            <div class="col-md-6  form-group mt-1">
                                <div class="d-flex align-items-center">
                                    <label class="col-md-5 my-1 field-label control-label">Save Address As </label>
                                    <div class="col-md-2 text-left">
                                        <div class="form-check form-check-inline field-input">
                                            <input class="form-check-input" type="radio" name="addressType" id="inlineRadio1" value="Home" checked>
                                            <label class="form-check-label field-label" for="inlineRadio1">Home</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-left ml-4">
                                        <div class="form-check form-check-inline field-input">
                                            <input class="form-check-input" type="radio" name="addressType" id="inlineRadio2" value="Work">
                                            <label class="form-check-label field-label" for="inlineRadio2">Work</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <button type="button" id="addAddressBtn" class="btn btn-primary" style="font-weight: 500;">Add Address</button>

                            </div>

                        </div>
                    </form>
                </div>
                <div class="collapse mb-5" id="shipping-address">
                    <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Shipping Address</span></h5>
                    <div class="bg-light p-30">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>First Name</label>
                                <input class="form-control" type="text" placeholder="John">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Last Name</label>
                                <input class="form-control" type="text" placeholder="Doe">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>E-mail</label>
                                <input class="form-control" type="text" placeholder="example@email.com">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Mobile No</label>
                                <input class="form-control" type="text" placeholder="+123 456 789">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Address Line 1</label>
                                <input class="form-control" type="text" placeholder="123 Street">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Address Line 2</label>
                                <input class="form-control" type="text" placeholder="Land Mark">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Country</label>
                                <select class="custom-select">
                                    <option selected>United States</option>
                                    <option>Afghanistan</option>
                                    <option>Albania</option>
                                    <option>Algeria</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>City</label>
                                <input class="form-control" type="text" placeholder="New York">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>State</label>
                                <input class="form-control" type="text" placeholder="New York">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>ZIP Code</label>
                                <input class="form-control" type="text" placeholder="123">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Order Total</span></h5>
                <div class="bg-light p-30 mb-5">
                    <div class="border-bottom">
                        <h6 class="mb-3">Products</h6>
                        <?php
                        $cod_available = false;
                        $total_mrp = $total_discount = $ewallet_limit = $total_coupon_discount = $total_shipping_cost = 0;
                        $total_coupons_id = "";
                        $i = 0;

                        $qry = "SELECT * from cart_items where save_type = 'CART' AND user_id = '" . $user_id . "' AND qty > 0 AND status = '1' order by created_date desc";
                        $res = mysqli_query($conn, $qry);
                        if (!$res) {
                            errlog(mysqli_error($conn), $qry);
                        }
                        $in_cart = mysqli_fetch_all($res, MYSQLI_ASSOC);

                        foreach ($in_cart as $item) {

                            $i++;
                            $qry = "SELECT *, product.id as prod_id from product where product.id = '" . $item['item_id'] . "'";

                            $res = mysqli_query($conn, $qry);
                            if (!$res) {
                                errlog(mysqli_error($conn), $qry);
                            }

                            $item_det = mysqli_fetch_assoc($res);
                            if (!isset($item_det['id'])) {
                                echo $qry . "<br>";
                            }

                            // if (!isset($item_det['ewallet_limit'])) {
                            //     $item_det['ewallet_limit'] = -1;
                            // }

                            // if ($item_det['ewallet_limit'] != -1) {
                            //     $ewallet_limit += ((float) (htmlspecialchars($item_det['ewallet_limit']))) * $item['quantity'];
                            // } else {
                            //     $ewallet_limit += ((float) (htmlspecialchars($item_det['price'])) * 50 / 100) * $item['quantity'];
                            // }

                            $price = $item_det['price'];
                            $discount = $item_det['discount'];
                            $single_item_discount = $discount;
                            $discount = $discount * $item['qty'];

                            $total_discount += $discount;

                            $total_mrp += (float)($price * $item['qty']);

                        ?>
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center" style="width: 300px;">
                                    <img src="<?= $this_site_url . $item_det['pic']; ?>" alt="" style="width: 50px;">
                                    <p style="margin:auto;"><?php echo substr(htmlspecialchars($item_det['product_name']), 0, 20) . "..."; ?></p>
                                </div>

                                <div class="d-flex  align-items-center">
                                    <p>&#8377;<?php
                                                if ($single_item_discount > 0) {
                                                    echo ($price - $single_item_discount) . " x " . $item['qty'];
                                                } else {
                                                    echo $price . " x " . $item['qty'];
                                                }  ?></p>
                                </div>

                            </div>
                        <?php } ?>
                    </div>
                    <div class="border-bottom pt-3 pb-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Subtotal</h6>
                            <h6>₹ <?php echo $total_mrp ?></h6>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Item Discount</h6>
                            <h6>₹ <?php echo $total_discount ?></h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Shipping</h6>
                            <h6 class="font-weight-medium">Free</h6>
                        </div>
                        <?php
                        if (isset($_SESSION['APPLIED_COUPON']['id'])) {
                            $price = $total_mrp;
                            $data = $_SESSION['APPLIED_COUPON'];
                            $discount = 0;
                            $data['discount'] = (float)($data['discount']);
                            if ($data['discount_type'] == '%') {
                                $discount = round($price * $data['discount'] / 100, 2);
                            } else {
                                $discount = $data['discount'];
                            }

                            $total_coupon_discount = $discount;

                        ?>
                            <div class="d-flex justify-content-between">
                                <h6 class="">Coupon Code:</h6>
                                <h6 class=""><?php echo strtoupper($data['coupon_code']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6 class="">Coupon Discount:</h6>
                                <h6 class="">₹ <?php echo $discount ?></h6>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="d-flex justify-content-between">
                                <h6 class="">Coupon Code:</h6>
                                <h6 class="">--NA--</h6>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6 class="">Coupon Discount:</h6>
                                <h6 class="">&#8377; 0</h6>
                            </div>

                        <?php
                        }
                        ?>
                    </div>
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total</h5>
                            <h5 id="grandTotal" data-value="<?php echo round((float)($total_mrp - ($total_discount + $total_coupon_discount)), 2) ?>">₹ <?php echo round((float)($total_mrp - ($total_discount + $total_coupon_discount)), 2) ?></h5>
                        </div>
                    </div>
                </div>
                <div class="mb-5">
                    <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Payment</span></h5>
                    <div class="bg-light p-30">
                        <div class="form-check">
                            <div class="">
                                <input type="radio" class="form-check-input" name="payment" id="Razorpay" checked>
                                <label class="form-check-label" for="Razorpay">Razorpay</label>
                            </div>
                        </div>

                        <div class="form-check">
                            <div class="">
                                <input type="radio" class="form-check-input" name="payment" id="cod">
                                <label class="form-check-label" for="cod">COD</label>
                            </div>
                        </div>
                        <div class="form-check mb-4">
                            <div class="">
                                <input type="radio" class="form-check-input" name="payment" id="banktransfer">
                                <label class="form-check-label" for="banktransfer">Bank Transfer</label>
                            </div>
                        </div>

                        <button id="placeOrderBtn" class="btn btn-block btn-primary font-weight-bold py-3">Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Checkout End -->

    <div id="paymentLoader">
        <div class="row">
            <div class="col-sm-12 text-center">
                <div class="spinner-border" style="height: 4rem; width: 4rem; color: #007bff; "></div>
            </div>
            <div class="col-sm-12 text-center">
                Making Payment...
            </div>

        </div>
    </div>

    <?php

    include 'footer.php';
    include 'common_scripts.php';
    ?>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        'use strict';

        $(document).ready(function() {

            $("#placeOrderBtn").on("click", function() {


                var address = -1;
                $(".addressSelection:checked").each(function() {
                    address = $(this).val();
                })
                if (address == -1) {
                    Swal.fire("Error", "Select Address", "error");
                    return false;
                }

                <?php
                if ($i == 0) {
                ?>
                    Swal.fire("Error", "No Items in Cart to Buy.", "error");
                    return false;
                <?php
                }
                ?>

                var payment_mode = 'RAZOR-PAY';
                if ($("#cod") && $("#cod").is(":checked")) {
                    payment_mode = 'COD';
                }

                var formData = new FormData();
                formData.append("make_payment", true);

                var targetURL = './buy_product.php';
                var finalKey = 'verifyPayment';
                var successMsg = 'Order has been placed successfully';
                var errorMsg = 'Something went wrong';
                var errorLink, successLink;
                var confirm_msg = false;
                successLink = "thank_you.php";
                errorLink = '<?php echo explode('.', $_SERVER['PHP_SELF'])[0] . "?e=" . urlencode(base64_encode("error")) ?>';

                $.ajax({
                    url: "cart_helper.php",
                    method: "POST",
                    data: {
                        address: address,
                        payment_mode: payment_mode,
                    },
                    success: function(data) {

                        var flag = true;
                        $.ajax({
                            url: targetURL,
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                $("#paymentLoader").css("display", "flex");
                            },
                            success: function(response) {
                                $("#paymentLoader").css("display", "none");
                                console.log(response);
                                var response = $.parseJSON(response);
                                if (response.cod) {
                                    Swal.fire(successMsg, "", "success");
                                    if (successLink != false) {
                                        location.href = successLink;
                                    } else {
                                        setTimeout(() => {
                                            location.reload();
                                        }, 2000);
                                    }

                                    return;
                                }

                                if (response.error) {
                                    Swal.fire(errorMsg, response.error, "error");
                                    flag = false;
                                    return;
                                }

                                var options = {
                                    "key": "<?php echo RAZORPAY_KEY ?>",
                                    "amount": response.amount,
                                    "currency": "INR",
                                    "name": "<?php echo htmlspecialchars(getUserInfo($user_id)['name']) ?>",
                                    "description": "Test Transaction",
                                    "image": "",
                                    "order_id": response.id,
                                    "handler": function(response) {
                                        $("#paymentLoader").css("display", "flex");
                                        var formData = new FormData();
                                        formData.append(finalKey, response);
                                        $.ajax({
                                            url: targetURL,
                                            method: "POST",
                                            data: formData,
                                            processData: false,
                                            contentType: false,
                                            success: function(data) {
                                                $("#paymentLoader").css("display", "none");
                                                if (data.trim() == '1') {
                                                    if (confirm_msg) {
                                                        Swal.fire({
                                                            title: "Confirmation",
                                                            icon: confirm_icon,
                                                            text: confirm_msg,
                                                            showConfirmButton: true,
                                                            confirmButtonText: "Yes",
                                                            showCancelButton: true,
                                                            cancelButtonText: "No, Skip",
                                                        }).then(result => {
                                                            if (result.isConfirmed) {
                                                                if (accept == 'reload')
                                                                    location.reload();
                                                            } else {
                                                                location.href = deny;
                                                            }
                                                        });
                                                    } else {
                                                        if (successMsg !== false)
                                                            Swal.fire(successMsg, "", "success");
                                                        if (successLink != false) {
                                                            location.href = successLink;
                                                        } else {
                                                            setTimeout(() => {
                                                                location.reload();
                                                            }, 2000);
                                                        }
                                                    }
                                                    return true;
                                                } else {
                                                    flag = false;
                                                    console.log(data);
                                                    Swal.fire(errorMsg, "", "error");
                                                    if (errorLink !== false) {
                                                        location.href = errorLink;
                                                    }
                                                    return false;
                                                }
                                            }
                                        })
                                    },
                                    "prefill": {
                                        "name": "<?php echo htmlspecialchars(getUserInfo($user_id)['name']) ?>",
                                        "email": "<?php echo htmlspecialchars(getUserInfo($user_id)['email']) ?>",
                                        "contact": "<?php echo htmlspecialchars(getUserInfo($user_id)['mobile']) ?>",
                                    },
                                    "notes": {
                                        "address": "Razorpay Corporate Office"
                                    },
                                    "theme": {
                                        "color": "#3399cc"
                                    }
                                };

                                var rzp1 = new Razorpay(options);
                                rzp1.on('payment.failed', function(response) {
                                    alert(response.error.code);
                                    alert(response.error.description);
                                    alert(response.error.source);
                                    alert(response.error.step);
                                    alert(response.error.reason);
                                    alert(response.error.metadata.order_id);
                                    alert(response.error.metadata.payment_id);
                                });

                                rzp1.open();
                            },
                            error: function() {
                                flag = false;
                            }
                        });


                    }
                })

            })

            var flag = '<?php echo $show_address_add_msg ?>';
            if (flag == '1') {
                Swal.fire(
                    'Success!',
                    "Address added Successfully",
                    'success'
                )
            }

            function formValidator() {
                // console.log("enter");
                if ($("#firstName").val().trim().length < 2) {
                    return ("First name is mandatory.");
                }

                if ($("#mobileNumber").val().trim().length < 10) {
                    return ("Enter Valid Mobile Number ");
                }

                if ($("#address1").val().trim().length < 2) {
                    return ("Street address is mandatory.");
                }

                if ($("#landmark").val().trim().length < 2) {
                    return ("Land Mark is required.");
                }

                if ($("#pincode").val().trim().length < 6 || $("#pincode").val().trim().length > 6) {
                    return ("Enter valid pincode.");
                }

                var flag = false;
                $('input[name="addressType"]:checked').each(function() {
                    flag = true;
                })

                if (!flag) {
                    return "Select Address Type.";
                }

                return true;
            }

            $(document).on("click", "#addAddressBtn", function(event) {

                event.preventDefault();

                var error = formValidator();
                if (error === true) {

                    $.ajax({
                        url: 'checkout',
                        method: "POST",
                        data: $("#tempForm").serialize(),
                        success: function(data) {
                            if (data.trim() == '1') {
                                $("#tempForm")[0].reset();
                                Swal.fire("Address Added Successfully.", "", "success");
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            } else {
                                Swal.fire(
                                    'Something went wrong!',
                                    '',
                                    'error'
                                )
                            }
                        }
                    })
                } else {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    )
                }
            })

            $(".removeAddress").on("click", function() {
                var id = $(this).data("id");
                var th = $(this);
                Swal.fire({
                    title: 'Are you sure ?',
                    text: "Do you really want to remove this address ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Remove it!',
                    cancelButtonText: 'No, Cancel!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "cart_helper",
                            method: "POST",
                            data: {
                                removeAddress: id
                            },
                            success: function(data) {
                                if (data.trim() == '1') {
                                    th.parent().parent().parent().parent().remove();
                                    Swal.fire(
                                        'Success!',
                                        "Address removed successfully",
                                        'success'
                                    )
                                } else {
                                    console.log(data);
                                    Swal.fire(
                                        'Error!',
                                        "Can't remove address at the moment",
                                        'error'
                                    )
                                }
                            }
                        })
                    }
                })
            })

        })
    </script>
</body>

</html>