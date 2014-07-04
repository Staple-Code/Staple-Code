<!DOCTYPE html>
<html>
	<head>
		<title>Staple Code - A PHP 5 Model-View-Controller Framework for Rapid Application Development</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseLink('style/style.css');?>">
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-23985131-1']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>
		<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
	</head>
	<body>
		<div id="main">
			<div id="title">
				<div style="float:right;"><g:plusone size="small"></g:plusone></div>
				<a href="<?php echo $this->link(array('index','index'));?>"><img src="<?php echo $this->baseLink('images/logo.png');?>" alt="Staple Code"></a>
			</div>
			<div id="mainlinks">
				<ul>
					<li><a href="<?php echo $this->link(array('index','index'));?>">Home</a></li> |
					<li><a href="<?php echo $this->link(array('tutorial','index'));?>">Tutorial</a></li> |
					<li><a href="<?php echo $this->link(array('index','documentation'));?>">Documentation</a></li> |
					<li><a href="<?php echo $this->link(array('download','index'));?>">Download</a></li> |
					<li><a href="<?php echo $this->link(array('index','license'));?>">License</a></li> |
					<li><a href="<?php echo $this->link(array('index','about'));?>">About</a></li>
				</ul>
			</div>
			<div id="body">