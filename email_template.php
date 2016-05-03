<?php
$template = '

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width"> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	<title></title> 

	
    <style type="text/css">

        html,
        body {
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }
        body {
        	margin: 0 !important;
        }
        
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        
        div[style*="margin: 16px 0"] {
            margin:0 !important;
        }
        
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
                
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            Margin: 0 auto !important;
        }
        table table table {
            table-layout: auto; 
        }
        
        img {
            -ms-interpolation-mode:bicubic;
        }
        
        .yshortcuts a {
            border-bottom: none !important;
        }
        
        .mobile-link--footer a,
        a[x-apple-data-detectors] {
            color:inherit !important;
            text-decoration: underline !important;
        }
      
    </style>
    
    <style>
        
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }
        .button-td:hover,
        .button-a:hover {
            background: #555555 !important;
            border-color: #555555 !important;
        }

        @media screen and (max-width: 600px) {

            .email-container {
                width: 100% !important;
            }

            .fluid,
            .fluid-centered {
                max-width: 100% !important;
                height: auto !important;
                Margin-left: auto !important;
                Margin-right: auto !important;
            }
            .fluid-centered {
                Margin-left: auto !important;
                Margin-right: auto !important;
            }

            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            .stack-column-center {
                text-align: center !important;
            }
        
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                Margin-left: auto !important;
                Margin-right: auto !important;
                float: none !important;
            }
            table.center-on-narrow {
                display: inline-block !important;
            }
                
        }

    </style>

</head>
<body bgcolor="#808080" width="100%" style="Margin: 0;">
<table bgcolor="#808080" cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" style="border-collapse:collapse;"><tr><td valign="top">
    <center style="width: 100%;">

        <div style="display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;mso-hide:all;font-family: sans-serif;">
            (Optional) This text will appear in the inbox preview, but not the email body.
        </div>

        <table align="center" width="600" class="email-container">
			<tr>
				<td style="padding: 20px 0; text-align: center">
					<img src="http://ticketpointe.com/img/logom.png" width="300" height="80" alt="logo" border="0">
				</td>
			</tr>
        </table>
        
        <table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#ffffff" width="600" class="email-container">
                       
           
            <tr>
                <td dir="rtl" align="center" valign="top" width="100%" style="padding: 10px;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            
                            <td width="66.66%" class="stack-column-center">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555; padding: 10px; text-align: left;" class="center-on-narrow">
                                            <strong style="color:#111111;">
                                            
                                            '.$name.'

                                            ,</strong>
                                            <br><br>
                                            Thank you for ordering your tickets from Ticket Pointe. Please use this email as admission to your event.
                                            <br><br>
                                            <br><br><b>Event: 

                                            '.$e_title.'</b><br>
                                            Location: '.$e_location.'<br>
                                            Valid from '.$e_start.' to '.$e_end.'<br>
                                            Operating Hours: '.$e_time1.' to '.$e_time2.'<br><br>  



                                        </td>
                                    </tr>
                                </table>
                            </td>
                            
                            <td width="33.33%" class="stack-column-center">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td dir="ltr" valign="top" style="padding: 0 10px;">
                                        
                                            


                                            <img src="'.$image.'" width="170" width="170" alt="ticket" border="0" class="center-on-narrow">



                                        <br><br>Ticket Type: RSVP<br>Quantity: 

                                        1


                                        <br><br><br>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
          
        <table align="center" width="600" class="email-container">
            <tr>
                <td style="padding: 40px 10px;width: 100%;font-size: 12px; font-family: sans-serif; mso-height-rule: exactly; line-height:18px; text-align: center; color: #ffffff;">
                    <br><br>
                    Ticket Pointe, LLC<br><span class="mobile-link--footer">P.O. Box 12657, Durham, NC, 27709</span><br>
                    <br><br> 
                    
                </td>
            </tr>
        </table>

    </center>
</td></tr></table>
</body>
</html>

';
?>
