<!DOCTYPE html>
<html lang="en">
<head>
<title> Ticket Pointe Event Page</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="">
<meta name="description" content="">
<!-- 
ROMNA Template
http://www.templatemo.com/free-website-templates/
-->
<!-- STYLESHEET CSS FILES -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/animate.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/templatemo-style.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.35.3/css/bootstrap-dialog.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


<style>
/* home section */
#home {


<?php 
$cover = "uploads/$row[userID]/cover/$row[id]/$row[cover_image]";
?>

    background: url('<?=$cover;?>') 50% 0 repeat-y fixed;
    color: #ffffff;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    height: 700px;
    text-align: center;
}
</style>


</head>

<body data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
<!-- preloader section -->
<div class="preloader">
  <div class="sk-spinner sk-spinner-rotating-plane"></div>
</div>



<!-- home section -->
<section id="home">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">

        </div>
    </div>
  </div>
</section>









<!-- navigation section -->
<div class="navbar navbar-default navbar-static-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="icon icon-bar"></span> <span class="icon icon-bar"></span> <span class="icon icon-bar"></span> </button>

<!--
            <div class="navbar-brand" id="timeleft">test test test</div>
              <script type="text/javascript">
              function refreshDiv() {
                 $('#timeleft').load('check_time.php?id=<?=$_GET['id']?>', function(){ /* callback code here */ });

              }
              setInterval(refreshDiv, 4000);
              </script>
-->

      <a class="navbar-brand"><!--Countdown*--></a></div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#home" class="smoothScroll">HOME</a></li>
        <li><a href="#details" class="smoothScroll">DETAILS</a></li>
        <li><a href="#map" class="smoothScroll">MAP</a></li>
        <li><a href="#tickets" class="smoothScroll">TICKETS</a></li>
        <li><a href="#share" class="smoothScroll">SHARE</a></li>
        <li><a href="#contact" class="smoothScroll">CONTACT</a></li>
      </ul>
    </div>
  </div>
</div>



<style>
.top-buffer { margin-top:10px; margin-left:10px; margin-right:10px; }
</style>


<?php
   print '
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
	';


                                                        $sql2 = "SELECT * FROM `tickets` WHERE `eventID` = '$row[id]'";
                                                        $result2 = $this->new_mysql($sql2);
                                                        for ($y=0; $y < 51; $y++) {
                                                                $qty .= "<option value=\"$y\">$y</option>";
                                                        }
                                                        $viewID = rand(50,500);

                                                        if ($row['enable_donation'] != "Yes") {
                                                        print "
                                                        <form name=\"myform\" action=\"index.php\" method=\"post\">
                                                        <input type=\"hidden\" name=\"id\" value=\"$row[id]\">
                                                        <input type=\"hidden\" name=\"section\" value=\"cart\">
                                                        <input type=\"hidden\" name=\"viewID\" value=\"$viewID\">
							<div class=\"row top-buffer\">
								<div class=\"col-sm-4\"><b>Ticket:</b></div>
								<div class=\"col-sm-4\"><b>Price:</b></div>
								<div class=\"col-sm-4\"><b>Quantity:</b></div>
							</div>";
                                                        while ($row2 = $result2->fetch_assoc()) {
                                                                // check qty
                                                                $total_avail = $row2['qty'];
                                                                $sql3 = "
                                                                SELECT
                                                                        SUM(`qty`) AS 'total'
                                                                FROM
                                                                        `cart`
                                                                WHERE
                                                                        `cart`.`ticketID` = '$row2[id]'
                                                                        AND `cart`.`status` = 'Paid'
                                                                ";
                                                                $qty_used = "0";
                                                                $result3 = $this->new_mysql($sql3);
                                                                while ($row3 = $result3->fetch_assoc()) {
                                                                        $qty_used = $row3['total'];
                                                                }
                                                                if ($qty_used >= $total_avail) {
                                                                        print "
									<div class=\"row top-buffer\">
									<div class=\"col-sm-12\"><font color=red>Sorry, <b>$row2[name]</b> is sold out.</font></div>
									</div>";
                                                                } else {
                                                                        print "<div class=\"row top-buffer\">
									<div class=\"col-sm-4\">$row2[name]</div>
									<div class=\"col-sm-4\">$".number_format($row2['price'],2,'.',',')."</div>
									<div class=\"col-sm-4\"><select name=\"qty$row2[id]\">$qty</select></div>
									</div>";
                                                                }
                                                                $found = "1";
                                                        }
                                                        if ($found != "1") {
                                                                print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">
								<font color=blue>Sorry, but tickets are not yet available.</font></div></div>";
                                                        } else {
                                                                $check_today = date("Ymd");
                                                                if ($check_today > $row['end_date2']) {
                                                                        print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">
									<font color=red>This event is now closed.</font></div></div>";
                                                                } else {
                                                                        print "<div class=\"row top-buffer\"><div class=\"col-sm-12\">
									<input type=\"submit\" class=\"btn btn-success btn-lg\" value=\"Purchase Tickets\"></div></div>";
                                                                }
                                                        }
                                                        print "
                                                        </form>";
                                                        } else {
                                                                print "
                                                                <form name=\"myform\" action=\"index.php\" method=\"post\">
                                                                <input type=\"hidden\" name=\"id\" value=\"$row[id]\">
                                                                <input type=\"hidden\" name=\"section\" value=\"donate\">
                                                                <input type=\"hidden\" name=\"viewID\" value=\"$viewID\">
                                                                ";

                                                                print "<div class=\"row top-buffer\">";
                                                                print "<div class=\"col-sm-12\">Donation Goal: $$row[donation_goal]</b></div></div>";
                                                                print "<div class=\"row top-buffer\">
								<div class=\"col-sm-6\">
                     							<input type=\"radio\" name=\"donate\" value=\"p1\" checked> Donate $5
								</div>
								<div class=\"col-sm-6\">
                                                                        <input type=\"radio\" name=\"donate\" value=\"p2\"> Donate $25
								</div>
								</div>

								<div class=\"row top-buffer\">
								<div class=\"col-sm-6\">
                                                                        <input type=\"radio\" name=\"donate\" value=\"p3\"> Donate $50
								</div>
								<div class=\"col-sm-6\">
                                                                        <input type=\"radio\" name=\"donate\" value=\"p4\"> Donate $100
								</div>
								</div>

								<div class=\"row top-buffer\">
								<div class=\"col-sm-6\">
                                                                        <input type=\"radio\" name=\"donate\" value=\"p5\"> Donate $250
								</div>
								<div class=\"col-sm-6\">
                                                                        <input type=\"radio\" name=\"donate\" value=\"p6\"> Donate $<input type=\"text\" name=\"custom_amount\" size=10>
								</div>
								</div>

								<div class=\"row top-buffer\">
								<div class=\"col-sm-12\">
                                                                <input type=\"submit\" value=\"Donate\" class=\"btn btn-success\">
                                                                </div></div>
								</form>";

                                                        }



	print '
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>
   ';

