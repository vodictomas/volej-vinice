// ===================================================================
// Author: Matt Kruse <matt@mattkruse.com>
// WWW: http://www.mattkruse.com/
//
// NOTICE: You may use this code for any purpose, commercial or
// private, without any further permission from the author. You may
// remove this notice from your final code if you wish, however it is
// appreciated by the author if at least my web site address is kept.
//
// You may *NOT* re-distribute this code in any way except through its
// use. That means, you can include it in your product, or your web
// site, or any other form where the code is actually being used. You
// may not put the plain javascript up on your site for download or
// include it in your javascript libraries for download. 
// If you wish to share this code with others, please just point them
// to the URL instead.
// Please DO NOT link directly to my .js files from your site. Copy
// the files to your server and use them there. Thank you.
// ===================================================================

/* SOURCE FILE: AnchorPosition.js */
function getAnchorPosition(anchorname){var useWindow=false;var coordinates=new Object();var x=0,y=0;var use_gebi=false, use_css=false, use_layers=false;if(document.getElementById){use_gebi=true;}else if(document.all){use_css=true;}else if(document.layers){use_layers=true;}if(use_gebi && document.all){x=AnchorPosition_getPageOffsetLeft(document.all[anchorname]);y=AnchorPosition_getPageOffsetTop(document.all[anchorname]);}else if(use_gebi){var o=document.getElementById(anchorname);x=AnchorPosition_getPageOffsetLeft(o);y=AnchorPosition_getPageOffsetTop(o);}else if(use_css){x=AnchorPosition_getPageOffsetLeft(document.all[anchorname]);y=AnchorPosition_getPageOffsetTop(document.all[anchorname]);}else if(use_layers){var found=0;for(var i=0;i<document.anchors.length;i++){if(document.anchors[i].name==anchorname){found=1;break;}}if(found==0){coordinates.x=0;coordinates.y=0;return coordinates;}x=document.anchors[i].x;y=document.anchors[i].y;}else{coordinates.x=0;coordinates.y=0;return coordinates;}coordinates.x=x;coordinates.y=y;return coordinates;}
function getAnchorWindowPosition(anchorname){var coordinates=getAnchorPosition(anchorname);var x=0;var y=0;if(document.getElementById){if(isNaN(window.screenX)){x=coordinates.x-document.body.scrollLeft+window.screenLeft;y=coordinates.y-document.body.scrollTop+window.screenTop;}else{x=coordinates.x+window.screenX+(window.outerWidth-window.innerWidth)-window.pageXOffset;y=coordinates.y+window.screenY+(window.outerHeight-24-window.innerHeight)-window.pageYOffset;}}else if(document.all){x=coordinates.x-document.body.scrollLeft+window.screenLeft;y=coordinates.y-document.body.scrollTop+window.screenTop;}else if(document.layers){x=coordinates.x+window.screenX+(window.outerWidth-window.innerWidth)-window.pageXOffset;y=coordinates.y+window.screenY+(window.outerHeight-24-window.innerHeight)-window.pageYOffset;}coordinates.x=x;coordinates.y=y;return coordinates;}
function AnchorPosition_getPageOffsetLeft(el){var ol=el.offsetLeft;while((el=el.offsetParent) != null){ol += el.offsetLeft;}return ol;}
function AnchorPosition_getWindowOffsetLeft(el){return AnchorPosition_getPageOffsetLeft(el)-document.body.scrollLeft;}
function AnchorPosition_getPageOffsetTop(el){var ot=el.offsetTop;while((el=el.offsetParent) != null){ot += el.offsetTop;}return ot;}
function AnchorPosition_getWindowOffsetTop(el){return AnchorPosition_getPageOffsetTop(el)-document.body.scrollTop;}

