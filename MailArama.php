
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



// gmail için : ("{imap.gmail.com:993/imap/ssl}INBOX", "********@gmail.com", "sifre")
if (!$mbox = imap_open ("{imap.gmail.com:993/imap/ssl}INBOX", "*********@gmail.com", "*********"))
{
  die ('Mail sunucusuna bağlanılamadı!');
}

if ($hdr = imap_check($mbox)) {
  $msgCount = $hdr->Nmsgs;
} else {
  echo "Maillere ulaşılamadı.";
  exit;
}


	echo "<center><table style='width:80%;  border: 1px solid black;'><tr><th>Mail No:</th><th>Mail Başlığı:</th> <th>Kimden:</th><th>Mail Durumu</th></tr>";
	$MN=$msgCount;
	for ($i=0; $i < 50; $i++) //kaç adet mail gösterileceği
	{
		$mesaj=$MN-$i;
		$govde = imap_body($mbox, $msgCount-$i);
		$baslik = imap_headerinfo($mbox, $mesaj);//son mesajı okuma


		echo "<tr><th>" . $mesaj ."</th>";
		echo "";	
		echo "<th><a href='Mail.php?mailNo=" . $mesaj . "' target='_blank'>" . $baslik->subject . "</a></th>";
		echo "<th> </b>" . $baslik->fromaddress . "</th>";
		$yazi=arama($govde);
		echo $yazi;
	}

function arama($mailIcerigi)
{
	global $Email__Full;
	global $Email__NotExist;
	global $Email__Banned;
	global $Email__OutOffice;

		for ($i=0; $i < count($Email__Full); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__Full[$i]);
			if($sonuc!==false)
			{
					return "<th> <b style='color:red;'>Mail Dolu</b></th></tr>";
			}
		}

		for ($i=0; $i < count($Email__NotExist); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__NotExist[$i]);
			if($sonuc!==false)
			{
			return "<th> <b style='color:red;'>Mail Adresi Bulunmuyor</b></th></tr>";
			}
		}

	for ($i=0; $i < count($Email__Banned); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__Banned[$i]);
			if($sonuc!==false)
			{
			return "<th> <b style='color:red;'>Mail Engellenmiş</b></th></tr>";
			}
		}

	for ($i=0; $i < count($Email__OutOffice); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__OutOffice[$i]);
			if($sonuc!==false)	
			{
			return "<th> <b style='color:red;'>Email__OutOffice</b></th></tr>";
			}
		}


		echo "<th> <b style='color:red;'>---------</b></th></tr>"; //sebep bulunamadığı durumlarda
}
echo "</center>";
imap_expunge($mbox);
imap_close($mbox);


?>


</body>
</html>
