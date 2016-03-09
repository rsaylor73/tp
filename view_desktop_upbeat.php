<div id="middle">


	<center>


	
	<table border=0 width=930 bgcolor="#fefefe" cellspacing="5" cellpadding="5">
	<tr>
	<!-- COVER PIC / TITLE PLACE and TIME -->
	
	<td valign="top" width="100%" align="center"><img src="uploads/<?=$row['userID']?>/cover/<?=$row['id']?>/<?=$row['cover_image']?>" width="930" height="620" border="0"></img></td>
	</tr>
	<tr>
	<!-- clock -->
	<td valign="top" width="100%" align="center"><br><br>
		<div id="timeleft"></div>
		      <script type="text/javascript">
		      function refreshDiv() {
		         $('#timeleft').load('check_time.php?id=<?=$_GET['id']?>', function(){ /* callback code here */ });

		      }
		      setInterval(refreshDiv, 4000);
		      </script>

	</td>
	</tr>
	</table>

        <table border=0 width=930 bgcolor="#fefefe" cellspacing="5" cellpadding="5">
	<tr bgcolor="#DEDEDE">
		<td width="360">	
		<table border=0 width=100%>	
		<tr>
			<td valign="top" width="100%" align="left" valign=top>
			<!-- slide show -->
                         <div id="jssor_1" style="position: relative; margin: 0 auto; top: 0px; left: 0px; width: 360px; height: 274px; overflow: hidden; visibility: hidden;">
                                        <!-- Loading Screen -->
                                        <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
                                        <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
                                        <div style="position:absolute;display:block;background:url('img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
                                </div>
                                <div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 360px; height: 274px; overflow: hidden;">
                                        <?php
                                        for ($x=1; $x < 6; $x++) {
                                                $var = "slide";
                                                $var .= $x;
                                                if ($row[$var] != "") {
                                                        print "
                                                        <div data-p=\"112.50\" style=\"display: none;\">
                                                        <img data-u=\"image\" src=\"uploads/$row[userID]/slide/$row[id]/$row[$var]\" />
                                                        </div>
                                                        ";
                                                }
                                        }
                                        ?>

                                        <!-- Bullet Navigator -->
                                        <div data-u="navigator" class="jssorb01" style="bottom:16px;right:10px;">
                                                <div data-u="prototype" style="width:12px;height:12px;"></div>
                                        </div>
                                        <!-- Arrow Navigator -->
                                        <span data-u="arrowleft" class="jssora02l" style="top:123px;left:8px;width:55px;height:55px;" data-autocenter="2"></span>
                                        <span data-u="arrowright" class="jssora02r" style="top:123px;right:8px;width:55px;height:55px;" data-autocenter="2"></span>
                                        </div>
			</td>
			<!-- end slide show -->
			</tr>
	                <tr bgcolor="#DEDEDE">
			<td valign="top" width="100%" align="left">

                        <input type="button" class="btn btn-default" value="Add To Calendar"
                        onclick="window.open('ical.php?r=<?=$rand2?>&id=<?=$row['id'];?>')">
                        &nbsp;&nbsp;
                        <a href="mailto:<?=$row['email'];?>?subject=<?=$row['title'];?>"><span class="btn btn-default">Contact Organizer</span></a>
                        &nbsp;<?=$page_views;?> views
			<br><br>

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

				<br><br>


                                <iframe width="300" height="250" frameborder="0" style="border:0"
                                src="https://www.google.com/maps/embed/v1/place?q=<?=$row['address']?>&key=AIzaSyD4rJhKUws_jnA1h8NttpfBsxWX4TwNWcY">
                                </iframe>
                        <br>
			</td>
			</tr>
			</table>
			</td>
			
		<!-- EVENT TITLE / DESCRIPTION -->
						
			<td valign="top" width="570"align="left" valign="top">
			<font face="arial" size="2">
			<center><b><h2><?=$row['title']?></h2></b></center>
			<center><h3><?=$row['tagline']?></h3></center>
			<center><?=$row['start_date']?> to <?=$row['end_date']?><br></center>
			<center><?=$row['start_time']?> to <?=$row['end_time']?><br></center>
			<?=$row['location']?><br>
			<br>
			<br>
			<?=$row['description']?>
			</font>
			<br>
			<br>
		
	
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
	<br><br><br><br><br>	
	
	</center>
</div>
