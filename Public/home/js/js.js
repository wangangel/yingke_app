// JavaScript Document

function tabsub(num,classname,sum,tid,cid){  
	for(i=1;i<=sum;i++)
	{         
		if(i==num){           
			document.getElementById(tid+i).className = classname ; 
			document.getElementById(cid+i).style.display = "block";		   
		}else{			
			document.getElementById(tid+i).className = "" ; 
			document.getElementById(cid+i).style.display = "none"; 			 
		}  		
	} 			   
}


$(function(){
	/*导航下拉框*/
	$('nav ul li').hover(function(){
		$(this).children('.nav_dpn').show();
	},function(){
		$(this).children('.nav_dpn').hide();
	});
	
	
	
	var tname=/^[\u4E00-\u9FA5A-Za-z][\u4E00-\u9FA5A-Za-z0-9]+$/;
	var tmail=/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
	var ttel=/^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/;
	var tpassword=/^[0-9a-zA-Z_]{6,20}$/;
	/*个人注册*/
	//用户名
	$('#pr_uname').focus(function(){
		$(this).siblings('.warn2').hide();
		$(this).siblings('.warn1').show();
		
	});
	$('#pr_uname').blur(function(){
		var tt=$(this).val();
		$(this).siblings('.warn1').hide();
		if(tt.length>=4 && tt.length<=20){
			if(tname.test(tt) || tmail.test(tt) || ttel.test(tt)){
				$(this).siblings('.warn2').hide();
			}
		}else{$(this).siblings('.warn2').show();}
	});
	//密码
	$('#pr_psw').focus(function(){
		$(this).siblings('.warn2').hide();
		$(this).siblings('.warn1').show();
	});
	$('#pr_psw').blur(function(){
		var tt=$(this).val();
		$(this).siblings('.warn1').hide();
		if(tt.length>=6 && tt.length<=20 && tpassword.test(tt)){
			$(this).siblings('.warn2').hide();
		}else{$(this).siblings('.warn2').show();}
	});
	//确认密码
	$('#pr_psw_check').blur(function(){
		if($(this).val()==$('#pr_psw').val()){
			$(this).siblings('.warn2').hide();
		}else{$(this).siblings('.warn2').show();}
	});
	//手机号
	$('#pr_psw_tel').blur(function(){
		if(ttel.test($(this).val())){
			$(this).siblings('.warn2').hide();
		}else{$(this).siblings('.warn2').show();}
	});
	//收货所在地
	$('.regist .fm .shdz').each(function(index, element) {
		$(this).children('input').click(function(){
			$(this).next('.shdz_dpd').toggle();
			
		});
		$(this).children('.shdz_dpd').children('a').click(function(e){
			var tx1=$(this).text();
			$(this).parent().prev('input').val(tx1);
			$(this).parent().hide();
		});
		
    });
	//所在部门
	$('.regist .fm .department').each(function(index, element) {
        $(this).children('input').click(function(){
			$(this).next('.dropdown').toggle();
		});
		$(this).children('.dropdown').children('a').click(function(e){
			var tx1=$(this).text();
			$(this).parent().prev('input').val(tx1);
			$(this).parent().hide();
		});
		
    });
	/*服务详情*/
	$('.servdetail .section1 .delivery').each(function(index, element) {
        $(this).children('.dropdown').each(function(index, element) {	//dropdown定位(左侧)
            var w1=$(this).prev('.ipt1').position().left;
			$(this).css('left',w1);
        });
		$(this).children('.dropdown').children('a').click(function(){
			$(this).parent().prev('.ipt1').val($(this).text());
			$(this).parent().hide();
		});
		$(this).children('.ipt1').click(function(){
			$(this).next('.dropdown').toggle();
		});
    });
	$('.servdetail .section1 .serve .d1').each(function(index, element) {
        $(this).hover(function(){
			$(this).children('.dropdown').show();
		},function(){
			$(this).children('.dropdown').hide();
		})
    });
	//评论
	$('.servdetail .discuss_con .dd2').each(function(index, element) {
        var w1=$(this).prev('.dd1').find('.bot1 .reply').position().left+10;
		$(this).find('.fm1 .ico').css('left',w1);
		
    });
	$('.servdetail .discuss_con dl .dd1 .bot1 .reply').click(function(){
		var obj = this;
		$('.servdetail .discuss_con dl .dd1 .bot1 .reply').each(function(index, element) {
            if(element != obj){
				$(element).parents('.dd1').next('.dd2').slideUp();
			}
        });
		
		$(this).parents('.dd1').next('.dd2').slideToggle();
	});
	/*购物车*/
	$('.shopcar .con1 .list').each(function(index, element) {
        $(this).find('.add').click(function(){
			var t= parseInt($(this).next().val());
			if(t<1 || isNaN(t) || t==""){$(this).next().val(1);}else{$(this).next().val(t+1);}
		});
		$(this).find('.cut').click(function(){
			var t= parseInt($(this).prev().val());
			if(t>1){$(this).prev().val(t-1);}
		});
    });
	$('.shopcar .con1 .list .num .ssel').each(function(index, element) {
		$(this).children('dt').click(function(e){
			e.stopPropagation();
			$(this).next('dd').removeClass('hide');
		});
		$(this).children('dd').click(function(e){
			e.stopPropagation();
			$(this).removeClass('hide');
		});
		$(document).click(function(){
			var lg1=$('.shopcar .con1 .list .num .ssel').children('dd').length;
			var lg2=$('.shopcar .con1 .list .num .ssel').children('dd.hide').length;
			if(lg1>lg2){
				$('.shopcar .con1 .list .num .ssel').children('dd').addClass('hide');
			}
		});
    });
	/*订单确认*/
	$('.order_check .title2').each(function(index, element) {
        var w1=$(this).width() - $(this).children('.sp1').width() - 20;
		$(this).children('.line').width(w1);
    });
	
	$('.order_check .bill_info ul .jiaji_toggle1').click(function(){
		$(this).siblings('.addinfo').slideDown();
	});
	$('.order_check .bill_info ul .jiaji_toggle2').click(function(){
		$(this).siblings('.addinfo').slideUp();
	});
	/*$('#reservation1').focus(function(){
		$(this).siblings('.addinfo').slideDown();
	});
	$('#reservation2').focus(function(){
		$(this).siblings('.addinfo').slideUp();
	});*/
	/*支付*/
	$('.order_check .pay .pay_list1 .Unionpays label').bind('click',function(){
		var obj=this;
		$(obj).addClass('sel');
		$('.order_check .pay .pay_list1 .Unionpays label').each(function(index, element) {
            if(element != obj){
				$(element).removeClass('sel');
			}
        });
		
	});
	
	
	/*个人中心*/
	//个人中心
	$('.myspace .content .collect_list').each(function(index, element) {
        $(this).children('.next').children('a').click(function(){
			if($(this).parent().siblings('.pic_wrap').scrollLeft() <= 1350){
				$(this).parent().siblings('.pic_wrap').animate({scrollLeft:'+=150px'});
			}else{
				$(this).parent().siblings('.pic_wrap').animate({scrollLeft:'1500px'});
			}
		});
		$(this).children('.prev').children('a').click(function(){
			if($(this).parent().siblings('.pic_wrap').scrollLeft() >= 150){
				$(this).parent().siblings('.pic_wrap').animate({scrollLeft:'-=150px'});
			}else{
				$(this).parent().siblings('.pic_wrap').animate({scrollLeft:'0'});
			}
		});
    });
	$('.myspace .content .collect_list .pic_wrap li').hover(function(e){
		$(this).children('.price').css('background','rgba(0,0,0,.8)');
	},function(e){
		$(this).children('.price').css('background','rgba(0,0,0,.3)');
	});
	//我的预约
	$('.myspace .reservation .myorder2 .list .dd3').each(function(index, element) {
        $(this).find('input.yes').focus(function(){
			$(this).parent().siblings('.drw').slideDown();
		});
		$(this).find('input.no').focus(function(){
			$(this).parent().siblings('.drw').slideUp();
		});
		
    });
	
	
	//我的订单
	$('.myspace .tail_wrap').each(function(index, element) {
        var w1=$(this).width();
		//$(this).children('.dropdown').outerWidth(w1);
		$(this).children('.tail').hover(function(){
			$(this).next('.dropdown').slideDown('fast');
		},function(){
			$(this).next('.dropdown').slideUp('fast');	
		});
    });
	//修改密码
	$('#e_newpsw').focus(function(){
		$(this).siblings('.warn1').hide();
		$(this).siblings('.warn2').show();
	});
	$('#e_newpsw').blur(function(){
		var tt=$(this).val();
		$(this).siblings('.warn2').hide();
		if(tt.length>=6 && tt.length<=20 && tpassword.test(tt)){
			$(this).siblings('.warn1').hide();
		}else{$(this).siblings('.warn1').show();}
	});
	$('#e_checknewpsw').blur(function(){
		if($(this).val()==$('#e_newpsw').val()){
			$(this).siblings('.warn1').hide();
		}else{$(this).siblings('.warn1').show();}
	});
	/*商品详情*/
	$('.servdetail .section1 .info .list .num').each(function(index, element) {
        $(this).find('.add').click(function(){
			var t= parseInt($(this).prev().val());
			if(t<1 || isNaN(t) || t==""){$(this).prev().val(1);}else{$(this).prev().val(t+1);}
		});
		$(this).find('.cut').click(function(){
			var t= parseInt($(this).next().val());
			if(t>1){$(this).next().val(t-1);}else{$(this).next().val(1)}
		});
    });
	/*安居易代收地址*/
	/*$('.collection .sel_addr .list').each(function(index, element) {
        var obj=this;
		$(this).click(function(){
			$(this).addClass('sel');
			if(obj != index){
				
			}
		});
    });*/
	$('.collection .sel_addr .list').bind('click',function(){
		var obj=this;
		$('.collection .sel_addr .list').each(function(index, element) {
            if(element != obj){
				$(element).removeClass('sel');
			}
        });
		$(obj).addClass('sel');
	});
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*下拉框--input仿select dropdown*/
	$('.dn_toggle').each(function(index, element) {	//给input.dn_toggle执行方法
        var w1=$(this).position().left;		//当前input相对父元素的left定位
		var h1=$(this).position().top + $(this).outerHeight();		//当前input相对父元素的top定位
		var drWidth=$(this).outerWidth();
		$(this).next('.dropdown').outerWidth(drWidth);		//当前input下拉框的宽度
		$(this).next('.dropdown').css({'position':'absolute','left':w1,'top':h1});
		$(this).click(function(){
			$(this).next('.dropdown').toggle();
		});
		$(this).next('.dropdown').children('a').click(function(){
			$(this).parent('.dropdown').prev('.dn_toggle').val($(this).text());
			$(this).parent('.dropdown').hide();
		});
		
    });
	
	/*价格+服务*/
	$('.ProductColor').find('.child').bind('click',function(){
			var obj=this;
			$('.ProductColor').find('.child').each(function(index, element) {
                	if(element != obj){
							$(element).removeClass('sel');
					}
            });
			if($(this).hasClass('sel')){
					$(this).removeClass('sel');
			}else{
					$(this).addClass('sel');
			}
	});
	$('.ProductNorm').find('.child').bind('click',function(){
			var obj=this;
			$('.ProductNorm').find('.child').each(function(index, element) {
                	if(element != obj){
							$(element).removeClass('sel');
					}
            });
			if($(this).hasClass('sel')){
					$(this).removeClass('sel');
			}else{
					$(this).addClass('sel');
			}
	});
	/*$('.ProductServe').find('.child').bind('click',function(){
			var obj=this;
			$('.ProductServe').find('.child').each(function(index, element) {
                	if(element != obj){
							$(element).removeClass('sel');
					}
            });
			if($(this).hasClass('sel')){
					$(this).removeClass('sel');
			}else{
					$(this).addClass('sel');
			}
	});*/
	$('.ProductServe').find('.child').click(function(){	//服务项目选择
			if($(this).hasClass('sel')){
					$(this).removeClass('sel');
					if($(this).hasClass('child_1')){
							$(this).siblings('.child_2').hide();
							$(this).siblings('.child_2').removeClass('sel');
					}
			}else{
					$(this).addClass('sel');
					if($(this).hasClass('child_1')){
							$(this).siblings('.child_2').show();
					}
			}
	});
	
	

	/**商品详情页
	 * 8-1添加浮点整数运算
	 */
	/**
     * [accAdd description]浮点的加法运算
     * @param  {[type]} arg1 [description]
     * @param  {[type]} arg2 [description]
     * @return {[type]}      [description]
     */
    function accAdd(arg1, arg2) {

        var r1, r2, m, c;

        try { r1 = arg1.toString().split(".")[1].length } catch (e) { r1 = 0 }

        try { r2 = arg2.toString().split(".")[1].length } catch (e) { r2 = 0 }

        c = Math.abs(r1 - r2);
        m = Math.pow(10, Math.max(r1, r2))
        if (c > 0) {
            var cm = Math.pow(10, c);
            if (r1 > r2) {
                arg1 = Number(arg1.toString().replace(".", ""));
                arg2 = Number(arg2.toString().replace(".", "")) * cm;
            }
            else {
                arg1 = Number(arg1.toString().replace(".", "")) * cm;
                arg2 = Number(arg2.toString().replace(".", ""));
            }
        }
        else {
            arg1 = Number(arg1.toString().replace(".", ""));
            arg2 = Number(arg2.toString().replace(".", ""));
        }
        return (arg1 + arg2) / m

    }
    /**
     * [accSub description]动态控制减法
     * @param  {[type]} arg1 [description]
     * @param  {[type]} arg2 [description]
     * @return {[type]}      [description]
     */
    function accSub(arg1,arg2){
    　　 var r1,r2,m,n;
    　　 try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
    　　 try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
    　　 m=Math.pow(10,Math.max(r1,r2));
    　　 //last modify by deeka
    　　 //动态控制精度长度
    　　 n=(r1>=r2)?r1:r2;
    　　 return ((arg1*m-arg2*m)/m).toFixed(n);
    }
	
	
})


















