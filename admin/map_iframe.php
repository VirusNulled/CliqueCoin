<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2021 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2021.01.05 13:41:53 EST
 * @license       This program is free software; you can redistribute it and/or modify
 *        it under the terms of the GNU General Public License as published by
 *        the Free Software Foundation; either version 3 of the License, or
 *        (at your option) any later version.
 *
 *        This program is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *        GNU General Public License for more details.
 *
 *        You should have received a copy of the GNU General Public License along
 *        with this program;  If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 *
 *  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *        Million Dollar Script
 *        A pixel script for selling pixels on your website.
 *
 *        For instructions see README.txt
 *
 *        Visit our website for FAQs, documentation, a list team members,
 *        to post any bugs or feature requests, and a community forum:
 *        https://milliondollarscript.com/
 *
 */

ini_set( 'max_execution_time', 10000 );
define( 'NO_HOUSE_KEEP', 'YES' );

require_once __DIR__ . "/../include/init.php";

require( 'admin_common.php' );

global $f2;

$BID = $f2->bid();

$banner_data = load_banner_constants( $BID );

?>
<script>

	window.winWidth = 0;
	window.winHeight = 0;
	initFrameSize();

	function initFrameSize() {

		var myWidth = 0, myHeight = 0;
		if (typeof (window.innerWidth) === 'number') {
			//Non-IE
			myWidth = window.innerWidth;
			myHeight = window.innerHeight;
		} else if (document.documentElement &&
			(document.documentElement.clientWidth || document.documentElement.clientHeight)) {
			//IE 6+ in 'standards compliant mode'
			myWidth = document.documentElement.clientWidth;
			myHeight = document.documentElement.clientHeight;
		} else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
			//IE 4 compatible
			myWidth = document.body.clientWidth;
			myHeight = document.body.clientHeight;
		}
		window.winWidth = myWidth;
		window.winHeight = myHeight;

	}

	pos = 'right';

	h_padding = 10;
	v_padding = 10;

	/*

	Block moving functions

	*/

	var bm_move_order_state = false;
	var bm_move_block_state = false;

	var BID = <?php echo $BID; ?>;

	function bm_state_change(button) {

		is_moving = false;

		if (button === 'MOVE_ORDER') {
			bm_move_block_state = false;
			document.button_move_b.src = 'images/move_b.gif';
			if (bm_move_order_state) {
				bm_move_order_state = false;
				document.button_move.src = 'images/move.gif';
			} else {
				bm_move_order_state = true;
				document.button_move.src = 'images/move_down.gif';
			}
		}

		if (button === 'MOVE_BLOCK') {
			bm_move_order_state = false;
			document.button_move.src = 'images/move.gif';

			if (bm_move_block_state) {
				bm_move_block_state = false;
				document.button_move_b.src = 'images/move_b.gif';
			} else {
				bm_move_block_state = true
				document.button_move_b.src = 'images/move_b_down.gif';
			}
		}

		if ((bm_move_block_state === true) || (bm_move_order_state === true)) {
			document.body.style.cursor = 'move';

		} else {
			document.body.style.cursor = 'default';

		}

	}

	var is_moving = false;

	var cb_from;

	function do_block_click(banner_id) {

		document.body.style.cursor = 'default';
		is_moving = true;
		let cb = get_clicked_block();

		if (bm_move_order_state) {
			document.pointer_img.src = 'get_pointer_image2.php?BID=' + banner_id + '&block_id=' + cb;
		} else {
			document.pointer_img.src = 'get_pointer_image.php?BID=' + banner_id + '&block_id=' + cb;

		}
		cb_from = cb

	}

	function put_pixels(e) {

		is_moving = false;

		document.move_form.cb_to.value = get_clicked_block();
		document.move_form.cb_from.value = cb_from;

		if (bm_move_order_state) {
			document.move_form.move_type.value = 'O'; // Move order
		} else {
			document.move_form.move_type.value = 'B'; // Move block

		}

		document.move_form.submit();

		document.pointer_img.src = 'images/pointer.png';

	}

	function show_pointer(e) {

		var pixelimg = document.getElementById('pixelimg');

		if (!pos) {
			var pos = getObjCoords(pixelimg);
		}

		if (e.offsetX) {
			var OffsetX = e.offsetX;
			var OffsetY = e.offsetY;
		} else {
			var OffsetX = e.pageX - pos.x;
			var OffsetY = e.pageY - pos.y;

		}

		OffsetX = Math.floor(OffsetX / <?php echo $banner_data['BLK_WIDTH']; ?>) *<?php echo $banner_data['BLK_WIDTH']; ?>;
		OffsetY = Math.floor(OffsetY / <?php echo $banner_data['BLK_HEIGHT']; ?>) *<?php echo $banner_data['BLK_HEIGHT']; ?>;

		var pointer = document.getElementById('block_pointer');

		if (!is_moving) {
			//return false;
			pointer.style.visibility = 'hidden';

		} else {
			pointer.style.visibility = 'visible';

		}

		if (pos.y + OffsetY) {
			pointer.style.top = pos.y + OffsetY;
			pointer.style.left = pos.x + OffsetX;

			pointer.map_x = OffsetX;
			pointer.map_y = OffsetY;
		}

		return true;

	}

	var pos;

	function getObjCoords(obj) {
		var pos = {x: 0, y: 0};
		var curtop = 0;
		var curleft = 0;
		if (obj.offsetParent) {
			while (obj.offsetParent) {
				curtop += obj.offsetTop;
				curleft += obj.offsetLeft;
				obj = obj.offsetParent;
			}
		} else if (obj.y) {
			curtop += obj.y;
			curleft += obj.x;
		}
		pos.x = curleft;
		pos.y = curtop;
		return pos;
	}

	function get_clicked_block() {

		var pointer = document.getElementById('block_pointer');

		var grid_width =<?php echo $banner_data['G_WIDTH'] * $banner_data['BLK_WIDTH'];?>;
		var grid_height =<?php echo $banner_data['G_HEIGHT'] * $banner_data['BLK_HEIGHT'];?>;

		var blk_width = <?php echo $banner_data['BLK_WIDTH']; ?>;
		var blk_height = <?php echo $banner_data['BLK_HEIGHT']; ?>;

		var clicked_block = ((pointer.map_x) / blk_width) + ((pointer.map_y / blk_height) * (grid_width / blk_width));

		if (clicked_block === 0) {
			// convert to string
			clicked_block = "0";
		}

		return clicked_block;
	}

