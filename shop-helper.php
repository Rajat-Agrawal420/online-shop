<?php
session_start();
include 'connection.php';

$sql = "SELECT * FROM product WHERE status=1";


if (isset($_POST['min_price']) && $_POST['min_price'] != '' && isset($_POST['max_price']) && $_POST['max_price'] != '') {
    $value1 = realEscape($_POST['min_price']);
    $value2 = realEscape($_POST['max_price']);
    $sql .= " AND price between $value1 and $value2";
}

if (isset($_POST['color']) && count($_POST['color']) > 0) {

    $sql .= " AND color IN ('" . implode("','", $_POST['color']) . "')";
}

if (isset($_POST['size']) && count($_POST['size']) > 0) {

    $sql .= " AND size IN ('" . implode("','", $_POST['size']) . "')";
}

if (isset($_POST['keyword']) && !empty($_POST['keyword'])) {
    $keyword = realEscape($_POST['keyword']);
    $sql .= " AND (product_name LIKE '%$keyword%' OR Category LIKE '%$keyword%' )";
}
if (isset($_POST['category']) && !empty($_POST['category'])) {
    $cat = realEscape($_POST['category']);
    $sql .= " AND Category='$cat'";
}
if (isset($_POST['sub_cat']) && !empty($_POST['sub_cat'])) {
    $cat = realEscape($_POST['sub_cat']);
    $sql .= " AND sub_cat='$cat'";
}
if (isset($_POST['loadMoreProds'])) {
    $items_on_page = $_SESSION['items'];

    foreach ($items_on_page as $key => $value) {
        $id = $items_on_page[$key]['id'];
        $sql .= " AND id <> $id";
    }
} else {
    $_SESSION['items'] = array();
}

if (isset($_POST['order']) && !empty($_POST['order'])) {
    $value = realEscape($_POST['order']);
    if ($value == 'Popularity') {
        $sql .= " order by views DESC";
    } else if ($value == 'Low to High') {
        $sql .= " order by price ASC";
    } else if ($value == 'High to Low') {
        $sql .= " order by price DESC";
    } else if ($value == 'Latest') {
        $sql .= " order by created_date DESC";
    } else if ($value == 'Best Rating') {
        $sql .= "";
    }
}

if (isset($_POST['show']) && !empty($_POST['show'])) {
    $value = realEscape($_POST['show']);
    if ($value == '10') {
        $sql .= " limit 10";
    } else if ($value == '20') {
        $sql .= " limit 20";
    } else if ($value == '30') {
        $sql .= " limit 30";
    }
} else {
    $sql .= " limit 5";
}

// echo $sql;
$result = mysqli_query($conn, $sql);
if (!$result) {
    errlog(mysqli_error($conn), $sql);
}
if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {
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
} else { ?>
    <div class=" col-12 d-flex justify-content-center align-items-center text-center">
        <div class="alert alert-danger fade show text-center" style="margin: auto; text-align:center;" role="alert"><b> No Result Found.</b>
        </div>
    </div>
<?php } ?>