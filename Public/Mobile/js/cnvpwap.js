var config = {};
var _this,bantime,scLock,pn,domload=false;
;(function($){		
	function reset(){
		//config.w = $(window).width();
		config.w=document.body.offsetWidth;
		config.w = config.w>config.maxw?config.maxw:config.w;
		//config.h = $(window).height();	
		config.h = $(window).height();	
		if(config.w>config.h){
		config.h=600;
		}
		if($(window).width()>config.maxw)//容器居中
			$('.container').css('left',($(window).width()-config.w)/2);
		$('.container').width(config.w);
		$('.container').height(config.h);
		if($('.content').length>0)//右侧内容高度
			$('.content').height(config.h - $('.header').height());		
	};
	function show(){
		
		if($('.guide').length>0){//引导进入动画
			$('.guide').removeClass('opa');
			$('.guide .logo').css('top',350);
			setTimeout(function(){$('.guide .logo').animate({opacity:1,top:180},950,'ease-in-out')},200);
			$('.guide h1').css('top',400);
			setTimeout(function(){$('.guide h1').animate({opacity:1,top:200},950,'ease-in-out')},300);
			setTimeout(function(){$('.guide .b').animate({opacity:1},800)},1200);
			//自动跳转
			setTimeout(function(){$('.logo').click();},6000);	
		}else if($('.home').length>0){
			$('.home').removeClass('pull');
			$('.bannerpic').slider({loop:true,container:'.item',autoPlay:true,dots:true});
		}else if($('.content').length>0)
			$('.content').removeClass('pull');
		if($('.header').length>0)
			$('.header').removeClass('pull');
	};
	function init(){
		//iphone 禁止上下滚动
		iphonescroll(true);
		$('.lda').click(function(){
			_this = $(this);
			$('body').addClass('loading');			
			$('.guide').addClass('opa');
			$('.home').addClass('opa');
			$('.mask').removeClass('push');
			$('.header').removeClass('push');
			$('.content').removeClass('push');	
			$('.header').addClass('opa');
			$('.content').addClass('opa');
			$('.menu').addClass('pull');			
			setTimeout(function(){window.location.href=_this.attr('href');},800);	
			return false;
		});
		menuinit();
		contactinit();
		listload();
	};
	function iphonescroll(b){
		$(window).unbind('touchmove');
		if(b){
			$(window).bind('touchmove',function(e){
				e.stopImmediatePropagation();
			});	
		}else{
			$(window).bind('touchmove',function(e){
				e.preventDefault();
				e.stopImmediatePropagation();
			});	
		};	
	};
	function menuinit(){
		if($('.header').length>0){//导航条初始化
			$('.header .menuback').click(function(){
				if($('.menu').hasClass('pull')){
					$('.header').addClass('push');
					$('.content').addClass('push');	
					$('.menu').removeClass('pull');
					$('.mask').addClass('push');
					iphonescroll(false);					
				}else{
					$('.header').removeClass('push');
					$('.content').removeClass('push');	
					$('.menu').addClass('pull');
					$('.mask').removeClass('push');
					iphonescroll(true);
				};
			});
			$('.mask').click(function(){
				$('.header .menuback').click();	
			});
			menuslide(menuid);
		};
		$('.menu .one0').click(function(){
			menuslide(0);
		});
		$('.menu .one1').click(function(){
			menuslide(1);
		});
	};
	function menuslide(id){	
		$('.menu .one'+(id==0?1:0)).removeClass('ones');
		$('.menu .one2').removeClass('ones');
		$('.menu .two').addClass('scale');
		$('.menu .one').removeClass('oney0');
		$('.menu .one').removeClass('oney1');
		if(!$('.menu .one'+id).hasClass('ones')){
			$('.menu .one'+id).addClass('ones');
			$('.menu .two'+id).removeClass('scale');
			$('.menu .one').addClass('oney'+id);
		}else{
			$('.menu .one'+id).removeClass('ones');
			$('.menu .two'+id).addClass('scale');				
		};
		if(id == 0&&$('.menu .one'+id).hasClass('ones'))
			$('.menu .two1').addClass('two1y');
		else
			$('.menu .two1').removeClass('two1y');	
		
	};
	function contactinit(){
		if($('.contact').length == 0)return false;
		var map = new BMap.Map("map");	
		map.addControl(new BMap.NavigationControl());
		var hzpt = new BMap.Point(120.265982,30.316414);
		var wzpt = new BMap.Point(120.754195,27.983142);
		var lspt = new BMap.Point(119.930447,28.4707);
		var markerhz = new BMap.Marker(hzpt);
		var markerwz = new BMap.Marker(wzpt);
		var markerls = new BMap.Marker(lspt);
		map.addOverlay(markerhz);
		map.addOverlay(markerwz);
		map.addOverlay(markerls);
		function mapptshow(id){
			$('.contact .btn a').removeClass('s');
			$('.contact .btn a').eq(id).addClass('s');
			$('.contact .text li').addClass('opa');
			$('.contact .text li').eq(id).removeClass('opa');
			if(id == 0)
				map.centerAndZoom(wzpt,18);
			else if(id == 1)
				map.centerAndZoom(hzpt,18);
			else if(id == 2)
				map.centerAndZoom(lspt,18);
		};
		$('.contact .btn a').click(function(){
			mapptshow($(this).index());	
		});
		mapptshow(0);
	};	
	$(window).bind('resize',function(){
		reset();
	});	
	$(window).bind("DOMContentLoaded",function(){	
		config.maxw = parseInt($('.container').css('max-width'));		
		reset();	
		setTimeout(function(){$('body').removeClass('loading');},400);
		setTimeout(show,600);
		init();			
	});
})(Zepto)
function listload(){
	pn = 1;
	if(document.getElementById('caselist')){		
		$(window).bind('load',function(){
			scLock = true;
			$('.content').bind('scroll',function(){
				var t = $('.content').scrollTop();
				if(t>=($('#caselist').height()-config.h)&&scLock==true){
					pn++;
					scLock = false;
					$.get('/api/case.php',{pn:pn,tid:tid},function(data){
						if(data==0||data=="")
							$('.list-load').html("<span>没有了!</span>");
						else{
							scLock = true;
							$('#caselist').append(data);
						}
					});
				}
			});
		});
	}	
}