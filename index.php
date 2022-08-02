<?php
	include_once('i18n.php');

	$k = $_GET["k"] ?? "";
	$confirm = boolval($_GET["confirm"] ?? false);
	$filename = rawurldecode($_GET["filename"] ?? $k);

	$content = (function() {
		global $k, $confirm, $filename;

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

		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename=\"" . rawurlencode($filename) . "\""); 
		echo(file_get_contents($path));
		unlink($path);
		exit();
	})();

	$loading = i18n("loading.md");
	$invalid = i18n("invalid.md");
	$error = i18n("error.md");
?>

<!DOCTYPE html>
<html>
	<head>
		<title>VisitOnce</title>

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

			code {
				margin: 0 5px;
				padding: 5px;

				border-radius: 5px;

				background: lightgray;
			}
		</style>
	</head>

	<body>
		<div id="content"></div>

		<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
		<script>
			const args = new URLSearchParams(window.location.search);

			function load(str) {
				document.getElementById('content').innerHTML = marked.parse(str);
			}

			function confirm() {
				load(<?php echo(json_encode($loading)); ?>);

				const type = args.get('type') ?? "markdown";
				switch(type) {
					case "markdown":
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
						xhr.open("GET", window.location.search + "&confirm=true", true);
						xhr.send();
						break;
					case "download":
						window.location.replace(window.location.search + "&confirm=true");
						break;
					default:
						load(<?php echo(json_encode($invalid)); ?>);
						break;
				}
			}

			load(<?php echo(json_encode($content)); ?>);
		</script>
	</body>

</html>
