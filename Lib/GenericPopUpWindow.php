<?php
  session_start ();

  //---------------------------------------------------------------------------------------------------------------
  // for use in the actual new window popped up
  function GetPopupWindowObj ( $wname )
  { 
    return unserialize( $_SESSION ['wpopup_'.$wname] );
  }  
  
  //---------------------------------------------------------------------------------------------------------------
  // for use in the opener window
  class GenericPopUpWindow
  {
    var $jsver = 'javascript';
    var $wname, $wurl, $wwidth, $wheight, $worigurl; 
    var $wvarname, $wfuncopen, $wfuncreturn, $wfuncsetval;
    var $inputvar, $sess_name;
    var $popupform, $openerform;
    var $headscript;
    
    var $multiple = false; // multi result
    var $multidelim = ', ';
    
    var $returndesc = false;
    var $descvar;
    var $descdelim = ':';  // description delimiter
    
    //.............................................................................................................
    // functions below are used only in the OPENER window
    //.............................................................................................................
    function GenericPopUpWindow ( $wurl, $inputvar, $openerform='', $popupform='POPUP', $wwidth = 500, $wheight = 300, $onepopuponly = true )
    {                                    
      $wname = strtolower ( $popupform ); // window name
      
      $this->worigurl    = $wurl;      
      $wurl              = $wurl . ((strpos( $wurl, '?' ) === false) ? '?' : '&').'popupobj='.$wname;
      $this->wurl        = $wurl;

      $this->openerform  = $openerform;
      $this->popupform   = $popupform;
      $this->inputvar    = $inputvar;

      $this->wname       = $wname;
      $this->wwidth      = $wwidth;
      $this->wheight     = $wheight;

      $wvarname          = strtolower('wp_'.$wname);
      $wfuncopen         = $wvarname.'_open';
      $wfuncsetval       = $wvarname.'_setval';
      $wfuncreturn       = $wvarname.'_return';
      
      $this->wvarname    = $wvarname;
      $this->wfuncopen   = $wfuncopen;
      $this->wfuncsetval = $wfuncsetval;
      $this->wfuncreturn = $wfuncreturn;
      
      $this->sess_name   = 'wpopup_'.$wname;
      
      $jsver             = $this->jsver;

      // if onepopuponly = false, then each popupwindow will open in separate window independent of each other
      // if true, then the new popup window will override the currently unclosed popup window
      // default is true...
      if ( $onepopuponly ) $popupname = 'genericpopup'; // window name
      else                 $popupname = strtolower ( $popupform ); // window name
      
      // open script
      $this->headscript = 
        "<SCRIPT LANGUAGE=$jsver><!--
          var $wvarname;
          var $inputvar"."_retval;
        
          function $wfuncopen ()
          {
            $wvarname = window.open('$wurl','$popupname','resizable=yes,width=$wwidth,height=$wheight');
            $wvarname.location.href = '$wurl';
            if ($wvarname.opener == null) $wvarname.opener = self;
            $wvarname.focus();
          }  
        //--></SCRIPT>";

      //             window.close();
        
      $this->saveObject ();
    } 

    //.............................................................................................................
    function saveObject ()
    {                      
      $_SESSION [$this->sess_name] = serialize ($this);
    }  

    //.............................................................................................................
    // enable returning multiple result, such as from multi select list box
    function enableMultiResult ( $multidelim = ', ' )
    {                      
      $this->multiple   = true;              
      $this->multidelim = $multidelim;
      $this->saveObject ();
    }  

    //.............................................................................................................
    // enable returning description along with values, such as the caption in list boc
    function enableReturnDesc ( $descvar, $descdelim = ' : ' )
    {                      
      $this->returndesc = true;
      $this->descvar    = $descvar;
      $this->descdelim  = $descdelim;  // description delimiter
      $this->saveObject ();
    }  

    //.............................................................................................................
    function renderOpenScript()
    {
      echo ( $this->headscript . "\r\n" );
    }  

    //.............................................................................................................
    function makePopupInput ( $size = 30, $value = '', $style = 'class=h4' )
    {
      $varname = $this->inputvar;
      $onclick = $this->makeOnClickOpen ();
      $input   = "<input $style type='text' name='$varname' size='$size' value='$value' readonly>"; 
      $btn     = "<img src='img/popup.gif' border=0 $onclick>";
      return $input.' '.$btn;
    }  

    //.............................................................................................................
    function makePopupDesc ( $size = 30, $value = '', $style = 'class=h4' )
    {
      $varname = $this->descvar;
      $input   = "<input $style type='text' name='$varname' size='$size' value='$value' readonly>"; 
      return $input;
    }  

    //.............................................................................................................
    function makePopupDescTextArea ( $rows = 5, $cols=50, $value = '', $style = 'class=h4' )
    {
      $varname = $this->descvar;
      $input   = "<textarea $style name='$varname' rows=$rows cols=$cols>$value</textarea>"; 
      return $input;
    }  

    //.............................................................................................................
    function makeOnClickOpen ()
    {
      $func = $this->wfuncopen;
      return "onClick='$func()'";
    }  

    //.............................................................................................................
    // functions below are used only in the POPUP window
    //.............................................................................................................
    function renderReturnScript( $returnvar )
    {      
      $jsver       = $this->jsver;
      $wfuncreturn = $this->wfuncreturn;
      $inputvar    = $this->inputvar;
      $wfuncsetval = $this->wfuncsetval;
      $popupform   = $this->popupform;
      $openerform  = $this->openerform;
      
      $multiple    = $this->multiple;
      $multidelim  = $this->multidelim;
      
      $descvar     = $this->descvar;
      $descdelim   = $this->descdelim;
      $returndesc  = $this->returndesc;
      
      if (trim($popupform)  == '') $popupform  = 'forms[0]';
      if (trim($openerform) == '') $openerform = 'forms[0]';

      // multiple flag
      if ( !$multiple )
        $inputvarscript = "
              window.opener.document.$openerform.$inputvar.value = document.$popupform.$returnvar.value;";

      else // multiple       
         $inputvarscript = "
              var output = '';
              for (var i=0;i < document.$popupform.$returnvar.options.length;i++) {
                   if (document.$popupform.$returnvar.options[i].selected) {
                       if (output !== '') output += '$multidelim';                   
                       output += document.$popupform.$returnvar.options[i].value;
                   }
               }
              window.opener.document.$openerform.$inputvar.value = output;
              ";

      // returndesc flag
      $showdescscript = '';
      if ( $returndesc )
      {
        if ( !$multiple )
          $showdescscript = "
              var index = document.$popupform.$returnvar.selectedIndex;
              window.opener.document.$openerform.$descvar.value = document.$popupform.$returnvar.options[index].text;
              ";
        else // multiple       
          $showdescscript = "
              output = '';
              for (var i=0;i < document.$popupform.$returnvar.options.length;i++) {
                   if (document.$popupform.$returnvar.options[i].selected) {
                       if (output !== '') output += '$multidelim';                   
                       output += document.$popupform.$returnvar.options[i].text;
                   }
               }
              window.opener.document.$openerform.$descvar.value = output;
              ";
      }  
              
      // complete script        
      $this->returnscript =   
        "<SCRIPT LANGUAGE=$jsver><!--
          function $wfuncreturn".'_'.$returnvar." ()
          {
            $inputvarscript
            $showdescscript
          }            
        //--></SCRIPT>";

      echo ( $this->returnscript . "\r\n" );
    }  

    //.............................................................................................................
    function makeOnClickReturn ( $returnvar )
    {
      $func = $this->wfuncreturn.'_'.$returnvar;
      return "onClick='return $func ()'";
    }  

    //.............................................................................................................
    function makeOnChangeReturn ( $returnvar )
    {
      $inputvar = $this->inputvar ;
      $func = $this->wfuncreturn.'_'.$returnvar;
      return "onChange='return $func ()'";
    }  
  }  
?>