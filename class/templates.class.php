<?php

/*
Google reCAPTCHA validations:
site key: 6LcnkA4TAAAAAD04ISkLGW_X0GWQnkbAUflFkI3E
secret key: 6LcnkA4TAAAAAINvZHGtuE2Y3IJbB2oYFTIB9CG_

*/


if( !class_exists( 'Templates')) {
class Templates {
        public $linkID;

        function __construct($linkID){ $this->linkID = $linkID; }

        /*
        The function is set to only allow mysql calls to be driven
        from inside this class.
        */

        public function new_mysql($sql) {
                $result = $this->linkID->query($sql) or die($this->linkID->error.__LINE__);
                return $result;
        }

        public static function load_template($file,$result) {
                if (file_exists($file)) {
                        include "$file";
                } else {
                        include "templates/error.phtml";
                }

        }


	public function isMobile() {
		//print "TEST: $_SERVER[HTTP_USER_AGENT]<br>";
		//die;
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|iphone|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}

	public function tiny_init() {
		?>
		<script type="text/javascript">
		tinymce.remove();
		if (typeof(tinyMCE) != "undefined") {

		tinymce.init({
			mode: "exact",
			elements: "tiny,tiny1,tiny2,tiny3,tiny4",
			theme: "modern",
		        force_br_newlines : false,
		        force_p_newlines : false,
		        forced_root_block : '',
		        height : "300",
		        verify_html : "false",
		        browser_spellcheck: true,

	        plugins: [
        	    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
	            "searchreplace wordcount visualblocks visualchars code fullscreen",
        	    "insertdatetime media nonbreaking save table contextmenu directionality",
	            "emoticons template paste textcolor"
	         ],
	         toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spellchecker",
        	 toolbar2: "print preview media | forecolor backcolor emoticons",
	         image_advtab: true,
		});
		}
		</script>
		<?php
	}

	public function OLDnavigation() {
		?>

		<div id="dashboard_left">
		<form name="myform">
                <button type="button" class="btn btn-primary btn-lg" onclick="load_details(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Event Details</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>

                <br><br>

                <button type="button" class="btn btn-primary btn-lg" onclick="load_design(this.form)" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Event Design</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>

                <br><br>

                <button type="button" class="btn btn-primary btn-lg" onclick="document.location.href='index.php?section=dashboard&part=design'" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Social</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>

                <br><br>

                <button type="button" class="btn btn-primary btn-lg" onclick="document.location.href='index.php?section=dashboard&part=design'" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Event Settings</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>

                <br><br>

                <button type="button" class="btn btn-primary btn-lg" onclick="document.location.href='index.php?section=dashboard&part=design'" style="width:300px">
                <table border=0 width=300>
                <tr><td width=250>Create Tickets</td><td width=50 align="left"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></td></tr>
                </table>
                </button>

		</form>
		</div>


		<?php
	}

}
}
?>
