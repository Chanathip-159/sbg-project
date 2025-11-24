<?php
$php_root_file=$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_FILENAME'];
?>
<style>
	#table-scroll {
  height:700px;
  overflow:auto;  
  margin-top:20px;
  overflow-y: auto;
}
thead {
	/* .table-bordered thead{ */
	position: sticky;
	top: 0;
	background-color: #f2f3f4;
	z-index: 1;
}
/* th.text-center {
  border: 2px solid #090909;
} */
</style>
			<div class="card <?=$table_font_size;?>">
				<div class="card-header">
					<div class="form-row ">
<?php if (strlen($table_name)>0) { ?>
						<div><h4><?=$table_name?></h4></div><!-- table name -->
<?php } ?>
						<div class="col text-right justify-content-right d-md-inline-block form-inline">
							<form name="sort">Sort : 
							<select id="<?=_SORT_FIELD;?>" name="<?=_SORT_FIELD;?>" class="form-control">
								<?php
								foreach($fields_info AS $field) {
									if (!$field[_SORT_BOC_FIELD]) {
								?>
									<option value="<?=$field[_ID_FIELD]; ?>" <?php if($sort_field == $field[_ID_FIELD]) echo "selected";?>>
								<?php
										if (($fields_info[$field[_ID_FIELD]][_TYP_FIELD]>=100)||($fields_info[$field[_ID_FIELD]][_COM_TYP_FIELD]>=100)){
											echo "&nbsp;&nbsp;&nbsp;".trim(str_replace(Array("<div>","</div>"),"",$field[_LAB_FIELD]));
										}else{
											echo (strlen($field[_ID_FIELD])==1?"&nbsp;".$field[_ID_FIELD]:$field[_ID_FIELD])."|".trim(str_replace(Array("<div>","</div>"),"",$field[_LAB_FIELD]));
										}
								?>
								</option>
								<?php
									}
								}
								?>
							</select>
							<script type="text/javascript">
							function execSort(field, desc){
								if (desc) {
									dest_file = '<?php echo $dest_file;?>&'+field+'='+document.getElementById(field).value+'&<?php echo _SORT_DESC_FIELD;?>=1';
								}else{
									dest_file = '<?php echo $dest_file;?>&'+field+'='+document.getElementById(field).value+'&<?php echo _SORT_DESC_FIELD;?>=0';
								}
								//alert(dest_file);
								gotoPageWithLastParam(dest_file, <?=$select_page;?>, -1);
							}
							</script>
							<a href="javascript:execSort('<?=_SORT_FIELD;?>', false);"><button class="btn btn <?php if (!$sort_desc_field && strlen($sort_field)>0) echo "btn-primary";?>" type="button" title="ASC">&nbsp;<i class="fa fa-fw fa-sort-amount-up" style="transform: rotate(180deg)scale(-1, 1);"></i>&nbsp;</button></a>
							<a href="javascript:execSort('<?=_SORT_FIELD;?>', true);"><button class="btn btn <?php if ($sort_desc_field && strlen($sort_field)>0) echo "btn-primary";?>" type="button" title="DESC">&nbsp;<i class="fa fa-fw fa-sort-amount-down"></i>&nbsp;</button></a>
							<?php if ($save_enable) {
								// clear old_query
								$_SESSION[_SES_SQL_CUR_QRY_ROW] = 0;
								$_SESSION[_SES_SQL_CUR_QRY_SEL] = "";
								$_SESSION[_SES_SQL_CUR_QRY_ODR] = "";
								$bf = $dest_file;
								$bw = "&bw=".@unserialize($_GET);
							?>
								&nbsp;&nbsp;<a href="<?=_MAIN_DF."generate_csv&bf=".$bf.$bw;?>"><button class="btn btn btn-primary" type="button" title="Export CSV">&nbsp;<i class="fa fa-fw fa-save"></i>&nbsp;</button></a>
							<?php }?>
							</form>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="panel panel-default">
						<div class="panel-body"> <!-- /.panel-heading -->
							<div class="table-responsive" id="table-scroll">
								<table class="table table-bordered table-hover">
									<thead>
									<?php
									if (strlen($custom_headers_table)) {
										echo $custom_headers_table;
									}else{
									?>
										<tr>
											<th class="text-center">#</th>
											<?php
													foreach($labels_info AS $label) {
											?>
											<th class="text-center">
											<?php
												if (($fields_info[$label][_TYP_FIELD]>=100)||($fields_info[$label][_COM_TYP_FIELD]>=100)) {
													echo $fields_info[$label][_LAB_FIELD];
												}else{
													if (!$fields_info[$label][_SORT_BOC_FIELD]) {
														echo (strlen($fields_info[$label][_ID_FIELD])==1?"&nbsp;".$fields_info[$label][_ID_FIELD]:$fields_info[$label][_ID_FIELD])."|".$fields_info[$label][_LAB_FIELD];
													}
												}
												
												if (strlen($fields_info[$label][_COM_FIELD1])>0) {
													if (($fields_info[$fields_info[$label][_COM_FIELD1]][_TYP_FIELD]>=100)||($fields_info[$fields_info[$label][_COM_FIELD1]][_COM_TYP_FIELD]>=100)) {
														echo "<div class=\"text-primary\">".$fields_info[$fields_info[$label][_COM_FIELD1]][_LAB_FIELD]."</div>";
													}else{
														if (!$fields_info[$fields_info[$label][_COM_FIELD1]][_SORT_BOC_FIELD]) {
															echo "<div class=\"text-primary\">".$fields_info[$fields_info[$label][_COM_FIELD1]][_ID_FIELD]."|".$fields_info[$fields_info[$label][_COM_FIELD1]][_LAB_FIELD]."</div>";
														}
													}
												}
												
												if (strlen($fields_info[$label][_COM_FIELD2])>0) {
													if (($fields_info[$fields_info[$label][_COM_FIELD1]][_TYP_FIELD]>=100)||($fields_info[$fields_info[$label][_COM_FIELD1]][_COM_TYP_FIELD]>=100)) {
														echo "<div class=\"text-success\">".$fields_info[$fields_info[$label][_COM_FIELD2]][_LAB_FIELD]."</div>";
													}else{
														if (!$fields_info[$fields_info[$label][_COM_FIELD2]][_SORT_BOC_FIELD]) {
															echo "<div class=\"text-success\">".$fields_info[$fields_info[$label][_COM_FIELD2]][_ID_FIELD]."|".$fields_info[$fields_info[$label][_COM_FIELD2]][_LAB_FIELD]."</div>";
														}
													}
												}
												
												if (strlen($fields_info[$label][_COM_FIELD3])>0) {
													if (($fields_info[$fields_info[$label][_COM_FIELD1]][_TYP_FIELD]>=100)||($fields_info[$fields_info[$label][_COM_FIELD1]][_COM_TYP_FIELD]>=100)) {
														echo "<div class=\"text-danger\">".$fields_info[$fields_info[$label][_COM_FIELD3]][_LAB_FIELD]."</div>";
													}else{
														if (!$fields_info[$fields_info[$label][_COM_FIELD3]][_SORT_BOC_FIELD]) {
															echo "<div class=\"text-danger\">".$fields_info[$fields_info[$label][_COM_FIELD3]][_ID_FIELD]."|".$fields_info[$fields_info[$label][_COM_FIELD3]][_LAB_FIELD]."</div>";
														}
													}
												}
											?>
											</th>
										<?php
												}
										?>
										</tr>
									<?php
									}
									?>
									</thead>
								<tbody>
