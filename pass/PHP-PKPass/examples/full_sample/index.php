<?php
use PKPass\PKPass;

require_once('../../PKPass.php');

	// User has filled in the flight info, so create the pass now

	// Predefined data
	/*
	$labels = [
		'SFO' => 'San Francisco',
		'LAX' => 'Los Angeles',
		'LHR' => 'London',
	];
	$gates  = ['F12', 'G43', 'A2', 'C5', 'K9'];

	// User-set vars
	$passenger         = addslashes($_POST['passenger']);
	$origin            = $_POST['origin'];
	$origin_label      = $labels[$origin];
	$destination       = $_POST['destination'];
	$destination_label = $labels[$destination];
	$gate              = $gates[array_rand($gates)]; // Yup, pick a random gate
	$date              = date('m/d/Y H:i', $_POST['date']); // Convert date to string
	*/

	// Create pass

	//Set certifivate and path in the constructor
	$pass = new PKPass('/home/dub/www/pass/certificates/tppass.p12', 'rb102573');

	// Add the WWDR certificate 
	$pass->setWWDRcertPath('/home/dub/www/pass/certificates/wwdr.pem');

	//Check if an error occured within the constructor
	if ($pass->checkError($error) == true) {
		exit('An error occured: ' . $error);
	}

	//Or do it manually outside of the constructor
	/*
	// Set the path to your Pass Certificate (.p12 file)
	if($pass->setCertificate('../../Certificate.p12') == false) {
		echo 'An error occured';
		if($pass->checkError($error) == true) {
			echo ': '.$error;
		}
		exit('.');
	} 
	// Set password for certificate
	if($pass->setCertificatePassword('test123') == false) {
		echo 'An error occured';
		if($pass->checkError($error) == true) {
			echo ': '.$error;
		}
		exit('.');
	}  */

	$sn = rand(100,3000);

