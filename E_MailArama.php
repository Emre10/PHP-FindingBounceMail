
<!DOCTYPE html>
<html>
<head>
	<title>
		PROGRAM
	</title>

	<style>
	table, th, td 
	{
    border: 1px solid black;
    border-collapse: collapse;
	}
	</style>
</head>
<body>

<?php


// webmail için imap_open ("{webmail.*******.com:143/notls}INBOX", "****@******.com", "sifre")
// gmail için : imap_open ("{imap.gmail.com:993/imap/ssl}INBOX", "********@gmail.com", "sifre")
	if (!$MailKutusu = imap_open ("{webmail.turkiyeegitim.com:143/notls}INBOX", "bilgi@turkiyeegitim.com", "2NF1Xw3Vk7hpfztn"))
	{
		die ('Mail sunucusuna bağlanılamadı!');
	}

	if ($hdr = imap_check($MailKutusu)) 
	{
  		$mesajSayisi = $hdr->Nmsgs; //toplam mail sayısı
	} 
	else 
	{
 		echo "Maillere ulaşılamadı.";
 		exit;
	}

	

	include('Liste.php');//maillerdeki hata listelerinin programa eklenmesi

	$okunacakMailSayisi=40; //eger tüm mailler okunacaksa $okunacakMailSayisi=$mesajSayisi
	$HB=0; $SB=0; $incelenenMail=0; //Oranların hesaplanması için tanımlanan değerler

	echo "<center><table style='width:80%;  border: 1px solid black;'><tr><th>Mail No:</th><th>Mail Başlığı:</th> <th>Kimden:</th><th>Hata Mesajı</th><th>Mail Durumu</th></tr>";




	for ($i=0; $i < $okunacakMailSayisi; $i++) //Kaç adet mail gösterileceği(inceleneceği), en fazla $mesajSayisi kadar olabilir.
	{
		$okunanMesajNo=$mesajSayisi-$i;
		$mailGovde= imap_body($MailKutusu, $okunanMesajNo);//mailin içeriği, gövde kısmı
		$mailDetay = imap_headerinfo($MailKutusu, $okunanMesajNo);//$mesaj mailin içeriği

		echo "<tr><th>" . $okunanMesajNo ."</th>";	
		echo "<th><a href='Mail.php?mailNo=" . $okunanMesajNo . "' target='_blank'>" . $mailDetay->subject . "</a></th>";
		//echo "<th> </b>" .$mailDetay->from[0]->mailbox  . "@" . $mailDetay->from[0]->host . "</th>";

		$gonderenMail=gonderenMailBul($mailGovde);
		echo "<th> </b>". $gonderenMail ."</th>";

		$yazi=arama($mailGovde);
		echo $yazi;
		$incelenenMail++;
	}



	function gonderenMailBul($mailIcerigi)
	{
			$gonderenMailSira=strpos($mailIcerigi, "Final-Recipient: rfc822; ");
			$gonderenMailSira2=strpos($mailIcerigi, "Original-Recipient: rfc822");

			if($gonderenMailSira!==false)
			{
				$fark=$gonderenMailSira2-$gonderenMailSira;
				$metin=substr($mailIcerigi, $gonderenMailSira+25, $fark-26);
				return $metin;

			}
			else
			{
				return "Bulunamadı";

			}

		
	}

	function arama($mailIcerigi)
	{
		global $Email__Full;
		global $Email__NotExist;
		global $Email__Banned;
		global $Email__OutOffice;
		global $SB;
		global $HB;

		for ($i=0; $i < count($Email__Full); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__Full[$i]);
			
			if($sonuc!==false)
			{
				$SB++;
				return "<th>". $Email__Banned[$i] . "</th><th> <b style='color:green;'>Mail Dolu</b></th></tr>";
			}
		}

		for ($i=0; $i < count($Email__NotExist); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__NotExist[$i]);
			
			if($sonuc!==false)
			{
				$HB++;
				return "<th>". $Email__Banned[$i] . "</th><th> <b style='color:red;'>Mail Adresi Bulunmuyor</b></th></tr>";
			}
		}

	for ($i=0; $i < count($Email__Banned); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__Banned[$i]);
			
			if($sonuc!==false)
			{
				$SB++;
				return "<th>". $Email__Banned[$i] . "</th><th> <b style='color:green;'>Mail Engellenmiş</b></th></tr>";
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
		echo "<th><b style='color:red;';> Hata Bulunamadı </b></th>";
		echo "<th> <b style='color:red;'>---------</b></th></tr>"; //sebep bulunamadığı durumlarda
}


//oranların hesaplanması ve yazdırılması
	$bulunamayan=$incelenenMail-($SB+$HB);
	echo "<center><table style='width:80%;  border: 1px solid black;'><tr></tr>";
	echo "<th><b>İncelenen Mail Sayısı: " . $incelenenMail . "</b><br></th>";
	echo "<th><b style='color:red;'>Hard Bounce Mail Sayısı: " . $HB . "<br> Oranı: %" . $HB*(100/$okunacakMailSayisi) . "</b><br></th>";
	echo "<th><b style='color:green;'>Soft Bounce Mail Sayısı: " . $SB ."<br> Oranı: %" . $SB*(100/$okunacakMailSayisi) . "</b><br></th>";
	echo "<th><b style='color:black;'>Geri Dönüşü Olmayan veya Bilinemeyen Mail Sayısı: " . $bulunamayan . "<br> Oranı: %" . $bulunamayan*(100/$okunacakMailSayisi) . "</b><br></th>";
	echo "</center>";

	//imap bağlantısının kapatılması
	imap_expunge($MailKutusu);
	imap_close($MailKutusu);

?>

</body>
</html>
