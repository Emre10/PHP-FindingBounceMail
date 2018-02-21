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

if (!$MailKutusu = imap_open ("{webmail.*******.com:143/notls}INBOX", "******@******.com", "*******"))//Edit this line
{
  die ('Mail sunucusuna bağlanılamadı!');
}

if ($hdr = imap_check($MailKutusu)) {
  $mesajSayisi = $hdr->Nmsgs;
} else {
  echo "Maillere ulaşılamadı.";
  exit;
}

		$mailNo=$_GET['mailNo'];
		$govde = imap_body($MailKutusu, $mailNo);
		echo $govde;

imap_expunge($MailKutusu);
imap_close($MailKutusu);


?>


</body>
</html>
