<?php

if(
	!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
	md5($_SERVER['PHP_AUTH_USER'] . 'HU3') !== md5('tcommerceuser' . 'HU3') ||
	md5($_SERVER['PHP_AUTH_PW'] . 'HU3') !== md5('tcommercepass' . 'HU3')
) {
	header('WWW-Authenticate: Basic realm="TCommerce"');
	header('HTTP/1.0 401 Unauthorized');

	echo 'Não autorizado';
	exit(0);
}

set_time_limit(300);

ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7);

session_name('apitester');
session_set_cookie_params(60 * 60 * 24 * 7, '/');
session_start();

define('LOG_CALLS', true);

$data = [
	'url' => '',
	'method' => 'GET'
];
if(isset($_POST['data']))
{
	$config = require_once '../config/config.php';

	$data = $_POST['data'];

	$method = $data['method'];
	$url = $data['url'];
	if(!isset($_SESSION['calls'])) $_SESSION['calls'] = [];

	$call = array(
		'method' => $method,
		'url' => $url,
		'post_names' => isset($_POST['post_names']) ? $_POST['post_names'] : array(),
		'post_values' => isset($_POST['post_values']) ? $_POST['post_values'] : array(),
		'body' => isset($_POST['body']) ? $_POST['body'] : '',
		'body_type' => isset($_POST['body_type']) ? $_POST['body_type'] : ''
	);
	$hash = md5(var_export($call, true));

	$unshift = true;
	if(count($_SESSION['calls']) > 0)
	{
		$last_hash = md5(var_export($_SESSION['calls'][0], true));
		if($hash == $last_hash) $unshift = false;
	}

	if($unshift) {
		array_unshift($_SESSION['calls'], $call);
	}

	$headers = [
		'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MX0.qWs87GOtlg-obGmcM-0AppGzkDBehQMpU8vCqbNqUXo',
		'Accept' => 'application/json'
	];
	if(isset($_SESSION['X-TSession']))
	{
		$headers['X-TSession'] = $_SESSION['X-TSession'];
	}

	$ctype = 'application/x-www-form-urlencoded';
	if(isset($_POST['body']) && trim($_POST['body']) != '' || isset($_POST['body_type']) && $_POST['body_type'] != '')
	{
		$body_type = isset($_POST['body_type']) ? trim($_POST['body_type']) : null;
		$ctype = !empty($body_type) ? $body_type : 'text/plain; charset=UTF-8';
		$fields = $_POST['body'];
	}
	else
	{
		$query = $amp = '';
		if(isset($_POST['post_names']))
		{
			foreach($_POST['post_names'] as $i=>$post_name)
			{
				$query .= $amp . $post_name . '=' . urlencode(@$_POST['post_values'][$i]);
				$amp = '&';
			}
		}
		parse_str($query, $fields);
	}


	$full_url = $url;
	//Se não tiver http no começo, e começar com uma barra
	if(!preg_match('/^https?:\\/\\//', $url) && preg_match('/^\\//', $url)) {
		$full_url = 'http' . ($config->application->forceSSL ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . rtrim($config->application->baseUri, '/') . '/api/v1' . $url;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_URL, $full_url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

	$input = '';
	if(!empty($fields))
	{
		if(empty($fields)) $input = '';
		elseif(is_array($fields) || is_object($fields)) $input = http_build_query($fields);
		else
		{
			$input = (string)$fields;
		}

		curl_setopt($ch, CURLOPT_POSTFIELDS, $input);

		if(!empty($ctype)) $headers['Content-Type'] = $ctype;
	}

	if(!empty($headers))
	{
		$headers_arr = array();
		foreach($headers as $header=>$header_value)
		{
			if(is_array($header_value))
			{
				foreach($header_value as $header_single_value)
				{
					$headers_arr[] = $header . ': ' . $header_single_value;
				}
			}
			else $headers_arr[] = $header . ': ' . $header_value;
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_arr);
	}

	function parse_header($headerStr, &$httpResponse)
	{
		$headers = array(); $httpResponse = null;
		foreach(explode("\r\n", $headerStr) as $header)
		{
			if(empty($header)) { continue; }
			if(strtolower(substr($header, 0, 4)) == 'http')
			{
				$httpResponse = $header;
			}
			elseif(strpos($header, ':') !== false)
			{
				list($name, $val) = explode(':', $header);
				$headers[$name] = trim($val);
			}
		}

		return $headers;
	}

	$raw_response = curl_exec($ch);
	$raw_request_headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
	$raw_request = $raw_request_headers . $input;
	$full_request = $raw_request_headers . $raw_response;
	if($raw_response === false) {
		throw new \Exception('Curl Call Error: ' . curl_error($ch));
	}

	$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$header_length = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($raw_response, 0, $header_length);
	$body = substr($raw_response, $header_length);

	$header_parsed = parse_header($header, $http_response);
	$response = [
		'http' => $http_response,
		'raw_header' => $header,
		'header' => $header_parsed,
		'body' => $body
	];

	if(LOG_CALLS)
	{
		$fh = fopen($config->application->logsDir . 'apitester.log', 'a');
		fwrite($fh, "-------------------------------------------------------------------------------------------------------------------\n\n");
		fwrite($fh, 'URL: ' . $method . ' ' . $full_url . "\n\n");
		fwrite($fh, "REQUEST:\n" . print_r($raw_request, true) . "\n\n");
		fwrite($fh, "RESPONSE:\n" . rtrim($raw_response, "\r\n") . "\n\n");
		fclose($fh);
	}

	if($url == '/sessions' && $method == 'POST' && floor($code/100) == 2)
	{
		$json = json_decode($response['body'], true);
		if($json) $_SESSION['X-TSession'] = $json['data']['id'];
	}

	$headers = $response['header'];
	header($response['http']);

	unset($header_parsed['Transfer-Encoding']);
	foreach($header_parsed as $k=>$v)
	{
		header("$k: $v");
	}

	echo $response['body'];
	exit(0);
}
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<title>API Tester</title>
	<link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap/bootstrap-theme.min.css">
	<style>
		.container {
			width:800px;
		}
	</style>
	<script type="text/javascript" src="js/jquery-2.1.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap/bootstrap.min.js"></script>

</head>

<body>

<div class="container">
	<h2>API Tester</h2>
	<form action="" method="post" target="_blank">
		<div class="form-group">
			<label>URL:</label>
			<input type="text" id="txtUrl" name="data[url]" class="form-control" style="margin-bottom:5px;">
			<?php if(!empty($_SESSION['calls'])) { ?>
				<label>Or use a previous call: </label>
				<select id="txtPrevUrls" name="data[prev-url]" class="form-control">
					<option value="">Selecionar...</option>
					<?php $i = 0; foreach($_SESSION['calls'] as $call) { if($i > 20) break; ?>
						<option value="<?php echo $i + 1; ?>"
								data-content="<?php echo htmlspecialchars(json_encode($call)); ?>">
							<?php echo $call['method'], ' ', $call['url']; ?>
						</option>
						<?php $i++; } ?>
				</select>
			<?php } ?>
		</div>
		<div class="form-group">
			<label>Method</label>
			<select id="txtMethod" name="data[method]" class="form-control">
				<option value="GET">GET</option>
				<option value="POST">POST</option>
				<option value="PUT">PUT</option>
				<option value="DELETE">DELETE</option>
			</select>
		</div>
		<div style="border:1px solid #ccc; border-radius:4px; padding:10px; margin-bottom:10px;">
			<h3 class="text-center">POST fields</h3>
			<div class="fields">
				<div class="row form-group">
					<label class="col-sm-1">Name</label>
					<div class="col-sm-4">
						<input type="text" name="post_names[]" class="form-control post-name" value="">
					</div>
					<label class="col-sm-1">Value</label>
					<div class="col-sm-4">
						<input type="text" name="post_values[]" class="form-control post-value" value="">
					</div>
					<div class="col-sm-2 text-right">
						<button type="button" class="btn btn-primary btn-add"><span class="glyphicon glyphicon-plus"></span></button>
					</div>
				</div>
			</div>
			<hr>
			<h3 class="text-center">Or raw data</h3>
			<div class="row form-group">
				<label class="col-sm-2">Content-Type</label>
				<div class="col-sm-10">
					<input type="text" id="txtBodyType" name="body_type" class="form-control" value="">
				</div>
			</div>
			<div class="row form-group">
				<label class="col-sm-2">Body</label>
				<div class="col-sm-10">
					<textarea name="body" id="txtBody" class="form-control" rows="10"><?php echo (isset($_POST['body']) ? $_POST['body'] : '') ; ?></textarea>
				</div>
			</div>
		</div>
		<div class="form-group text-right">
			<input type="submit" class="btn btn-default" value="Submit">
		</div>
	</form>
</div>
<script type="text/javascript">
	jQuery(function ($)
	{
		var $fields = $(".fields"),
			$url = $("#txtUrl"),
			$method = $("#txtMethod"),
			$bodyType = $("#txtBodyType"),
			$body = $("#txtBody"),
			$prevUrls = $("#txtPrevUrls");

		$prevUrls.change(function (event)
		{
			var val = parseInt($prevUrls.val());
			if(val)
			{
				var data = $prevUrls.find('option[value="' + val + '"]').data("content");

				$url.val(data.url);
				$method.val(data.method);
				$bodyType.val(data.bodyType ? data.bodyType : data.body_type);
				$body.val(data.body);

				var i = -1;
				$fields.find(".row").each(function ()
				{
					var $this = $(this); i++;
					if(i == 0)
					{
						$this.find("input:text").val("");
						return;
					}

					removeRow($this);
				});

				if($.isArray(data.post_names) && data.post_names.length > 0)
				{
					for(i = 0; i < data.post_names.length; i++)
					{
						var n = data.post_names[i],
							v = data.post_values[i],
							$row = $fields.find(".row:eq(0)");

						if(i > 0)
						{
							$row = addRow();
						}

						var $name = $row.find(".post-name"),
							$value = $row.find(".post-value");

						$name.val(n);
						$value.val(v);
					}
				}
			}
		});

		function addRow(event)
		{
			var $mainRow = $fields.find(".row:eq(0)");

			var $row = $(
				'<div class="' + $mainRow.attr("class") + '">' +
				$mainRow.html() +
				'</div>'
			);

			var $btn = $row.find(".btn-add");
			$btn.removeClass("btn-primary btn-add");
			$btn.addClass("btn-danger btn-remove");
			$btn.html('<span class="glyphicon glyphicon-remove"></span>');
			$btn.click(removeRow);

			$fields.append($row);

			return $row;
		}
		function removeRow(event)
		{
			if(event instanceof jQuery) $target = event;
			else var $target = $(event.currentTarget);

			$target.unbind("click");
			if(!$target.hasClass("row"))
			{
				$target = $target.closest(".row");
			}

			$target.remove();
		}
		$fields.find(".btn-add").click(addRow);
		$fields.find(".btn-remove").click(removeRow);
	});
</script>

</body>

</html>
