<?php

	/*
			 #  BIG DATA ANALTICS & SMART SYSTEMS 2016 #
	 
	 #  THIS PROJECT WAS DEVELOPED FOR EDUCATIONAL PURPOSES ONLY  #
	 
	 # ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ #

	      #  THIS IS THE INSTALLATION FILE FOR THE CACHE DB   #
	*/

	       // THE AES KEY USED TO CRYPTE THE RSA PRIVATE KEY

			$AESKEY = "DxSSDugKGhXQxCCx8qVU0WeBpDMfiffZ";

	/*

		#  GENERATING THE RSA KEYS (PUBLIC & PRIVATE ) #

	*/

		$res=openssl_pkey_new();
		openssl_pkey_export($res, $PRIVATE_KEY);
		$pubbkey=openssl_pkey_get_details($res);
		$PUBLIC_KEY=  $pubbkey["key"];


		echo "#  ==================================  AES KEY  ==================================  </br></br>";
		echo $AESKEY."</br></br>" ;

		echo "#  ==================================  RSA PRIVATE KEY  ==================================  </br></br>";
		echo $PRIVATE_KEY."</br></br>" ;
		
		echo "#  ==================================  RSA PUBLIC KEY  ==================================  </br></br>";
		echo $PUBLIC_KEY."</br>" ;
		echo "#  ==================================  RSA PUBLIC KEY  ==================================  </br></br>";

		/*
			Generating the  Hash of the RSA , AES Hash  
		*/

			$hashRsa = sha1($PRIVATE_KEY);
			$hashAes = sha1($AESKEY);
			echo "#  ==================================  RSA PRIVATE KEY HASH   ==================================  </br></br>";
			echo $hashRsa."</br>" ;
			

			
			echo "#  ==================================  AES KEY  HASH   ==================================  </br></br>";
			echo $hashAes."</br>" ;
			

		/*
			Crypting the RSA PULBIC   
		*/
			include('functions.php');
			$cryptedRsaPrivateKey = crypteObject( base64_encode($PRIVATE_KEY) , $AESKEY ,  $hashAes );

		/*
			Store the Config File 
		*/
			$config = array();
			$config['PUBLIC_KEY'] = $PUBLIC_KEY ;
			$config['HASHAES'] = $hashAes ;
			$config['HASHRSAPRIVATEKEY'] = $hashRsa ;

			file_put_contents("CACHEDBA.cfg", base64_encode(json_encode($config)));


		/*
			Download the RSA Encrypted File  
		*/

		/*
			Checking Operation  
		*/

			$cfg= file_get_contents("CACHEDBA.cfg");
			$cfg = base64_decode(trim($cfg));

			$cfg = json_decode($cfg , true) ;

			// Checking AES Hash 

			if($cfg['HASHAES'] === $hashAes ){
				echo "AES HASH CHECK :  SUCCED</br>";
			}

			if($cfg['HASHRSAPRIVATEKEY'] === $hashRsa ){
				echo "RSA PRIVATE KEY  HASH CHECK :  SUCCED</br>";
			}

			// Check THE CRYPTED RSA 

			$RSADecBase64 = decrypteObject($cryptedRsaPrivateKey , $AESKEY ,  $cfg['HASHAES'] );
			$RSADec =  base64_decode(trim($RSADecBase64));

			if(sha1($RSADec)===$hashRsa ){
				echo "RSA PRIVATE KEY  DECRYPTION  :  SUCCED</br>";

				/*
				Download the RSA Encrypted File  
				*/

				file_put_contents("RSA.ky", base64_encode($cryptedRsaPrivateKey));
			}
				// Lets go To test OUR INSTALLATION FILE
			
			

	







?>