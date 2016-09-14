<?php

/*
To reload the current page with a form button, use the following:
<form>
<INPUT TYPE="button" VALUE="Refresh" onclick='location.reload()'>
</form>

To reload a page from a link, use this:

<a href="javascript:location.reload()">
Click to Refresh This Page
</a>

*/

   //--------------------------------------------------------------------------------
   function CreateDivShowHideScript () // $imgplus = 'img/plus.png',  $imgminus= 'img/minus.png' ) 
   {
     $str    = array();
     $str [] ="<script> ";
     $str [] ="function _shdiv_toggle( divid, imgplus, imgminus ) {";
     $str [] ="  var elimg = document.getElementById( '_shimg_' + divid);";
     $str [] ="  var eldiv = document.getElementById( '_shdiv_' + divid);";
     $str [] ="  var imgsrc = elimg.src;";
     $str [] ="  if (eldiv.style.display=='block') {";
     $str [] ="    elimg.src = imgplus;";
     $str [] ="    eldiv.style.display='none';";
     $str [] ="  }";
     $str [] ="  else {";
     $str [] ="    elimg.src = imgminus;";
     $str [] ="    eldiv.style.display='block';";
     $str [] ="    eldiv.style.visibility = 'visible';";
     $str [] ="  }";     
     $str [] ="}";
     $str [] ="</script> ";
     
     $line = '';
     for($i=0;$i<count($str);$i++) $line .= $str[$i] . "\r\n";
     
     echo "\r\n" . $line;
   }

   //--------------------------------------------------------------------------------
   function CreateDivShowHideDiv_Start ( $divid, $title, $display = 'none', $imgplus = 'img/plus.png',  $imgminus= 'img/minus.png', $valign = 'baseline' ) 
   {
     // display could be 'block' if needs to be initially shown
     // use 'none' to init in hidden state
     $img = ($display == 'none') ? $imgplus : $imgminus;
     
     $str    = array();
     $str [] = "<div class=h4 id=_shimgdiv_$divid><img id=_shimg_$divid src='$img' style='vertical-align: $valign' onclick='_shdiv_toggle(\"$divid\", \"$imgplus\", \"$imgminus\");'>&nbsp;$title"; // <hr>
     $str [] = "<div id=_shdiv_$divid style='display:$display'>";
     
     $line = '';
     for($i=0;$i<count($str);$i++) $line .= $str[$i] . "\r\n";
     
     echo "\r\n" . $line;
   }     

   //--------------------------------------------------------------------------------
   function CreateDivShowHideDiv_End ( $breakbefore = true, $breakafter = true ) 
   {
     $str    = array();
     if ($breakbefore) $str [] = "<br>";
     $str [] = "</div>"; // <hr>
     $str [] = "</div>";
     if ($breakafter) $str [] = "<br>";
     
     $line = '';
     for($i=0;$i<count($str);$i++) $line .= $str[$i] . "\r\n";
     
     echo "\r\n" . $line;
   }     

   //==================================================================================================
   // Javascript snippets
   //==================================================================================================
  
   /*  SAMPLE use :
   
      _print ("<LINK REL='stylesheet' HREF='common_stylesheet.css'>");   
        
      $popupnote = 'popupnote';
      CreatePopupDiv ( $popupnote,  'iplan_wz_125_fcst_adjustment_notes.php', 500, 400, 'Close Note' );  
        
      $popupchart = 'popupchart';
      CreatePopupDiv ( $popupchart,  'iplan_wz_125_fcst_adjustment_chart.php', 700, 450, 'Close Chart' );  
      
      echo 'This is a link to test popup div.<br>On mouse over a popup will appear.<br>';
      for($i=8900;$i<8912;$i++){
        echo 'Link ' . CreatePopupLink ( $popupnote, $i, $i, "Notes for $i" ) . 
             CreatePopupLink ( $popupchart, 'Chart_' . $i, '  View Chart', "Chart for $i" ) . 
             '.<br>';
      }     
   */

   //--------------------------------------------------------------------------------
   function CreatePopupLink ( $popupid, $id, $linkcaption = null, $title=null, $class='warning', $underline=false ) 
   {
     $class       = !is_null($class) ? " class=$class" : '';
     $linkcaption = is_null($linkcaption) ? $id : $linkcaption;
     $linkcaption = $underline ? '<u>' . $linkcaption . '</u>' : $linkcaption;
	 
	 $id = urlencode ($id);
     return "<a $class href='#' onclick='showPopup_$popupid(\"$id\", \"$title\");' >$linkcaption</a>";
   }
  
   //--------------------------------------------------------------------------------
   function CreatePopupDiv ( $popupid, $phpfile, $popupwidth = 500, $popupheight = 400, 
                             $closecaption = 'Close', $classbar='h3', $classcon='h4' )  
   {   
     $str = "
              <style>
                  #$popupid{
                  position: absolute;
                  visibility: hidden;
                  overflow: none;
                  border:1px solid #CCC;
                  background-color:#F9F9F9;
                  border:1px solid #333;
                  padding:5px;
                  filter:alpha(opacity=95);  
                  -moz-opacity: 0.95;   
                  opacity: 0.95;
                  z-index: 999; 
                  }
              </style>
              <div id='$popupid' onMousedown='initializedrag_$popupid(event)' onMouseup='stopdrag_$popupid()' onSelectStart='return false'>This is a popup div!</div>

              <script>
                  var dragapproved_$popupid=false;
                  var ie5=document.all&&document.getElementById;
                  var ns6=document.getElementById&&!document.all;       
                 
                  function initializedrag_$popupid(e){
                    if (ie5) e = event;
                    offsetx = e.clientX;
                    offsety = e.clientY;
                    
                    //document.getElementById('content_$popupid').style.display='none'; //extra
                    tempx=parseInt(document.getElementById('$popupid').style.left);
                    tempy=parseInt(document.getElementById('$popupid').style.top);

                    if (offsety - tempy > 40) return;  
                    // only allow drag on the bar section which is assumed to be n the top 40px

                    dragapproved_$popupid=true;
                    document.getElementById('$popupid').onmousemove=drag_drop_$popupid;
                  }
                   
                  function drag_drop_$popupid(e){
                    if (ie5&&dragapproved_$popupid&&event.button==1){
                      document.getElementById('$popupid').style.left=tempx+event.clientX-offsetx+'px';
                      document.getElementById('$popupid').style.top=tempy+event.clientY-offsety+'px';
                      }
                    else if (ns6&&dragapproved_$popupid){
                      document.getElementById('$popupid').style.left=tempx+e.clientX-offsetx+'px';
                      document.getElementById('$popupid').style.top=tempy+e.clientY-offsety+'px';
                    }
                  }
                  
                  function stopdrag_$popupid(){
                    dragapproved=false;
                    document.getElementById('$popupid').onmousemove=null;
                    document.getElementById('content_$popupid').style.display='' //extra
                  }
                  
                  function showPopup_$popupid( key, title ){
                    var baseText = null;
                    var w = document.body.clientWidth;
                    var h = document.body.clientHeight;
                    var pw = $popupwidth;
                    var ph = $popupheight;

                    var popUp = document.getElementById('$popupid');
                    popUp.style.top = (parseInt((h - ph) / 2)) +  'px';
                    popUp.style.left = (parseInt((w - pw) / 2)) + 'px';
                    popUp.style.width = pw + 'px';
                    popUp.style.height = ph + 'px';
                    
                    var url = '$phpfile?id=' + key;
                    var req = new XMLHttpRequest();
                    req.open('GET', url, false);
                    req.send(null);
                    var baseText = req.responseText;
                    
                    popUp.innerHTML = 
                    '<div class=$classbar id=\'statusbar_$popupid\'> </div>' +
                    '<div class=$classcon id=\'content_$popupid\'> </div>';
                    
                    var sbar = document.getElementById('statusbar_$popupid');
                    var scon = document.getElementById('content_$popupid');
                    var sbarText;
                    
                    sbarText = '<table class=$classbar width=100% border=0 cellspacing=0 cellpadding=4><tr bgcolor=silver><td>&nbsp;' + title + '&nbsp;</td><td align=right><button class=\'$classcon genericButton\' onclick=\'hidePopup_$popupid();\'>$closecaption</button>&nbsp;</td></tr></table>';
                    
                    //sbar.style.marginTop = '5px';
                    sbar.innerHTML = sbarText; 
                    
                    scon.style.marginTop = '5px';  
                    scon.style.overflow = 'auto';
                    scon.style.height = (parseInt($popupheight - 5 - sbar.offsetHeight)) + 'px';
                    scon.style.width  = '100%'; //(parseInt($popupwidth - 12)) + 'px';
                    scon.innerHTML = baseText;
                    
                    popUp.style.visibility = 'visible';
                  }
                  
                  function hidePopup_$popupid(){
                    var popUp = document.getElementById('$popupid');
                    popUp.innerHTML = ' ';
                    popUp.style.visibility = 'hidden';
                  }
              </script>
            ";
      return $str;      
   }   
   
  //------------------------------------------------------------------------------------------
   function CreateSaveAsPopupDiv ( $popupid, $phpfile, $popupwidth = 500, $popupheight = 400, $position_left = 100, $position_top = 100, $closecaption = 'Close', $classbar='h3', $classcon='h4' )  
   {   
     $str = "
              <style>
                  #$popupid{
                  position: absolute;
                  visibility: hidden;
                  overflow: none;
                  border:1px solid #CCC;
                  background-color:#F9F9F9;
                  border:1px solid #333;
                  padding:5px;
                  filter:alpha(opacity=95);  
                  -moz-opacity: 0.95;   
                  opacity: 0.95;
                  z-index: 999; 
                  }
              </style>
              <div id='$popupid' onMousedown='initializedrag_$popupid(event)' onMouseup='stopdrag_$popupid()' onSelectStart='return false'>This is a popup div!</div>

              <script>
                  var dragapproved_$popupid=false;
                  var ie5=document.all&&document.getElementById;
                  var ns6=document.getElementById&&!document.all;       
                 
                  function initializedrag_$popupid(e){
                    if (ie5) e = event;
                    offsetx = e.clientX;
                    offsety = e.clientY;
                    
                    //document.getElementById('content_$popupid').style.display='none'; //extra
                    tempx=parseInt(document.getElementById('$popupid').style.left);
                    tempy=parseInt(document.getElementById('$popupid').style.top);

                    if (offsety - tempy > 40) return;  
                    // only allow drag on the bar section which is assumed to be n the top 40px

                    dragapproved_$popupid=true;
                    document.getElementById('$popupid').onmousemove=drag_drop_$popupid;
                  }
                   
                  function drag_drop_$popupid(e){
                    if (ie5&&dragapproved_$popupid&&event.button==1){
                      document.getElementById('$popupid').style.left=tempx+event.clientX-offsetx+'px';
                      document.getElementById('$popupid').style.top=tempy+event.clientY-offsety+'px';
                      }
                    else if (ns6&&dragapproved_$popupid){
                      document.getElementById('$popupid').style.left=tempx+e.clientX-offsetx+'px';
                      document.getElementById('$popupid').style.top=tempy+e.clientY-offsety+'px';
                    }
                  }
                  
                  function stopdrag_$popupid(){
                    dragapproved=false;
                    document.getElementById('$popupid').onmousemove=null;
                    document.getElementById('content_$popupid').style.display='' //extra
                  }
                  
                  function showPopup_$popupid( key, title ){
                    var baseText = null;
                    var w = document.body.clientWidth;
                    var h = document.body.clientHeight;
                    var pw = $popupwidth;
                    var ph = $popupheight;
					var position_left = $position_left;
					var position_top = $position_top;
                   
                    
					var popUp = document.getElementById('$popupid');
                    // popUp.style.top = (parseInt((h - ph) / 2)) +  'px';
                    // popUp.style.left = (parseInt((w - pw) / 2)) + 'px';
					
					popUp.style.left = position_left + 'px'; 
					popUp.style.top  = position_top + 'px';
					
                    popUp.style.width = pw + 'px';
                    popUp.style.height = ph + 'px';
                    
                    var url = '$phpfile?id=' + key;
                    var req = new XMLHttpRequest();
                    req.open('GET', url, false);
                    req.send(null);
                    var baseText = req.responseText;
                    
                    popUp.innerHTML = 
                    '<div class=$classbar id=\'statusbar_$popupid\'> </div>' +
                    '<div class=$classcon id=\'content_$popupid\'> </div>';
                    
                    var sbar = document.getElementById('statusbar_$popupid');
                    var scon = document.getElementById('content_$popupid');
                    var sbarText;
                    
                    sbarText = '<table class=$classbar width=100% border=0 cellspacing=0 cellpadding=4><tr bgcolor=silver><td>&nbsp;' + title + '&nbsp;</td><td align=right><button class=\'$classcon genericButton\' onclick=\'hidePopup_$popupid();\'>$closecaption</button>&nbsp;</td></tr></table>';
                    
                    //sbar.style.marginTop = '5px';
                    sbar.innerHTML = sbarText; 
                    
                    scon.style.marginTop = '5px';  
                    scon.style.overflow = 'auto';
                    scon.style.height = (parseInt($popupheight - 5 - sbar.offsetHeight)) + 'px';
                    scon.style.width  = '100%'; //(parseInt($popupwidth - 12)) + 'px';
                    scon.innerHTML = baseText;
                    
                    popUp.style.visibility = 'visible';
                  }
                  
                  function hidePopup_$popupid(){
                    var popUp = document.getElementById('$popupid');
                    popUp.innerHTML = ' ';
                    popUp.style.visibility = 'hidden';
                  }
              </script>
            ";
      return $str;      
   }   
   
   
