
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
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
		include('ListeMailAdres.php');//maillerdeki adres bulma metodlarını içeren dizinin programa eklenmesi
		include('ListeMailIcerik.php');

	$okunacakMailSayisi=15; //eger tüm mailler okunacaksa $okunacakMailSayisi=$mesajSayisi
	$HB=0; $SB=0; $incelenenMail=0; //Oranların hesaplanması için tanımlanan değerler

	echo "<center><table style='width:80%;  border: 1px solid black;'><tr><th>Mail No:</th><th>Mail Başlığı:</th> <th>Kimden:</th><th>Hata Mesajı Tam</th><th>Hata Mesajı</th><th>Mail Durumu</th></tr>";




	for ($i=0; $i < $okunacakMailSayisi; $i++) //Kaç adet mail gösterileceği(inceleneceği), en fazla $mesajSayisi kadar olabilir.
	{
		$okunanMesajNo=$mesajSayisi-$i;
		$mailGovde= imap_body($MailKutusu, $okunanMesajNo);//mailin içeriği, gövde kısmı
		$mailDetay = imap_headerinfo($MailKutusu, $okunanMesajNo);//$mesaj mailin içeriği
		echo "<tr><th>" . $okunanMesajNo ."</th>";	
		echo "<th><a href='Mail.php?mailNo=" . $okunanMesajNo . "' target='_blank'>" . mb_convert_encoding($mailDetay->subject ,'UTF-8',mb_detect_encoding($mailDetay->subject,'ISO-8859-9',true)) . "</a></th>";

	
		//echo "<th> </b>" .$mailDetay->from[0]->mailbox  . "@" . $mailDetay->from[0]->host . "</th>";

		$gonderenMail=gonderenMailBul($mailGovde);
		echo "<th> </b>". $gonderenMail ."</th>";
		//$mailIcerikTam=mailIcerikBul($mailGovde);
		$mailIcerikTam= imap_fetchbody($MailKutusu, $okunanMesajNo,1);
		echo "<th> </b>". $mailIcerikTam ."</th>";
		$yazi=arama($mailGovde);
		echo $yazi;
		$incelenenMail++;
	}



		function gonderenMailBul($mailIcerigi)
		{
			global $mailServerBulma;

			for ($i=0; $i < count($mailServerBulma); $i++) 
			{ 
				$gonderenMailSira=strpos($mailIcerigi, $mailServerBulma[$i][0]);

					if($gonderenMailSira!==false)
				{
					$gonderenMailSira2=strpos($mailIcerigi, $mailServerBulma[$i][1]);
					$fark=$gonderenMailSira2-$gonderenMailSira;
					$metin=substr($mailIcerigi, $gonderenMailSira+$mailServerBulma[$i][2], $fark - $mailServerBulma[$i][2]-1);

					return $metin;
				}


			}
				
				return "Bulunamadı";
		}


	function arama($mailIcerigi)
	{
		global $Email__Full;
		global $Email__NotExist;
		global $Email__Banned;
		global $Email__OutOffice;
		global $SB;
		global $HB;

		for ($i=0; $i < count($Email__NotExist); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__NotExist[$i]);
			
			if($sonuc!==false)
			{
				$HB++;
				return "<th>". $Email__NotExist[$i] . "</th><th> <b style='color:red;'>Mail Adresi Bulunmuyor</b></th></tr>";
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

		for ($i=0; $i < count($Email__Full); $i++) 
		{ 
			$sonuc=strpos($mailIcerigi, $Email__Full[$i]);
			
			if($sonuc!==false)
			{
				$SB++;
				return "<th>". $Email__Full[$i] . "</th><th> <b style='color:green;'>Mail Dolu</b></th></tr>";
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
