                <div id="page_view">
                <table border=0 width="1600">
			<tr bgcolor="#D8D8D8">
				<td colspan=3 width=100%>
				<img src="uploads/<?=$row['userID']?>/cover/<?=$row['id']?>/<?=$row['cover_image']?>" width="1600" height="400">
				</td>
			</tr>


			<tr bgcolor="#D8D8D8">
				<!-- column 1 -->
				<td width="33%" valign="top">
				<div id="jssor_1" style="position: relative; margin: 0 auto; top: 0px; left: 0px; width: 600px; height: 300px; overflow: hidden; visibility: hidden;">
				        <!-- Loading Screen -->
				        <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
				        <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
				        <div style="position:absolute;display:block;background:url('img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
				</div>
				<div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 600px; height: 300px; overflow: hidden;">
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
				<!-- column 2 -->
				<td width=33% valign=top>
                                                <center><h1><?=$row['title']?></h1></center>
						<center><h2><?=$row['tagline']?></h2></center>
                                                                <span class="normalText">
                                                                <center><?=$row['start_date']?> to <?=$row['end_date']?><br><?=$row['start_time']?> to <?=$row['end_time']?><br><?=$row['location']?></center><br>
                                                                <div id="timeleft"></div>
      <script type="text/javascript">
      function refreshDiv() {
         $('#timeleft').load('check_time.php', function(){ /* callback code here */ });

      }
      setInterval(refreshDiv, 1000);
      </script>



                                                                </span>

				</td>
				<!-- column 3 -->
				<td width=33% valign=top>
                                                <iframe width="400" height="350" frameborder="0" style="border:0"
                                                src="https://www.google.com/maps/embed/v1/place?q=<?=$row['address']?>&key=AIzaSyD4rJhKUws_jnA1h8NttpfBsxWX4TwNWcY">
                                                </iframe><br>
						<a href="mailto:<?=$row['email'];?>&subject=<?=$row['title'];?>"><span class="btn btn-primary">Contact Organizer</span></a><br>
						</td>
					</tr>

					<tr bgcolor="#D8D8D8">
					<!-- column 1B -->
					<td width=33% valign=top>
                                                        <input type="button" class="btn btn-default" value="Add To Calendar"
                                                        onclick="window.open('ical.php?r=<?=$rand2?>&id=<?=$row['id'];?>')">

                                                        <?php
                                                        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                                                        ?>

                                                        &nbsp;&nbsp;&nbsp;<input type="button" class="btn btn-default" value="Share With Friends" onclick="window.location.href='mailto:?subject=Tickete Pointe&body=Hi please visit <?=$url;?>';"> <?=$page_views;?>
                                                        views<br><br>
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
					</td>
					<!-- column 2B -->
					<td width=33% valign=top>
                                                                <?=$row['description']?>
					</td>
					<!-- column 3B -->
                                        <td width=33% valign=top>

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

							if ($row['registration'] == "Yes") {
								print "
								<form action=\"registration.php\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"$row[id]\"><input type=\"submit\" class=\"btn btn-success\" value=\"Register For Event\"></form>";
							}

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

                                                        ?>
					</td>
				</tr>

				</table>
<br><br><br><br><br>
               </div>
