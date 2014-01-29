<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
//<![CDATA[
<?php
global $validation_info;
$js_code = '';
//$js_code .= '//global vars ';
$js_code .= 'var submit_form = jQuery("#submit_form");'; //form Id
$jsfunction = array();
for($i=0;$i<count($validation_info);$i++) {
	$title = $validation_info[$i]['title'];
	$name = $validation_info[$i]['name'];
	$espan = $validation_info[$i]['espan'];
	$type = $validation_info[$i]['type'];
	$text = __($validation_info[$i]['text'],DOMAIN);
	$validation_type = $validation_info[$i]['validation_type'];
	$is_required = $validation_info[$i]['is_require'];
	if($is_required ==''){
		$is_required = 0;
	}
	$js_code .= '
	dml = document.forms[\'submit_form\'];
	var '.$name.' = jQuery("#'.$name.'"); ';
	$js_code .= '
	var '.$espan.' = jQuery("#'.$espan.'"); 
	';
	if($type=='selectbox' || $type=='checkbox')
	{
		$msg = sprintf(__("%s",DOMAIN),$text);
	}else
	{
		$msg = sprintf(__("%s",DOMAIN),$text);
	}
	if($type == 'multicheckbox' || $type=='checkbox' || $type=='radio' || $type=='post_categories' || $type=='upload')
	{
		$js_code .= '
		function validate_'.$name.'()
		{
			if("'.$type.'" != "upload")
			  {
				var chklength = jQuery("#'.$name.'").length;
			  }
			if("'.$type.'" =="multicheckbox")
			  {
				chklength = document.getElementsByName("'.$name.'[]").length;
			  }
			if("'.$name.'" == "category"){
				chklength = document.getElementsByName("'.$name.'[]").length;
			}
			if("'.$type.'" =="radio")
			  {
				if(!jQuery("input[name='.$name.']:checked").length > 0) {
					flag = 1;
				}
			  }
			
			if("'.$type.'" =="upload")
			  {
				  var id_value = jQuery('.$name.').val();
				  var valid_extensions = /(.txt|.pdf|.doc|.xls|.xlsx|.csv|.docx|.rar|.zip|.jpg|.jpeg|.gif|.png)$/i;
				  if(valid_extensions.test(id_value))
				  {
					  
				  }
				  else
				  {
				   jQuery("#'.$name.'_error").html("You are uploading invalid file type. Allowed file types are : txt, pdf, doc, xls, csv, docx, xlsx, zip, rar");
				   return false;
				  }
			  }
 			var temp	  = "";
			var i = 0;
			chk_'.$name.' = document.getElementsByName("'.$name.'[]");
			if("'.$name.'" == "category"){
				chk_'.$name.' = document.getElementsByName("'.$name.'[]");
			}
			if(chklength == 0){
				if ((chk_'.$name.'.checked == false)) {
					flag = 1;	
				} 
			} else {
				var flag      = 0;
				for(i=0;i<chklength;i++) {
					if ((chk_'.$name.'[i].checked == false)) { ';
						$js_code .= '
						flag = 1;
					} else {
						flag = 0;
						break;
					}
				}
			}
			if(flag == 1)
			{
				'.$espan.'.text("'.$msg.'");
				'.$espan.'.addClass("message_error2");
				 return false;
			}
			else{			
				'.$espan.'.text("");
				'.$espan.'.removeClass("message_error2");
				return true;
			}
		}
	';
	}else {
		$js_code .= '
		function validate_'.$name.'()
		{';
			if($validation_type == 'email') {
				$js_code .= '
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
				if(jQuery("#'.$name.'").val() == "" && '.$is_required.') { ';
					$msg = __("Please provide your email address",DOMAIN);
				$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
				return false;';
				$js_code .= ' } else if(!emailReg.test(jQuery("#'.$name.'").val()) && jQuery("#'.$name.'").val()) { ';
					$msg = __("Please provide valid email address",DOMAIN);
					$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
					return false;';
				$js_code .= '
				} else {
					'.$name.'.removeClass("error");
					'.$espan.'.text("");
					'.$espan.'.removeClass("message_error2");
					return true;
				}';
			} if($validation_type == 'phone_no'){
				$js_code .= '
				var phonereg = /^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/;
				if(jQuery("#'.$name.'").val() == "" && '.$is_required.') { ';
					$msg = $text;
					$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
				return false;';
				$js_code .= ' } else if(!phonereg.test(jQuery("#'.$name.'").val()) && jQuery("#'.$name.'").val()) { ';
					$msg = "Enter Valid Phone No.";
					$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
					return false;';
				$js_code .= '
				} else {
					'.$name.'.removeClass("error");
					'.$espan.'.text("");
					'.$espan.'.removeClass("message_error2");
					return true;
				}';
			}if($validation_type == 'digit'){
				$js_code .= '
				var digitreg = /^[0-9.,]/;
				if(jQuery("#'.$name.'").val() == "" && '.$is_required.') { ';
					$msg = $text;
				$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
				return false;';
				$js_code .= ' } else if(!digitreg.test(jQuery("#'.$name.'").val())) { ';
					$msg = "This field allow only digit.";
					$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
					return false;';
				$js_code .= '
				} else {
					'.$name.'.removeClass("error");
					'.$espan.'.text("");
					'.$espan.'.removeClass("message_error2");
					return true;
				}';
			}
			if($type == 'texteditor'){
				$js_code .= 'if(jQuery("#'.$name.'").css("display") == "none")
				{
				if(tinyMCE.get("'.$name.'").getContent().replace(/<[^>]+>/g, "") == "") { ';
					$msg = $text;
				$js_code .= $name.'.addClass("error");
					'.$espan.'.text("'.$msg.'");
					'.$espan.'.addClass("message_error2");
				return false;';
				$js_code .= ' }  else {
					'.$name.'.removeClass("error");
					'.$espan.'.text("");
					'.$espan.'.removeClass("message_error2");
					return true;
				}
				}else
				{
					if(jQuery("#'.$name.'").val() == "")
					{
						'.$espan.'.text("'.$msg.'");
						'.$espan.'.addClass("message_error2");
						return false;
					}
					else
					{
						'.$espan.'.text("");
						'.$espan.'.removeClass("message_error2");
						return true;
					}
				}';
			}
			if($type == 'image_uploader'){
				$js_code .= 'if(jQuery("#imgarr").val() == "")
				{
					if("'.$msg.'" == "")
					{
						jQuery("#post_images_error").html("Please upload atleast one image here..");
						return false;
					}
					else
					{
						jQuery("#post_images_error").html("'.$msg.'");
						return false;
					}
				}';
			}
			$js_code .= 'if("'.$name.'" == "end_date")';
			{
				$js_code .= '
				{
					 if(jQuery("#'.$name.'").val() < jQuery("#st_date").val() || jQuery("#'.$name.'").val() == "")
					{
						';
						$js_code .= $name.'.addClass("error");
						'.$espan.'.text("'.$msg.'");
						'.$espan.'.addClass("message_error2");
						return false;
					}
					else
					{
						'.$name.'.removeClass("error");
						'.$espan.'.text("");
						'.$espan.'.removeClass("message_error2");
						return true;
					}
				}';
			}
		$js_code .= 'if(jQuery("#select_category").val() == "")';
		$js_code .= '
			{
				'.$espan.'.text("'.$msg.'");
				'.$espan.'.addClass("message_error2");
				return false;
			}';
		$js_code .= 'if(jQuery("#'.$name.'").val() == "" && '.$is_required.')';
		$js_code .= '
			{ 
				'.$espan.'.text("'.$msg.'");
				'.$espan.'.addClass("message_error2");
				return false;
			}
			else{
				'.$espan.'.text("");
				'.$espan.'.removeClass("message_error2");
				return true;
			}
		}
		';
	}
	//$js_code .= '//On blur ';	
	$js_code .= $name.'.blur(validate_'.$name.'); ';
	//$js_code .= '//On key press ';
	$js_code .= $name.'.keyup(validate_'.$name.'); ';
	if($name=='category' && $type=='checkbox')
	{
		$js_code .= "jQuery('.checkbox').click(function(){
										validate_".$name."()
											});"; 
	}
	$jsfunction[] = 'validate_'.$name.'()';
}
if($jsfunction)
{
	$jsfunction_str = implode(' & ', $jsfunction);	
}
//$js_code .= '//On Submitting ';
$js_code .= '	
submit_form.submit(function()
{
	var package_select = jQuery("input[name=package_select]");	
	var package_type=package_select.attr("type");
	if (document.getElementsByName("package_select").length >0){
		if(package_type =="radio")
		{
			if (!jQuery("input:radio[name=package_select]:checked").val())
			 {
				jQuery("#all_packages_error").html("'.__(PRICE_PACKAGE_ERROR,DOMAIN).'");
				return false; // add comment return false nothing add and directoly submit then only price package error will be shown
			 }
			else
			{
				jQuery("#all_packages_error").html("");
			}
		}
}
	if('.$jsfunction_str.')
	{
		jQuery("#common_error").html("");
		return true
	}
	else
	{
		jQuery("#common_error").html("'.__('Oops!, looks like you forgot to enter a value in some compulsory field',DOMAIN).'");
		return false;
	}
});
';
$js_code .= '
});';
echo $js_code;
?>
//]]>
</script>