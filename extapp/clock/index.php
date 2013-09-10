<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>无标题文档</title>
<style type="text/css">
body{margin:0;padding:0}
#clock-box{width:130px;height:130px;background:url(trad.png) no-repeat;position:relative}
#clock-box div{width:13px;height:129px;position:absolute;top:0px;left:58px}
#clock-box .dot{background:url(trad_dot.png) no-repeat}
#clock-box .h{background:url(trad_h.png) no-repeat}
#clock-box .m{background:url(trad_m.png) no-repeat}
#clock-box .s{background:url(trad_s.png) no-repeat}
#clock-box .animate{-webkit-transition:-webkit-transform 1s ease;-moz-transition:-moz-transform 1s ease;-o-transition:-o-transform 1s ease;transition:transform 1s ease}
</style>
</head>

<body>
<div id="clock-box">
	<div class="dot"></div>
	<div class="h animate"></div>
	<div class="m animate"></div>
	<div class="s animate"></div>
</div>
<script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
$(function(){
	var clock = $('#clock-box');
	var dom_h = clock.children('.h'), dom_m = clock.children('.m'), dom_s = clock.children('.s');
	var clockmove = function(){
		var time = new Date(), h = time.getHours(), m = time.getMinutes(), s = time.getSeconds();
		h = h > 12 ? h - 12 : h;
		h = h * 360 / 12 + parseInt(m / 12) * 6;
		m = m * 360 / 60;
		s = s * 360 / 60;
		dom_h.css('transform', 'rotate(' + (h + 360) + 'deg)');
		dom_m.css('transform', 'rotate(' + (m + 360) + 'deg)');
		dom_s.css('transform', 'rotate(' + (s + 360) + 'deg)');
	}
	clockmove();
	setTimeout(function(){
		dom_h.removeClass('animate');
		dom_m.removeClass('animate');
		dom_s.removeClass('animate');
	}, 1000);
	setInterval(clockmove, 1000);
});
</script>
</body>
</html>