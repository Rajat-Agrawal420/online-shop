 <!-- JavaScript Libraries -->
 <script src="js/jquery.min.js"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
 <script src="lib/easing/easing.min.js"></script>
 <script src="lib/owlcarousel/owl.carousel.min.js"></script>

 <!-- Contact Javascript File -->

 <!-- Template Javascript -->
 <script src="js/main.js"></script>

 <script src="js/sweetalert2.min.js"></script>

 <script>
    document.querySelector('#searchForm').addEventListener('submit', (e) => {

       e.preventDefault();

       if (document.querySelector('#keyword').value == '') {
          Swal.fire('Please Enter Keyword.', '', 'error');
          return;
       } else {
          document.querySelector('#searchForm').submit();
       }

    });
 </script>

 <script>
    $(document).on('submit', '#registerForm', function(event) {

       event.preventDefault();
       var email = $('#user_email').val();

       if ($('#user_name').val() == '') {
          Swal.fire("Your Name is Required.", "", "error");
          return;
       }
       if ($('#user_email').val() == '') {
          Swal.fire("E-mail is Required.", "", "error");
          return;
       }
       if ($('#user_mobile').val() == '') {
          Swal.fire("Your Mobile Number is Neccessory.", "", "error");
          return;
       }
       if ($('#user_pass').val() == '') {
          Swal.fire("Enter a Password.", "", "error");
          return;
       }
       if ($('#c_user_pass').val() == '') {
          Swal.fire("Please Confirm Password.", "", "error");
          return;
       }

       if ($('#user_pass').val() != $('#c_user_pass').val()) {
          Swal.fire("Invalid, Password Should be Same.", "", "error");
          return;
       }
       if ($('#agree').prop('checked') !== true) {
          Swal.fire("Please Agree with Privacy Policy.", "", "error");
          return;
       }

       var form_data = $('#registerForm').serialize() + '&SignUp=1';

       $.ajax({
          url: "login.php",
          type: "POST",
          data: form_data,
          cache: false,
          beforeSend: function() {
             $("#registerBtn").addClass('disabled');
             $("#registerBtn").html('Loading..');
          },
          success: function(data) {
             // console.log(data);
             $("#registerBtn").removeClass('disabled');
             $("#registerBtn").html('Sign Up');

             if (data == '1') {
                $('#div2').hide();
                $('#div3').hide();
                $('#div1').show();
                $("#div1").find("#span1").html(email);
                Swal.fire("OTP sent to your Email Address.", "", "success");
                var timer = 120;
                var timerInterval = setInterval(function() {
                   $("#para").html("Try again in " + timer + " seconds");
                   timer--;
                   if (timer == -1) {
                      clearInterval(timerInterval);
                      $('#timeExpiredDiv').show();
                   }
                }, 999);
             } else if (data == '2') {
                Swal.fire({
                   icon: "info",
                   title: "Already Registered",
                   text: "Email already registered<br>" + email,
                   showConfirmBtn: true,
                })
             } else if (data == '3') {
                Swal.fire({
                   icon: "error",
                   title: "Mobile Number Should be started from 6789",
                   text: "" + email,
                   showConfirmBtn: true,
                })
             } else {
                Swal.fire({
                   icon: "error",
                   title: "Error",
                   text: "Something went wrong. <br>Please try again later ...",
                   showConfirmBtn: true,
                })
             }

          }

       });


    });

    $("#signup_button").on("click", function(event) { // verify otp

       var otp = $('#signup_otp').val();

       if (otp == '' || otp == null) {
          Swal.fire("OTP is Required.", "", "error");
          return;
       }

       $.ajax({
          type: 'POST',
          url: 'login.php',
          data: {
             verifyOtp: otp
          },
          beforeSend: function() {
             $("#signup_button").addClass('disabled');
             $("#signup_button").html('Verifying..');
          },
          success: function(data) {
             $("#signup_button").removeClass('disabled');
             $("#signup_button").html('Verify');
             if (data == '1') {
                $('#div2').show();
                $('#div3').show();
                $('#div1').hide();
                $("#registerForm").trigger("reset");

                Swal.fire({
                   icon: 'success',
                   title: 'Account Created Successfully.',
                   showConfirmButton: true,
                   confirmButtonText: 'Ok'
                }).then(result => {
                   location.reload();
                })
                setTimeout(() => {
                   location.reload();
                }, 2000);
             } else if (data == '2') {
                Swal.fire({
                   icon: "error",
                   title: "Invalid OTP",
                   text: "",
                   showConfirmBtn: true,
                })
             } else if (data == '0') {
                Swal.fire({
                   icon: "error",
                   title: "Failed! Not Verfied, Try Again",
                   text: "",
                   showConfirmBtn: true,
                })
             } else {
                Swal.fire("Something went wrong", "", "error");
             }

          }
       });

    });

    $(document).on('click', '.logoutBtn', function(event) {

       event.preventDefault();

       $.ajax({
          type: 'POST',
          url: 'login.php',
          data: {
             Logout: true
          },
          beforeSend: function() {},
          success: function(data) {

             if (data == '1') {

                Swal.fire({
                   icon: 'success',
                   title: 'Logout Successfully.',
                   showConfirmButton: true,
                   confirmButtonText: 'Ok'
                }).then(result => {
                   location.reload();
                })
                setTimeout(() => {
                   location.reload();
                }, 1000);
             } else {
                Swal.fire({
                   icon: "error",
                   title: "Something went wrong",
                   text: "",
                   showConfirmBtn: true,
                })
             }

          }
       });

    });

    $(document).on('submit', '#loginForm', function(event) {

       event.preventDefault();
       var email = $('#u_email').val();
       var pass = $('#u_password').val();

       if (email == '') {
          Swal.fire("E-mail is Required.", "", "error");
          return;
       }
       if (pass == '') {
          Swal.fire("Enter a Password.", "", "error");
          return;
       }

       $.ajax({
          url: "login.php",
          type: "POST",
          data: {
             userLogin: email,
             pass: pass
          },
          cache: false,
          beforeSend: function() {
             $("#loginBtn").addClass('disabled');
             $("#loginBtn").html('Logging..');
          },
          success: function(data) {
             console.log(data);
             $("#loginBtn").removeClass('disabled');
             $("#loginBtn").html('Login');

             if (data == '1') {

                Swal.fire("Login Successful.", "", "success");
                setTimeout(() => {
                   location.reload();
                }, 1000);

             } else if (data == '2') {
                Swal.fire({
                   icon: "error",
                   title: "Invalid Password.",
                   text: "Please Try Again !<br>" + email,
                   showConfirmBtn: true,
                })
             } else if (data == '4') {
                Swal.fire({
                   icon: "error",
                   title: "Please Enter a valid Email or Mobile",
                   text: "" + email,
                   showConfirmBtn: true,
                })
             } else if (data == '3') {
                Swal.fire({
                   icon: "error",
                   title: "You are not Registered.",
                   text: "" + email,
                   showConfirmBtn: true,
                })
             } else if (data == '-1') {
                Swal.fire({
                   icon: "error",
                   title: "Sorry! Your Account is not Active.",
                   text: "" + email,
                   showConfirmBtn: true,
                })
             } else {
                Swal.fire({
                   icon: "error",
                   title: "Error",
                   text: "Something went wrong. <br>Please try again later ...",
                   showConfirmBtn: true,
                })
             }

          }

       });


    });

    $(document).on('click', '#backBtn', function(e) {

       $('#div1').fadeToggle(500);
       $('#div2').fadeToggle(400);
       $('#div3').fadeToggle(400);
    });

    $("#s_btn").on("click", function(e) {

       let email = $('#s_email').val();

       if (email == '') {
          Swal.fire("Email is Required.", "", "error");
          return;
       }
       $.ajax({
          type: 'POST',
          url: 'mail/contact-admin.php',
          data: {
             subscribe: true,
             e_mail: email
          },
          success: function(data) {
             if (data.trim() == '1') {
                $('#s_email').val('');
                Swal.fire("Email Sent.", "", "success");
             } else if (data.trim() == '-1') {
                Swal.fire("Invalid E-mail.", "", "error");
             } else {
                Swal.fire("Error.", "", "error");
             }

          }
       });

    });
 </script>

 <script>
    var x = null;
    var y = null;
    document.body.addEventListener("mousemove", function(ev) {

       img = document.getElementById('myimage');
       if (img === null) {
          return;
       }
       result = document.getElementById('myresult');

       a = img.getBoundingClientRect();
       x = ev.pageX; // pointer position with scroll.
       y = ev.pageY;
       /*consider any page scrolling:*/
       x = x - window.pageXOffset;
       y = y - window.pageYOffset;

       if ((x <= a.left || y <= a.top) || (y > a.top + img.offsetHeight || x > a.left + img.offsetWidth)) {
          result.style.display = 'none';
       }
       // a.top = viewport top position of element.
    });

    window.addEventListener("scroll", function(ev) {

       ev.preventDefault();
       img = document.getElementById('myimage');
       if (img === null) {
          return;
       }
       result = document.getElementById('myresult');

       a = img.getBoundingClientRect();

       if ((y > a.top + img.offsetHeight)) {
          result.style.display = 'none';
       }

    });

    function imageZoom(imgID, resultID) {
       var img, lens, result, cx, cy;
       img = document.getElementById(imgID);
       if (img === null) {
          return;
       }
       result = document.getElementById(resultID);
       /*create lens:*/
       lens = document.createElement("DIV");
       lens.setAttribute("class", "img-zoom-lens");
       /*insert lens:*/
       img.parentElement.insertBefore(lens, img);
       /*calculate the ratio between result DIV and lens:*/
       cx = result.offsetWidth / lens.offsetWidth;
       cy = result.offsetHeight / lens.offsetHeight;
       /*set background properties for the result DIV:*/
       result.style.backgroundImage = "url('" + img.src + "')";
       result.style.backgroundSize = (img.width * cx) + "px " + (img.height * cy) + "px";
       result.style.display = 'none';
       /*execute a function when someone moves the cursor over the image, or the lens:*/
       lens.addEventListener("mousemove", moveLens);
       img.addEventListener("mousemove", moveLens);

       /*and also for touch screens:*/
       lens.addEventListener("touchmove", moveLens);
       img.addEventListener("touchmove", moveLens);

       function moveLens(e) {
          var pos, x, y;
          /*prevent any other actions that may occur when moving over the image:*/
          e.preventDefault();
          /*get the cursor's x and y positions:*/
          pos = getCursorPos(e);

          /*calculate the position of the lens:*/
          x = pos.x - (lens.offsetWidth / 2);
          y = pos.y - (lens.offsetHeight / 2);
          /*prevent the lens from being positioned outside the image:*/
          if (x > img.width - lens.offsetWidth) {
             x = img.width - lens.offsetWidth;
          }
          if (x < 0) {
             x = 0;
          }
          if (y > img.height - lens.offsetHeight) {
             y = img.height - lens.offsetHeight;
          }
          if (y < 0) {
             y = 0;
          }
          /*set the position of the lens:*/
          lens.style.left = x + "px";
          lens.style.top = y + "px";
          /*display what the lens "sees":*/

          result.style.display = 'block';
          result.style.backgroundPosition = "-" + (x * cx) + "px -" + (y * cy) + "px";
       }

       function getCursorPos(e) {
          var a, x = 0,
             y = 0;
          e = e || window.event;
          /*get the x and y positions of the image:*/
          a = img.getBoundingClientRect();
          /*calculate the cursor's x and y coordinates, relative to the image:*/
          x = e.pageX - a.left;
          y = e.pageY - a.top;
          /*consider any page scrolling:*/
          x = x - window.pageXOffset;
          y = y - window.pageYOffset;
          return {
             x: x,
             y: y
          };
       }

    }

    // Initiate zoom effect:
    imageZoom("myimage", "myresult");
 </script>