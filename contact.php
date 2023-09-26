<?php
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<?php include 'site_head.php'; ?>
<script src="https://www.google.com/recaptcha/api.js"></script>
<script>
    function onSubmit(token) {

        var name = $("input#name").val();
        var email = $("input#email").val();
        var subject = $("input#subject").val();
        var message = $("textarea#message").val();
        var recaptcha_response = token;

        if (name == '') {
            Swal.fire("Name is Required.", "", "error");
            return;
        }
        if (email == '') {
            Swal.fire("Name is Required.", "", "error");
            return;
        }
        if (subject == '') {
            Swal.fire("Name is Required.", "", "error");
            return;
        }
        if (message == '') {
            Swal.fire("Name is Required.", "", "error");
            return;
        }

        $.ajax({
            url: "mail/contact-admin.php",
            type: "POST",
            data: {
                name: name,
                email: email,
                subject: subject,
                message: message,
                recaptcha_response: recaptcha_response
            },
            cache: false,
            beforeSend: function() {
                let btn = $("#sendMessageButton");
                btn.html('Sending...');
                btn.attr("disabled", true);

            },
            success: function(resp) {
                let btn = $("#sendMessageButton");
                btn.html('Send Message');
                btn.attr("disabled", false);

                console.log(resp);
                if (resp == '1') {
                    Swal.fire("Message Sent Successfully.", "", "success");
                    $('#contactForm').trigger("reset");
                } else if (resp == '-1') {
                    Swal.fire("Recaptcha Error, Please Try Again!", "", "error");
                } else {
                    Swal.fire("Failed ! Message not Sent.", "", "error");

                }
            },
            error: function() {
                $('#success').html("<div class='alert alert-danger'>");
                $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                    .append("</button>");
                $('#success > .alert-danger').append($("<strong>").text("Sorry " + name + ", it seems that our mail server is not responding. Please try again later!"));
                $('#success > .alert-danger').append('</div>');
                $('#contactForm').trigger("reset");
            },
            complete: function() {
                setTimeout(function() {
                    $this.prop("disabled", false);
                }, 1000);
            }
        });
    }
</script>

<body>
    <?php include 'header.php'; ?>


    <!-- Breadcrumb Start -->
    <div class="container-fluid">
        <div class="row px-xl-5">
            <div class="col-12">
                <nav class="breadcrumb bg-light mb-30">
                    <a class="breadcrumb-item text-dark" href="#">Home</a>
                    <span class="breadcrumb-item active">Contact</span>
                </nav>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->


    <!-- Contact Start -->
    <div class="container-fluid">
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">Contact Us</span></h2>
        <div class="row px-xl-5">
            <div class="col-lg-7 mb-5">
                <div class="contact-form bg-light p-30">
                    <div id="success"></div>
                    <form name="sentMessage" id="contactForm" method="post" action="mail/contact-admin.php">
                        <div class="control-group">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Your Name" data-validation-required-message="Please enter your name" />
                            <p class="help-block text-danger"></p>
                        </div>
                        <div class="control-group">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" data-validation-required-message="Please enter your email" />
                            <p class="help-block text-danger"></p>
                        </div>
                        <div class="control-group">
                            <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" data-validation-required-message="Please enter a subject" />
                            <p class="help-block text-danger"></p>
                        </div>
                        <div class="control-group">
                            <textarea class="form-control" rows="8" name="message" id="message" placeholder="Message" data-validation-required-message="Please enter your message"></textarea>
                            <p class="help-block text-danger"></p>
                        </div>
                        <div>
                            <button data-sitekey="6LdraxUnAAAAAGJgff6bYPpk6cCZ73qGSYUQV9Cz" data-callback='onSubmit' data-action='submit' class="g-recaptcha btn btn-primary py-2 px-4" type="submit" id="sendMessageButton">Send
                                Message</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 mb-5">
                <div class="bg-light p-30 mb-30">

                    <iframe style="width: 100%; height: 250px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3526.3466806371066!2d78.07569247500707!3d27.891332717353446!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3974a5ab24f3633b%3A0x506dccc72ba20c2c!2sCenter%20point%20%2CAligarh!5e0!3m2!1sen!2sin!4v1695650930224!5m2!1sen!2sin" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0" loading="lazy" frameborder="0" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="bg-light p-30 mb-5">
                    <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>Center Point, Aligarh</p>
                    <p class="mb-2"><i class="fa fa-envelope text-primary mr-3"></i>rajatagrawal9394@gmail.com</p>
                    <p class="mb-2"><i class="fa fa-phone-alt text-primary mr-3"></i>+91 9045067810</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->


    <?php

    include 'footer.php';
    include 'common_scripts.php';
    ?>


</body>

</html>