//        "logoText": "FLIGHT_INFO_LABEL",


	$pass->setJSON('{
	"passTypeIdentifier": "pass.ticketpointe",
	"formatVersion": 1,
	"organizationName": "Ticket Pointe",
	"serialNumber": "'.$sn.'",
	"teamIdentifier": "QT68494FGX",
	"backgroundColor": "rgb(107,156,196)",
	"logoText": "Ticket Pointe",
	"description": "Ticket Pointe :: Rocking 80s",
  "eventTicket" : {
    "primaryFields" : [
      {
        "key" : "event",
        "label" : "EVENT",
        "value" : "Rocking 80s"
      }
    ],
	"secondaryFields" : [
      {
        "key" : "loc",
        "label" : "LOCATION",
        "value" : "209 Hudson Trace, Augusta, GA 30907"
      }
    ],
    "auxiliaryFields" : [
      {
        "key" : "date",
        "label" : "DATE",
        "value" : "July 30, 2016"
      },
      {
        "key" : "time",
        "label" : "TIME",
        "value" : "5 pm - 10 pm",
        "textAlignment" : "PKTextAlignmentRight"
      }
    ],
  },
    "barcode": {
        "format": "PKBarcodeFormatQR",
        "message": "SDfsdsvsdfdsfscvsdfsdfsdf",
        "messageEncoding": "iso-8859-1"
    },

       "backFields" : [
         {
           "key" : "terms",
           "label" : "TERMS AND CONDITIONS",
           "value" : "TERMS OF USE\r\rUSE OF THIS TICKET POINTE MOBILE APPLICATION (THE \"APPLICATION\") CONSTITUTES ACCEPTANCE OF THESE TERMS OF USE (\"TERMS\"), AS SUCH MAY BE REVISED BY TICKET POINTE FROM TIME TO TIME, AND IS A BINDING AGREEMENT BETWEEN THE USER (\"USER\") AND TICKET POINTE (\"TICKET POINTE\") GOVERNING THE USE OF THE APPLICATION. BY DOWNLOADING OR INSTALLING THIS APPLICATION USER ACKNOWLEDGES AND AGREES TO BE BOUND BY THESE TERMS. IF USER DOES NOT AGREE TO THESE TERMS USER SHOULD UNINSTALL THIS APPLICATION IMMEDIATELY.\r\rEligibility\r\rThe Application may only be used by individuals aged thirteen (13) years or older. If the User is thirteen (13) years or older but under the age of eighteen (18) years, User should review these Terms with User’s parent or guardian to make sure the User and User’s parent or guardian understand them.\r\rApple Terms and Conditions;TICKET POINTE Policies\r\rThese Terms supplement and incorporate (a) the Apple, Inc. (\"Apple\") Terms and Conditions (located at www.apple.com/legal/itunes/us/terms.html#service) including, without limitation, the Licensed Application End User License Agreement provided therein (\"Apple Terms\"); and other TICKET POINTE policies, posted at www.ticketpointe.com (\"TICKET POINTE Website\").  If any of the provisions of the Apple Terms and Conditions or the any applicable TICKET POINTE policies conflict with these Terms, these Terms will control, solely to the extent such terms apply to the Application.\r\rUser License\r\rSubject to these Terms, TICKET POINTE grants the User a personal, non-exclusive, non-transferable, limited and revocable license to use the Application for personal use only on an Apple iPhone, iPad, iPad Mini or iPod Touch (each a \"Device\") owned or controlled by User as permitted by the Usage Rules contained in the Apple Terms and in accordance with these Terms (\"User License\"). Any use of the Application in any other manner, including, without limitation, resale, transfer, modification or distribution of the Application or text, pictures, music, barcodes, video, data, hyperlinks, displays and other content associated with the Application (\"Content\") is prohibited. This Agreement and User License also governs any updates to, or supplements or replacements for, this Application unless separate terms accompany such updates, supplements or replacements, in which case the separate terms will apply.\r\rMobile Payment\r\rUsers who download the Application to a Device may also elect to participate in certain functionality of the Application which will allow the User to use a Device to purchase TICKET POINTE products in the same manner as is possible with a TICKET POINTE stored value card (\"TICKET POINTE Card\") in accordance with the TICKET POINTE Card Terms and Conditions. Mobile Payment is accepted at all company operated TICKET POINTE retail locations and some TICKET POINTE licensed stores. TICKET POINTE reserves the right at any time to discontinue Mobile Payment or change the location of stores accepting Mobile Payment.\r\rUser Information\r\rSome functionality of the Application, including Mobile Payment and location based services and functionality, may require the transmission of information provided by the User including user names and passwords, addresses, e-mail addresses, financial information (such as credit card numbers), information related to a TICKET POINTE Card or GPS location (\"User Information\"). If the User uses such Application functionality, the User consents to the transmission of User Information to TICKET POINTE, or its agents and authorizes TICKET POINTE and its agents to record, process and store such User Information as necessary for the Application functionality.\r\rThe User is solely responsible for maintenance of the confidentiality and security of any User Information transmitted from or stored on a Device for purposes of the Application, including Mobile Payment, for all transactions and other activities undertaken with any TICKET POINTE Card registered in the User’s name , whether authorized or unauthorized. The User agrees to immediately notify TICKET POINTE of any unauthorized transactions associated with the Application including Mobile Payment or any other breach of security. TICKET POINTE shall not be responsible for any losses arising out of the loss or theft of User Information transmitted from or store on a Device or from unauthorized or fraudulent transactions associated with Application.\r\rAcceptable Use\r\rUse of the Application and any Content and User Information transmitted in connection with the Application is limited to the contemplated functionality. In no event may the Application be used in a manner that (a) harasses, abuses, stalks, threatens, defames or otherwise infringe or violate the rights of any other party (including but not limited to rights of publicity or other proprietary rights); (b) is unlawful, fraudulent or deceptive; (c) uses technology or other means to access TICKET POINTE or Content that is not authorized by TICKET POINTE; (d) use or launch any automated system, including without limitation, \"robots,\" \"spiders,\" or \"offline readers,\" to access TICKET POINTE or Content;  (e) attempts to introduce viruses or any other computer code, files or programs that interrupt, destroy or limit the functionality of any computer software or hardware or telecommunications equipment; (f) attempts to gain unauthorized access to TICKET POINTE computer network or user accounts; (g) encourages conduct that would constitute a criminal offense, or that gives rise to civil liability; (h) violates these Terms; (i) attempts to damage, disable, overburden, or impair TICKET POINTE servers or networks; or (j) fails to comply with applicable third party terms (collectively \"Acceptable Use\"). TICKET POINTE reserves the right, in its sole discretion, to terminate any User License, terminate  any User’s participation in Mobile Payment, remove Content or assert legal action with respect to Content or use of the Application, including Mobile Payment, that TICKET POINTE reasonably believes is or might be in violation of these terms of Acceptable Use or TICKET POINTE Policies including the TICKET POINTE Card Terms of Use, but TICKET POINTE failure or delay in taking such actions does not constitute a waiver of its rights to enforce these Terms.\r\rIndemnification\r\rAt TICKET POINTE request, the User agrees to defend, indemnify, and hold harmless TICKET POINTE and its parent and other affiliated companies , and their employees, contractors, officers, and directors from any and all claims, suits, damages, costs, lawsuits, fines, penalties, liabilities, expenses (including attorneys fees)  that arise from the User’s  use or misuse of the Application (including Mobile Payment), violation of these Terms or violation of any rights of a third party. TICKET POINTE reserves the right to assume the exclusive defense and control of any matter otherwise subject to indemnification by the User, in which event the User will cooperate in asserting any available defenses. In the event of any third party claim that the Application or User’s possession and use of the Application infringes that third party’s intellectual property right, TICKET POINTE, not Apple, will be solely responsible for the investigation, defense, settlement and discharge of any such intellectual property infringement claim.\r\rNo Warranties\r\rTICKET POINTE IS PROVIDING THE APPLICATION TO THE USER \"AS IS\" AND THE USER IS USING THE APPLICATION AT HIS OR HER OWN RISK. TO THE FULLEST EXTENT ALLOWABLE UNDER APPLICABLE LAW, TICKET POINTE DISCLAIMS ALL WARRANTIES, WHETHER EXPRESS OR IMPLIED, INCLUDING ANY WARRANTIES THAT THE APPLICATION IS MERCHANTABLE, RELIABLE, ACCURATE, FIT FOR A PARTICULAR PURPOSE OR NEED, NON-INFRINGING OR FREE OF DEFECTS OR ABLE TO OPERATE ON AN UNINTERRUPTED BASIS, OR THAT THE USE OF THE APPLICATION BY THE USER IS IN COMPLIANCE WITH LAWS APPLICABLE TO THE USER OR THAT USER INFORMATION TRANSMITTED IN CONNECTION WITH THE APPLICATION (INCLUDING AS PART OF MOBILE PAYMENT) WILL BE SUCCESSFULLY, ACCURATELY OR SECURELY TRANSMITTED. In the event of any failure of the Application to conform to any applicable warranty, User may notify Apple, and Apple will refund the purchase price for the Application to the User and, to the maximum extent permitted by applicable law, Apple will have no other warranty obligation whatsoever with respect to the Application, and any other claims, losses, liabilities, damages, costs or expenses attributable to any failure to conform to any warranty will be TICKET POINTE sole responsibility.\r\rNo Liability\r\rTO THE FULLEST EXTENT ALLOWABLE UNDER APPLICABLE LAW, IN NO EVENT SHALL TICKET POINTE (A) BE LIABLE TO THE USER WITH RESPECT TO USE OF THE APPLICATION, INCLUDING WITHOUT LIMITATION PARTICIPATION IN MOBILE PAYMENT; AND (B) BE LIABLE TO THE USER FOR ANY INDIRECT, SPECIAL, INCIDENTAL, CONSEQUENTIAL, OR EXEMPLARY DAMAGES, INCLUDING, WITHOUT LIMITATION, DAMAGES FOR LOSS OF GOODWILL, LOST PROFITS, LOSS, THEFT OR CORRUPTION OF USER INFORMATION, THE INABILITY TO USE THE APPLICATION OR DEVICE FAILURE OR MALFUNCTION. THE USER’S SOLE REMEDY IS TO CEASE USE OF THE APPLICATION OR TO CEASE PARTICIPATION IN MOBILE PAYMENT OR THE VISA PROMOTION.\r\rMarks, Application and Content\r\rTICKET POINTE, the TICKET POINTE logo, and other TICKET POINTE trademarks, service marks, graphics and logos used in connection with the Application are trademarks or registered trademarks of WAYNEWORKS THREADS, LLC (collectively \"TICKET POINTE Marks\"). Other trademarks, service marks, graphics and logos used in connection with the Application are the trademarks of their respective owners (collectively \"Third Party Marks\"). The TICKET POINTE Marks and Third Party Marks may not be copied, imitated or used, in whole or in part, without the prior written permission of TICKET POINTE or the applicable trademark holder. The Application and the Content are protected by copyright, trademark, patent, trade secret, international treaties, laws and other proprietary rights, and also may have security components that protect digital information only as authorized by TICKET POINTE or the owner of the Content.\r\rGoverning Law and Jurisdiction\r\rThese Terms are governed by the laws of the state of Georgia, United States of America, without regard to Georgia`s conflict of laws rules. The United Nations Convention on Contracts for the International Sale of Goods shall have no applicability. The User irrevocably consents to the exclusive jurisdiction of the federal and state courts in Richmond County, Georgia, United States of America, for purposes of any legal action arising out of or related to the use of the Application or these Terms.\r\rThird Party Beneficiary\r\rApple, and Apple’s subsidiaries, are third party beneficiaries of these Terms. Upon User’s acceptance of these Terms Apple will have the right (and will be deemed to have accepted the right) to enforce these Terms against User as a third party beneficiary thereof.\r\rChanges\r\rTICKET POINTE reserves the right to change or modify these Terms or any other TICKET POINTE policies related to use of the Application at any time and at its sole discretion by posting revisions on the TICKET POINTE Website. Continued use of the Application following the posting of these changes or modifications will constitute acceptance of such changes or modifications.\r\rContact TICKET POINTE\r\rAny questions, complaints or claims regarding the Application should be directed to:\r\rTICKET POINTE\rCustomer Care\r"
         }
       ]



    }');
	if ($pass->checkError($error) == true) {
		exit('An error occured: ' . $error);
	}

	// add files to the PKPass package
	$pass->addFile('/home/dub/www/pass/PHP-PKPass/images/icon.png');
	$pass->addFile('/home/dub/www/pass/PHP-PKPass/images/icon@2x.png');
	$pass->addFile('/home/dub/www/pass/PHP-PKPass/images/icon.png');
	// specify english and french localizations
	//$pass->addFile('en.strings', 'en.lproj/pass.strings');
	//$pass->addFile('fr.strings', 'fr.lproj/pass.strings');
	if ($pass->checkError($error) == true) {
		exit('An error occured: ' . $error);
	}

	//If you pass true, the class will output the zip into the browser.
	$result = $pass->create(true);
	if ($result == false) { // Create and output the PKPass
		echo $pass->getError();
	}
?>
