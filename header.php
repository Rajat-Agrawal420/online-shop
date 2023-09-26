 <!-- Topbar Start -->
 <div class="container-fluid">
   <div class="row bg-secondary py-1 px-xl-5">
     <div class="col-lg-6 d-none d-lg-block">
       <div class="d-inline-flex align-items-center h-100">
         <a class="text-body mr-3" href="about">About</a>
         <a class="text-body mr-3" href="contact">Contact</a>
         <a class="text-body mr-3" href="">Help</a>
         <a class="text-body mr-3" href="">FAQs</a>
       </div>
     </div>
     <div class="col-lg-6 text-center text-lg-right">
       <div class="d-inline-flex align-items-center">
         <div class="btn-group">
           <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">My Account</button>
           <div class="dropdown-menu dropdown-menu-right">
             <button class="dropdown-item" data-toggle="modal" data-target="#loginModal" type="button">Sign in</button>
             <button class="dropdown-item" data-toggle="modal" data-target="#SignUpModal" type="button">Sign up</button>
             <?php if (isset($_SESSION['user_id'])) { ?>
               <button class="dropdown-item logoutBtn" type="button">Logout (<?= getUserInfo($_SESSION['user_id'])['name'] ?? ''; ?>)</button>
             <?php } ?>
           </div>
         </div>
         <div class="btn-group mx-2">
           <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">USD</button>
           <div class="dropdown-menu dropdown-menu-right">
             <button class="dropdown-item" type="button">EUR</button>
             <button class="dropdown-item" type="button">GBP</button>
             <button class="dropdown-item" type="button">CAD</button>
           </div>
         </div>
         <div class="btn-group">
           <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">EN</button>
           <div class="dropdown-menu dropdown-menu-right">
             <button class="dropdown-item" type="button">FR</button>
             <button class="dropdown-item" type="button">AR</button>
             <button class="dropdown-item" type="button">RU</button>
           </div>
         </div>
       </div>
       <div class="d-inline-flex align-items-center d-block d-lg-none">
         <a href="" class="btn px-0 ml-2">
           <i class="fas fa-heart text-dark"></i>
           <span class="badge text-dark border wishCount border-dark rounded-circle" style="padding-bottom: 2px;"><?php echo getCartItems('WISHLIST'); ?></span>
         </a>
         <a href="" class="btn px-0 ml-2">
           <i class="fas fa-shopping-cart text-dark"></i>
           <span class="badge text-dark border cartCount border-dark rounded-circle" style="padding-bottom: 2px;"><?php echo getCartItems(); ?></span>
         </a>
       </div>
     </div>
   </div>
   <div class="row align-items-center bg-light py-3 px-xl-5 d-none d-lg-flex">
     <div class="col-lg-4">
       <!-- <img src="<?= $site_logo; ?>" alt="logo"> -->
       <div class="" style="width: 45%;">
         <a class="btn d-flex align-items-center justify-content-between" data-toggle="collapse" href="#navbar-vertical" style="height: 65px; padding: 0 30px; color:#3D464D !important;">
           <h6 class=" m-0" style="color: #3D464D;"><i class="fa fa-bars mr-3"></i>Categories</h6>
           <i class="fa fa-angle-down" style="color: #3D464D;"></i>
         </a>
         <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 bg-primary" id="navbar-vertical" style="width: 45%; z-index: 999;">
           <div class="navbar-nav bg-primary w-100">
             <?php
              $sql = "SELECT DISTINCT Category FROM product WHERE status=1";
              $res = mysqli_query($conn, $sql);
              if (!$res) {
                errlog(mysqli_error($conn), $sql);
              }

              while ($row = mysqli_fetch_assoc($res)) {

                $sql = "SELECT sub_cat FROM product WHERE Category='" . $row['Category'] . "' AND sub_cat IS NOT NULL AND sub_cat <> ''";
                $res2 = mysqli_query($conn, $sql);
                if (!$res2) {
                  errlog(mysqli_error($conn), $sql);
                }
                if (mysqli_num_rows($res2) > 0) {
              ?>
                 <div class="nav-item dropdown dropright">
                   <a href="shop?cat=<?= urlencode(base64_encode($row['Category'])); ?>" class="nav-link dropdown-item dropdown-toggle" data-toggle="dropdown" style="color:#212529 !important;"><?= $row['Category']; ?> <i class="fa fa-angle-right float-right mt-1"></i></a>
                   <div class="dropdown-menu position-absolute rounded-0 border-0 m-0">
                     <?php while ($sub_cat = mysqli_fetch_assoc($res2)) { ?>
                       <a href="shop?sub_cat=<?= urlencode(base64_encode($sub_cat['sub_cat'])); ?>" class="dropdown-item" style="color:#212529 !important;"><?= $sub_cat['sub_cat']; ?></a>
                     <?php } ?>
                   </div>
                 </div>

               <?php } else {
                ?>
                 <a href="shop?cat=<?= urlencode(base64_encode($row['Category'])); ?>" class="nav-item nav-link dropdown-item" style="color:#212529 !important;"><?= $row['Category']; ?></a>
             <?php
                }
              }
              ?>

           </div>
         </nav>
       </div>
     </div>
     <div class="col-lg-4 col-6 text-left">
       <form id="searchForm" action="shop.php" method="post">
         <div class="input-group">
           <input type="text" id="keyword" name="keyword" class="form-control" placeholder="Search for products">
           <div class="input-group-append">
             <button style="outline:none;" id="searchBtn" type="submit" class="input-group-text bg-transparent text-primary">
               <i class="fa fa-search"></i>
             </button>
           </div>
         </div>
       </form>
     </div>
     <div class="col-lg-4 col-6 text-right">
       <p class="m-0">Customer Service</p>
       <h5 class="m-0">+012 345 6789</h5>
     </div>
   </div>
 </div>
 <!-- Topbar End -->


 <!-- Navbar Start -->
 <div class="container-fluid bg-dark mb-30">
   <div class="row px-xl-5">
     <div class="col-lg-3 d-none">
       <a class="btn d-flex align-items-center justify-content-between w-100" data-toggle="collapse" href="#navbar-vertical" style="height: 65px; padding: 0 30px; color:white !important;">
         <h1 class=" m-0" style="color: white;"><i class="fa fa-bars mr-2"></i>Categories</h1>
         <i class="fa fa-angle-down" style="color: white;"></i>
       </a>
       <nav class="collapse position-absolute navbar navbar-vertical navbar-light align-items-start p-0 bg-light" id="navbar-vertical" style="width: calc(100% - 30px); z-index: 999;">
         <div class="navbar-nav w-100">
           <?php
            $sql = "SELECT DISTINCT Category FROM product WHERE status=1";
            $res = mysqli_query($conn, $sql);
            if (!$res) {
              errlog(mysqli_error($conn), $sql);
            }

            while ($row = mysqli_fetch_assoc($res)) {

              $sql = "SELECT sub_cat FROM product WHERE Category='" . $row['Category'] . "' AND sub_cat IS NOT NULL AND sub_cat <> ''";
              $res2 = mysqli_query($conn, $sql);
              if (!$res2) {
                errlog(mysqli_error($conn), $sql);
              }
              if (mysqli_num_rows($res2) > 0) {
            ?>
               <div class="nav-item dropdown dropright">
                 <a href="shop?cat=<?= urlencode(base64_encode($row['Category'])); ?>" class="nav-link dropdown-toggle" data-toggle="dropdown"><?= $row['Category']; ?> <i class="fa fa-angle-right float-right mt-1"></i></a>
                 <div class="dropdown-menu position-absolute rounded-0 border-0 m-0">
                   <?php while ($sub_cat = mysqli_fetch_assoc($res2)) { ?>
                     <a href="shop?sub_cat=<?= urlencode(base64_encode($sub_cat['sub_cat'])); ?>" class="dropdown-item"><?= $sub_cat['sub_cat']; ?></a>
                   <?php } ?>
                 </div>
               </div>

             <?php } else {
              ?>
               <a href="shop?cat=<?= urlencode(base64_encode($row['Category'])); ?>" class="nav-item nav-link"><?= $row['Category']; ?></a>
           <?php
              }
            }
            ?>

         </div>
       </nav>
     </div>
     <div class="col-lg-12">
       <nav class="navbar navbar-expand-lg bg-dark navbar-dark py-3 py-lg-0 px-0">
         <img src="<?= $site_logo; ?>" class="ml-5" style="height: 70px;" alt="logo">
         <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
           <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse justify-content-between ml-5 pl-5" id="navbarCollapse">
           <div class="navbar-nav mr-auto py-0">
             <a href="index" class="nav-item nav-link active">Home</a>
             <a href="shop" class="nav-item nav-link">Shop</a>
             <a href="blog" class="nav-item nav-link">Blog</a>
             <div class="nav-item dropdown">
               <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages <i class="fa fa-angle-down mt-1"></i></a>
               <div class="dropdown-menu bg-primary rounded-0 border-0 m-0">
                 <a href="cart" class="dropdown-item">Cart</a>
                 <a href="wishlist" class="dropdown-item">Wish List</a>
                 <a href="checkout" class="dropdown-item">Checkout</a>
                 <a href="my-orders" class="dropdown-item">My Orders</a>
               </div>
             </div>
             <a href="contact" class="nav-item nav-link">Contact</a>
           </div>
           <div class="navbar-nav ml-auto py-0 d-none d-lg-block">
             <a href="wishlist" class="btn px-0">
               <i class="fas fa-heart text-primary"></i>
               <span class="badge text-secondary border wishCount border-secondary rounded-circle" style="padding-bottom: 2px;"><?php echo getCartItems('WISHLIST'); ?></span>
             </a>
             <a href="cart" class="btn px-0 ml-3">
               <i class="fas fa-shopping-cart text-primary"></i>
               <span class="badge text-secondary cartCount border border-secondary rounded-circle" style="padding-bottom: 2px;"><?php echo getCartItems(); ?></span>
             </a>
           </div>
         </div>
       </nav>
     </div>
   </div>
 </div>
 <!-- Navbar End -->


 <!-- <div class="container">
  <button type="button" class="btn btn-info btn-round" data-toggle="modal" data-target="#loginModal">
    Login
  </button>
