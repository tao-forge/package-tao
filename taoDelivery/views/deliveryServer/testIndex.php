<?php
session_start();

if(!isset($_SESSION["subject"]["uri"])){
	header("location: index.php");
}
echo $_SESSION["subject"]["uri"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta name="title" content="TAO platform">
  <meta name="author" content="Administrator">
  <meta name="description" content="TAO, Testing assist&eacute; par ordinateur, computer based testing, evaluation, assessment, CBT, CAT, elearning, competencies, comp&eacute;tences">
  <meta name="keywords" content="TAO, Testing assist&eacute; par ordinateur, computer based testing, evaluation, assessment, CBT, CAT, elearning, competencies, comp&eacute;tences">
  <meta name="robots" content="index, follow">
  <title>TAO - An Open and Versatile Computer-Based Assessment Platform - Test Index</title>

	<style type="text/css">
		body {background: #CDCDCD;color: #022E5F; font-family: verdana, arial, sans-serif;}
		td {font-size: 14px;}
		a {text-decoration: none;font-weight: bold; border: none; color: #BA122B;}
		a:hover {text-decoration: underline; border: none; color: #BA122B;}
		tabCenter{align: center;}
	</style>
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript">
		function get_tests(page){
			
			page = parseInt(page);
			data ="page="+page;
			$.ajax({
				type: "POST",
				url: "testListing.php",
				data: data,
				dataType: "json",
				success: function(result){
					// alert(result);
					// alert(result.pager.total);
					 print_result(result);
				}
			});
		}
		
		function print_result(result){
				// var json_string = result.substr(result.indexOf("result=") + 7);
						//var data= "test no="+r.tests[5]+" current page="+r.pager.current+" total="+r.pager.total;//for test only
						r=result;
						//pager creation
						var pager = "";
						if (r.pager.total > 1) {
							pager += '<p align="center">';
							
							//previous page
							if (r.pager.current > 1) {
								url = "get_tests(" + (parseInt(r.pager.current) - 1) + ")";
								pager += '<a href="#" onclick="' + url + '">prev.</a>&nbsp;&nbsp;';
							}
							// page listing
							imax = Math.min(r.pager.total, 10);
							for (i = 1; i <= imax; i++) {
								url = "get_tests('" + i + "')";
								pager += '<a href="#" onclick="' + url + '">[' + i + ']</a>';
								// pager += '<a onclick="alert(url)">[' + i + ']</a>';
							}
							// following page
							if (r.pager.current < r.pager.total) {
								url = "get_tests(" + (parseInt(r.pager.current) + 1) + ")";
								pager += '&nbsp;&nbsp;<a href="#" onclick="' + url + '">next</a>';
							}
							pager += '</p>';
						}
						
						//table creation
						var testTable = '<table><thead><tr>' 
							+ '<td>Test no</td>'
							+ '<td>Label</td>'
							+ '<td>Comment</td>'
							+ '</tr></thead><tbody>';
						var clazz = '';
						for (i = r.pager.start; i <= r.pager.end; i++) {
							if ((i % 2) == 0)
								clazz = "even";
							else
								clazz = "odd";
							
							var url="../../compiled/"+r.tests[i].uri +"/theTest.php?subject="+r.subject.uri;	
							testTable += '<tr class="test_list ' + clazz + '">';
							testTable += '<td>'+ i +'</td>';
							testTable += '<td><a href="'+ url +'" target="_blank">'+ r.tests[i].label +'</a></td>';
							testTable += '<td>'+ r.tests[i].comment +'</td>';
							testTable += '</tr>';
						}
						testTable += '</tbody></table>';
						
						$("#result").html(testTable+pager);
		}
			
		$(document).ready(function(){
			get_tests(1);
			
			$("#result").html();
		});
	</script>
</head>
<body>
<div align="center" style="position:relative; top:50px;">
<table  width="759px" height="569px" cellpadding="10" cellspacing="0" background="bg_index.jpg" style="border:thin solid #022E5F;">
	<tr height="20px">
		<td><a href="logout.php">logout</a></td>
	</tr>
	<tr>
		<td>
			<table class="tabCenter" align="center">
				<tr>
				  <th>Company</th>
				  <th>Contact</th>
				  <th>Country</th>
				</tr>
				<tr>
				<td>Alfreds Futterkiste</td>
				<td>Maria Anders</td>
				<td>Germany</td>
				</tr>
				<tr class="alt">
				<td>Berglunds snabbk�p</td>
				<td>Christina Berglund</td>
				<td>Sweden</td>
				</tr>
				<tr>
				<td>Centro comercial Moctezuma</td>
				<td>Francisco Chang</td>
				<td>Mexico</td>
				</tr>
				<tr class="alt">
				<td>Ernst Handel</td>
				<td>Roland Mendel</td>
				<td>Austria</td>
				</tr>
				<tr>
				<td>Island Trading</td>
				<td>Helen Bennett</td>
				<td>UK</td>
				</tr>
				<tr class="alt">
				<td>K�niglich Essen</td>
				<td>Philip Cramer</td>
				<td>Germany</td>
				</tr>
				<tr>
				<td>Laughing Bacchus Winecellars</td>
				<td>Yoshi Tannamuri</td>
				<td>Canada</td>
				</tr>
				<tr class="alt">
				<td>Magazzini Alimentari Riuniti</td>
				<td>Giovanni Rovelli</td>
				<td>Italy</td>
				</tr>
				<tr>
				<td>North/South</td>
				<td>Simon Crowther</td>
				<td>UK</td>
				</tr>
				<tr class="alt">
				<td>Paris sp�cialit�s</td>
				<td>Marie Bertrand</td>
				<td>France</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>

<div id="result"></div>
</body>
</html>