?>




<!-- details section -->
<section id="details">
  <div class="container">
    <div class="row">
      
	<div class="col-md-12  col-sm-12 col-xs-12 center">
            <center><div class="navbar-brand" id="timeleft"></div></center><br>
              <script type="text/javascript">

/*
Code disabled per David

              function refreshDiv() {
                 $('#timeleft').load('check_time.php?id=<?=$_GET['id']?>', function(){  });

              }
              setInterval(refreshDiv, 4000);
*/	
              </script>
	</div>

        <div class="col-md-12  col-sm-12 col-xs-12 title">
	<br><br>
          <h2><?=$row['title']?></h2>
           <h5><?=$row['tagline']?></h5>
          <hr>


          <h5><?=$row['start_date']?> to <?=$row['end_date']?></h5>
          <h5><?=$row['start_time']?> to <?=$row['end_time']?></h5>
          
          <p><?=$row['description']?>
          </p>
        </div>

          <div class="col-md-4  col-sm-3 col-xs-1"> 

          </div>

        <!--<div class="col-md-5  col-sm-6 col-xs-10 center"> -->

                                        <?php
                                        for ($x=1; $x < 6; $x++) {
                                                $var = "slide";
                                                $var .= $x;
                                                if ($row[$var] != "") {
							if ($x == "1") {
								$active = "item active";
								$active2 = "class=\"active\"";
							} else {
								$active = "item";
								$active2 = "";
							}
							$slider .= "<div class=\"$active\">\n
                                                        <img src=\"uploads/$row[userID]/slide/$row[id]/$row[$var]\"/>\n
                                                        </div>\n
                                                        ";
							$y = $x - 1;

							$slider2 .= "<li data-target=\"#myCarousel\" data-slide-to=\"$y\"$active2></li>\n";
                                                }
                                        }
                                        ?>
	<div class="col-md-5  col-sm-6 col-xs-10 center">
		<div id="myCarousel" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators">
			<?=$slider2;?>
			</ol>
			<div class="carousel-inner" role="listbox">
			<?=$slider;?>
			</div>
<!-- Left and right controls -->
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
</div>


	</div>
          
        <div class="col-md-3  col-sm-3 col-xs-1"> 

        </div>
       
    </div>



  </div>
</section>


<!-- map section -->
<section id="map">
<div class="container">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 title">
      <br>
      <br>
      <h2>Location</h2>
      <hr>
 <?=$row['location'];?>
      
      
      </div>
      </div>
      </div>

