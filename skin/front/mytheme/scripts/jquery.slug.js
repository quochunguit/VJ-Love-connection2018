(function($){
$.fn.slug = function(from,to)
{		
	var slugStr='';
	var vn= new Array("à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă",
		"ằ","ắ","ặ","ẳ","ẵ","è","é","ẹ","ẻ","ẽ","ê","ề"
		,"ế","ệ","ể","ễ",
		"ì","í","ị","ỉ","ĩ",
		"ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ"
		,"ờ","ớ","ợ","ở","ỡ",
		"ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
		"ỳ","ý","ỵ","ỷ","ỹ",
		"đ",
		"À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă"
		,"Ằ","Ắ","Ặ","Ẳ","Ẵ",
		"È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
		"Ì","Í","Ị","Ỉ","Ĩ",
		"Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ"
		,"Ờ","Ớ","Ợ","Ở","Ỡ",
		"Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
		"Ỳ","Ý","Ỵ","Ỷ","Ỹ",
		"Đ","Q"," ","--","---","----");
			
	var en=new Array("a","a","a","a","a","a","a","a","a","a","a","a",
		"a","a","a","a","a","e","e","e","e","e","e","e"
		,"e","e","e","e",
		"i","i","i","i","i",
		"o","o","o","o","o","o","o","o","o","o","o","o"
		,"o","o","o","o","o",
		"u","u","u","u","u","u","u","u","u","u","u",
		"y","y","y","y","y",
		"d",
		"A","A","A","A","A","A","A","A","A","A","A","A"
		,"A","A","A","A","A",
		"E","E","E","E","E","E","E","E","E","E","E",
		"I","I","I","I","I",
		"O","O","O","O","O","O","O","O","O","O","O","O"
		,"O","O","O","O","O",
		"U","U","U","U","U","U","U","U","U","U","U",
		"Y","Y","Y","Y","Y",
		"D","q","-","-","-","-");
	
	$('#'+from).keyup(function() {
		slugStr = trim($(this).val());
		slugStr = str_replace(vn, en, slugStr);
		slugStr = slugStr.replace(/[^a-zA-Z0-9\-_]/g,'');
		slugStr = str_replace("--", "-", slugStr);
		slugStr = str_replace("---", "-", slugStr);
		$('#'+to).val(slugStr);			
	});	
	
	trim = function (str) {
		var whitespace, l = 0, i = 0;
		str += '';
		whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
		l = str.length;
		for (i = 0; i < l; i++) {if (whitespace.indexOf(str.charAt(i)) === -1) {str = str.substring(i);break;}}
		l = str.length;
		for (i = l - 1; i >= 0; i--) {if (whitespace.indexOf(str.charAt(i)) === -1) {str = str.substring(0, i + 1);break;}}
		return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
	}
	str_replace = function(f, r, s){
		var ra = r instanceof Array, sa = s instanceof Array, l = (f = [].concat(f)).length, r = [].concat(r), i = (s = [].concat(s)).length;
		while(j = 0, i--)
			while(s[i] = s[i].split(f[j]).join(ra ? r[j] || "" : r[0]), ++j < l);
		return sa ? s : s[0];
	}
}
})(jQuery);