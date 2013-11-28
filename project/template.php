<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	</head>
	<body>
		<table>
			<?php foreach ($links as $link => $number) { ?>
				<tr>
					<td><?php echo rawurldecode($link); ?></td>
					<td><?php echo $number; ?></td>
				</tr>
			<?php }?>
		</table>
	</body>
</html>