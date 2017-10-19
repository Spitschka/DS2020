<?php

function myErrorHandlerImpl ($code, $msg, $file, $line, $context) {
	if(isFatalError($code)) {
		// Fehler anzeigen
		?>
		<html>
			<head>
				<title>DS2020 Fatal Error</title>
			</head>
			<body>
				<center>
				<h1>FatalError Executing DS2020</h1>
			
				<table border="1" width="50%">
					<tr>
						<td width="15%">Code</td>
						<td><?php echo($code); ?></td>
					</tr>
					<tr>
						<td width="15%">Nachricht</td>
						<td><?php echo($msg); ?></td>
					</tr>
					<tr>
						<td width="15%">Datei</td>
						<td><?php echo($file); ?></td>
					</tr>
					<tr>
						<td width="15%">Zeile</td>
						<td><?php echo($line); ?></td>
					</tr>
					<tr>
						<td width="15%">Kontext</td>
						<td><pre><?php print_R($context); ?></pre></td>
					</tr>
				</table>
				</center>
			</body>
		
		</html>
		<?php
		// exit(0);
	}
	else {
		// Fehler aufzeichnen und Fehlerseite anzeigen
		if(isFatalError($code)) {
			// TODO Fehler aufzeichnen
		}
	}

}

function isFatalError($code) {
	return $code == E_ERROR || $code == E_PARSE || $code == E_COMPILE_ERROR;
}