<?php
	echo "<style>
	
	.table_tnews {
		float: right;
		width: 63%;
		}
		
	.t_theme {
		float: left;
		width: 34%;
		margin-right: 3%;
		}
		
	.t_theme img {
		max-width: 100%;
		}
	.pimg img{ border: 1px solid #ccc }	
	.clearfix { clear:both; }
	ul#templatic-services li{
		list-style:disc inside;
	}
	.clearfix:after{
		clear: both;
		content: ".";
		display: block;
		font-size: 0;
		height: 0;
		line-height: 0;
		visibility: hidden;
		}
	
	.theme_meta .more a.btn_viewdetails,
	.theme_meta .more a.btn_viewdemo {
		margin: 0;
		}
		
	.table_tnews .news li p {
		margin-top: 0;
		}
	.templatic-dismiss {
		background: url('images/xit.gif') no-repeat scroll 0px 2px transparent;
		position: absolute;
		right: 60px;
		top: 8px;
		width: 0px;
		font-size: 13px;
		line-height: 1;
		padding: 0 0 0 10px;
		text-decoration: none;
		text-indent: 3px;
	}
	.templatic-dismiss:hover {
		background-position: -10px 2px;
	}
	
	.templatic_autoinstall{
		position:relative;
		padding: 12px !important;
	}
	div.updated, .login .message,
	{
		background: #FFFBE4;
		border-color: #DFDFDF;
		}
	
	.postbox .inside {
		margin: 15px 0 !important;
		}
	
	.themeunit{
		margin-bottom: 10px;
		}
	
	
	#TB_window,
	#TB_iframeContent {
		height: 460px !important;
		margin-top: 0 !important;
		}
		
	#TB_iframeContent body {
		padding: 0 !important;
		}
	
	.templatic_login {
		background: none repeat scroll 0 0 #FFFFFF;
		font-size: 14px;
		font-weight: normal;
		padding-top: 20px ;
		width:40%;
		}
		
	.templatic_login label {
		color: #777777;
		font-size: 14px;
		}
		
	.templatic_login form .input, .templatic_login input[type='text'], .templatic_login input[type='password'] {
		background: none repeat scroll 0 0 #FBFBFB;
		border: 1px solid #E5E5E5;
		box-shadow: 1px 1px 2px rgba(200, 200, 200, 0.2) inset;
		color: #555555;
		font-size: 24px;
		font-weight: 200;
		line-height: 1;
		margin-bottom: 16px;
		margin-right: 6px;
		margin-top: 2px;
		outline: 0 none;
		padding: 10px 8px 6px;
		width: 100%;
		}
		
	.templatic_login input[type='submit'] {
		background-color: #21759B;
		background-image: linear-gradient(to bottom, #2A95C5, #21759B);
		border-color: #21759B #21759B #1E6A8D;
		box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset;
		color: #FFFFFF;
		text-decoration: none;
		text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
		height: 30px;
    	line-height: 28px;
   		padding: 0 12px 2px;
		border-radius: 3px 3px 3px 3px;
		border-style: solid;
		border-width: 1px;
		cursor: pointer;
		display: inline-block;
		font-size: 12px;
		margin-right: 10px;
		}
		
	.templatic_login p.info {
		margin-top: 0; 
		}
	
	body {
		height: auto;
		min-width: 380px !important;
		}
		
	#pblogo {
		margin-top: 10px;
		}
		
	#TB_window {
		left: 53% !important;
		top: 100px !important;
		}
	
	/* Theme Autoupdate css start */
	.templatic_login {
		background: none repeat scroll 0 0 #FFFFFF;
		border:0 !important;
		margin:0 !important;
		font-size: 14px;
		font-weight: normal;
		padding: 15px;
		padding-top:20px;
		width:40%;
		}
		
	.templatic_login label {
		color: #777777;
		font-size: 14px;
		}
		
	.templatic_login form .input, .templatic_login input[type='text'], .templatic_login input[type='password'] {
		background: none repeat scroll 0 0 #FBFBFB;
		border: 1px solid #E5E5E5;
		box-shadow: 1px 1px 2px rgba(200, 200, 200, 0.2) inset;
		color: #555555;
		font-size: 24px;
		font-weight: 200;
		line-height: 1;
		margin-bottom: 16px;
		margin-right: 6px;
		margin-top: 2px;
		outline: 0 none;
		padding: 10px 8px 6px;
		width: 100%;
		}
		
	.templatic_login input[type='submit'] {
		background-color: #21759b;
		background-image: -webkit-gradient(linear, left top, left bottom, from(#2a95c5), to(#21759b));
		background-image: -webkit-linear-gradient(top, #2a95c5, #21759b);
		background-image:    -moz-linear-gradient(top, #2a95c5, #21759b);
		background-image:     -ms-linear-gradient(top, #2a95c5, #21759b);
		background-image:      -o-linear-gradient(top, #2a95c5, #21759b);
		background-image:   linear-gradient(to bottom, #2a95c5, #21759b);
		border-color: #21759b;
		box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset;
		color: #FFFFFF;
		text-decoration: none;
		text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
		height: 30px;
		line-height: 28px;
		padding: 0 12px 2px;
		border-radius: 3px 3px 3px 3px;
		border-style: solid;
		border-width: 1px;
		cursor: pointer;
		display: inline-block;
		font-size: 12px;
		margin-right: 10px;
		}
		
	.templatic_login p.info {
		margin-top: 0; 
	}
	body {
	min-width: 380px !important;
	}
	#pblogo {
	margin-top: 10px;
	text-align:left !important;
	}
	#TB_window {
	left: 53% !important;
	top: 100px !important;
	}
	/* Theme Autoupdate css end */	
	
	</style>";
?>