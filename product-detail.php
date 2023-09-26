<?php
session_start();
include 'connection.php';
include_once 'use_cart_and_wishlist.php';

if (isset($_GET['id'])) {
    $p_id = realEscape(urldecode(base64_decode($_GET['id'])));
}

$sql = "SELECT * FROM product WHERE id='$p_id'";

$result = mysqli_query($conn, $sql);
if (!$result) {
    errlog(mysqli_error($conn), $sql);
}
$detail = mysqli_fetch_assoc($result);


if (isset($_POST['count'])) {

    $sql = "update product set views= views + 1 where id='$p_id'";
    $rs = mysqli_query($conn, $sql);
    if (!$rs) {
        errlog(mysqli_error($conn), $sql);
    }

    // recently viewed products logic
    if (!isset($_COOKIE['recent_items'])) {
        $recent_data = array();
    }
    if (isset($_COOKIE['recent_items']) && $_COOKIE['recent_items'] == null) {
        $recent_data = array();
    }
    if (isset($_COOKIE['recent_items']) && $_COOKIE['recent_items'] != null) {
        $recent_data = json_decode($_COOKIE['recent_items'], true);
    }

    $items = array(
        'id' => $p_id,
        'visit_time' => $curr_date
    );

    $ids = array_column($recent_data, 'id');

    if (in_array($p_id, $ids)) {
        $flag = 0;
        foreach ($recent_data as $key => $value) {
            if ($recent_data[$key]['id'] == $p_id) {  // update visit time
                $flag = 1;
                $recent_data[$key]['visit_time'] = $curr_date;
                $data = json_encode($recent_data);
                setcookie('recent_items', $data, time() + (86400 * 30), "/");
            }
        }
    } else {
        $recent_data[] = $items;
        $data = json_encode($recent_data);
        setcookie('recent_items', $data, time() + (86400 * 30), "/");
    }

    // delete 2 days old products

    if (isset($_COOKIE['recent_items']) && $_COOKIE['recent_items'] == null) {
        $recent_data = array();
    }
    if (isset($_COOKIE['recent_items']) && $_COOKIE['recent_items'] != null) {
        $recent_data = json_decode($_COOKIE['recent_items'], true);
    }

    foreach ($recent_data as $key => $value) {
        $product_time = $recent_data[$key]['visit_time'];
        if (date("Y-m-d H:i:s", strtotime($curr_date . "-2 days")) > date("Y-m-d H:i:s", strtotime($product_time))) {

            unset($recent_data[$key]);
            $recent_data = array_values($recent_data); // 'reindex' array
            $data = json_encode($recent_data);
            setcookie('recent_items', $data, time() + (86400 * 30), "/");
        }
    }
    die;
}

if (isset($_POST['item']) && isset($_POST['qty'])) {

    $item_id = realEscape($_POST['item']);
    $qty = realEscape($_POST['qty']);

    $res = '';
    $res = use_cart_n_wishlist($item_id, 'CART', false, false, $qty);

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
    $obj->set_resp($res);
    echo json_encode($obj);
    die;
}
if (isset($_POST['wish'])) {

    $item_id = realEscape($_POST['wish']);

    $result = use_cart_n_wishlist($item_id, 'WISHLIST');
    if ($result)
        echo 1;
    else
        echo 0;
    die;
}

if (isset($_POST['message'], $_POST['name'], $_POST['email2'], $_POST['rating'])) {

    $msg = realEscape($_POST['message']);
    $name = realEscape($_POST['name']);
    $email = realEscape($_POST['email2']);
    $rate = realEscape($_POST['rating']);
    $u_id = getUserID();

    $sql = "INSERT into product_rating(item_id,user_id,rating,title,comment,status,created,modified) values('$p_id','$u_id','$rate','','$msg','1','$curr_date','$curr_date')";

    $res1 = mysqli_query($conn, $sql);
    if (!$res1) {
        errlog(mysqli_error($conn), $sql);
        echo 0;
    } else {
        echo 1;
    }
    die;
}

