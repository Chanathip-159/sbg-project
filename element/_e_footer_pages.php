<?php
$php_root_file=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_FILENAME'];
/*
# _e_<filename> need _#_<filename> at host file
require_once("_1_defines.php"); #use for define variable such as _CEO,_DEB
require_once("_2_static.var.php"); #use for static variable such as month name
require_once("_3_base.functions.php"); #use for basic functions such as echoMsg
require_once("_4_db.functions.php"); # db connect and db functions
//*/
if (($data_count > 0)&&($count_page > 0)) {
?>
				<table width="100%">
					<tr>
						<td style="text-align:right;">
							Total <?php echo $count_page; ?> page(s); page:
<?php
		if ($count_page>10) {
			for($i = 1; $i <= 3; $i++) {
				if ($i == $select_page) {
					echo "<B>>$i<</B> ";
				}else{
					echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",$i,-1);'>[$i]</a> "; // function from main.php
				}
			}
			if ($select_page == 3) echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",4,-1);'>[4]</a> ";
			if ($select_page != 4) echo " ... ";
			if (($select_page > 3) && ($select_page <= ($count_page-3))) {
				if ($select_page == 4) {
					for($i = $select_page; $i <= $select_page+3; $i++) {
						if ($i == $select_page) {
							echo "<B>>$i<</B> ";
						}else{
							echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",$i,-1);'>[$i]</a> "; // function from main.php
						}
					}
				}else if ($select_page == $count_page-3){
					for($i = $count_page-5; $i <= $count_page-3; $i++) {
						if ($i == $select_page) {
							echo "<B>>$i<</B> ";
						}else{
							echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",$i,-1);'>[$i]</a> "; // function from main.php
						}
					}
				}else{
					for($i = $select_page - 1; $i <= $select_page + 1; $i++) {
						if ($i == $select_page) {
							echo "<B>>$i<</B> ";
						}else{
							echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",$i,-1);'>[$i]</a> "; // function from main.php
						}
					}
				}
			}else{
				for($i = ceil($count_page/2) - 1; $i <= ceil($count_page/2) + 1; $i++) {
					if ($i == $select_page) {
						echo "<B>>$i<</B> ";
					}else{
						echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",$i,-1);'>[$i]</a> "; // function from main.php
					}
				}
			}
			if ($select_page != $count_page-3) echo " ... ";
			if ($select_page == $count_page-2) echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",".($count_page-3).",-1);'>[".($count_page-3)."]</a> ";
			for($i = ($count_page-2); $i <= $count_page; $i++) {
				if ($i == $select_page) {
					echo "<B>>$i<</B> ";
				}else{
					echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",$i,-1);'>[$i]</a> "; // function from main.php
				}
			}
			?>
			<div class="table-responsive" width="100%" align="right">
				<div class="form-group input-group mt-1" style="width: 300px;">
					<input type="text" id="text_goto_page" name="text_goto_page" class="form-control" placeholder="Input page number">
					<div class="input-group-append">
						<button class="btn btn-default" type="button" title="Clear" onClick="gotoPageWithLastParam(<?php echo "'$dest_file'";?>,document.getElementById('text_goto_page').value,-1);">
						  <i class="fas fa-arrow-right"></i>
						</button>
					  </div>
				</div>
			</div>
			<?php
		}else{
			for($i = 1; $i <= $count_page; $i++) {
				if ($i == $select_page) {
					echo "<B>>$i<</B> ";
				}else{
					echo "<a href='javascript:gotoPageWithLastParam(\"$dest_file\",$i,-1);'>[$i]</a> "; // function from main.php
				}
			}
		}
?>
						</td>
					</tr>
				</table>
<?php
	}
?>