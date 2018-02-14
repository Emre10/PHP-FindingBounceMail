<!DOCTYPE html>
<html>
<head>
	<title>
		PROGRAM
	</title>

<style>table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}</style>
</head>
<body>

<?php

include('Liste.php');


if (!$mbox = imap_open ("{webmail.turkiyeegitim.com:143/notls}INBOX", "bilgi@turkiyeegitim.com", "2NF1Xw3Vk7hpfztn"))
{
  die ('Mail sunucusuna bağlanılamadı!');
}

if ($hdr = imap_check($mbox)) {
  $msgCount = $hdr->Nmsgs;
} else {
  echo "Maillere ulaşılamadı.";
  exit;
}

		$mailNo=$_GET['mailNo'];
		$govde = imap_body($mbox, $mailNo);
		echo $govde;

imap_expunge($mbox);
imap_close($mbox);


?>


</body>
</html>
