<?php session_start(); 

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TC01 - File Upload</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/custom.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Radius Accounting</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">01. File Upload</a></li>
            <li class="disabled"><a href="#">02. Log Processing</a></li>
            <li class="disabled"><a href="#">03. Display Result</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

      <div class="starter-template">
        <h1>File Log Available</h1>
        <p class="lead">Please use the following button to upload log file for processing.</p>
        <?php if (isset($_SESSION['error_message'])) : ?>
        <div id="error-message" class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Error:</strong> <?php echo $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <?php if (! file_exists('./uploads') || ! is_writable('./uploads')) : ?>
        <div class="alert alert-warning alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Warning:</strong> folder `uploads` should exists and writable.
        </div>
        <?php endif; ?>
        <form action="file_upload.php" method="post" enctype="multipart/form-data">
          <span class="fileUpload btn btn-default">
              <span class="glyphicon glyphicon-upload"></span> Upload file
              <input type="file" name="logfile" id="uploadFile" />
          </span>
      </form>
      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $("#uploadFile").change(function() {
        $("form").submit();
      });
      if ($("#error-message").length > 0) {
        setTimeout(function() {
          $("#error-message").fadeOut();
        }, 7000);
      }
    </script>
  </body>
</html>