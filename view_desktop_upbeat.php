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
      <a class="navbar-brand">Countdown*</a></div>
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



<!-- details section -->
<section id="details">
  <div class="container">
    <div class="row">
      

        <div class="col-md-12  col-sm-12 col-xs-12 title">
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
                                                        print "
                                                        <div class=\"col-md-2\">
                                                        <img src=\"uploads/$row[userID]/slide/$row[id]/$row[$var]\"  />
                                                        </div>
                                                        ";
                                                }
                                        }
                                        ?>

          
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


                                                        <?php
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
                                                        <table class=\"table\">
                                                        <tr><td><b>Ticket:</b></td><td><b>Price:</b></td><td><b>Quantity:</b></td></tr>";
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
                                                                        print "<tr><td colspan=3><font color=red>Sorry, <b>$row2[name]</b> is sold out.</font></td></tr>";
                                                                } else {
                                                                        print "<tr><td>$row2[name]</td><td>$".number_format($row2['price'],2,'.',',')."</td><td><select name=\"qty$row2[id]\">$qty</select></td></tr>";
                                                                }
                                                                $found = "1";
                                                        }
                                                        if ($found != "1") {
                                                                print "<tr><td colspan=3><font color=blue>Sorry, but tickets are not yet available.</font></td></tr>";
                                                        } else {
                                                                $check_today = date("Ymd");
                                                                if ($check_today > $row['end_date2']) {
                                                                        print "<tr><td colspan=3><font color=red>This event is now closed.</font></td></tr>";
                                                                } else {
                                                                        print "<tr><td colspan=3><input type=\"submit\" class=\"btn btn-primary\" value=\"Purchase Tickets\"></td></tr>";
                                                                }
                                                        }
                                                        print "</table>
                                                        </form>";
                                                        } else {
                                                                print "
                                                                <form name=\"myform\" action=\"index.php\" method=\"post\">
                                                                <input type=\"hidden\" name=\"id\" value=\"$row[id]\">
                                                                <input type=\"hidden\" name=\"section\" value=\"donate\">
                                                                <input type=\"hidden\" name=\"viewID\" value=\"$viewID\">
                                                                ";

                                                                print "<table class=\"table\">";
                                                                print "<tr><td colspan=2><b>Donation Goal: $$row[donation_goal]</b></td></tr>";
                                                                print "<tr>
                                                                        <td><input type=\"radio\" name=\"donate\" value=\"p1\" checked> Donate $5</td>
                                                                        <td><input type=\"radio\" name=\"donate\" value=\"p2\"> Donate $25</td>
                                                                </tr>
                                                                <tr>
                                                                        <td><input type=\"radio\" name=\"donate\" value=\"p3\"> Donate $50</td>
                                                                        <td><input type=\"radio\" name=\"donate\" value=\"p4\"> Donate $100</td>
                                                                </tr>
                                                                <tr>
                                                                        <td><input type=\"radio\" name=\"donate\" value=\"p5\"> Donate $250</td>
                                                                        <td><input type=\"radio\" name=\"donate\" value=\"p6\"> Donate $<input type=\"text\" name=\"custom_amount\" size=10></td>
                                                                </tr>
                                                                <tr><td colspan=2><input type=\"submit\" value=\"Donate\" class=\"btn btn-success\"></td></tr>
                                                                </table></form>";

                                                        }

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
