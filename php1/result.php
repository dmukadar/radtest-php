<?php
  include "common.inc.php";

  $page = 1;
  $rows = array();
  $dbLink = new DbLink($dbconfig);
  $dbLink->connect();
  $maxPage = $dbLink->getTotalPageNumber();

  if ($maxPage > 0) {
    $page = @$_GET['page'];
    if (empty($page)) $page = 1;
    else if ($page > $maxPage) $page = $maxPage;

    $rows = $dbLink->getPerPage($page);
  }
  $counter = $dbLink->rowLimit * ($page - 1) + 1;
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TC03 - View Result</title>

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
            <li class="disabled"><a href="progress.php">02. Log Processing</a></li>
            <li class="active"><a href="result.php">03. Display Result</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

      <div class="starter-template">
        <h1>Import Result</h1>
        <p class="lead">Result is split into pages, use navigation list to jump page. Or search box to find certain record.</p>

        <?php if (empty($rows)) : ?>
        <div id="error-message" class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Error:</strong> No data found, please process another log file or use different search query.
        </div>
        <?php endif; ?>

        <div class="pull-right">
          <form class="form-inline">
            <div class="form-group">
              <input class="form-control" type="text" placeholder="Search record" />
              <button class="btn">
                <span class="glyphicon glyphicon-search"></span>
              </button>
            </div>
          </form>
          <br/>
        </div>

        <div>
          <table class="table table-striped table-bordered table-condensed">
            <thead>
              <tr>
                <th>#</th>
                <th>Session ID</th>
                <th>Setup Time</th>
                <th>Connect Time</th>
                <th>Disconnect Time</th>
                <th>Calling Station ID</th>
                <th>Called Station ID</th>
              </tr>
            </thead>
            <?php if (! empty($rows)) : ?>
            <tbody>
              <?php foreach ($rows as $record) : ?>
              <?php $record = (object)$record; ?>
              <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo $record->acctSessionId; ?></td>
                <td><?php echo empty($record->setupTime) ? null : $dbLink->localConvert($record->setupTime); ?></td>
                <td><?php echo empty($record->connectTime) ? null : $dbLink->localConvert($record->connectTime); ?></td>
                <td><?php echo empty($record->disconnectTime) ? null : $dbLink->localConvert($record->disconnectTime); ?></td>
                <td><?php echo $record->callingStationId; ?></td>
                <td><?php echo $record->calledStationId; ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <?php endif; ?>
          </table>

          <div id="page-number" class="pagination-sm">
          </div>
        </div>
      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.twbsPagination.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script type="text/javascript">
        $('#page-number').twbsPagination({
          totalPages: <?php echo $maxPage; ?>,
          visiblePages: 7,
          href: '?page={{number}}',
          onPageClick: function (event, page) {
              $('#page-content').text('Page ' + page);
          }
      });
    </script>
  </body>
</html>