/* SOURCE FILE: PopupWindow.js */
function PopupWindow_getXYPosition(anchorname){var coordinates;if(this.type == "WINDOW"){coordinates = getAnchorWindowPosition(anchorname);}else{coordinates = getAnchorPosition(anchorname);}this.x = coordinates.x;this.y = coordinates.y;}
function PopupWindow_setSize(width,height){this.width = width;this.height = height;}
function PopupWindow_populate(contents){this.contents = contents;this.populated = false;}
function PopupWindow_setUrl(url){this.url = url;}
function PopupWindow_setWindowProperties(props){this.windowProperties = props;}
function PopupWindow_refresh(){if(this.divName != null){if(this.use_gebi){document.getElementById(this.divName).innerHTML = this.contents;}else if(this.use_css){document.all[this.divName].innerHTML = this.contents;}else if(this.use_layers){var d = document.layers[this.divName];d.document.open();d.document.writeln(this.contents);d.document.close();}}else{if(this.popupWindow != null && !this.popupWindow.closed){if(this.url!=""){this.popupWindow.location.href=this.url;}else{this.popupWindow.document.open();this.popupWindow.document.writeln(this.contents);this.popupWindow.document.close();}this.popupWindow.focus();}}}
function PopupWindow_showPopup(anchorname)
{
	this.getXYPosition(anchorname);
	console.log(this.x);
	console.log(this.offsetX);
	
	this.x += this.offsetX;
	this.y += this.offsetY;
	
	
	
	if(!this.populated &&(this.contents != ""))
	{
		this.populated = true;
		this.refresh();
	}
	
	console.log(this.divName);
	
	if(this.divName != null)
	{
		console.log(this.use_gebi);
		if(this.use_gebi)
		{
			document.getElementById(this.divName).style.left = this.x + "px";
			document.getElementById(this.divName).style.top = this.y;
			document.getElementById(this.divName).style.visibility = "visible";
		}
		else if(this.use_css)
		{
			document.all[this.divName].style.left = this.x;
			document.all[this.divName].style.top = this.y;
			document.all[this.divName].style.visibility = "visible";
		}
		else if(this.use_layers)
		{
			document.layers[this.divName].left = this.x;
			document.layers[this.divName].top = this.y;
			document.layers[this.divName].visibility = "visible";
		}
	}
	else
	{
		if(this.popupWindow == null || this.popupWindow.closed)
		{
			if(this.x<0)
			{
				this.x=0;
			}
			
			if(this.y<0)
			{
				this.y=0;
			}
			
			if(screen && screen.availHeight)
			{
				if((this.y + this.height) > screen.availHeight)
				{
					this.y = screen.availHeight - this.height;
				}
			}
			
			if(screen && screen.availWidth)
			{
				if((this.x + this.width) > screen.availWidth)
				{
					this.x = screen.availWidth - this.width;
				}
			}
			
			var avoidAboutBlank = window.opera ||( document.layers && !navigator.mimeTypes['*']) || navigator.vendor == 'KDE' ||( document.childNodes && !document.all && !navigator.taintEnabled);
			this.popupWindow = window.open(avoidAboutBlank?"":"about:blank","window_"+anchorname,this.windowProperties+",width="+this.width+",height="+this.height+",screenX="+this.x+",left="+this.x+",screenY="+this.y+",top="+this.y+"");
		}
		
		this.refresh();
	}
}
function PopupWindow_hidePopup(){if(this.divName != null){if(this.use_gebi){document.getElementById(this.divName).style.visibility = "hidden";}else if(this.use_css){document.all[this.divName].style.visibility = "hidden";}else if(this.use_layers){document.layers[this.divName].visibility = "hidden";}}else{if(this.popupWindow && !this.popupWindow.closed){this.popupWindow.close();this.popupWindow = null;}}}

