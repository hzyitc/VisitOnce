<?php
	include_once('i18n.php');

	$k = $_GET["k"] ?? "";
	$confirm = boolval($_GET["confirm"] ?? false);

	$content = (function() {
		global $k, $confirm;

		if($k == "")
			return i18n("index.md");

		if(!preg_match('/^[0-9a-zA-Z\-]+$/', $k)) {
			$k = "";
			return i18n("invalid.md");
		}

		$path = "contents/${k}.md";
		if(!is_file($path))
			return i18n("404.md");

		if(!$confirm)
			return i18n("confirm.md");

		echo(file_get_contents($path));
		unlink($path);
		exit();
	})();

	$loading = i18n("loading.md");
	$error = i18n("error.md");
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Read once</title>

		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<style type="text/css">
			html {
				background-color: #f6f8fa;
			}

			body {
				padding: 30px;

				font: 400 16px/1.5 "Helvetica Neue", Helvetica, Arial, sans-serif;
				color: #111;
			}

			body>#content {
				max-width: 900px;

				margin: 0px auto;
				padding: 10px 40px;

				border: 1px solid #e1e4e8;
				border-radius: 10px;

				background-color: #ffffff;
			}

			@media only screen and (max-width: 600px) {
				body {
					padding: 5px;
				}

				body>#content {
					padding: 0 20px !important;
				}
			}
		</style>
	</head>

	<body>
		<div id="content"></div>

		<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
		<script>
			function load(str) {
				document.getElementById('content').innerHTML = marked.parse(str);
			}

			function confirm() {
				document.getElementById('content').innerHTML = marked.parse(<?php echo(json_encode($loading)); ?>);

				var xhr = new XMLHttpRequest();
				xhr.onreadystatechange = function() {
					if(this.readyState == 4) {
						if(this.status == 200) {
							load(xhr.responseText);
						} else {
							load(<?php echo(json_encode($error)); ?>);
						}
					}
				};
				xhr.open("GET", "?k=<?php echo($k); ?>&confirm=true", true);
				xhr.send();
			}

			load(<?php echo(json_encode($content)); ?>);
		</script>
	</body>

</html>
