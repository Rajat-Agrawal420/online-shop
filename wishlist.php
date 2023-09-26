<?php
session_start();
include_once 'connection.php';
include_once 'use_cart_and_wishlist.php';


if (isset($_POST['item'])) {

    $item_id = realEscape($_POST['item']);

    $result = use_cart_n_wishlist($item_id, 'CART', false);
    $items = getCartItems();

    class Resp
    {
        public $num;
        public $res;

        function set_num($no)
        {
            $this->num = $no;
        }
        function set_resp($resp)
        {
            $this->res = $resp;
        }
    }
    $obj = new Resp();
    $obj->set_num($items);
    $obj->set_resp($result);
    echo json_encode($obj);
    die;
}
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
                    <span class="breadcrumb-item active">Wish List</span>
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
                            <th>Add to Cart</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        <?php
                        $total_mrp = $total_discount = 0;
                        $row_counter = 0;

                        if (isset($_SESSION['user_id'])) {

                            $user_id = $_SESSION['user_id'];

                            $qry = "SELECT * from cart_items where save_type = 'WISHLIST' AND user_id = '" . $user_id . "' AND qty > 0 AND status = '1' order by created_date desc";
                            $res = mysqli_query($conn, $qry);
                            if (!$res) {
                                errlog(mysqli_error($conn), $qry);
                            }
                            $in_cart = mysqli_fetch_all($res, MYSQLI_ASSOC);

                            foreach ($in_cart as $item) {
                                $qry = "";
                                $redirect_link = "#";

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

                                $total_discount += $discount;

                                $total_mrp += $item_det['price'];
                                ?>
                                <tr>
                                    <td class="align-middle d-flex align-items-center justify-content-around"><a href="<?= $redirect_link; ?>"><img src="<?= $this_site_url . $item_det['pic'] ?>" alt="" style="width: 50px;"></a> <span class="ml-4"><?= $item_det['product_name'] ?></span></td>
                                    <td class="align-middle">&#8377;<?php
                                                                    if ($single_item_discount > 0) {
                                                                        echo ($price - $single_item_discount);
                                                                    } else {
                                                                        echo $price;
                                                                    }  ?></td>

                                    <td class="align-middle"><button class="btn btn-sm btn-success" onclick="addCart(this)" value="<?= $item['item_id']; ?>"><i class="fa fa-shopping-cart"></i></button></td>
                                    <td class="align-middle"><button class="btn btn-sm btn-danger removeWish" data-id="<?php echo $item['item_id'] ?>"><i class="fa fa-times"></i></button></td>
                                </tr>
                            <?php
                            }
                        } else if (isset($_COOKIE['guestWishlist'])) {

                            $all_data = unserialize($_COOKIE['guestWishlist']);


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
                                $total_discount += $discount;

                                $total_mrp += $item_det['price'];
                                ?>

                                <tr>
                                    <td class="align-middle d-flex align-items-center justify-content-around"><a href="<?= $redirect_link; ?>"><img src="<?= $this_site_url . $item_det['pic'] ?>" alt="" style="width: 50px;"></a> <span class="ml-4"><?= $item_det['product_name'] ?></span></td>
                                    <td class="align-middle">&#8377;<?php
                                                                    if ($single_item_discount > 0) {
                                                                        echo ($price - $single_item_discount);
                                                                    } else {
                                                                        echo $price;
                                                                    }  ?></td>

                                    <td class="align-middle"><button class="btn btn-sm btn-success" onclick="addCart(this)" value="<?= $id ?>"><i class="fa fa-shopping-cart"></i></button></td>
                                    <td class="align-middle"><button class="btn btn-sm btn-danger removeWish" data-id="<?php echo $id ?>"><i class="fa fa-times"></i></button></td>
                                </tr>
                            <?php
                            }
                        }
                        if ($row_counter == 0) {
                            ?>
                            <tr>
                                <td colspan="5">
                                    <center>
                                        <h2>Wish List is Empty</h2>
                                    </center>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <!-- Cart End -->

    <?php

    include_once 'footer.php';
    include_once 'common_scripts.php';
    ?>

    <script>
        $(document).ready(function() {

            $(".removeWish").on("click", function() {
                var id = $(this).data("id");
                var th = $(this);
                Swal.fire({
                    icon: "question",
                    title: "Confirmation",
                    text: "Do you really want to remove this item from your Wish List ?",
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
                                removeWish: id
                            },
                            success: function(data) {
                                if (data.trim() == '1') {

                                    Swal.fire("Removed", "Item removed from Wish List!", "success");
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

        })

        function addCart(e) {
            let id = e.getAttribute("value");

            $.ajax({
                method: "POST",
                cache: false,
                data: {
                    item: id
                },
                success: function(data) {
                    console.log(data);
                    // return;
                    let obj = JSON.parse(data);
                    if (obj.res == true) {
                        // document.getElementsByClassName('cartCount').innerHTML = '<?= getCartItems() ?>';
                        // document.querySelectorAll('.cartCount').forEach(function(ele, index) {
                        //     ele.innerHTML = '<?= getCartItems() ?>';
                        // });
                        Swal.fire("Item added in Cart.", "", "success");
                    } else if (obj.res == false) {
                        Swal.fire("Error.", "", "error");
                    } else if (obj.res == 'increamented') {
                        Swal.fire("increamented", "", "success");
                    } else {
                        Swal.fire("Something went wrong", "", "error");
                    }

                }
            })
        }
    </script>

</body>

</html>