</div> -->

 <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
     <div class="modal-content">
       <div class="modal-header border-bottom-0">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <div class="modal-body">
         <div class="form-title text-center">
           <h4>Login</h4>
         </div>

         <div class="text-center my-3">
           <img src="<?= $site_logo; ?>" alt="site_logo" class="">
         </div>

         <ul class="nav nav-tabs tab-menu">
           <li class="active"><a href="#userLoginDiv" class="nav-link text-dark" data-toggle="tab">User Login</a></li>
           <!-- <li class=""><a href="#adminLoginDiv" class="nav-link text-dark" data-toggle="tab">Admin Login</a></li> -->
         </ul>

         <div class="tab-content">

           <div class="tab-pane active" id="userLoginDiv">
             <div class="d-flex flex-column text-center">

               <form id="loginForm" method="post">
                 <div class="form-group">
                   <input type="text" class="form-control" id="u_email" name="u_email" placeholder="Email or Mobile...">
                 </div>
                 <div class="form-group">
                   <input type="password" class="form-control" id="u_password" name="u_password" placeholder="password...">
                 </div>
                 <button type="submit" id="loginBtn" class="btn btn-info btn-block btn-round">Login</button>
               </form>

               <div class="text-center text-muted delimiter">or use a social network</div>
               <div class="d-flex justify-content-center social-buttons">
                 <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Twitter">
                   <i class="fab fa-twitter"></i>
                 </button>
                 <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Facebook">
                   <i class="fab fa-facebook"></i>
                 </button>
                 <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Linkedin">
                   <i class="fab fa-linkedin"></i>
                 </button>
                 </di>
               </div>
             </div>
           </div>

           <div class="tab-pane" id="adminLoginDiv">
             <div class="d-flex flex-column text-center">

               <form>
                 <div class="form-group">
                   <input type="text" class="form-control" id="email2" placeholder="Email or Mobile...">
                 </div>
                 <div class="form-group">
                   <input type="password" class="form-control" id="password2" placeholder="password...">
                 </div>
                 <button type="button" class="btn btn-info btn-block btn-round">Login</button>
               </form>

               <div class="text-center text-muted delimiter">or use a social network</div>
               <div class="d-flex justify-content-center social-buttons">
                 <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Twitter">
                   <i class="fab fa-twitter"></i>
                 </button>
                 <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Facebook">
                   <i class="fab fa-facebook"></i>
                 </button>
                 <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Linkedin">
                   <i class="fab fa-linkedin"></i>
                 </button>
                 </di>
               </div>
             </div>
           </div>

         </div>

       </div>
       <div class="modal-footer d-flex justify-content-center">
         <div class="signup-section">Not a member yet? <a href="#a" data-toggle="modal" data-target="#SignUpModal" class="text-info"> Sign Up</a>.</div>
       </div>
     </div>
   </div>
 </div>




 <div class="modal fade" id="SignUpModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
     <div class="modal-content">
       <div class="modal-header border-bottom-0">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <div class="modal-body">
         <div class="form-title text-center">
           <h4>Register Account</h4>
         </div>

         <div class="text-center my-3">
           <img src="<?= $site_logo; ?>" alt="site_logo" class="">
         </div>

         <div class="d-flex flex-column text-center">

           <form id="registerForm" method="post">

             <div id="div2">
               <div class="form-group">
                 <input type="text" class="form-control" id="user_name" name="user_name" placeholder="User Name">
               </div>

               <div class="form-group">
                 <input type="email" class="form-control" id="user_email" name="user_email" placeholder="User email">
               </div>

               <div class="form-group">
                 <input type="text" class="form-control" id="user_mobile" name="user_mobile" placeholder="User Mobile">
               </div>

               <div class="form-group">
                 <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder="Password">
               </div>

               <div class="form-group">
                 <input type="password" class="form-control" id="c_user_pass" name="c_user_pass" placeholder="Confirm password">
               </div>

               <div class="form-group">
                 <div>
                   <input type="checkbox" class="form-check-input" id="agree" name="agree" />
                   <label for="agree" class="form-label">I Agree with privacy policy</label>
                 </div>
               </div>
             </div>


             <div class="row mb-4" id="div1" style="display:none;">
               <div class="col-sm-12">
                 <div class="alert alert-success">
                   OTP has been sent to your E-mail <span id="span1" class="currentUserEmail">example-email@gmail.com</span>

                 </div>
                 <p id="para"></p>
               </div>
               <div class="row">
                 <div class="col-sm-12">
                   <input type="number" class="form-control ml-3 mt-2" name="signup_otp" id="signup_otp" placeholder="OTP"><br>
                 </div>
               </div>
               <button type="button" id="signup_button" class="btn btn-info ml-4 mt-2 confirmOtp" style="height: 40px; ">Verify</button>

               <div class="col-sm-12 text-center" style="display: none;" id="timeExpiredDiv">
                 <span id="para2" style="color:red; font-size:small;">Otp Time has Expired, Please Try Again </span><a id="backBtn" href="javascript:void(0)" style="color: #17a2b8;" class="ml-1">Back.</a>
               </div>

             </div>

             <div id="div3">
               <button type="submit" id="registerBtn" class="btn btn-info btn-block btn-round">Sign Up</button>
             </div>
           </form>

           <div class="text-center text-muted delimiter">or use a social network</div>
           <div class="d-flex justify-content-center social-buttons">
             <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Twitter">
               <i class="fab fa-twitter"></i>
             </button>
             <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Facebook">
               <i class="fab fa-facebook"></i>
             </button>
             <button type="button" class="btn btn-secondary btn-round" data-toggle="tooltip" data-placement="top" title="Linkedin">
               <i class="fab fa-linkedin"></i>
             </button>
             </di>
           </div>
         </div>




       </div>
       <div class="modal-footer d-flex justify-content-center">
         <div class="signup-section">Already have Account? <a href="#a" data-toggle="modal" data-target="#loginModal" class="text-info"> Login</a>.</div>
       </div>
     </div>
   </div>
 </div>