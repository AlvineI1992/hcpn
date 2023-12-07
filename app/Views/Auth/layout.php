<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">
    <title>DOH-eReferral</title>
    <!-- Bootstrap core CSS -->
    <script src="<?= base_url('../dist/bundle.js') ?>"></script>
    <style>
        body {
            padding-top: 5rem;
        }
    </style>
    <?= $this->renderSection('pageStyles') ?>
</head>
<body>
<main role="main" class="container">
	<?= $this->renderSection('main') ?>
</main><!-- /.container -->
<?= $this->renderSection('pageScripts') ?>
</body>
</html>
