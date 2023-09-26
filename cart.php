<?php
session_start();
include_once 'connection.php';
unset($_SESSION['APPLIED_COUPON']);

?>
<!DOCTYPE html>
<html lang="en">

<?php include 'site_head.php'; ?>

<body>
    <?php include 'header.php'; ?>

    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <a class="breadcrumb-item text-dark" href="#">Shop</a>
                    <span class="breadcrumb-item active">Shopping Cart</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->


    <!-- Cart Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-lg-8 table-responsive mb-5">
                <table class="table table-light table-borderless table-hover text-center mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        <?php
                        $total_mrp = $total_discount = 0;
                        $row_counter = 0;

                        if (isset($_SESSION['user_id'])) {

                            $user_id = $_SESSION['user_id'];

                            $qry = "SELECT * from cart_items where save_type = 'CART' AND user_id = '" . $user_id . "' AND qty > 0 AND status = '1' order by created_date desc";
                            $res = mysqli_query($conn, $qry);
                            if (!$res) {
                                errlog(mysqli_error($conn), $qry);
                            }
                            $in_cart = mysqli_fetch_all($res, MYSQLI_ASSOC);

                            foreach ($in_cart as $item) {
                                $redirect_link = '';
                                $redirect_link = 'product-detail?id=' . urlencode(base64_encode($item['item_id']));

                                $qry = "SELECT *, product.id as prod_id from product where product.id = '" . $item['item_id'] . "'";

                                $res = mysqli_query($conn, $qry);
                                if (!$res) {
                                    errlog(mysqli_error($conn), $qry);
                                }

                                $item_det = mysqli_fetch_assoc($res);

                                $row_counter++;
                        ?>
                                <?php
                                $price = $item_det['price'];
                                $discount = $item_det['discount'];
                                $single_item_discount = $discount;
                                $discount = $discount * $item['qty'];
                                $total_discount += $discount;

                                $total_mrp += $item_det['price'] * $item['qty'];
                                ?>
                                <tr>
                                    <td class="align-middle d-flex align-items-center justify-content-between"><a href="<?= $redirect_link; ?>"><img src="<?= $this_site_url . $item_det['pic'] ?>" alt="" style="width: 50px;"></a> <?= $item_det['product_name'] ?></td>
                                    <td class="align-middle">&#8377;<?php
                                                                    echo $price;
                                                                    ?></td>
                                    <td class="align-middle">
                                        <div class="input-group quantity mx-auto" style="width: 100px;">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary quantity-minus btn-minus" data-prod="<?php echo $item_det['prod_id'] ?>" data-price="<?php echo $item_det['price'] ?>" data-discount="<?php echo $single_item_discount; ?>">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <input type="text" class="form-control prodQuantity form-control-sm bg-secondary border-0 text-center" value="<?php echo (int)$item['qty'] ?>">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-plus quantity-plus" data-prod="<?php echo $item_det['prod_id'] ?>" data-price="<?php echo $item_det['price'] ?>" data-discount="<?php echo $single_item_discount; ?>">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle product-subtotal"><span class="product-price amountBlock"><?php
                                                                                                                        if ($discount > 0) {
                                                                                                                            echo "&#8377; " . htmlspecialchars(round(($item_det['price'] * $item['qty']) - ($discount), 2));
                                                                                                                        } else {
                                                                                                                            echo "&#8377; " . htmlspecialchars($item_det['price'] * $item['qty']);
                                                                                                                        }
                                                                                                                        ?></span>
                                        <span class="product-price discountBlock" style="margin: 1rem; color: grey; background-color: none;">
                                            <del>
                                                <?php
                                                if ($discount > 0) {
                                                    echo "&#8377; " . htmlspecialchars($item_det['price'] * $item['qty']);
                                                }
                                                ?>
                                            </del>
                                        </span>

                                    </td>
                                    <td class="align-middle"><button class="btn btn-sm btn-danger removeItem" data-id="<?php echo $item['item_id'] ?>"><i class="fa fa-times"></i></button></td>
                                </tr>
                            <?php
                            }
                        } else if (isset($_COOKIE['guestCart'])  &&  isset($_COOKIE['guestCartQuantity'])) {

                            $all_data = unserialize($_COOKIE['guestCart']);
                            $all_quantities = unserialize($_COOKIE['guestCartQuantity']);


                            foreach (explode(',', $all_data) as $id) {
                                if ($id == '')    continue;
                                $qry = "SELECT *, product.id as prod_id FROM product where id = '" . realEscape($id) . "' AND status = 1";
                                $res = mysqli_query($conn, $qry);
                                if (!$res) {
                                    errlog(mysqli_error($conn), $qry);
                                }
                                $item_det = mysqli_fetch_assoc($res);
                                if (!isset($item_det['id']))   continue;

                                $redirect_link = 'product-detail?id=' . urlencode(base64_encode($id));
                                $row_counter++;

                            ?>
                                <?php

                                $price = $item_det['price'];
                                $discount = $item_det['discount'];
                                $single_item_discount = $discount;
                                $discount = $discount * $all_quantities[$id];
                                $total_discount += $discount;

                                $total_mrp += $item_det['price'] * $all_quantities[$id];
                                ?>

                                <tr>
                                    <td class="align-middle d-flex align-items-center justify-content-between"><a href="<?= $redirect_link; ?>"><img src="<?= $this_site_url . $item_det['pic'] ?>" alt="" style="width: 50px;"></a> <?= $item_det['product_name'] ?></td>
                                    <td class="align-middle">&#8377;<?php echo $price; ?></td>
                                    <td class="align-middle">
                                        <div class="input-group quantity mx-auto" style="width: 100px;">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary quantity-minus btn-minus" data-prod="<?php echo $item_det['prod_id'] ?>" data-price="<?php echo $item_det['price'] ?>" data-discount="<?php echo $single_item_discount; ?>">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <input type="text" class="form-control prodQuantity form-control-sm bg-secondary border-0 text-center" value="<?php echo (int)$all_quantities[$id] ?>">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-primary btn-plus quantity-plus" data-prod="<?php echo $item_det['prod_id'] ?>" data-price="<?php echo $item_det['price'] ?>" data-discount="<?php echo $single_item_discount; ?>">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle product-subtotal"><span class="product-price amountBlock"><?php
                                                                                                                        if ($discount > 0) {
                                                                                                                            echo "&#8377; " . htmlspecialchars(round(($item_det['price'] * $all_quantities[$id]) - ($discount), 2));
                                                                                                                        } else {
                                                                                                                            echo "&#8377; " . htmlspecialchars($item_det['price'] * $all_quantities[$id]);
                                                                                                                        }
                                                                                                                        ?></span>
                                        <span class="product-price discountBlock" style="margin: 1rem; color: grey; background-color: none;">
                                            <del>
                                                <?php
                                                if ($discount > 0) {
                                                    echo "&#8377; " . htmlspecialchars($item_det['price'] * $all_quantities[$id]);
                                                }
                                                ?>
                                            </del>
                                        </span>

                                    </td>
                                    <td class="align-middle"><button class="btn btn-sm btn-danger removeItem" data-id="<?php echo $id ?>"><i class="fa fa-times"></i></button></td>
                                </tr>
                            <?php
                            }
                        }
                        if ($row_counter == 0) {
                            ?>
                            <tr>
                                <td colspan="5">
                                    <center>
                                        <h2>Cart is Empty</h2>
                                    </center>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>

                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <div class="mb-4">
                    <div class="input-group">
                        <input type="text" id="couponCode" name="coupon_code" class="form-control border-0 p-4" placeholder="Coupon Code">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary applyCouponBtn">Apply Coupon</button>
                        </div>
                    </div>
                </div>
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Cart Summary</span></h5>
                <div class="bg-light p-30 mb-5">
                    <div class="border-bottom pb-2">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Subtotal</h6>
                            <h6 id="subTotal">Rs <?php echo round($total_mrp, 2) ?></h6>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="font-weight-medium">Item Discount</h6>
                            <h6 class="font-weight-medium" id="itemDiscount">Rs <?php echo round($total_discount, 2) ?></h6>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="font-weight-medium">Coupon Discount</h6>
                            <h6 class="font-weight-medium" id="couponDiscountPreview">0 -/</h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="font-weight-medium">Shipping</h6>
                            <h6 class="font-weight-medium">Free</h6>
                        </div>
                    </div>
                    <div class="pt-2">
                        <div class="d-flex justify-content-between mt-2">
                            <h5>Total</h5>
                            <h5 id="grandTotal">Rs <?php echo round($total_mrp - $total_discount, 2) ?></h5>
                        </div>
                        <?php
                        if (getUserID() > 0) {
                        ?>
                            <a href="checkout" class="btn btn-block btn-primary checkoutBtn font-weight-bold my-3 py-3">Proceed To Checkout</a>

                        <?php
                        } else {
                        ?>
                            <a href="javascript:void(0)" class="btn btn-block btn-primary checkoutBtn font-weight-bold my-3 py-3">Proceed To Checkout</a>

                        <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Cart End -->

    <?php

    include_once 'footer.php';
    include_once 'common_scripts.php';
    ?>

    <script>
        'use strict';
        $(document).ready(function() {

            <?php

            if (getUserID() == -1) {
            ?>
                $(".checkoutBtn").on("click", function() {
                    Swal.fire("Error", "Please Login And Try Again...", "error");
                })
            <?php
            }

            ?>

            var total_price = parseFloat('<?php echo $total_mrp ?>');
            var couponDiscount = 0;

            $(".applyCouponBtn").on("click", function() {
                var coupon = $("#couponCode").val();
                couponDiscount = 0;
                // $("#couponCodePreview").html("--NA--");
                $("#couponDiscountPreview").html("0-/");
                $.ajax({
                    url: "cart_helper",
                    method: "POST",
                    data: {
                        applyCouponCode: coupon,
                        price: total_price,
                    },
                    success: function(data) {
                        console.log(data);
                        data = $.parseJSON(data);
                        if (data.success) {
                            // $("#couponCodePreview").html(coupon.toUpperCase());
                            couponDiscount = data.discount;
                            $("#couponDiscountPreview").html(couponDiscount + "-/");
                            Swal.fire("Coupon Applied", "", "success");
                        } else {
                            Swal.fire("Error", "Invalid Coupon", "error");
                        }
                        updateInvoice();
                    }
                })
            })

            $(".removeItem").on("click", function() {
                var id = $(this).data("id");
                var th = $(this);
                Swal.fire({
                    icon: "question",
                    title: "Confirmation",
                    text: "Do you really want to remove this item from your cart ?",
                    showConfirmButton: true,
                    confirmButtonText: "Yes, Remove it",
                    showDenyButton: true,
                    denyButtonText: "No",
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "cart_helper",
                            method: "post",
                            data: {
                                removeItem: id
                            },
                            success: function(data) {
                                if (data.trim() == '1') {
                                    Swal.fire("Removed", "Item removed from cart!", "success");
                                    $(th).parent().parent().remove();
                                } else {
                                    console.log(data);
                                    Swal.fire("Error", "Something went wrong", "error");
                                }
                            }
                        })
                    }
                })
            })


            function updateInvoice() {
                var price = 0;
                var discount = 0;
                let p = 0,
                    d = 0;
                $(".quantity-plus").each(function() {

                    p = parseFloat($(this).data("price"));
                    d = parseFloat($(this).data("discount"));

                    var q = $(this).parent().parent().children(".prodQuantity").val();

                    p *= q;
                    d *= q;

                    price += p;
                    discount += d;
                });

                total_price = price.toFixed(2);
                $("#subTotal").html((price).toFixed(2));
                $("#itemDiscount").html(discount.toFixed(2));
                $("#grandTotal").html((price - (discount + couponDiscount)).toFixed(2));

            }

            updateInvoice();


            $(".quantity-plus").on("click", function() {
                console.log("Plus");

                var curr = $(this).parent().parent().children(".prodQuantity").val();

                var id = $(this).data("prod");
                var price = parseFloat($(this).data("price"));
                var discount = parseFloat($(this).data("discount"));

                var th = $(this);
                // $(th).parent().parent().parent().parent().parent().siblings(".priceBlock").children("div").children("div").children(".discountBlock").children("del");
                // return;

                $.ajax({
                    url: "cart_helper",
                    method: "POST",
                    data: {
                        updateQuantity: "increase",
                        item: id,
                        curr: curr
                    },
                    success: function(data) {
                        if (data.trim() == 'out') {
                            Swal.fire("Stock limit reached for this item", "", "info");
                            return;
                        }

                        // console.log(data);

                        $(th).parent().parent().children('.prodQuantity').val(parseInt($(th).parent().parent().children('.prodQuantity').val()) + 1);

                        if (((parseInt(curr) + 1) * (parseFloat(price))).toFixed(2) > ((parseInt(curr) + 1) * (parseFloat(price)) - parseFloat(discount)).toFixed(2)) {
                            $(th).parent().parent().parent().siblings(".product-subtotal").children(".discountBlock").children("del").html("&#8377; " + ((parseInt(curr) + 1) * (parseFloat(price))).toFixed(2));
                        }


                        $(th).parent().parent().parent().siblings(".product-subtotal").children(".amountBlock").html("&#8377; " + (((parseInt(curr) + 1) * (parseFloat(price) - parseFloat(discount)))).toFixed(2));
                        updateInvoice();
                    }
                })
            })

            $(".quantity-minus").on("click", function() {
                // console.log("Minus");
                var curr = $(this).parent().parent().children(".prodQuantity").val();


                if (curr <= 1) {
                    return;
                }

                var th = $(this);

                $(th).parent().parent().children('.prodQuantity').val(parseInt($(th).parent().parent().children('.prodQuantity').val()) - 1);

                var id = $(this).data("prod");

                var price = parseFloat($(this).data("price"));
                var discount = parseFloat($(this).data("discount"));

                // $(this).parent().parent().children(".prodQuantity").each(function() {
                //     $(this).val(curr - 1);
                // });

                $.ajax({
                    url: "cart_helper",
                    method: "POST",
                    data: {
                        updateQuantity: "decrease",
                        item: id
                    },
                    success: function(data) {
                        if (((parseInt(curr) - 1) * (parseFloat(price))).toFixed(2) > ((parseInt(curr) - 1) * (parseFloat(price)) - parseFloat(discount)).toFixed(2)) {
                            $(th).parent().parent().parent().siblings(".product-subtotal").children(".discountBlock").children("del").html("&#8377; " + ((parseInt(curr) - 1) * (parseFloat(price))).toFixed(2));
                        }

                        $(th).parent().parent().parent().siblings(".product-subtotal").children(".amountBlock").html("&#8377; " + (((parseInt(curr) - 1) * (parseFloat(price) - parseFloat(discount)))).toFixed(2));

                        updateInvoice();
                    }
                })
            })
        })
    </script>

</body>

</html>