</script>
<span id='block_pointer' onclick="put_pixels(event);" style='cursor: pointer;position:absolute;left:0; top:0;background-color:#FFFFFF; visibility:hidden; '><img name='pointer_img' src='images/pointer.png'></span>

<form method='post' name="move_form" action='map_iframe.php'>
    <input name='cb_from' type="hidden" value="">
    <input name='cb_to' type="hidden" value="">
    <input name='move_type' type="hidden" value="B">
    <input name='BID' type="hidden" value="<?php echo $BID; ?>">
</form>

<?php

if ( isset( $_REQUEST['move_type'] ) && ! empty( $_REQUEST['move_type'] ) ) {

	if ( $_REQUEST['move_type'] == 'B' ) {
		// move block
		move_block( $_REQUEST['cb_from'], $_REQUEST['cb_to'], $BID );
	} else if ( $_REQUEST['move_type'] == 'O' ) {
		// move order
		move_order( $_REQUEST['cb_from'], $_REQUEST['cb_to'], $BID );
	}
}

$sql = "SELECT * FROM blocks WHERE  banner_id=" . intval( $BID );
$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

?>

<IMG name='button_move' SRC="images/move.gif" WIDTH="24" HEIGHT="20" BORDER="0" ALT="Move Order" onclick='bm_state_change("MOVE_ORDER")'>
<IMG name='button_move_b' SRC="images/move_b.gif" WIDTH="24" HEIGHT="20" BORDER="0" ALT="Move Block" onclick='bm_state_change("MOVE_BLOCK")'>
<map name="main" id="main">

	<?php

	while ( $row = mysqli_fetch_array( $result ) ) {

		$sql = "select * from users where ID=" . intval( $row['user_id'] );
		$res = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$user_row = mysqli_fetch_array( $res );

		$sql = "select * from orders where order_id=" . intval( $row['order_id'] );
		$res = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$order_row = mysqli_fetch_array( $res );

		if ( $order_row['days_expire'] > 0 ) {

			if ( $order_row['published'] != 'Y' ) {
				$time_start = strtotime( gmdate( 'r' ) );
			} else {
				$time_start = strtotime( $order_row['date_published'] . " GMT" );
			}

			$elapsed_time = strtotime( gmdate( 'r' ) ) - $time_start;
			$elapsed_days = floor( $elapsed_time / 60 / 60 / 24 );

			$exp_time = ( $order_row['days_expire'] * 24 * 60 * 60 );

			$exp_time_to_go = $exp_time - $elapsed_time;
			$exp_days_to_go = floor( $exp_time_to_go / 60 / 60 / 24 );

			$to_go = elapsedtime( $exp_time_to_go );

			$elapsed = elapsedtime( $elapsed_time );

			$days = "$elapsed passed<br> $to_go to go (" . $order_row['days_expire'] . ")";

			if ( $order_row['published'] != 'Y' ) {
				$days = "not published";
			} else if ( $exp_time_to_go <= 0 ) {
				$days .= 'Expired!';
			}
		} else {

			$days = "Never";
		}

		$alt_text = "<b>Customer:</b> " . $user_row['FirstName'] . " " . $user_row['LastName'] . " <br><b>Username:</b> " . $user_row['Username'] . "<br><b>Email:</b> " . $user_row['Email'] . "<br><b>Order</b> # : " . $row['order_id'] . " <br> <b>Block Status:</b> " . $row['status'] . "<br><b>Published:</b> " . $order_row['published'] . "<br><b>Approved:</b> " . $order_row['published'] . "<br><b>Expires:</b> " . $days . "<br><b>Click Count:</b> " . $row['click_count'] . "<br><b>Block ID:</b> " . $row['block_id'] . "<br><b>Co-ordinate:</b> x:" . $row['x'] . ", y:" . $row['y'] . "";

		?>

        <area
                onclick="if (bm_move_block_state || bm_move_order_state) {do_block_click(<?php echo $BID; ?>)} else { window.parent.location='<?php echo $f2->value( BASE_HTTP_PATH ); ?>admin/#orders.php?user_id=<?php echo( $row['user_id'] ); ?>&BID=<?php echo $BID; ?>&order_id=<?php echo $row['order_id']; ?>';}return false;"
                onmousemove="show_pointer(event)"
                href="<?php echo( $row['url'] ); ?>"

                shape="RECT" coords="<?php echo $row['x']; ?>,<?php echo $row['y']; ?>,<?php echo $row['x'] + $banner_data['BLK_WIDTH']; ?>,<?php echo $row['y'] + $banner_data['BLK_HEIGHT']; ?>"

                target="_blank">
	<?php } ?>

</map>

<img border=0 usemap="#main" id="pixelimg" onmousemove="show_pointer(event)" src='show_map.php?BID=<?php echo $BID; ?>&time=<?php echo time(); ?>'>