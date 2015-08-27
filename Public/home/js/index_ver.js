//验证个人注册用户名
function VeriftyUsername(){
    var username = $("#pr_uname").val();
    var o  = document.getElementById("tishi1"); 
    var unlength = username.length;
            if(unlength<4){
                o.innerHTML = "您输入的用户名太短,请重新输入!";
                return false;
            }else if(unlength>20){
                o.innerHTML = "您输入的用户名太长,请重新输入!";
                return false;
            }else if(4<=unlength<=20){
                  $.getJSON(usernameurl,{"username":username,"type":"name"} ,function(json){
                    if(json.status == 1){
						alert('用户名已经被注册!');
						$('#pr_uname').val("");  
						return false;
                    }
                  });
            }

}



function AddFavorite(sURL, sTitle) {  
    try {  
        window.external.addFavorite(sURL, sTitle);  
    } catch (e) {  
        try {  
            window.sidebar.addPanel(sTitle, sURL, "");  
        } catch (e) {  
            alert("加入收藏失败，请使用Ctrl+D进行添加");  
        }  
    }  
}

//验证密码
function VeriftyPWD(){
    var password = $("#pr_psw").val();
    var plength = password.length;
    if(plength<6){
        document.getElementById("tishi2").innerHTML = "您输入的密码太短,请重新输入!";
        $('#pr_psw').val("");
        return false;
    }else if(plength>20){
        document.getElementById("tishi2").innerHTML = "您输入的密码太长,请重新输入!";
         $('#pr_psw').val("");
        return false;
    }
}
//验证密码重复
function VeriftyPWD2(){
    var password = $("#pr_psw_check").val();
    var password1 = $("#pr_psw").val();
    var plength = password.length;
    if(plength<6){
        document.getElementById("tishi3").innerHTML = "您输入的密码太短,请重新输入!";
           $('#pr_psw_check').val("");
        return false;
    }else if(plength>20){
        document.getElementById("tishi3").innerHTML = "您输入的密码太长,请重新输入!";
          $('#pr_psw_check').val("");
        return false;
    }else if(password != password1){

    	//$("tishi3").val("两次输入的密码不一致!");
        document.getElementById("tishi3").innerHTML = "两次输入的密码不一致!";
        $('#pr_psw_check').val("");
        return false;
    }
}

//验证手机号码
function VeriftyPhone(){
        var isMobile=/^(?:13\d|17\d|15\d|18\d)\d{5}(\d{3}|\*{3})$/; //手机号码验证规则
        var phone = $("#pr_psw_tel").val();   //获得用户填写的号码值 赋值给变量phone
        if(!isMobile.test(phone)){ //如果用户输入的值不同时满足手机号和座机号的正则
		        if("" == phone){
			        document.getElementById("tishi4").innerHTML = "手机号码不能为空,请正确填写电话号码!";
           			$('#pr_psw_tel').val("");
			       
			        return false;
		        }else{
			        document.getElementById("tishi4").innerHTML = "请正确填写手机号码!";
			       	$('#pr_psw_tel').val("");
			        return false; 
		        }       
        }else{
        		$.getJSON(usernameurl,{"phone":phone,"type":"phone"} ,function(json){
                    if(json.status == 1){
						alert('手机号码已经被注册!');
						$('#pr_psw_tel').val("");  
						return false;
                    }
                  });
        }
}
//获取短信验证码
$(function() {  
$("#b01").click(function() {  
	var o = this;
    var phone = $("#pr_psw_tel").val(); 
    if('' == phone){
    	alert("电话号码不能为空!");
    	return false;
    }else{
        $.post(smsurl,{'phone':phone} ,function(json){
        	//alert(json);
                    if(json == 'Success'){
						alert('短信发送成功!');
						get_code_time(o);
                    }else{
                    	alert('短信发送失败!');
                    	return false;
                    }
                  });
    }

}); });
  


//获取验证码发送倒计时
var wait = 60;  
get_code_time = function (o) {  
    if (wait == 0) {  
        o.removeAttribute("disabled");  
        o.value = "免费获取验证码"; 
        document.getElementById("b01").style.background="#1163AF";  
        wait = 60;  
    } else {  
        o.setAttribute("disabled", true);  
        o.value = "(" + wait + ")秒后重新获取";
        document.getElementById("b01").style.background="#666"; 
        wait--;  
        setTimeout(function() {  
            get_code_time(o)  
        }, 1000)  
    }  
}  




//验证固定电话号码
function check_number(){
	 var re = /^0\d{2,3}-?\d{7,8}$/;
	 var email = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/
	 var number = $("number").val();
	 if(!re.test(number)){ //如果用户输入的值不同时满足手机号和座机号的正则
		        if("" == number){
			        document.getElementById("tishi4").innerHTML = "手机号码不能为空,请正确填写电话号码!";
           			$('#number').val("");
			       
			        return false;
		        }else{
			        document.getElementById("tishi4").innerHTML = "请正确填写手机号码，例如:13415764179";
			       	$('#number').val("");
			        return false; 
		        }       
        }else{
        		$.getJSON(usernameurl,{"number":number,"type":"company"} ,function(json){
                    if(json.status == 1){
						alert('电话号码已经被注册!');
						$('#number').val("");  
						return false;
                    }
                  });
        }
}


