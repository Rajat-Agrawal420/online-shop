<?php
session_start();
include 'connection.php';

?>
<!DOCTYPE html>
<html lang="en">

<?php include 'site_head.php'; ?>

<body>
    <?php include 'header.php'; ?>

    <!--================================
            START DASHBOARD AREA
    =================================-->
    <section class="dashboard-area dashboard_purchase">
        <?php
        if (isset($_SESSION['RECENT_ORDER_ID'])) {
            $o_id = $_SESSION['RECENT_ORDER_ID'];
            $sql = "SELECT * FROM order_detail WHERE order_id='$o_id'";
        }

        $res = mysqli_query($conn, $sql);
        if (!$res) {
            errlog(mysqli_error($conn), $sql);
        }
        ?>
        <div class="dashboard_contents">
            <div class="container">
                <div class="shortcode_modules">
                    <div class="modules__title ">
                        <h3 class="text-center scolor">Thanks For your Order</h3>

                    </div>
                    <h6 class="text-center pcolor" style="padding:2rem;">Your Recent Order Id #<?= $_SESSION['RECENT_ORDER_ID']; ?></h6>

                </div>
                <div class="product_archive">

                    <div class="title_area">
                        <div class="row">
                            <div class="col-md-5">
                                <h4>Item Details</h4>
                            </div>
                            <div class="col-md-3">
                                <h4 class="add_info">Additional Info</h4>
                            </div>
                            <div class="col-md-2">
                                <h4>Price</h4>
                            </div>
                            <div class="col-md-2">
                                <h4>Rate Product</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">

                        <?php
                        if (mysqli_num_rows($res) > 0) {

                            while ($row = mysqli_fetch_assoc($res)) {

                                $pid = realEscape($row['item_id']);

                                $sql = "SELECT *, product.id as prod_id from product where product.id = '" . $pid . "'";
                                $rs = mysqli_query($conn, $sql);
                                if (!$rs) {
                                    errlog(mysqli_error($conn), $sql);
                                } else {
                                    $row2 = mysqli_fetch_assoc($rs);
                                }
                        ?>
                                <div class="col-md-12">
                                    <div class="single_product clearfix">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-5">
                                                <div class="product__description">
                                                    <img src="<?= $this_site_url . htmlspecialchars($row2['pic']); ?>" width="120px" alt="Purchase image">
                                                    <div class="short_desc">
                                                        <h4><?= htmlspecialchars($row2['product_name']); ?></h4>
                                                        <!-- <p>Nunc placerat mi id nisi inter dum mollis. Praesent phare...</p> -->
                                                    </div>
                                                </div>
                                                <!-- end /.product__description -->
                                            </div>
                                            <!-- end /.col-md-5 -->

                                            <div class="col-lg-3 col-md-3 col-6 xs-fullwidth">
                                                <div class="product__additional_info">
                                                    <ul>
                                                        <li>
                                                            <p>
                                                                <span>Date: </span> <?php echo date("F d, Y", strtotime($row['order_date'])); ?>
                                                            </p>
                                                        </li>
                                                        <li class="license">
                                                            <p>
                                                                <span>Status:</span> <?php if ($row['status'] == 0) {
                                                                                            echo "Default";
                                                                                        }
                                                                                        if ($row['status'] == 1) {
                                                                                            echo "Placed";
                                                                                        }
                                                                                        if ($row['status'] == 2) {
                                                                                            echo "Confirmed";
                                                                                        } ?>
                                                            </p>
                                                        </li>
                                                        <li>
                                                            <p>
                                                                <span>Qty:</span> <?= htmlspecialchars($row['quantity']); ?>
                                                            </p>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <!-- end /.product__additional_info -->
                                            </div>
                                            <!-- end /.col-md-3 -->

                                            <div class="col-lg-4 col-md-4 col-6 xs-fullwidth">
                                                <div class="product__price_download">
                                                    <div class="item_price v_middle">
                                                        <span>&#8377; <?php $total = 0;
                                                                        $total = htmlspecialchars($row['price']) + htmlspecialchars($row['discount']);
                                                                        echo number_format($total, 2);
                                                                        ?></span>
                                                    </div>
                                                    <!-- <div class="item_action v_middle">
                                                        <a href="#" class="btn btn--md btn--round btn--white rating--btn not--rated" data-toggle="modal" data-target="#myModal">
                                                            <P class="rate_it">Rate Now</P>
                                                            <div class="rating product--rating">
                                                                <ul>
                                                                    <li>
                                                                        <span class="fa fa-star-o"></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="fa fa-star-o"></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="fa fa-star-o"></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="fa fa-star-o"></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="fa fa-star-o"></span>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </a>
                                                      
                                                    </div> -->
                                                    <!-- end /.item_action -->
                                                </div>
                                                <!-- end /.product__price_download -->
                                            </div>
                                            <!-- end /.col-md-4 -->
                                        </div>
                                    </div>
                                    <!-- end /.single_product -->
                                </div>
                        <?php
                            }
                        } else {
                        } ?>
                        <div class="col-md-12">
                            <button class="btn btn-primary btn--round<?php if (!isset($_SESSION['user_id'])) echo " mt-3"; ?>" onclick=" location.href='shop.php'" style=" margin-left:30rem;">Continue Shopping</button>
                        </div>
                        <!-- end /.col-md-12 -->
                    </div>
                    <!-- end /.row -->
                </div>
                <!-- end /.product_archive2 -->
            </div>
            <!-- end /.container -->
        </div>
        <!-- end /.dashboard_menu_area -->
    </section>

    <?php

    include 'footer.php';
    include 'common_scripts.php';
    ?>


</body>

</html>