<?php
	$data_count = getRows($dbh, $sql_from_where, $orderby);
	// echo $sql_from_where;
	// echo $data_count;
	if ($data_count > 0) {
		if (!strlen($select_page) || $select_page < 1) $select_page = 1;
		if (!$max_rows) $max_rows = 50;
		if ($max_rows > _MAX_NUM_ROWS) $max_rows = _MAX_NUM_ROWS;
		if (($select_page -1)*$max_rows > $data_count) $select_page = 1;
		$count_page = splitItemsToPages($data_count, $max_rows);
		$_SESSION[_SES_SQL_CUR_QRY_ROW] = $data_count;
		$_SESSION[_SES_SQL_CUR_QRY_SEL] = $sql_fields_from_where;
		$_SESSION[_SES_SQL_CUR_QRY_ODR] = $orderby;
		if ($stmt = selectByPage($dbh, $sql_fields_from_where, $orderby, $select_page, $max_rows)) {
			$no = 1;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$write_2_table=Array(); $mark=null; $oper=null; $value=null; $color=null; $bg_color="";//$pattern_icon_replace=$pattern_icons;
				
				foreach($labels_info AS $label) {
					$coms = Array();
					if (strlen($fields_info[$label][_COM_FIELD1])>0) $coms[] = Array($fields_info[$label][_COM_FIELD1], "text-primary");
					if (strlen($fields_info[$label][_COM_FIELD2])>0) $coms[] = Array($fields_info[$label][_COM_FIELD2], "text-success");
					if (strlen($fields_info[$label][_COM_FIELD3])>0) $coms[] = Array($fields_info[$label][_COM_FIELD3], "text-danger");
					$temp = ""; // clear
					$value = tableFormat($fields_info[$label][_TYP_FIELD], $row[getFieldName($fields_info[$label][_SQL_FIELD])]);
					
					if (@count($fields_info[$label][_VAL_HTML_FIELD]) == 2) {
						$macth_values = $fields_info[$label][_VAL_HTML_FIELD][0];
						$macth_htmls = $fields_info[$label][_VAL_HTML_FIELD][1];
						if ((count($macth_values) == count($macth_htmls)) && (count($macth_values) > 1)) {
							for($nn=0; $nn < count($macth_values); $nn++) {
								$range_values = explode("-", $macth_values[$nn]);
								if (count($range_values) == 2) {
									if ($value >= trim($range_values[0]) && $value <= trim($range_values[1])) {
										$temp = $macth_htmls[$nn];
									}
								}else{
									if ($value == trim($macth_values[$nn])) {
										$temp = trim($macth_htmls[$nn]);
									}
								}
							}
							if (strlen($temp) == 0) $temp = $value;
						}else{
							$temp = $value;
						}
						/*
						for($nn=0; $nn < count($macth_values); $nn++) {
							$range_values = explode("-", $macth_values[$nn]);
							if (count($range_values) == 2) {
								if ($value >= trim($range_values[0]) && $value <= trim($range_values[1])) {
									$temp = $macth_htmls[$nn];
								}
							}else{
								if ($value == trim($macth_values[$nn])) {
									$temp = trim($macth_htmls[$nn]);
								}
							}
						}
						if (strlen($temp) == 0) {
							if ((count($macth_values) == count($macth_htmls)) && (count($macth_values) > 1)) {
								$temp = str_replace($macth_values, $macth_htmls, $value);
							}else{
								$temp = $value;
							}
						}
						//*/
						if (strlen($fields_info[$label][_RID_FIELD]) > 0) {
							$field_ids = explode(",", $fields_info[$label][_RID_FIELD]);
							for($mm=0; $mm < count($field_ids); $mm++) {
								$temp = str_replace("_SID_".($mm+1)."_", $row[getFieldName($field_ids[$mm])], $temp);
							}
						}
					}else{
						$temp = $value;
					}
					
					if (strlen($fields_info[$label][_HREF_FIELD]) && $value > 0) {
						$temp = str_replace(Array('_SID_','_R_'), Array($row[getFieldName($fields_info[$label][_RID_FIELD])], $temp), $href_fields[$fields_info[$label][_HREF_FIELD]]);
					}
					if (strlen($fields_info[$label][_HREF_FIELD_ZERO])) {
						$temp = str_replace(Array('_SID_','_R_'), Array($row[getFieldName($fields_info[$label][_RID_FIELD])], $temp), $href_fields[$fields_info[$label][_HREF_FIELD_ZERO]]);
					}
					
					foreach($coms AS $com) {
						$temp_value = tableFormat($fields_info[$com[0]][_COM_TYP_FIELD], $row[getFieldName($fields_info[$com[0]][_SQL_FIELD])]);
						$com_id = $com[0];
						if (@count($fields_info[$com_id][_VAL_HTML_FIELD]) == 2) {
							$temp2 = "";
							$macth_values = $fields_info[$com_id][_VAL_HTML_FIELD][0];
							$macth_htmls = $fields_info[$com_id][_VAL_HTML_FIELD][1];
							for($nn=0; $nn < count($macth_values); $nn++) {
								$range_values = explode("-", $macth_values[$nn]);
								if (count($range_values) == 2) {
									if ($temp_value >= trim($range_values[0]) && $temp_value <= trim($range_values[1])) {
										$temp2 = $macth_htmls[$nn];
									}
								}else{
									if ($temp_value == trim($macth_values[$nn])) {
										$temp2 = trim($macth_htmls[$nn]);
									}
								}
							}
							if (strlen($temp2) == 0) {
								if ((count($macth_values) == count($macth_htmls))&&(count($macth_values) > 1)) {
									$temp2 = str_replace($macth_values, $macth_htmls, $temp_value);
								}else{
									$temp2 = $temp_value;
								}
							}
							if (strlen($fields_info[$com_id][_RID_FIELD]) > 0) {
								$field_ids = explode(",", $fields_info[$com_id][_RID_FIELD]);
								for($mm=0; $mm < count($field_ids); $mm++) {
									$temp2 = str_replace("_SID_".($mm+1)."_", $row[getFieldName($field_ids[$mm])], $temp2);
								}
							}
							if (strlen($temp2) > 0) $temp_value = $temp2;
						}
						
						if (strlen($fields_info[$com_id][_HREF_FIELD]) && $temp_value > 0) {
							$temp_value = str_replace(Array('_SID_','_R_'), Array($row[getFieldName($fields_info[$com_id][_RID_FIELD])], $temp_value), $href_fields[$fields_info[$com_id][_HREF_FIELD]]);
						}
						if (strlen($temp_value)>0) $temp .= "<div class=\"".$com[1]."\">$temp_value </div>";
					}
					$write_2_table[] = Array($temp,$fields_info[$label][_ALG_FIELD], trim($fields_info[$label][_RID_FIELD]), $fields_info[$label][_FORCE_WORD_WARP_FIELD]);
					$color = $fields_info[$label][_BG_COLOR_FIELD];
					if (strlen($color)>0 && strlen($bg_color)==0) {
						$mark = $fields_info[$label][_BG_MARK_FIELD];
						$oper = $fields_info[$label][_BG_OPER_FIELD];
						$bg_color = tableBgColor($table_mode, $no, $mark, $oper, $value, $color);
					}
				}
				foreach($hidden_info AS $hidden) {
					$value = tableFormat($fields_info[$hidden][_COM_TYP_FIELD], $row[getFieldName($fields_info[$hidden][_SQL_FIELD])]);
					$color = $fields_info[$hidden][_BG_COLOR_FIELD];
					if (strlen($color)>0  && strlen($bg_color)==0) {
						$mark = $fields_info[$hidden][_BG_MARK_FIELD];
						$oper = $fields_info[$hidden][_BG_OPER_FIELD];
						$bg_color = tableBgColor($table_mode, $no, $mark, $oper, $value, $color);
					}
				}
				if (strlen($bg_color)==0 && $table_mode == 2 && $no%2 != 0) $bg_color = convColorStyle("active");
				?><tr <?=$bg_color;?>><td class="text-center"><?=$row['RowID'];?></td><?php
				foreach($write_2_table AS $field) {
					if (strlen($pattern_icons[$field[0]])>0) {
						$pattern_icon_replace=$pattern_icons[$field[0]];
						$rid = explode(",", $field[2]);
						for($ii=0; $ii<count($rid); $ii++) {
							if (strlen($row[getFieldName($rid[$ii])])>0) $pattern_icon_replace = str_replace("_SID_".($ii+1)."_", $row[getFieldName($rid[$ii])], $pattern_icon_replace);
						}
						$field[0] = $pattern_icon_replace;
					}
					?><td class="<?=tableAlign($field[1])?><?=($field[3]?" force-word-warp":"");?>"><?=$field[0];?></td><?php
				}
				?></tr><?php
				$no++;
			}
		}else{
?>
					<tr><td class="text-center" colspan="<?=(count($labels_info)+1)?>">DB Query Error</td></tr>
					<tr><td><?php echo $sql_fields_from_where; ?></td></tr>
<?php
		}
	}else{
		$_SESSION[_SES_SQL_CUR_QRY_ROW] = 0;
		$_SESSION[_SES_SQL_CUR_QRY_SEL] = "";
		$_SESSION[_SES_SQL_CUR_QRY_ODR] = "";
?>
					<tr><td class="text-center" colspan="<?=(count($labels_info)+1)?>">Empty data</td></tr>
<?php
	}
?>
									</tbody>
								</table>
							</div>
							<?php require_once("_e_footer_pages.php");?>
						</div>
					</div>
				</div>
				<div class="card-footer small text-muted">
				<?php if (strlen($table_define)) {?>
					<div class="row">
						<div class="col-md-4">Total <?=number_format($data_count, 0, ".", "," );?> rows,&nbsp;&nbsp;Updated <?=date("Y-m-d H:i:s");?></div>
						<div class="col-md-8 text-right"><?=$table_define; ?></div>
					</div>
				<?php }else{?>
				Total <?=number_format($data_count, 0, ".", "," );?> rows,&nbsp;&nbsp;Updated <?=date("Y-m-d H:i:s");?>
				<?php }?>
				</div>
			</div>