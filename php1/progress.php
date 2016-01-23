<?php
  include "common.inc.php";

  $filesize = 0;
  if (file_exists($filepath)) {
    $decimals = 2;
    $sz = 'BKMGTP';
    $bytes = filesize($filepath);
    $factor = floor((strlen($bytes) - 1) / 3);
    $filesize = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
  }
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TC02 - File Process</title>

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
          <a class="navbar-brand" href="index.php">Radius Accounting</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="disabled"><a href="index.php">01. File Upload</a></li>
            <li class="active"><a href="progress.php">02. Log Processing</a></li>
            <li class="disabled"><a href="#">03. Display Result</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

      <div class="starter-template">
        <h1>Processing Log File</h1>
        <p id="progress_message" class="lead">Please wait while the script is running...</p>
        <p>
          <div id="bar-container" class="progress" style="display:none;">
            <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
              <span class="sr-only" style="position: relative;" id="progress_count">&nbsp;</span>
            </div>
          </div>
        </p>

        <div id="error-message" class="alert alert-danger alert-dismissible" role="alert" style="display:none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Error:</strong> Can not find any file, please upload log file to continue.
        </div>
        
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Stat</th>
              <th>Result</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th>1.</th>
              <td>Filesize</td>
              <td id="result-1"></td>
            </tr>
            <tr>
              <th>2.</th>
              <td>Number of lines processed</td>
              <td id="result-2"></td>
            </tr>
            <tr>
              <th>3.</th>
              <td>Record processed</td>
              <td id="result-3"></td>
            </tr>
            <tr>
              <th>4.</th>
              <td>Number of Call</td>
              <td id="result-4"></td>
            </tr>
            <tr>
              <th>5.</th>
              <td>Done</td>
              <td id="result-5"></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script type="text/javascript">
      var filesize = "<?php echo $filesize; ?>";
      var spinnerTemplate = '<span class="spinner"><img src="assets/img/6.gif"/></span>';
      var bar = {
        interval: 20,
        percentCount: 0,
        barID: "#progress-bar",
        countID: "#progress_count",
        run: function() {
          var self = this;
          self.percentCount += self.interval;
          if (self.percentCount > 100) self.percentCount = 100;
          else {
            var completed = self.percentCount + "%";
            $(self.barID).attr("aria-valuenow", self.percentCount);
            $(self.barID).attr("style", "width: "+completed);
            $(self.countID).empty().append(completed + " Complete");
          }
        }
      }

      $("#result-1").html(spinnerTemplate);
      setTimeout(function() {
        $("#result-1").empty().append(filesize);
        if (filesize != "0") {
          $("#bar-container").show();
          bar.run();
          $("#result-2").html(spinnerTemplate);
          $("#result-3").html(spinnerTemplate);
          $("#result-4").html(spinnerTemplate);
          $.ajax({
            url: "process.php",
            dataType: "json"
          })
          .done(function(stat) {
            $("#result-2").empty().append(stat.lineCount);
            bar.run();
            $("#result-3").empty().append(stat.recordCount);
            bar.run();
            $("#result-4").empty().append(stat.callCount);
            bar.run();
            $("#result-5").html(spinnerTemplate);
            setTimeout(function() {
              bar.run();
              $("#result-5").empty().append('<a href="result.php" class="btn btn-primary btn-sm">View Result</a>');
            }, 2000);
          })
          .fail(function() {
            $("#result-2").empty();
            $("#result-3").empty();
            $("#result-4").empty();
            alert("Error: Connection failed!");
          });
        } else {
          $("#error-message").show();
          setTimeout(function() {
            $("#error-message").fadeOut();
          }, 7000);
        }
      }, 2000);
      // setTimeout(function(){progressBar.increase();}, 3000);
      // progressBar.run();
    </script>
  </body>
</html>