// ------------------
      function CreateShowDetailsPopupDiv ( $object_num, $popupid, $phpfile, $popupwidth = 500, $popupheight = 400, 
                             $closecaption = 'Close', $classbar='h3', $classcon='h4' )  
   {   
     $str = "
              <style>
                  #$popupid{
                  position: absolute;
                  visibility: hidden;
                  overflow: none;
                  border:1px solid #CCC;
                  background-color:#F9F9F9;
                  border:1px solid #333;
                  padding:5px;
                  filter:alpha(opacity=95);  
                  -moz-opacity: 0.95;   
                  opacity: 0.95;
                  z-index: 999; 
                  }
              </style>
              <div id='$popupid' onMousedown='initializedrag_$popupid(event)' onMouseup='stopdrag_$popupid()' onSelectStart='return false'>This is a popup div!</div>

              <script>
                  var dragapproved_$popupid=false;
                  var ie5=document.all&&document.getElementById;
                  var ns6=document.getElementById&&!document.all;       
                 
                  function initializedrag_$popupid(e){
                    if (ie5) e = event;
                    offsetx = e.clientX;
                    offsety = e.clientY;
                    
                    //document.getElementById('content_$popupid').style.display='none'; //extra
                    tempx=parseInt(document.getElementById('$popupid').style.left);
                    tempy=parseInt(document.getElementById('$popupid').style.top);

                    if (offsety - tempy > 40) return;  
                    // only allow drag on the bar section which is assumed to be n the top 40px

                    dragapproved_$popupid=true;
                    document.getElementById('$popupid').onmousemove=drag_drop_$popupid;
                  }
                   
                  function drag_drop_$popupid(e){
                    if (ie5&&dragapproved_$popupid&&event.button==1){
                      document.getElementById('$popupid').style.left=tempx+event.clientX-offsetx+'px';
                      document.getElementById('$popupid').style.top=tempy+event.clientY-offsety+'px';
                      }
                    else if (ns6&&dragapproved_$popupid){
                      document.getElementById('$popupid').style.left=tempx+e.clientX-offsetx+'px';
                      document.getElementById('$popupid').style.top=tempy+e.clientY-offsety+'px';
                    }
                  }
                  
                  function stopdrag_$popupid(){
                    dragapproved=false;
                    document.getElementById('$popupid').onmousemove=null;
                    document.getElementById('content_$popupid').style.display='' //extra
                  }
                  
                  function showPopup_$popupid( key, title ){
                    var baseText = null;
                    var w = document.body.clientWidth;
                    var h = document.body.clientHeight;
                    var pw = $popupwidth;
                    var ph = $popupheight;
                   
                    
                    var popUp = document.getElementById('$popupid');
                    popUp.style.top = (parseInt((h - ph) / 2)) +  'px';
                    popUp.style.left = (parseInt((w - pw) / 2)) + 'px';
                    
                    //popUp.style.left = '370px'; 
                    //popUp.style.top  = '850px';
                    
                    popUp.style.width = pw + 'px';
                    popUp.style.height = ph + 'px';
                    
                    var selectedvalue = document.IRIMFORM.input$object_num.value;
                    var url = '$phpfile?id=' + selectedvalue;
                    
                    
                    
                    var req = new XMLHttpRequest();
                    req.open('GET', url, false);
                    req.send(null);
                    var baseText = req.responseText;

                    //alert (baseText);
                    
                    popUp.innerHTML = 
                    '<div class=$classbar id=\'statusbar_$popupid\'> </div>' +
                    '<div class=$classcon id=\'content_$popupid\'> </div>';
                    
                    var sbar = document.getElementById('statusbar_$popupid');
                    var scon = document.getElementById('content_$popupid');
                    var sbarText;
                    
                    sbarText = '<table class=$classbar width=100% border=0 cellspacing=0 cellpadding=4><tr bgcolor=silver><td>&nbsp;' + title + '&nbsp;</td><td align=right><button class=\'$classcon genericButton\' onclick=\'hidePopup_$popupid();\'>$closecaption</button>&nbsp;</td></tr></table>';
                    
                    //sbar.style.marginTop = '5px';
                    sbar.innerHTML = sbarText; 
                    
                    scon.style.marginTop = '5px';  
                    scon.style.overflow = 'auto';
                    scon.style.height = (parseInt($popupheight - 5 - sbar.offsetHeight)) + 'px';
                    scon.style.width  = '100%'; //(parseInt($popupwidth - 12)) + 'px';
                    scon.innerHTML = baseText;
                    
                    popUp.style.visibility = 'visible';
                  }
                  
                  function hidePopup_$popupid(){
                    var popUp = document.getElementById('$popupid');
                    popUp.innerHTML = ' ';
                    popUp.style.visibility = 'hidden';
                  }
                  
              </script>
            ";
      return $str;      
   }      
 
   
   //--------------------------------------------------------------------------------
   function HeadScript_WindowMaximize ()
   {
     // http://www.websitemasterminds.com/page/page/905823.htm
     // Window Maximiser : Place this script in the head of any document that you want to be maximised.
     _print ("<script language=\"Javascript\">");
     _print ("<!--");
     _print ("window.moveTo(0,0)");
     _print ("window.resizeTo(screen.width,screen.height)");
     _print ("//-->");
     _print ("</script> ");
   }

   //--------------------------------------------------------------------------------
   function HeadScript_NoRightClick ()
   {
     // http://www.websitemasterminds.com/page/page/905823.htm
     // No Right Click : Place this script between the head tags.
     // Just be aware that these "no right click" type scripts only deter the inexperienced user.
     // There are many ways around them
     _print ("<script LANGUAGE=\"JavaScript\">");
     _print ("<!--");
     _print ("function click() {");
     _print ("if (event.button==2) {");
     _print ("alert('Right Click Option Not Available!');");
     _print ("}");
     _print ("}");
     _print ("document.onmousedown=click");
     _print ("// -->");
     _print ("</script> ");
   }
   
   //--------------------------------------------------------------------------------
   function HeadScript_AutoRefresh ( $seconds = 60, $url = null )
   {
     if (is_null ($url)) $url = $_SERVER['PHP_SELF'];
     _print ( "<META HTTP-EQUIV='Refresh' CONTENT='$seconds; URL=$url'>" );
   }

   //--------------------------------------------------------------------------------
   function Link_PrintPage ( $caption = 'Print This Page')
   {
     _print ("<a href='javascript:;' onClick='window.print();return false'>$caption</a> ");
   }

   //--------------------------------------------------------------------------------
   function Link_BookmarkPage ( $caption = 'Bookmark This Page', $pagetitle = 'My Favorite Page', $pageurl = '' )
   {
     if ($pageurl=='') { $pageurl = $_SERVER['PHP_SELF']; }

     _print ("<a href=\"javascript:window.external.AddFavorite('$pageurl', '$caption');\">");
     _print ("$caption!</a> ");
   }

   //--------------------------------------------------------------------------------
   function Link_BackToTop ( $caption = 'Back to Top of Page')
   {
     _print ("<a href=\"javascript:window.scrollTo(0,0);\">$caption</a> ");
   }

   //--------------------------------------------------------------------------------
   // popup window
   //--------------------------------------------------------------------------------
   function HeadScript_PopupWindow ()
   {
      _print ("<script language=\"javascript\" type=\"text/javascript\">");
      _print ("function wopen( url, name, w, h)");
      _print ("{");
      _print (" w += 32;");
      _print (" h += 96;");
      _print (" var win = window.open(url,");
      _print ("  name,");
      _print ("  'width=' + w + ', height=' + h + ', ' +");
      _print ("  'location=no, directories=no, menubar=no, ' +");
      _print ("  'status=no, toolbar=no, scrollbars=yes, copyhistory=no, resizable=yes');");
      _print (" win.resizeTo(w, h);");
      _print (" win.focus();");
      _print ("}");
      _print ("</script>");
   }

   //--------------------------------------------------------------------------------
   // use '_blank' as $winname if you want to open a new window everytime
   function Link_PopupWindow ( $url, $caption, $width=400, $height=200, $winname='popup' )
   {
     // to add an image, just add it in the caption, eg: <IMG src='img/x.gif'>
     _print ("<a href='$url' target='$winname' onClick=\"wopen('$url', '$winname', $width, $height); return false;\">$caption</a>");
   }
 
   //--------------------------------------------------------------------------------
   // reload another frame
   //--------------------------------------------------------------------------------
   function HeadScript_ReloadFrame ( $framename, $url='' )
   {
     // for multiple frames, separate using; even in the url list
     _print ("<script LANGUAGE=\"JavaScript\">");
     _print ("<!--");
     _print ("function ReloadFrame() {");

     $frames = explode (';', $framename);
     $urls   = explode (';', $url);

     for ($i=0;$i<count($frames);$i++)
     {
       $framename = $frames[$i];
       $url       = $urls[$i];
       if ($url=='')  _print ("  parent.$framename.location.reload();");
       else           _print ("  parent.$framename.location.href='$url';");
     }

     _print ("}");
     _print ("// -->");
     _print ("</script> ");
   }

   //--------------------------------------------------------------------------------
   function MakeOnload_ReloadFrame ()
   {
     return " onload='ReloadFrame()'";
   }

   //--------------------------------------------------------------------------------
   // delete confirmation
   //--------------------------------------------------------------------------------
   function HeadScript_DeleteConfirm ()
   {
     _print ("<script language='JavaScript'>");
     _print ("function DeleteConfirm()
              {
                if (confirm('Are you sure you want to delete this record ?'))
                { return true; }
                else
                { return false;}
              } ");
     _print ("</script>");
   }

	//--------------------------------------------------------------------------------
   function HeadScript_CustomerDeleteConfirm ()
   {
     _print ("<script language='JavaScript'>");
     _print ("function DeleteConfirm()
              {
                if (confirm('Are you sure you want to delete this customer ? The Customer directory will be removed as well.'))
                { return true; }
                else
                { return false;}
              } ");
     _print ("</script>");
   }

	//--------------------------------------------------------------------------------
   function HeadScript_CustomerDeleteConfirmyn ()
   {
     _print ("<script language='JavaScript'>");
     _print ("function DeleteConfirmyn()
              {
                if (confirm('Apply to All Locations is checked, this actions will be performed on all locations for the selected item.  Click OK to confirm.'))
                { return true; }
                else
                { return false;}
              } ");
     _print ("</script>");
   }
   
   //--------------------------------------------------------------------------------
   function MakeOnClick_DeleteConfirm ()
   {
     return "OnClick='return DeleteConfirm();'";
   }

   //--------------------------------------------------------------------------------
   function MakeOnClick_DeleteConfirmyn ()
   {
     return "OnClick='return DeleteConfirmyn();'";
   }

   //--------------------------------------------------------------------------------
   // show alert
   //--------------------------------------------------------------------------------
   function ShowJSAlert ( $alertmsg )
   {
     _print ("<script language='JavaScript'>");
     _print ("alert ('$alertmsg');");
     _print ("</script>");
   }

   //--------------------------------------------------------------------------------
   function HeadScript_CustomConfirm ( $id, $msg ) // not necessarily in <HEAD>
   {
     _print ("<script language='JavaScript'>");
     _print ("function Confirm_$id ()
              {
                if (confirm('$msg'))
                { return true; }
                else
                { return false;}
              } ");
     _print ("</script>");
   }

   //--------------------------------------------------------------------------------
   function MakeOnClick_CustomConfirm ( $id ) // related to the above function
   {
     return "OnClick='return Confirm_$id();'";
   }

   //--------------------------------------------------------------------------------
   function MakeCheckCBGroupScript ( $varname )
   {
     $js = "<SCRIPT LANGUAGE='JavaScript'>
           <!-- Begin
           var checkflag = 'false';
           function checkcbgroup_$varname() 
           {
             if (checkflag == 'false') 
             {
               frm = document.forms[0];
               for(i=0;i< frm.length;i++)							 
               {																
                 e=frm.elements[i];								 
                 if(e.type=='checkbox' && e.name.indexOf('$varname') != -1)
		              e.checked= true ;					 
		           }
               checkflag = 'true';
               return 'img/cb_unchecked.gif'; 
             }
             else 
             {
               frm = document.forms[0];
               for(i=0;i< frm.length;i++)							 
               {																
                 e=frm.elements[i];								 
                 if(e.type=='checkbox' && e.name.indexOf('$varname') != -1)
		              e.checked= false ;					 
		           }
               checkflag = 'false';
               return 'img/cb_checked.gif'; 
             }
           }
           //  End -->
           </script>";
           
      print $js."\n";     
    }
           
   //==================================================================================================
   // Download File
   // it will not work if run locally
   //==================================================================================================
   function RenderSaveAsScript( $sourcefile, $destfile ) // should include path
   {
     // example
     //  $sourcefile = "http://192.168.0.102/prima/ftpfiles/".basename($_SESSION['rptfile']); // this is the file in remote server
     //  $destfile   = 'c:\\\\temp\\\\PrimaTest_'.basename($sourcefile);   // double backward slash

     $screenx = 10000; // was 10000
     $width   = 100;

     _print ("<script>");
     _print (" function FileSaveAs(){");
     _print ("    var win = window.open('$sourcefile','','left=$screenx,screenX=$screenx');");

     //_print ("    var screenX = Math.floor((screen.availWidth-$width)/2);");
     //_print ("    var screenY = Math.floor((screen.availHeight-$width)/2);");
     _print ("    var screenX = -300;");
     _print ("    var screenY = -300;");
     _print ("    win.offScreenBuffering = true;");
     _print ("    win.resizeTo($width,$width);");
     _print ("    win.moveTo(screenX,screenY);");

     if ($destfile != '')
     {
       _print ("    win.document.execCommand('SaveAs',false,'$destfile');");
       _print ("    win.close();");
     }

     _print ("}");
     _print ("</script>");

     // also needs to add BODY onload
     // <BODY onload="javascript:FileSaveAs()">
   }

   //==================================================================================================
   // Java Scripts
   //==================================================================================================

   //==================================================================================================
   function AddJSFile( $jsfile ) // should include path
   {
     _print ("<script language='JavaScript' src='$jsfile'></script>\n");
   }

   //==================================================================================================
   // TigraTable and ScrollableTable Scripts
   //==================================================================================================
   // usage
   //  $s = new TigraScrollableTableScript ("js\\");
   //  // inside <HEAD>
   //  $s->AddHeaderScripts();
   //  //before body closes </BODY>
   //  $s->AddFooterScripts ($tableid);
   //==================================================================================================
   class TigraScrollableTableScript
   {
     var $path = "js\\";
     var $scripts = array();
     var $usetigra = true;
     // if true, slower, if false, please provide alternating table row color yourself
     // in wizard class, go to the scrolltablepanel set altrowcolor to true

     //................................................................................................
     function TigraScrollableTableScript ( $path = "js\\" )
     {
       $this->path = $path;
       if ($this->usetigra == true) { $this->scripts[] = $path."tigra_tables.js"; }
       $this->scripts[] = $path."ScrollableTable.js";
     }

     //................................................................................................
     function AddHeaderScripts ()
     {
       for ($i=0;$i<count($this->scripts);$i++)
       {
         AddJSFile( $this->scripts[$i] );
       }
     }

     //................................................................................................
     function AddFooterScripts ( $tableid, $scrollheight = 180 )
     {
         _print ("\n<script language='JavaScript'>\n");
         _print ("<!-- \n");
       if ($this->usetigra == true) {  $this->TigraTableScript ( $tableid ); }
         $this->ScrollableTableScript ( $tableid, $scrollheight );
         _print ("\n// -->\n</script>\n");
     }

     //................................................................................................
     function TigraTableScript ( $tableid,
                                 $num_header_offset = 1,
                                 $num_footer_offset = 0,
                                 $str_odd_color = '#ffffff',
                                 $str_even_color = '#F2F2F2',
                                 $str_mover_color = '#DBEAF5',
                                 $str_onclick_color = '#cccccc'
                               )
     {
       //$num_header_offset - how many rows to skip before applying effects at the begining (opt.)
       //$num_footer_offset - how many rows to skip at the bottom of the table (opt.)
       //$str_odd_color - background color for odd rows (opt.)
       //$str_even_color - background color for even rows (opt.)
       //$str_mover_color - background color for rows with mouse over (opt.)
       //$str_onclick_color - background color for marked rows (opt.)
       _print ("tigra_tables('$tableid', $num_header_offset, $num_footer_offset, '$str_odd_color', '$str_even_color', '$str_mover_color', '$str_onclick_color');\n");
     }

     //................................................................................................
     function ScrollableTableScript ($tableid, $height = 180)
     {
       // requires tableid
       _print ("makeScrollableTable('$tableid', true, '$height');\n");
     }
   }

   //==================================================================================================
   // popoup date
   //==================================================================================================
   // usage :
   /*
      case 'D' : // date
      $input  = "<INPUT $style TYPE='TEXT' NAME='$name' SIZE=13 VALUE='$result' ";
      //$input .= "$onKeyPress ";
      $input .= "onFocus=\"javascript:vDateType='1'\" ";
      $input .= "onKeyUp=\"DateFormat(this,this.value,event,false,'1')\" ";
      $input .= "onBlur=\"DateFormat(this,this.value,event,true,'1')\">";

      _print ($input);
      _print ("<INPUT $style TYPE='button' value='...' onClick='getCalendarFor(document.forms[0].$name)'>");
      break;
   */
   //==================================================================================================
   function IncludePopUpJS ()   // MUST be located in HEADER
   {
     $path = "js\\";
     AddJSFile ( $path."PupDate.js" );
   }

   //==================================================================================================
   function PopUpDateScript ()   // MUST be located before the closing </BODY>
   {
     _print ("");
     _print ("<script language='JavaScript'>");
     _print ('if (document.all) {');
     _print (' document.writeln("<div id=\"PopUpCalendar\" style=\"position:absolute; left:0px; top:0px; z-index:7; width:200px; height:77px; overflow: visible; visibility: hidden; background-color: #FFFFFF; border: 1px none #000000\" onMouseOver=\"if(ppcTI){clearTimeout(ppcTI);ppcTI=false;}\" onMouseOut=\"ppcTI=setTimeout(\'hideCalendar()\',500)\">");');
     _print (' document.writeln("<div id=\"monthSelector\" style=\"position:absolute; left:0px; top:0px; z-index:9; width:181px; height:27px; overflow: visible; visibility:inherit\">");}');
     _print ('else if (document.layers) {');
     _print (' document.writeln("<layer id=\"PopUpCalendar\" pagex=\"0\" pagey=\"0\" width=\"200\" height=\"200\" z-index=\"100\" visibility=\"hide\" bgcolor=\"#FFFFFF\" onMouseOver=\"if(ppcTI){clearTimeout(ppcTI);ppcTI=false;}\" onMouseOut=\"ppcTI=setTimeout(\'hideCalendar()\',500)\">");');
     _print (' document.writeln("<layer id=\"monthSelector\" left=\"0\" top=\"0\" width=\"181\" height=\"27\" z-index=\"9\" visibility=\"inherit\">");}');
     _print ('else {');
     //_print (' document.writeln("<p><font color=\"#FF0000\"><b>Error ! The current browser is either too old or too modern (usind DOM document structure).</b></font></p>");}');
     _print (' document.writeln("<div id=\"PopUpCalendar\" style=\"position:absolute; left:0px; top:0px; z-index:7; width:200px; height:77px; overflow: visible; visibility: hidden; background-color: #FFFFFF; border: 1px none #000000\" onMouseOver=\"if(ppcTI){clearTimeout(ppcTI);ppcTI=false;}\" onMouseOut=\"ppcTI=setTimeout(\'hideCalendar()\',500)\">");');
     _print (' document.writeln("<div id=\"monthSelector\" style=\"position:absolute; left:0px; top:0px; z-index:9; width:181px; height:27px; overflow: visible; visibility:inherit\">");}');
     _print ("</script>");
     _print ("");
     _print ("<noscript><p><font color='#FF0000'><b>JavaScript is not activated !</b></font></p></noscript>");
     _print ("<table border='0' cellspacing='1' cellpadding='2' width='200' bordercolorlight='#000000' bordercolordark='#000000' vspace='0' hspace='0'>");
     _print ("<form name='ppcMonthList'><tr><td align='center' bgcolor='#37A7EF'>");
     _print ("<a href='javascript:moveMonth(\"Back\")' onMouseOver='window.status=\" \";return true;'><font face='Verdana' size='1px' color='#000000'><b><</b></font></a>&nbsp;");
     _print ("<font face='Verdana, MS Sans Serif, sans-serif' size='1px'>");
     _print ("<select name='sItem' onMouseOut='if(ppcIE){window.event.cancelBubble = true;}' onChange='switchMonth(this.options[this.selectedIndex].value)' style='font-family: \"Verdana\", sans-serif; font-size:10px'>");
     _print ("<option value='-10'>2000 ? January</option>");
     _print ("<option value='-9'>2000 ? January</option>");
     _print ("<option value='-8'>2000 ? January</option>");
     _print ("<option value='-7'>2000 ? January</option>");
     _print ("<option value='-6'>2000 ? January</option>");
     _print ("<option value='-5'>2000 ? January</option>");
     _print ("<option value='-4'>2000 ? January</option>");
     _print ("<option value='-3'>2000 ? January</option>");
     _print ("<option value='-2'>2000 ? January</option>");
     _print ("<option value='-1'>2000 ? January</option>");
     _print ("<option value='0' selected>2000 ? January</option>");
     _print ("<option value='1'>2000 ? January</option>");
     _print ("<option value='2'>2000 ? January</option>");
     _print ("<option value='3'>2000 ? January</option>");
     _print ("<option value='4'>2000 ? January</option>");
     _print ("<option value='5'>2000 ? January</option>");
     _print ("<option value='6'>2000 ? January</option>");
     _print ("<option value='7'>2000 ? January</option>");
     _print ("<option value='8'>2000 ? January</option>");
     _print ("<option value='9'>2000 ? January</option>");
     _print ("<option value='10'>2000 ? January</option>");
     _print ("<option value='11'>2000 ? January</option>");
     _print ("<option value='+1'>2000 ? January</option>");
     _print ("<option value='+2'>2000 ? January</option>");
     _print ("<option value='+3'>2000 ? January</option>");
     _print ("<option value='+4'>2000 ? January</option>");
     _print ("<option value='+5'>2000 ? January</option>");
     _print ("<option value='+6'>2000 ? January</option>");
     _print ("<option value='+7'>2000 ? January</option>");
     _print ("<option value='+8'>2000 ? January</option>");
     _print ("<option value='+9'>2000 ? January</option>");
     _print ("<option value='+10'>2000 ? January</option>");
     _print ("</select></font>");
     _print ("&nbsp;<a href='javascript:moveMonth(\"Forward\")' onMouseOver='window.status=\" \";return true;'>");
     _print ("<font face='Verdana' size='1px' color='#000000'><b>></b></font></a></td></tr></form></table>");
     _print ("<table border='0' cellspacing='1' cellpadding='2' bordercolorlight='#000000' bordercolordark='#000000' width='200' vspace='0' hspace='0'>");
     _print ("<tr align='center' bgcolor='#37A7EF'>");
     _print ("<td width='20' bgcolor='#FFDF4F'><b><font face='Verdana' size='1'>Su</font></b></td>");
     _print ("<td width='20'><b><font face='Verdana' size='1'>Mo</font></b></td>");
     _print ("<td width='20'><b><font face='Verdana' size='1'>Tu</font></b></td>");
     _print ("<td width='20'><b><font face='Verdana' size='1'>We</font></b></td>");
     _print ("<td width='20'><b><font face='Verdana' size='1'>Th</font></b></td>");
     _print ("<td width='20'><b><font face='Verdana' size='1'>Fr</font></b></td>");
     _print ("<td width='20' bgcolor='#FFDF4F'><b><font face='Verdana' size='1'>Sa</font></b></td></tr></table>");
     _print ("");
     _print ("<script language='JavaScript'>");
     _print ('if (document.all) {');
     _print (' document.writeln("</div>");');
     _print (' document.writeln("<div id=\"monthDays\" style=\"position:absolute; left:0px; top:43px; z-index:8; width:200px; height:21px; overflow: visible; visibility:inherit; background-color: #FFFFFF; border: 1px none #000000\">?</div></div>");}');
     _print ('else if (document.layers) {');
     _print (' document.writeln("</layer>");');
     _print (' document.writeln("<layer id=\"monthDays\" left=\"0\" top=\"43\" width=\"200\" height=\"21\" z-index=\"8\" bgcolor=\"#FFFFFF\" visibility=\"inherit\">?</layer></layer>");}');
     _print ('else {/*NOP*/}');
     _print ("</script>");
   }
   //==================================================================================================
   function FormSaveAsValidationScripts ()
   {
    //Validation For Save As Popup
	echo "<Script>
		  <!--
			function saveascheckform() {
			    if ( document.saveas.saveasname.value == '' ) {
					alert('Please enter a value for the Name field.');
					return false;
				}
			    if ( document.saveas.saveasdesc.value == '' ) {
					alert('Please enter a value for the Description field.');
					return false;
				}
			}
		  //-->
		  </Script>";
   }   
   //==================================================================================================
   function IncludejQueryLibary()
   {
     //echo "<script type='text/JavaScript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>";
	 echo "<script type='text/JavaScript' src=' http://code.jquery.com/jquery-latest.min.js'></script>";
   };   
?>