<div class="google_map">

                               <iframe width="100%" height="400" frameborder="0" style="border:0"
                                src="https://www.google.com/maps/embed/v1/place?q=<?=$row['address']?>&key=AIzaSyD4rJhKUws_jnA1h8NttpfBsxWX4TwNWcY">
                                </iframe>

</div>
</section>

<!-- tickets section -->
<section id="tickets"> 

  <div class="container">
    <div class="col-md-12 col-sm-12 col-xs-12 title">
      <h2>Tickets</h2>
      <hr>
     
    </div>

    <div class="col-md-3  col-sm-3 "> 

          </div>

        <div class="col-md-6  col-sm-6 col-xs-12 center"> 

<!-- TICKET INFO and PRICE OPTIONS / PURCHASE BUTTON / REGISTER FOR EVENT BUTTON -->    
        <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Buy Tickets</button><br><br>


			<?php
                                                        if ($row['registration'] == "Yes") {
                                                                print "
                                                                <form action=\"registration.php\" style=\"display:inline\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"$row[id]\">&nbsp;&nbsp;<input type=\"submit\" class=\"btn btn-success\" value=\"Register For Event\"></form>";
                                                        }
                            ?>  

            </td>
            <tr></tr>   
        </table>
    </td>
    </table>
    <!-- end tickets -->
        </div>

        <div class="col-md-3  col-sm-3 ">
        </div>

      
    </div>


  </div>
</div>

</section>
<!-- share section -->

<section id="share">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 title">
        <h2 class="wow fadeIn" data-wow-delay="0.9s">Share</h2>

                                                        <?php
                                                        $sql2 = "SELECT * FROM `social` WHERE `userID` = '$row[userID]'";
                                                        $result2 = $this->new_mysql($sql2);
                                                        while ($row2 = $result2->fetch_assoc()) {
                                                                if ($row['homepage'] != "") {
                                                                        $url = "http://" . $row['homepage'] . "." . $settings[8];
                                                                } else {
                                                                        $url = $settings[1] . "index.php?section=page_view&id=$_GET[id]"; // To Do
                                                                }
                                                                if ($row2['facebook'] == "Yes") {
                                                                        $this->social_link("facebook",$url,$row['title'],$row['tagline']);
                                                                }
                                                                if ($row2['google'] == "Yes") {
                                                                        $this->social_link("google",$url,$row['title'],$row['tagline']);
                                                                }
                                                                if ($row2['linkedin'] == "Yes") {
                                                                        $this->social_link("linkedin",$url,$row['title'],$row['tagline']);
                                                                }
                                                                if ($row2['tumbler'] == "Yes") {
                                                                        $this->social_link("tumbler",$url,$row['title'],$row['tagline']);
                                                                }
                                                                if ($row2['twitter'] == "Yes") {
                                                                        $this->social_link("twitter",$url,$row['title'],$row['tagline']);
                                                                }
                                        
                                                        }
                                                
                                                        ?>

<!--
        <ul class="social-icon">
          <li><a href="#" class="fa fa-facebook wow bounceIn" data-wow-delay="0.3s"></a></li>
          <li><a href="#" class="fa fa-google-plus wow bounceIn" data-wow-delay="0.6s"></a></li>
          <li><a href="#" class="fa fa-linkedin wow bounceIn" data-wow-delay="0.9s"></a></li>
          <li><a href="#" class="fa fa-tumblr wow bounceIn" data-wow-delay="0.9s"></a></li>
          <li><a href="#" class="fa fa-twitter wow bounceIn" data-wow-delay="0.9s"></a></li>
          </ul>
          -->
      </div>
</section>




<!-- contact section -->
<section id="contact">
  <div class="container">
    <div class="row">
      <div class="col-md-offset-3 col-md-6 col-md-offset-3  col-sm-offset-2 col-sm-8 col-sm-offset-2 title">
        <h2>Contact Us</h2>
        <hr>
       
      </div>
      <div class="col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8 contact-form wow fadeInUp" data-wow-delay="0.9s">

<a href="mailto:<?=$row['email'];?>?subject=<?=$row['title'];?>"><span class="btn btn-default">Contact Organizer</span></a>

        <!--
        <form action="#" method="post">
          <input type="text" class="form-control" placeholder="Name">
          <input type="email" class="form-control" placeholder="Email">
          <textarea class="form-control" placeholder="Message" rows="6"></textarea>
          <input type="submit" class="form-control" value="SEND EMAIL">
        </form>
        -->
      </div>
    </div>
  </div>
</section>




<!-- JAVASCRIPT JS FILES --> 
<script src="js/jquery.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<script src="js/nivo-lightbox.min.js"></script> 
<script src="js/smoothscroll.js"></script> 
<script src="js/jquery.sticky.js"></script> 
<script src="js/jquery.parallax.js"></script> 
<script src="js/wow.min.js"></script> 
<script src="js/custom.js"></script>
</body>
</html>