$flag = 0;
if (isset($_SESSION['user_id'])) {

    $id = realEscape($detail['id']);
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT od.* FROM orders o INNER JOIN order_detail od ON o.id=od.order_id WHERE o.user_id='" . $user_id . "' AND o.payment_status='PAID' AND od.item_id='$id'";
    $res2 = mysqli_query($conn, $sql);
    if (!$res2) {
        errlog(mysqli_error($conn), $sql);
    }
    if (mysqli_num_rows($res2) > 0) {
        $flag = 1;
    }
}
if (getUserID() <= 0) {

    $flag = -1;
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
                    <span class="breadcrumb-item active">Shop Detail</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->


    <!-- Shop Detail Start -->
    <div class="container-fluid pb-5">
        <div class="row px-xl-5">
            <div class="col-lg-5 h-auto mb-30">
                <div id="product-carousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner bg-light">
                        <div class="carousel-item active d-flex align-items-center justify-content-center" style="height: 450px;;">
                            <div style="width: 55%;" class="img-zoom-container">
                                <img class="w-100" id="myimage" src="<?= $this_site_url . $detail['pic']; ?>" alt="Image">

                                <div id="myresult" class="img-zoom-result"></div>
                            </div>
                        </div>
                        <!-- <div class="carousel-item">
                            <img class="w-100 h-100" src="img/product-2.jpg" alt="Image">
                        </div>
                        <div class="carousel-item">
                            <img class="w-100 h-100" src="img/product-3.jpg" alt="Image">
                        </div>
                        <div class="carousel-item">
                            <img class="w-100 h-100" src="img/product-4.jpg" alt="Image">
                        </div> -->
                    </div>
                    <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                        <i class="fa fa-2x fa-angle-left text-dark"></i>
                    </a>
                    <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                        <i class="fa fa-2x fa-angle-right text-dark"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-7 h-auto mb-30">
                <div class="h-100 bg-light p-30">
                    <h3><?= $detail['product_name']; ?></h3>
                    <div class="d-flex mb-3">
                        <div class="text-primary mr-2">
                            <?php
                            $i = 0;
                            $avgRating = getProductRating($p_id)[0];
                            while ($avgRating) {
                                $i++;
                                $avgRating--;
                            ?>
                                <small class="fas fa-star"></small>
                            <?php }
                            if ($i == 0) {
                            ?>
                                <small class="far fa-star"></small>
                                <small class="far fa-star"></small>
                                <small class="far fa-star"></small>
                                <small class="far fa-star"></small>
                                <small class="far fa-star"></small>
                            <?php } ?>
                        </div>
                        <small class="pt-1">(<?= getProductRating($p_id)[1]; ?> Reviews)</small>
                    </div>
                    <h3 class="font-weight-semi-bold mb-4">&#8377; <?php
                                                                    if ($detail['discount'] > 0) {
                                                                        echo number_format($detail['price'] - $detail['discount']);
                                                                    } else {
                                                                        echo number_format($detail['price']);
                                                                    }
                                                                    ?></h3>
                    <p class="mb-4"><?= decoder($detail['description']); ?></p>
                    <div class="d-flex mb-3">
                        <strong class="text-dark mr-3">Sizes:</strong>
                        <form>
                            <?php
                            $qry = "SELECT DISTINCT size FROM product WHERE status=1 AND size IS NOT NULL AND size <> '' LIMIT 5";
                            $result = mysqli_query($conn, $qry);
                            if (!$result) {
                                errlog(mysqli_error($conn), $qry);
                            }
                            $i = 0;
                            while ($data = mysqli_fetch_assoc($result)) {
                                $i++;
                            ?>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="size-<?= $data['size']; ?>" name="size" <?php if ($detail['size'] == $data['size']) echo "checked";
                                                                                                                                    if ($i == 1) echo "checked"; ?>>
                                    <label class="custom-control-label" for="size-<?= $data['size']; ?>"><?= $data['size']; ?></label>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                    <div class="d-flex mb-4">
                        <strong class="text-dark mr-3">Colors:</strong>
                        <form>
                            <?php
                            $qry = "SELECT DISTINCT color FROM product WHERE status=1 AND color IS NOT NULL AND color <> '' ORDER BY color ASC LIMIT 5";
                            $result = mysqli_query($conn, $qry);
                            if (!$result) {
                                errlog(mysqli_error($conn), $qry);
                            }
                            $i = 0;
                            while ($data = mysqli_fetch_assoc($result)) {
                                $i++;
                            ?>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="color-<?= $data['color']; ?>" name="color" <?php if ($detail['color'] == $data['color']) echo "checked";
                                                                                                                                    if ($i == 1) echo "checked"; ?>>
                                    <label class="custom-control-label" for="color-<?= $data['color']; ?>"><?= $data['color']; ?></label>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                    <div class="d-flex align-items-center mb-4 pt-2">
                        <div class="input-group quantity mr-3" style="width: 130px;">
                            <div class="input-group-btn">
                                <button class="btn btn-primary btn-minus">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <?php
                            $itemQuantity = 1;
                            if (isset($_SESSION['user_id'])) {
                                $user_id = getUserID();
                                $qry = "SELECT * from cart_items where `status` = '1' AND user_id = '$user_id' and save_type = 'CART' AND item_id='$p_id'";

                                if (!$res = mysqli_query($conn, $qry)) {
                                    errlog(mysqli_error($conn), $qry);
                                }
                                $wishes = mysqli_fetch_assoc($res);
                                if (isset($wishes['id']))
                                    $itemQuantity = $wishes['qty'];
                            } else {

                                $data = '';
                                if (isset($_COOKIE['guestCart']) && isset($_COOKIE['guestCartQuantity'])) {
                                    $data = unserialize($_COOKIE['guestCart']);
                                    $quantity = unserialize($_COOKIE['guestCartQuantity']);

                                    $arr = explode(',', $data);

                                    if (in_array($p_id, $arr)) {
                                        $itemQuantity = $quantity[$p_id];
                                    }
                                }
                            }
                            ?>
                            <input type="text" id="itemQty" class="form-control bg-secondary border-0 text-center" value="<?= $itemQuantity ?>">
                            <div class="input-group-btn">
                                <button class="btn btn-primary btn-plus">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button class="btn btn-primary px-3" onclick="addCart(this)" value="<?= $detail['id']; ?>"><i class="fa fa-shopping-cart mr-1"></i> Add To
                            Cart</button>
                    </div>
                    <div class="d-flex pt-2">
                        <strong class="text-dark mr-2">Share on:</strong>
                        <div class="d-inline-flex">
                            <a class="text-dark px-2" href="http://facebook.com/sharer.php?u=<?php echo $this_site_url . '/product-detail?id=' . urlencode(base64_encode($p_id)); ?>">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a class="text-dark px-2" href="http://twitter.com/share?text=share&url=<?php echo $this_site_url . '/product-detail?id=' . urlencode(base64_encode($p_id)); ?>">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a class="text-dark px-2" href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php echo $this_site_url . '/product-detail?id=' . urlencode(base64_encode($p_id)); ?>">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a class="text-dark px-2" href="">
                                <i class="fab fa-pinterest"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row px-xl-5">
            <div class="col">
                <div class="bg-light p-30">
                    <div class="nav nav-tabs mb-4">
                        <?php
                        $sql = "SELECT count(*) from product_rating where item_id='$p_id' and status=1";
                        $rating_res = mysqli_query($conn, $sql);
                        if (!$rating_res) {
                            errlog(mysqli_error($conn), $sql);
                        }
                        ?>
                        <a class="nav-item nav-link text-dark active" data-toggle="tab" href="#tab-pane-1">Description</a>
                        <a class="nav-item nav-link text-dark" data-toggle="tab" href="#tab-pane-2">Information</a>
                        <a class="nav-item nav-link text-dark" data-toggle="tab" href="#tab-pane-3">Reviews (<?= mysqli_fetch_assoc($rating_res)['count(*)']; ?>)</a>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-pane-1">
                            <h4 class="mb-3">Product Description</h4>
                            <p><?= decoder($detail['description']); ?></p>

                        </div>
                        <div class="tab-pane fade" id="tab-pane-2">
                            <h4 class="mb-3">Additional Information</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0">
                                            Size :
                                        </li>
                                        <li class="list-group-item px-0">
                                            Colour :
                                        </li>
                                        <li class="list-group-item px-0">
                                            Availability :
                                        </li>
                                        <li class="list-group-item px-0">
                                            Views :
                                        </li>
                                        <li class="list-group-item px-0">
                                            Category :
                                        </li>
                                        <li class="list-group-item px-0">
                                            Brand :
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0">
                                            <?php if ($detail['size'] == NULL) echo "Not Mentioned";
                                            else echo $detail['size']; ?>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <?php if ($detail['color'] == NULL) echo "Not Mentioned";
                                            else echo $detail['color']; ?>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <?php if ($detail['availability'] == NULL) echo "Not Mentioned";
                                            else echo $detail['availability']; ?>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <?php if ($detail['views'] == NULL) echo "0";
                                            else echo $detail['views']; ?>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <?php if ($detail['Category'] == NULL) echo "Not Mentioned";
                                            else echo $detail['Category']; ?>
                                        </li>
                                        <li class="list-group-item px-0">
                                            <?php if ($detail['brand'] == NULL) echo "Not Mentioned";
                                            else echo $detail['brand']; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-pane-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $sql = "SELECT * from product_rating where item_id='$p_id' and status=1 order by created desc;";
                                    $rating_res = mysqli_query($conn, $sql);
                                    if (!$rating_res) {
                                        errlog(mysqli_error($conn), $sql);
                                    }
                                    ?>
                                    <h4 class="mb-4"><?= mysqli_num_rows($rating_res) ?> review for <?= $detail['product_name'] ?></h4>

                                    <?php
                                    if (mysqli_num_rows($rating_res) > 0) {
                                        while ($r = mysqli_fetch_assoc($rating_res)) {
                                    ?>
                                            <div class="media mb-4">
                                                <img src="<?php
                                                            if (getUserInfo($r['user_id'])['pic'] == NULL) {
                                                                echo $avatar2;
                                                            } else {
                                                                echo $this_site_url . getUserInfo($r['user_id'])['pic'];
                                                            }  ?>" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                                                <div class="media-body">
                                                    <h6><?= getUserInfo($r['user_id'])['name'] ?? 'Guest'; ?><small> - <i><?php echo date("d F, Y h:m A", strtotime(htmlspecialchars($r['created']))); ?></i></small></h6>
                                                    <div class="text-primary mb-2">

                                                        <?php
                                                        $rate_number = $r['rating'];
                                                        while ($rate_number) {
                                                        ?>
                                                            <i class="far fa-star"></i>
                                                        <?php
                                                            $rate_number--;
                                                        } ?>
                                                        <!-- <i class="fas fa-star-half-alt"></i> -->

                                                    </div>
                                                    <p><?= htmlspecialchars($r['comment']); ?></p>
                                                </div>
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <div class="media mb-4">
                                            <img src="img/user.jpg" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;">
                                            <div class="media-body">
                                                <h6>John Doe<small> - <i>01 Jan 2045</i></small></h6>
                                                <div class="text-primary mb-2">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                    <i class="far fa-star"></i>
                                                </div>
                                                <p>Diam amet duo labore stet elitr ea clita ipsum, tempor labore accusam ipsum et no at. Kasd diam tempor rebum magna dolores sed sed eirmod ipsum.</p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="mb-4">Leave a review</h4>
                                    <small>Your email address will not be published. Required fields are marked *</small>
                                    <form id="review_form" method="post" action="#">
                                        <div class="d-flex my-3">
                                            <p class="mb-0 mr-2">Your Rating * :</p>
                                            <div class="text-primary">
                                                <ul class="stars">
                                                    <li data-id="1"><i class="far fa-star"></i></li>
                                                    <li data-id="2"><i class="far fa-star"></i></li>
                                                    <li data-id="3"><i class="far fa-star"></i></li>
                                                    <li data-id="4"><i class="far fa-star"></i></li>
                                                    <li data-id="5"><i class="far fa-star"></i></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="message">Your Review *</label>
                                            <textarea name="message" id="message" cols="30" rows="5" class="form-control" placeholder="Comment Here.."></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Your Name *</label>
                                            <input type="text" id="name" name="name" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Your Email *</label>
                                            <input type="email" id="email_ID" name="email2" class="form-control">
                                        </div>
                                        <div class="form-group mb-0">
                                            <input type="hidden" id="rating" name="rating" value="0">
                                            <input type="submit" value="Leave Your Review" class="btn btn-primary px-3">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Shop Detail End -->


    <!-- Products Start -->
    <div class="container-fluid py-5">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">You May Also Like</span></h2>
        <div class="row px-xl-5">
            <div class="col">
                <div class="owl-carousel related-carousel">

                    <?php
                    $cat = realEscape($detail['Category']);
                    $qry = "SELECT * FROM product WHERE Category='$cat' AND id <> '" . realEscape($detail['id']) . "' ORDER BY created_date DESC LIMIT 4";
                    $res = mysqli_query($conn, $qry);
                    if (!$res) {
                        errlog(mysqli_error($conn), $qry);
                    }
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                    ?>
                            <!-- <div class="col-lg-2 col-md-4 col-sm-6 pb-1"> -->
                            <div class="product-item bg-light">
                                <div class="product-img position-relative overflow-hidden w-75 text-center mx-auto">
                                    <img class="img-fluid w-100" src="<?= $this_site_url . $row['pic'] ?? ''; ?>" alt="product-image">
                                    <div class="product-action">
                                        <a class="btn btn-outline-dark btn-square" onclick="addCart(this)" value="<?= $row['id']; ?>" href="javascript:void(0)"><i class="fa fa-shopping-cart"></i></a>
                                        <a class="btn btn-outline-dark btn-square" onclick="addWishlist(this)" value="<?= $row['id']; ?>" href="javascript:void(0)"><i class="far fa-heart"></i></a>
                                        <a class="btn btn-outline-dark btn-square" href="javascript:void(0)"><i class="fa fa-sync-alt"></i></a>
                                        <a class="btn btn-outline-dark btn-square" href="product-detail?id=<?= urlencode(base64_encode($row['id'])); ?>"><i class="fa fa-search"></i></a>
                                    </div>
                                </div>
                                <div class="text-center py-4">
                                    <a class="h6 text-decoration-none text-truncate" href="product-detail?id=<?= urlencode(base64_encode($row['id'])); ?>"><?php
                                                                                                                                                            if (strlen($row['product_name'] > 25)) {
                                                                                                                                                                echo  substr($row['product_name'], 0, 25) . "...";
                                                                                                                                                            } else {
                                                                                                                                                                echo  $row['product_name'];
                                                                                                                                                            }
                                                                                                                                                            ?></a>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <h5>&#8377;<?php
                                                    if ($row['discount'] > 0) {
                                                        echo number_format($row['price'] - $row['discount']);
                                                    } else {
                                                        echo number_format($row['price']);
                                                    }
                                                    ?></h5>
                                        <?php
                                        if ($row['discount'] > 0) {
                                        ?><h6 class="text-muted ml-2"><del>&#8377;<?= $row['price']; ?></del></h6>
                                        <?php } ?>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mb-1">
                                        <?php
                                        $avgRating = getProductRating($row['id'])[0];
                                        while ($avgRating) {
                                            $avgRating--;
                                        ?>
                                            <small class="fa fa-star text-primary mr-1"></small>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                            <!-- </div> -->
                        <?php }
                    } else {
                        ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Products End -->
    <?php

    include 'footer.php';
    include 'common_scripts.php';
    ?>

    <script>
        function counter_fn() {
            $.ajax({
                method: "POST",
                data: {
                    count: true
                },
                success: function(data) {}
            })
        }
        window.onload = counter_fn;

        $('.quantity button').on('click', function() {
            var button = $(this);
            var oldValue = button.parent().parent().find('input').val();
            if (button.hasClass('btn-plus')) {
                var newVal = parseFloat(oldValue) + 1;
            } else {
                if (oldValue > 0) {
                    var newVal = parseFloat(oldValue) - 1;
                } else {
                    newVal = 0;
                }
            }
            button.parent().parent().find('input').val(newVal);
        });

        $(".stars li").hover(
            function() { // mouseover
                $('.stars i').removeClass("fas");
                $(this).find('i').addClass("fas");
                $(this).prevAll().find('i').addClass("fas");
                let id = $(this).data("id");
                $('#rating').val(id);
            },
            function() { // mouseleave
            }
        );

        function addCart(e) {
            let id = e.getAttribute("value");
            let qty = $('#itemQty').val();

            $.ajax({
                method: "POST",
                cache: false,
                data: {
                    item: id,
                    qty: qty
                },
                success: function(data) {
                    console.log(data);
                    // return;
                    let obj = JSON.parse(data);
                    // console.log(obj.res);
                    // return;
                    if (obj.res == true) {
                        // document.getElementsByClassName('cartCount').innerHTML = '<?= getCartItems() ?>';
                        // document.querySelectorAll('.cartCount').forEach(function(ele, index) {
                        //     ele.innerHTML = '<?= getCartItems() ?>';
                        // });
                        Swal.fire("Item added in Cart.", "", "success");
                        setTimeout(() => {
                            location.reload();
                        }, 1000);

                    } else if (obj.res == false) {
                        Swal.fire("Error.", "", "error");
                    } else if (obj.res == 'increamented') {
                        Swal.fire("increamented", "", "success");
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        Swal.fire("Something went wrong", "", "error");
                    }

                }
            })
        }

        function addWishlist(e) {
            let id = e.getAttribute("value");
            // let variant = $(e).data("variant");
            //   alert(id+variant);
            $.ajax({
                method: "POST",
                cache: false,
                data: {
                    wish: id
                },
                success: function(data) {
                    console.log(data);
                    if (data == '1') {
                        // document.getElementsByClassName('wishCount').innerHTML = '<?= getCartItems('WISHLIST') ?>';
                        Swal.fire("Item Added in Wishlist.", "", "success");
                    } else {
                        Swal.fire("Something went wrong", "", "error");
                    }

                }
            })
        }

        $('#review_form').on("submit", function(e) {
            e.preventDefault();

            let f = '<?php echo $flag; ?>';

            if (f == '0') {
                Swal.fire("Error", "You have'nt buy this Product yet, Please buy first for rating.", "error");
                return;
            }
            if (f == '-1') {
                Swal.fire("Please Login for rating.", "", "error");
                return;
            }

            let form_data = $(this).serialize();
            // console.log(form_data);
            if ($('#rating').val() <= 0) {

                Swal.fire("Please do Rating.", "", "error");
                return;
            }
            if ($('#message').val() == '') {

                Swal.fire("Review Message is Required.", "", "error");
                return;
            }
            if ($('#name').val() == '') {

                Swal.fire("Name is Required.", "", "error");
                return;
            }
            if ($('#email_ID').val() == '') {

                Swal.fire("Email is Required.", "", "error");
                return;
            }
            $.ajax({
                method: "POST",
                data: form_data,
                success: function(data) {
                    // alert(data);
                    if (data == '1') {
                        $('#review_form')[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Your Review Added.',
                            showConfirmButton: true,
                            confirmButtonText: 'Ok'
                        }).then(result => {
                            location.reload();
                        })
                        setTimeout(() => {
                            location.reload();
                        }, 1000);

                    } else if (data == '0') {
                        Swal.fire("Not Added.", "", "error");
                    } else {
                        Swal.fire("Something went wrong.", "", "error");
                    }
                }
            })

        });
    </script>


</body>

</html>