function PopupWindow_isClicked(e)
{
	if(this.divName != null)
	{
		if(this.use_layers)
		{
			var clickX = e.pageX;
			var clickY = e.pageY;
			var t = document.layers[this.divName];
			
			if((clickX > t.left) &&(clickX < t.left+t.clip.width) &&(clickY > t.top) &&(clickY < t.top+t.clip.height))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else if(document.all)
		{
			var t = window.event.srcElement;
			while(t.parentElement != null)
			{
				if(t.id==this.divName)
				{
					return true;
				}
				
				t = t.parentElement;
			}
			
			return false;
		}
		else if(this.use_gebi && e)
		{
			var t = e.srcElement;
			
			while(t.parentNode != null)
			{
				if(t.id==this.divName)
				{
					return true;
				}
				
				t = t.parentNode;
			}
			
			return false;
		}
		
		return false;
	}
	
	return false;
}

function PopupWindow_hideIfNotClicked(e){if(this.autoHideEnabled && !this.isClicked(e)){this.hidePopup();}}
function PopupWindow_autoHide(){this.autoHideEnabled = true;}
function PopupWindow_hidePopupWindows(e){for(var i=0;i<popupWindowObjects.length;i++){if(popupWindowObjects[i] != null){var p = popupWindowObjects[i];p.hideIfNotClicked(e);}}}
function PopupWindow_attachListener(){if(document.layers){document.captureEvents(Event.MOUSEUP);}window.popupWindowOldEventListener = document.onmouseup;if(window.popupWindowOldEventListener != null){document.onmouseup = new Function("window.popupWindowOldEventListener();PopupWindow_hidePopupWindows();");}else{document.onmouseup = PopupWindow_hidePopupWindows;}}
function PopupWindow(){if(!window.popupWindowIndex){window.popupWindowIndex = 0;}if(!window.popupWindowObjects){window.popupWindowObjects = new Array();}if(!window.listenerAttached){window.listenerAttached = true;PopupWindow_attachListener();}this.index = popupWindowIndex++;popupWindowObjects[this.index] = this;this.divName = null;this.popupWindow = null;this.width=0;this.height=0;this.populated = false;this.visible = false;this.autoHideEnabled = false;this.contents = "";this.url="";this.windowProperties="toolbar=no,location=no,status=no,menubar=no,scrollbars=auto,resizable,alwaysRaised,dependent,titlebar=no";if(arguments.length>0){this.type="DIV";this.divName = arguments[0];}else{this.type="WINDOW";}this.use_gebi = false;this.use_css = false;this.use_layers = false;if(document.getElementById){this.use_gebi = true;}else if(document.all){this.use_css = true;}else if(document.layers){this.use_layers = true;}else{this.type = "WINDOW";}this.offsetX = 0;this.offsetY = 0;this.getXYPosition = PopupWindow_getXYPosition;this.populate = PopupWindow_populate;this.setUrl = PopupWindow_setUrl;this.setWindowProperties = PopupWindow_setWindowProperties;this.refresh = PopupWindow_refresh;this.showPopup = PopupWindow_showPopup;this.hidePopup = PopupWindow_hidePopup;this.setSize = PopupWindow_setSize;this.isClicked = PopupWindow_isClicked;this.autoHide = PopupWindow_autoHide;this.hideIfNotClicked = PopupWindow_hideIfNotClicked;}


/* SOURCE FILE: ColorPicker2.js */

ColorPicker_targetInput = null;
var ColorPicker_targetInput2 = null;
function ColorPicker_writeDiv(){document.writeln("<DIV ID=\"colorPickerDiv\" STYLE=\"position:absolute;visibility:hidden;\"> </DIV>");}
function ColorPicker_show(anchorname){this.showPopup(anchorname);}
function ColorPicker_pickColor(color,obj){obj.hidePopup();pickColor(color);}
function pickColor(color){
  if(ColorPicker_targetInput==null){
    alert("Target Input is null, which means you either didn't use the 'select' function or you have no defined your own 'pickColor' function to handle the picked color!");
    return;
  }
  if(ColorPicker_targetInput.type=="text" || ColorPicker_targetInput.type=="hidden" || ColorPicker_targetInput.type=="textarea")
    ColorPicker_targetInput.value = color;
  
    ColorPicker_targetInput2.style.background = color;
    
    var datum= new Date();
    datum.setTime(datum.getTime() + 1000 * 60 * 60 * 24 * 30);
    
    setCookie('volejbal_chat_barva',color,datum,'/');
}
function ColorPicker_select(inputobj,inputobj2,linkname){
  if(inputobj.type!="text" && inputobj.type!="hidden" && inputobj.type!="textarea" && inputobj2.type!="div"){
    alert("colorpicker.select: Input object passed is not a valid form input object");
    window.ColorPicker_targetInput=null;return;
  }
  window.ColorPicker_targetInput = inputobj;
  ColorPicker_targetInput2 = inputobj2;
  this.show(linkname);
  }
function ColorPicker_highlightColor(c){var thedoc =(arguments.length>1)?arguments[1]:window.document;var d = thedoc.getElementById("colorPickerSelectedColor");d.style.backgroundColor = c;d = thedoc.getElementById("colorPickerSelectedColorValue");d.innerHTML = c;}
function ColorPicker(){var windowMode = false;if(arguments.length==0){var divname = "colorPickerDiv";}else if(arguments[0] == "window"){var divname = '';windowMode = true;}else{var divname = arguments[0];}if(divname != ""){var cp = new PopupWindow(divname);}else{var cp = new PopupWindow();cp.setSize(225,250);}cp.currentValue = "#FFFFFF";cp.writeDiv = ColorPicker_writeDiv;cp.highlightColor = ColorPicker_highlightColor;cp.show = ColorPicker_show;cp.select = ColorPicker_select;var colors = new Array("#000000","#000033","#000066","#000099","#0000CC","#0000FF","#330000","#330033","#330066","#330099","#3300CC",
"#3300FF","#660000","#660033","#660066","#660099","#6600CC","#6600FF","#990000","#990033","#990066","#990099",
"#9900CC","#9900FF","#CC0000","#CC0033","#CC0066","#CC0099","#CC00CC","#CC00FF","#FF0000","#FF0033","#FF0066",
"#FF0099","#FF00CC","#FF00FF","#003300","#003333","#003366","#003399","#0033CC","#0033FF","#333300","#333333",
"#333366","#333399","#3333CC","#3333FF","#663300","#663333","#663366","#663399","#6633CC","#6633FF","#993300",
"#993333","#993366","#993399","#9933CC","#9933FF","#CC3300","#CC3333","#CC3366","#CC3399","#CC33CC","#CC33FF",
"#FF3300","#FF3333","#FF3366","#FF3399","#FF33CC","#FF33FF","#006600","#006633","#006666","#006699","#0066CC",
"#0066FF","#336600","#336633","#336666","#336699","#3366CC","#3366FF","#666600","#666633","#666666","#666699",
"#6666CC","#6666FF","#996600","#996633","#996666","#996699","#9966CC","#9966FF","#CC6600","#CC6633","#CC6666",
"#CC6699","#CC66CC","#CC66FF","#FF6600","#FF6633","#FF6666","#FF6699","#FF66CC","#FF66FF","#009900","#009933",
"#009966","#009999","#0099CC","#0099FF","#339900","#339933","#339966","#339999","#3399CC","#3399FF","#669900",
"#669933","#669966","#669999","#6699CC","#6699FF","#999900","#999933","#999966","#999999","#9999CC","#9999FF",
"#CC9900","#CC9933","#CC9966","#CC9999","#CC99CC","#CC99FF","#FF9900","#FF9933","#FF9966","#FF9999","#FF99CC",
"#FF99FF","#00CC00","#00CC33","#00CC66","#00CC99","#00CCCC","#00CCFF","#33CC00","#33CC33","#33CC66","#33CC99",
"#33CCCC","#33CCFF","#66CC00","#66CC33","#66CC66","#66CC99","#66CCCC","#66CCFF","#99CC00","#99CC33","#99CC66",
"#99CC99","#99CCCC","#99CCFF","#CCCC00","#CCCC33","#CCCC66","#CCCC99","#CCCCCC","#CCCCFF","#FFCC00","#FFCC33",
"#FFCC66","#FFCC99","#FFCCCC","#FFCCFF","#00FF00","#00FF33","#00FF66","#00FF99","#00FFCC","#00FFFF","#33FF00",
"#33FF33","#33FF66","#33FF99","#33FFCC","#33FFFF","#66FF00","#66FF33","#66FF66","#66FF99","#66FFCC","#66FFFF",
"#99FF00","#99FF33","#99FF66","#99FF99","#99FFCC","#99FFFF","#CCFF00","#CCFF33","#CCFF66","#CCFF99","#CCFFCC",
"#CCFFFF","#FFFF00","#FFFF33","#FFFF66","#FFFF99","#FFFFCC","#FFFFFF");var total = colors.length;var width = 18;var cp_contents = "";var windowRef =(windowMode)?"window.opener.":"";if(windowMode){cp_contents += "<HTML><HEAD><TITLE>Select Color</TITLE></HEAD>";cp_contents += "<BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0><CENTER>";}cp_contents += "<TABLE BGCOLOR='#ffffff' BORDER=1 CELLSPACING=1 CELLPADDING=0>";var use_highlight =(document.getElementById || document.all)?true:false;for(var i=0;i<total;i++){if((i % width) == 0){cp_contents += "<TR>";}if(use_highlight){var mo = 'onMouseOver="'+windowRef+'ColorPicker_highlightColor(\''+colors[i]+'\',window.document)"';}else{mo = "";}cp_contents += '<TD BGCOLOR="'+colors[i]+'"><FONT SIZE="-3"><A HREF="#" onClick="'+windowRef+'ColorPicker_pickColor(\''+colors[i]+'\','+windowRef+'window.popupWindowObjects['+cp.index+']);return false;" '+mo+' STYLE="text-decoration:none;">&nbsp;&nbsp;&nbsp;</A></FONT></TD>';if( ((i+1)>=total) ||(((i+1) % width) == 0)){cp_contents += "</TR>";}}if(document.getElementById){var width1 = Math.floor(width/2);var width2 = width = width1;cp_contents += "<TR><TD COLSPAN='"+width1+"' BGCOLOR='#ffffff' ID='colorPickerSelectedColor'>&nbsp;</TD><TD COLSPAN='"+width2+"' ALIGN='CENTER' ID='colorPickerSelectedColorValue'>#FFFFFF</TD></TR>";}cp_contents += "</TABLE>";if(windowMode){cp_contents += "</CENTER></BODY></HTML>";}cp.populate(cp_contents+"\n");cp.offsetY = 25;cp.autoHide();return cp;}
 

/* SOURCE FILE: PicturePicker2.js */

var PicturePicker_date = null;
var PicturePicker_person = null;
function PicturePicker_writeDiv(){document.writeln("<DIV ID=\"picturePickerDiv\" STYLE=\"position:absolute;visibility:hidden;\"> </DIV>");}
function PicturePicker_show(anchorname){this.showPopup(anchorname);}
function PicturePicker_pickPicture(color,obj){obj.hidePopup();pickPicture(color);}
function pickPicture(img_number){
  
  var ahref = document.getElementById('href_img_' + img_number);
  
  ahref.href = "index.php?datum="+window.PicturePicker_date+"&jmeno="+window.PicturePicker_person+"&set_pivo="+img_number;
}
function PicturePicker_select(date,person,linkname){
 
  window.PicturePicker_date = date;
  window.PicturePicker_person = person;
  this.show2(linkname);
  }
function PicturePicker_highlightColor(c){var thedoc =(arguments.length>1)?arguments[1]:window.document;var d = thedoc.getElementById("picturePickerSelectedPictureNote");d.innerHTML = c;}
function PicturePicker(){
var windowMode = false;
if(arguments.length==0){var divname = "picturePickerDiv";}
else if(arguments[0] == "window"){var divname = '';windowMode = true;}
      else{var divname = arguments[0];}
if(divname != ""){var cp = new PopupWindow(divname);}
else{var cp = new PopupWindow();cp.setSize(225,250);}
cp.currentValue = "#FFFFFF";
cp.writeDiv = PicturePicker_writeDiv;
cp.highlightColor = PicturePicker_highlightColor;cp.show2 = PicturePicker_show;
cp.select = PicturePicker_select;
var width = 18;
var cp_contents = "";
var windowRef =(windowMode)?"window.opener.":"";
if(windowMode){cp_contents += "<HTML><HEAD><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"><TITLE>Select Color</TITLE></HEAD>";
cp_contents += "<BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0><CENTER>";}
cp_contents += "<style type='text/css'>td.click:hover  {border: 1px solid blue;} td.click a img {border: none;}</style>";
cp_contents += "<TABLE BGCOLOR='#ffffff' BORDER=1 CELLSPACING=0 CELLPADDING=0 onMouseOut='"+windowRef+"PicturePicker_highlightColor(\"&nbsp;\",window.document)'>";
var use_highlight =(document.getElementById || document.all)?true:false;

var obj_pom = windowRef+"window.popupWindowObjects['"+cp.index+"']";

cp_contents += "<tr><td>Hospa</td><td class='click'><a href='javascript:void(0);' id='href_img_1' onClick= \""+windowRef+"PicturePicker_pickPicture('1',"+obj_pom+")\"><img src='../../images/pivo3b.jpg' alt='Jo na jedno klidně zajdu.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Jo na jedno klidně zajdu.\",window.document)'/></a>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_9' onClick=\""+windowRef+"PicturePicker_pickPicture('9',"+obj_pom+")\"><img src='../../images/vino.png' alt='Du na víno.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Du na víno.\",window.document)'/></a>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_2' onClick=\""+windowRef+"PicturePicker_pickPicture('2',"+obj_pom+")\"><img src='../../images/kofola3r.jpg' alt='Du jen na kofolu.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Du jen na kofolu.\",window.document)'/></a>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_6' onClick=\""+windowRef+"PicturePicker_pickPicture('6',"+obj_pom+")\"><img src='../../images/moch2.jpg' alt='Jdu na \"houbu\".' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Já jdu houbařit (; .\",window.document)'/></a>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_3' onClick=\""+windowRef+"PicturePicker_pickPicture('3',"+obj_pom+")\"><img src='../../images/hranolky4.jpg' alt='A já si dám hranolky, dvojitý' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"A já si dám hranolky,dvojitý.\",window.document)'/></a>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_8' onClick=\""+windowRef+"PicturePicker_pickPicture('8',"+obj_pom+")\"><img src='../../images/invalidaSPivem3.jpg' alt='Sem zraněnej, ale na pivo mohu.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Sem zraněnej, ale na pivo mohu.\",window.document)'/></a></td>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_4' onClick=\""+windowRef+"PicturePicker_pickPicture('4',"+obj_pom+")\"><img src='../../images/dortik.jpg' alt='Slavíííím' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Slavíííím\",window.document)'/></a></td>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_5' onClick=\""+windowRef+"PicturePicker_pickPicture('5',"+obj_pom+")\"><img src='../../images/nonstop.jpg' alt='Pojďte do nonstopu! &copy; Bělka' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Pojďte do nonstopu! &copy; Bělka\",window.document)'/></a>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_7' onClick=\""+windowRef+"PicturePicker_pickPicture('7',"+obj_pom+")\"><img src='../../images/mochnon.gif' alt='Na houbu,pak do nonstopu.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Na houbu,pak do nonstopu.\",window.document)'/></a>";
cp_contents += "</td></tr>";
cp_contents += "<tr><td>Výmluva</td>";
cp_contents += "<td class='click'><a href='javascript:void(0);' id='href_img_0' onClick=\""+windowRef+"PicturePicker_pickPicture('0',"+obj_pom+")\"><img src='../../images/postel2.jpg' alt='Musim jít brzo spát.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Musim jít brzo spát.\",window.document)'/></a>";
cp_contents += "</td><td class='click'><a href='javascript:void(0);' id='href_img_-2' onClick=\""+windowRef+"PicturePicker_pickPicture('-2',"+obj_pom+")\"><img src='../../images/kniha3.jpg' alt='Musim se učit.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Musim se učit.\",window.document)'/></a></td>";
cp_contents += "<td class='click'><a href='javascript:void(0);' id='href_img_-3' onClick=\""+windowRef+"PicturePicker_pickPicture('-3',"+obj_pom+")\"><img src='../../images/ski2.jpg' alt='Sem na horách.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Sem na horách.\",window.document)'/></a></td>";
cp_contents += "<td class='click'><a href='javascript:void(0);' id='href_img_-4' onClick=\""+windowRef+"PicturePicker_pickPicture('-4',"+obj_pom+")\"><img src='../../images/nemoc.jpg' alt='Sem nachcípanej, nebudu to dráždit.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Sem nachcípanej.\",window.document)'/></a></td>";
cp_contents += "<td class='click'><a href='javascript:void(0);' id='href_img_-6' onClick=\""+windowRef+"PicturePicker_pickPicture('-6',"+obj_pom+")\"><img src='../../images/invalida3.png' alt='Sem zraněnej.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Sem zraněnej.\",window.document)'/></a></td>";
cp_contents += "<td class='click'><a href='javascript:void(0);' id='href_img_-7' onClick=\""+windowRef+"PicturePicker_pickPicture('-7',"+obj_pom+")\"><img src='../../images/kocarek2.jpg' alt='Mateřske povinnosti.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Mateřske povinnosti.\",window.document)'/></a></td>";
cp_contents += "<td class='click'><a href='javascript:void(0);' id='href_img_-8' onClick=\""+windowRef+"PicturePicker_pickPicture('-8',"+obj_pom+")\"><img src='../../images/prace3.png' alt='Musím být v práci.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Musím být v práci.\",window.document)'/></a></td>";
cp_contents += "<td>&nbsp;</td><td>&nbsp;</td></tr>";
cp_contents += "<tr><td>Uvidim</td><td class='click'><a href='javascript:void(0);' id='href_img_-1' onClick=\""+windowRef+"PicturePicker_pickPicture('-1',"+obj_pom+")\"><img src='../../images/question.jpg' alt='Uvidim až na místě.' onMouseOver='"+windowRef+"PicturePicker_highlightColor(\"Uvidim až na místě.\",window.document)'/></a></td>";
cp_contents += "<td colspan='8'>&nbsp;</td></tr>";

  if(document.getElementById){var width1 = Math.floor(width/2);
  var width2 = width = width1;
  cp_contents += "<TR><TD style='font-size: 80%;' align='center' COLSPAN='10' BGCOLOR='#ffffff' id='picturePickerSelectedPictureNote'>&nbsp;</TD></TR>";}
  cp_contents += "</TABLE>";if(windowMode){cp_contents += "</CENTER></BODY></HTML>";}cp.populate(cp_contents+"\n");
  cp.offsetY = 25;
  cp.autoHide();
  return cp;
}

/* SOURCE FILE: PicturePicker2.js */

var PicturePicker2_date = null;
var PicturePicker2_person = null;
function PicturePicker2_writeDiv(){document.writeln("<DIV ID=\"picturePickerDiv2\" STYLE=\"position:absolute;visibility:hidden;\"> </DIV>");}
function PicturePicker2_show(anchorname){this.showPopup(anchorname);}
function PicturePicker2_pickPicture(color,obj){obj.hidePopup();pickPicture2(color);}
function pickPicture2(img_number){
  
  var ahref = document.getElementById('href_img2_' + img_number);
  
  ahref.href = "index.php?datum="+window.PicturePicker2_date+"&jmeno="+window.PicturePicker2_person+"&set="+img_number;
}
function PicturePicker2_select(date,person,linkname){
  window.PicturePicker2_date = date;
  window.PicturePicker2_person = person;
  this.show2(linkname);
  }
  
function PicturePicker2_highlightColor(c){var thedoc =(arguments.length>1)?arguments[1]:window.document;/*var d = thedoc.getElementById("picturePickerSelectedPictureNote2");d.innerHTML = c;*/}
function PicturePicker2(){
var windowMode = false;
if(arguments.length==0){var divname = "picturePickerDiv2";}
else if(arguments[0] == "window"){var divname = '';windowMode = true;}
      else{var divname = arguments[0];}
if(divname != ""){var cp = new PopupWindow(divname);}
else{var cp = new PopupWindow();cp.setSize(225,250);}
cp.currentValue = "#FFFFFF";
cp.writeDiv = PicturePicker2_writeDiv;
cp.highlightColor = PicturePicker2_highlightColor;cp.show2 = PicturePicker2_show;
cp.select = PicturePicker2_select;
var width = 18;
var cp_contents = "";
var windowRef =(windowMode)?"window.opener.":"";
if(windowMode){cp_contents += "<HTML><HEAD><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"><TITLE>Select Color</TITLE></HEAD>";
cp_contents += "<BODY MARGINWIDTH=0 MARGINHEIGHT=0 LEFTMARGIN=0 TOPMARGIN=0><CENTER>";}
cp_contents += "<style type='text/css'>td.click:hover  {border: 1px solid blue;} td.click a img {border: none;}</style>";
cp_contents += "<TABLE BGCOLOR='#ffffcc' BORDER=1 CELLSPACING=0 CELLPADDING=4 onMouseOut='"+windowRef+"PicturePicker2_highlightColor(\"&nbsp;\",window.document)'>";
var use_highlight =(document.getElementById || document.all)?true:false;


var obj_pom = windowRef+"window.popupWindowObjects['"+cp.index+"']";

cp_contents += "<tr><td class='click'><a href='javascript:void(0);' id='href_img2_1' onClick= \""+windowRef+"PicturePicker2_pickPicture('1',"+obj_pom+")\"><img src=\"../../images/check.jpg\"/></a></td><td>Přijdu</td>";
cp_contents += "<tr>";
cp_contents += "<td class='click'><a href='javascript:void(0);' id='href_img2_0' onClick=\""+windowRef+"PicturePicker2_pickPicture('0',"+obj_pom+")\"><img src=\"../../images/cross.gif\" /></a></td><td>Nemůžu</td>";
cp_contents += "<tr><td class='click'><a href='javascript:void(0);' id='href_img2_-1' onClick=\""+windowRef+"PicturePicker2_pickPicture('-1',"+obj_pom+")\"><img src=\"../../images/question.jpg\"/></a></td><td>Nevim</td></tr>";

  if(document.getElementById){var width1 = Math.floor(width/2);
  var width2 = width = width1;
  }
  cp_contents += "</TABLE>";if(windowMode){cp_contents += "</CENTER></BODY></HTML>";}cp.populate(cp_contents+"\n");
  cp.offsetY = 25;
  cp.autoHide();
  return cp;
}
