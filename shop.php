<?php
session_start();
include 'connection.php';
include 'use_cart_and_wishlist.php';

$_SESSION['items'] = array();

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
if (isset($_POST['wish'])) {

    $item_id = realEscape($_POST['wish']);

    $result = use_cart_n_wishlist($item_id, 'WISHLIST');
    if ($result)
        echo 1;
    else
        echo 0;
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
                    <span class="breadcrumb-item active">Shop List</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->


    <!-- Shop Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <!-- Shop Sidebar Start -->
            <div class="col-lg-3 col-md-4">
                <!-- Price Start -->
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Filter by price</span></h5>

                <div class="bg-light p-4 mb-30">
                    <?php
                    $qry = "SELECT min(price), max(price) FROM product WHERE status=1";
                    $result = mysqli_query($conn, $qry);
                    if (!$result) {
                        errlog(mysqli_error($conn), $qry);
                    }
                    $price = mysqli_fetch_assoc($result);
                    $maxPrice = 0;
                    $minPrice = 0;
                    if (isset($price['min(price)'])) {
                        $maxPrice = $price['max(price)'];
                        $minPrice = $price['min(price)'];
                    }
                    ?>
                    <div class="range-slider">
                        <span class="rangeValues"></span>
                        <input value="0" min="0" max="50000" step="500" type="range" id="min_val" class="filter">
                        <input value="<?= $maxPrice; ?>" min="0" max="<?= $maxPrice; ?>" step="500" type="range" id="max_val" class="filter">
                    </div>
                    <!-- <form>
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <input type="checkbox" class="custom-control-input" checked id="price-all">
                            <label class="custom-control-label" for="price-all">All Price</label>
                            <span class="badge border font-weight-normal">1000</span>
                        </div>
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <input type="checkbox" class="custom-control-input" id="price-1">
                            <label class="custom-control-label" for="price-1">$0 - $100</label>
                            <span class="badge border font-weight-normal">150</span>
                        </div>
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <input type="checkbox" class="custom-control-input" id="price-2">
                            <label class="custom-control-label" for="price-2">$100 - $200</label>
                            <span class="badge border font-weight-normal">295</span>
                        </div>
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <input type="checkbox" class="custom-control-input" id="price-3">
                            <label class="custom-control-label" for="price-3">$200 - $300</label>
                            <span class="badge border font-weight-normal">246</span>
                        </div>
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <input type="checkbox" class="custom-control-input" id="price-4">
                            <label class="custom-control-label" for="price-4">$300 - $400</label>
                            <span class="badge border font-weight-normal">145</span>
                        </div>
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between">
                            <input type="checkbox" class="custom-control-input" id="price-5">
                            <label class="custom-control-label" for="price-5">$400 - $500</label>
                            <span class="badge border font-weight-normal">168</span>
                        </div>
                    </form> -->
                </div>
                <!-- Price End -->

                <!-- Color Start -->
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Filter by color</span></h5>
                <div class="bg-light p-4 mb-30">
                    <form>
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <input type="checkbox" class="custom-control-input" checked id="color-all">
                            <label class="custom-control-label" for="price-all">All Color</label>
                            <!-- <span class="badge border font-weight-normal">1000</span> -->
                        </div>
                        <?php
                        $qry = "SELECT DISTINCT color FROM product WHERE status=1 AND color IS NOT NULL AND color <> '' ORDER BY color ASC LIMIT 7";
                        $result = mysqli_query($conn, $qry);
                        if (!$result) {
                            errlog(mysqli_error($conn), $qry);
                        }
                        while ($data = mysqli_fetch_assoc($result)) {
                            if ($data['color'] == '')
                                continue;
                        ?>
                            <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" class="custom-control-input filter color" value="<?= $data['color']; ?>" id="<?= $data['color']; ?>">
                                <label class="custom-control-label" for="<?= $data['color']; ?>"><?= $data['color']; ?></label>
                                <!-- <span class="badge border font-weight-normal">150</span> -->
                            </div>
                        <?php } ?>
                    </form>
                </div>
                <!-- Color End -->

                <!-- Size Start -->
                <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Filter by size</span></h5>
                <div class="bg-light p-4 mb-30">
                    <form>
                        <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                            <input type="checkbox" class="custom-control-input" checked id="size-all">
                            <label class="custom-control-label" for="size-all">All Size</label>
                            <!-- <span class="badge border font-weight-normal">1000</span> -->
                        </div>
                        <?php
                        $qry = "SELECT DISTINCT size FROM product WHERE status=1 AND size IS NOT NULL AND size <> '' LIMIT 5";
                        $result = mysqli_query($conn, $qry);
                        if (!$result) {
                            errlog(mysqli_error($conn), $qry);
                        }
                        while ($data = mysqli_fetch_assoc($result)) {
                            if ($data['size'] == '')
                                continue;
                        ?>
                            <div class="custom-control custom-checkbox d-flex align-items-center justify-content-between mb-3">
                                <input type="checkbox" class="custom-control-input filter size" value="<?= $data['size']; ?>" id="<?= $data['size']; ?>">
                                <label class="custom-control-label" for="<?= $data['size']; ?>"><?= $data['size']; ?></label>
                                <!-- <span class="badge border font-weight-normal">150</span> -->
                            </div>
                        <?php } ?>
                    </form>
                </div>
                <!-- Size End -->
            </div>
            <!-- Shop Sidebar End -->


            <!-- Shop Product Start -->
            <div class="col-lg-9 col-md-8">
                <div class="row pb-3">
                    <div class="col-12 pb-1">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <button class="btn btn-sm btn-light"><i class="fa fa-th-large"></i></button>
                                <button class="btn btn-sm btn-light ml-2"><i class="fa fa-bars"></i></button>
                            </div>
                            <div class="ml-2">
                                <div class="btn-group">
                                    <button type="button" id="sort" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">Sorting</button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item find sort" href="#">Default</a>
                                        <a class="dropdown-item find sort" href="#">Latest</a>
                                        <a class="dropdown-item find sort" href="#">Popularity</a>
                                        <a class="dropdown-item find sort" href="#">Low to High</a>
                                        <a class="dropdown-item find sort" href="#">High to Low</a>
                                        <a class="dropdown-item find sort" href="#">Best Rating</a>
                                    </div>
                                </div>
                                <div class="btn-group ml-2">
                                    <button type="button" id="show_item" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">Showing</button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item find show_item" href="#">10</a>
                                        <a class="dropdown-item find show_item" href="#">20</a>
                                        <a class="dropdown-item find show_item" href="#">30</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row pb-1" id="content">
                        <?php
                        $qry = "SELECT * FROM product WHERE status=1";
                        if (isset($_POST['keyword']) && !empty($_POST['keyword'])) {
                            $keyword = realEscape($_POST['keyword']);
                            $qry .= " AND (product_name LIKE '%$keyword%' OR Category LIKE '%$keyword%' )";
                            //  echo $qry;  die;
                        } else if (isset($_GET['cat']) && !empty($_GET['cat'])) {
                            $cat = realEscape(urldecode(base64_decode($_GET['cat'])));
                            $qry .= " AND Category='$cat'";
                        } else if (isset($_GET['sub_cat']) && !empty($_GET['sub_cat'])) {
                            $cat = realEscape(urldecode(base64_decode($_GET['sub_cat'])));
                            $qry .= " AND sub_cat='$cat'";
                        } else {
                            $qry .= " ORDER BY created_date DESC LIMIT 5";
                        }
                        // echo $qry; 
                        $res = mysqli_query($conn, $qry);
                        if (!$res) {
                            errlog(mysqli_error($conn), $qry);
                        }
                        if (mysqli_num_rows($res) > 0) {
                            $data = mysqli_fetch_all($res, MYSQLI_ASSOC);
                            shuffle($data);
                            foreach ($data as $row) {
                                $pid = $row['id'];
                                $arr = array(
                                    'id' => $pid
                                );
                                array_push($_SESSION['items'], $arr);
                        ?>
                                <div class="col-lg-3 col-md-3 col-sm-3 pb-1">
                                    <div class="product-item bg-light mb-4">
                                        <div class="product-img position-relative overflow-hidden">
                                            <img class="img-fluid w-100" src="<?= $this_site_url . $row['pic'] ?? ''; ?>" alt="product-image">
                                            <div class="product-action">
                                                <a class="btn btn-outline-dark btn-square" onclick="addCart(this)" value="<?= $row['id']; ?>" href="javascript:void(0)"><i class="fa fa-shopping-cart"></i></a>
                                                <a class="btn btn-outline-dark btn-square" onclick="addWishlist(this)" value="<?= $row['id']; ?>" href="javascript:void(0)"><i class="far fa-heart"></i></a>
                                                <a class="btn btn-outline-dark btn-square" href=""><i class="fa fa-sync-alt"></i></a>
                                                <a class="btn btn-outline-dark btn-square" href="product-detail?id=<?= urlencode(base64_encode($row['id'])); ?>"><i class="fa fa-search"></i></a>
                                            </div>
                                        </div>
                                        <div class="text-center py-4">
                                            <a class="h6 text-decoration-none text-truncate" href="product-detail?id=<?= urlencode(base64_encode($row['id'])); ?>"><?php
                                                                                                                                                                    if (strlen($row['product_name'] > 25)) {
                                                                                                                                                                        echo  substr($row['product_name'], 0, 25) . "...";
                                                                                                                                                                    } else {
                                                                                                                                                                        echo  $row['product_name'];
                                                                                                                                                                    }                                                                                                                              ?></a>
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
                                                if (isset($avgRating) && $avgRating > 0) {
                                                    while ($avgRating) {
                                                        $avgRating--;
                                                ?>
                                                        <small class="fa fa-star text-primary mr-1"></small>
                                                    <?php } ?>
                                                    <small>(<?= getProductRating($row['id'])[1] ?>)</small>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else {
                            ?>
                            <div class=" col-12 d-flex justify-content-center align-items-center text-center">
                                <div class="alert alert-danger fade show text-center" style="margin: auto; text-align:center;" role="alert"><b> No Result Found.</b>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- <div class="col-12">
                        <nav>
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled"><a class="page-link" href="#">Previous</span></a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div> -->
                    </div>
                    <div class="spinner-border text-primary mx-auto" id="loader" style="display: none; color: #0d6efd !important;"></div>
                </div>
            </div>
            <!-- Shop Product End -->
        </div>
    </div>
    <!-- Shop End -->

    <?php

    include 'footer.php';
    include 'common_scripts.php';
    ?>

    <script>
        function getVals() {
            // Get slider values
            let parent = this.parentNode;
            let slides = parent.getElementsByTagName("input");
            let slide1 = parseFloat(slides[0].value);
            let slide2 = parseFloat(slides[1].value);
            // Neither slider will clip the other, so make sure we determine which is larger
            if (slide1 > slide2) {
                let tmp = slide2;
                slide2 = slide1;
                slide1 = tmp;
            }

            let displayElement = parent.getElementsByClassName("rangeValues")[0];
            displayElement.style.fontWeight = 'bold';
            displayElement.style.color = 'black';
            displayElement.innerHTML = "&#8377;" + slide1 + " - &#8377;" + slide2;

        }

        window.onload = function() {
            // Initialize Sliders
            let sliderSections = document.getElementsByClassName("range-slider");
            for (let x = 0; x < sliderSections.length; x++) {
                let sliders = sliderSections[x].getElementsByTagName("input");
                for (let y = 0; y < sliders.length; y++) {
                    if (sliders[y].type === "range") {
                        sliders[y].oninput = getVals;
                        // Manually trigger event first time to display values
                        sliders[y].oninput();
                    }
                }
            }
        };
    </script>

    <script>
        'use strict';
        $(function($) {

            var orderContainer = '';
            var showContainer = '';

            function getCheckboxValues(checkboxClass) {
                var values = new Array();
                $("." + checkboxClass + ":checked").each(function() {
                    values.push($(this).val());
                });
                return values;
            }

            function getClickedValues(Class) {
                var value = '';
                $("." + Class + ":active").each(function() {
                    value = $(this).html();
                });
                return value;
            }

            $(".filter").on("change", function(e) {
                var min_price = $('#min_val').val();
                var max_price = $('#max_val').val();
                //  alert(min_price+max_price);

                var color = getCheckboxValues('color');
                var size = getCheckboxValues('size');

                console.log(min_price);
                console.log(max_price);
                console.log(color);
                console.log(size);

                $.ajax({
                    type: 'POST',
                    url: 'shop-helper.php',
                    data: {
                        min_price: min_price,
                        max_price: max_price,
                        color: color,
                        size: size
                    },
                    beforeSend: function() {},
                    success: function(data) {
                        console.log(data);
                        $('#content').html(data);
                    }
                });

            });

            $(".find").on("click", function(e) {

                var show_item = '';
                var order = '';
                if ($(this).hasClass('show_item')) {

                    show_item = $(this).html();
                } else if ($(this).hasClass('sort')) {

                    order = $(this).html();
                }

                if (show_item == '') {
                    show_item = showContainer;
                } else {
                    showContainer = show_item;
                }

                if (order == '') {
                    order = orderContainer;
                } else {
                    orderContainer = order;
                }

                if (show_item == '') {
                    $('#show_item').html('Showing');
                } else {
                    $('#show_item').html(show_item);
                }

                if (order == '') {
                    $('#sort').html('Sorting');
                } else {
                    $('#sort').html(order);
                }

                console.log(order);
                console.log(show_item);

                var min_price = $('#min_val').val();
                var max_price = $('#max_val').val();
                //  alert(min_price+max_price);

                var color = getCheckboxValues('color');
                var size = getCheckboxValues('size');


                $.ajax({
                    type: 'POST',
                    url: 'shop-helper.php',
                    data: {
                        min_price: min_price,
                        max_price: max_price,
                        color: color,
                        size: size,
                        order: order,
                        show: show_item
                    },
                    beforeSend: function() {},
                    success: function(data) {
                        console.log(data);
                        $('#content').html(data);
                    }
                });

            });


            var windowHeight = $(window).height();

            $(window).on("scroll", function() {
                var windowTop = $(window).scrollTop() + 1;

                if (windowTop >= windowHeight) {
                    windowHeight = $(window).height() + windowTop - 100;

                    var min_price = $('#min_val').val();
                    var max_price = $('#max_val').val();

                    var color = getCheckboxValues('color');
                    var size = getCheckboxValues('size');

                    $.ajax({
                        type: 'POST',
                        url: 'shop-helper.php',
                        data: {
                            loadMoreProds: true,
                            min_price: min_price,
                            max_price: max_price,
                            color: color,
                            size: size,
                            order: orderContainer,
                            show: showContainer,
                            keyword: '<?php if (isset($_POST['keyword']) && $_POST['keyword'] != '') echo realEscape($_POST['keyword']);
                                        //                             else echo ""; 
                                        ?>',
                            category: '<?php if (isset($_GET['cat']) && $_GET['cat'] != '') echo realEscape(urldecode(base64_decode($_GET['cat'])));
                                        //                             else echo ""; 
                                        ?>',
                            sub_cat: '<?php if (isset($_GET['sub_cat']) && $_GET['sub_cat'] != '') echo realEscape(urldecode(base64_decode($_GET['sub_cat'])));  ?>'
                        },
                        beforeSend: function() {
                            $('#loader').show();
                        },
                        success: function(html) {
                            $('#loader').hide();
                            $('#content').append(html);
                        }
                    });
                }
            });

        });

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
    </script>
</body>

</html>