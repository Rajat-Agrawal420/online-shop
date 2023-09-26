<head>
  <meta charset="utf-8">
  <title><?php echo $site_name; ?></title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="Free HTML Templates" name="keywords">
  <meta content="Free HTML Templates" name="description">

  <!-- Favicon -->
  <link href="img/shop3.png" rel="icon">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

  <!-- Customized Bootstrap Stylesheet -->
  <link href="css/style.css" rel="stylesheet">

  <link href="css/sweetalert2.min.css" rel="stylesheet">

  <style>
    .dropdown-item {
      outline: none !important;
    }

    .stars li {
      display: inline-block;
      list-style-type: none;
    }

    /* doesnt work funnly on firefox or edge, need to fix */

    .range-slider {
      width: 200px;
      text-align: center;
      position: relative;
      margin-bottom: 1rem;
    }

    .rangeValues {
      display: block;

    }


    input[type="range"] {
      -webkit-appearance: none;
      border: 1px solid white;
      width: 300px;
      position: absolute;
      left: 0;
    }

    input[type="range"]::-webkit-slider-runnable-track {
      width: 300px;
      height: 5px;
      background: #ddd;
      border: none;
      border-radius: 3px;
    }

    input[type="range"]::-webkit-slider-thumb {
      -webkit-appearance: none;
      border: none;
      height: 16px;
      width: 16px;
      border-radius: 50%;
      background: #0d6efd !important;
      margin-top: -4px;
      cursor: pointer;
      position: relative;
      z-index: 1;
    }

    input[type="range"]:focus {
      outline: none;
    }

    input[type="range"]:focus::-webkit-slider-runnable-track {
      background: #ccc;
    }

    input[type="range"]::-moz-range-track {
      width: 300px;
      height: 5px;
      background: #ddd;
      border: none;
      border-radius: 3px;
    }

    input[type="range"]::-moz-range-thumb {
      border: none;
      height: 16px;
      width: 16px;
      border-radius: 50%;
      background: #21c1ff;
    }

    /*hide the outline behind the border*/

    input[type="range"]:-moz-focusring {
      outline: 1px solid white;
      outline-offset: -1px;
    }

    input[type="range"]::-ms-track {
      width: 300px;
      height: 5px;
      /*remove bg colour from the track, we'll use ms-fill-lower and ms-fill-upper instead */
      background: transparent;
      /*leave room for the larger thumb to overflow with a transparent border */
      border-color: transparent;
      border-width: 6px 0;
      /*remove default tick marks*/
      color: transparent;
      z-index: -4;
    }

    input[type="range"]::-ms-fill-lower {
      background: #777;
      border-radius: 10px;
    }

    input[type="range"]::-ms-fill-upper {
      background: #ddd;
      border-radius: 10px;
    }

    input[type="range"]::-ms-thumb {
      border: none;
      height: 16px;
      width: 16px;
      border-radius: 50%;
      background: #21c1ff;
    }

    input[type="range"]:focus::-ms-fill-lower {
      background: #888;
    }

    input[type="range"]:focus::-ms-fill-upper {
      background: #ccc;
    }

    .spinner-border {
      width: 40px;
      height: 40px;
    }

    .container {
      padding: 2rem 0rem;
    }

    @media (min-width: 576px) {
      .modal-dialog {
        max-width: 400px;
      }

      .modal-dialog .modal-content {
        padding: 1rem;
      }
    }

    .modal-header .close {
      margin-top: -1.5rem;
    }

    .form-title {
      margin: -2rem 0rem 2rem;
    }

    .btn-round {
      border-radius: 3rem;
    }

    .delimiter {
      padding: 1rem;
    }

    .social-buttons .btn {
      margin: 0 0.5rem 1rem;
    }

    .signup-section {
      padding: 0.3rem 0rem;
    }

    .modal-dialog .close {
      outline: none;
    }
  </style>

  <style>
    * {
      box-sizing: border-box;
    }

    .img-zoom-container {
      position: relative;
    }

    .img-zoom-lens {
      position: absolute;
      border: 1px solid #d4d4d4;
      /*set the size of the lens:*/
      width: 60px;
      height: 60px;
    }

    .img-zoom-result {
      border: 1px solid #d4d4d4;
      position: fixed;
      float: right;
      /*set the size of the result div:*/
      width: 300px;
      height: 300px;
      top: 40vh;
      left: 32vw;
      z-index: 10;
    }
  </style>
</head>