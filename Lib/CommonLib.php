<?php

   //==================================================================================================
   // echo print
   //==================================================================================================
   function _print ($ctext)
   {
      //print trim($ctext)."\n";  // for debugging
      print $ctext."\n";  // for debugging
      //print trim($ctext);
   }

   //---------------------------------------------------------------------------------------------------
   function _print_r ($any)
   {
      echo "<pre>";
      print_r($any);
      echo "</pre>";
   }

   //---------------------------------------------------------------------------------------------------
   function _print_log ( $any, $append=true )
   {
     _print_to_file ( null, $any, $append );
   }  

   //---------------------------------------------------------------------------------------------------
   function _reset_log ()
   {
     _print_to_file ( null, '', false );
   }  

   //---------------------------------------------------------------------------------------------------
   function _print_to_file ( $file, $any, $append=true )
   {
      $id = $_SESSION ['curr_user']['id']; // bad workaround
      if ( is_null($id)) $id = 'default';
       
      $path = getcwd() . "/logs/$id/";
      if (!is_dir($path)) mkdir ( $path, 0777, true );
      
      if (is_null($file))  $file = "__log_".session_id().".txt";
      $mode = ($append) ? "a" : "w";

      $f = fopen ( $path . basename($file), $mode);
      ob_start ();

      print_r($any);

      $buffer = trim(ob_get_contents());

      //if (strpos($buffer, chr(13)) === false)  $buffer = chr(13).$buffer;
      //$buffer = str_replace ( chr(10), chr(13), $buffer );

      ob_end_clean();
      fwrite ($f,  "\r\n".$buffer);
      fclose ($f);
      
      chmod ( $path . basename($file), 0777);
   }

   //==================================================================================================
   // string
   //==================================================================================================
   function TitleCase ( $string )
   {
     return preg_replace("/(^(\w*?)|(\w{4,}?))/e", "ucfirst('$1')", $string);
   }
   
   //--------------------------------------------------------------------------------------------------
   function IsAlphaNum ( $string )
   {
     return ($string == MakeAlphaNumOnly ($string)); 
   }  
   
   //--------------------------------------------------------------------------------------------------
   function MakeAlphaNumOnly ($string, $min='', $max='')
   {
     $string = preg_replace("/[^a-zA-Z0-9]/", "", $string);
     $len = strlen($string);
     if((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) return FALSE;
     return $string;
   }

   //==================================================================================================
   // client related
   //==================================================================================================
   function ClientIPAddress ()
   {
     return $_SERVER["REMOTE_ADDR"];
   }

   //--------------------------------------------------------------------------------------------------
   function ClientIsMSIE ()
   {
      $__msie = false;
      if (ereg( 'MSIE ([0-9].[0-9]{1,2})',$HTTP_USER_AGENT,$log_version) ) {  $__msie = true;  }
      return $__msie;
   }

   //--------------------------------------------------------------------------------------------------
   function RedirectToPage ( $page )
   {
     //session_write_close();  // insure that php finishes writing the session info before page redirect gets underway.
     header("Location: $page"); /* Redirect browser */
   }

   //==================================================================================================
   // sessions
   //==================================================================================================
   function GetSessionVar ( $sesvar )
   {
     return unserialize ($_SESSION [strtolower($sesvar)]);
   }

   //--------------------------------------------------------------------------------
   function SetSessionVar ( $sesvar, $val )
   {
     $_SESSION [strtolower($sesvar)] = serialize($val);
   }

   //--------------------------------------------------------------------------------
   function SessionExists ( $sesvar )
   {
     $sesvar = strtolower($sesvar);
     $val = $_SESSION [$sesvar];
     return (session_is_registered($sesvar) and (!is_null($val)));
   }

   //==================================================================================================
   // arrays
   //==================================================================================================
   function array_delete ( $arr, $index )
   {
     unset($arr[$index]);
     return $arr;
   }

   //--------------------------------------------------------------------------------
   function array_reindex ( $arr )
   {
     return array_values ($arr);
   }

   //--------------------------------------------------------------------------------
   function array_insert ( $arr, $pos, $val )
   {
      $val = array ($val);
      if ($pos == 0) // insert on top
      {
        $arr = array_merge ($val, $arr);
      }
      else if ($pos > (count($arr)-1))
      {
        $arr = array_merge ($arr, $val);
      }
      else
      {
        $left = array_slice ($arr, 0, $pos-1);
        $right = array_slice ($arr, $pos-1);
        $insert = $val;
        $arr = array_merge ($left, $insert, $right);
      }
      return $arr;
   }

   //==================================================================================================
   // file and directory related
   //==================================================================================================
   function CurrentDrive ()
   {
     $dir = getcwd();  //echo ("CWD : ".$dir."<BR>");
     $drive = substr ( $dir, 0,2 );  //echo "Drive : $drive<BR>";
     return $drive;
   }

   //--------------------------------------------------------------------------------
   function ExtractFilePath ( $filename )
   {
     $filename = str_replace ( '\\', '/', $filename);
     return substr( $filename, 0, strrpos ($filename,'/'));
   }

   //--------------------------------------------------------------------------------
   function ExtractFileName ( $filename )
   {
     $filename = str_replace ( '\\', '/', $filename);
     return substr( $filename, strrpos ($filename,'/')+1);
   }

   //--------------------------------------------------------------------------------
   function ExtractFileExt ( $filename )
   {
     $filename = str_replace ( '\\', '/', $filename);
     return substr( $filename, strrpos ($filename,'.')+1);
   }

   //--------------------------------------------------------------------------------
   /* example of use
     $path = "C:/temp";
     $ext = "jpg";
     $list = BuildFileList ( $path, true, $ext );

     echo "<FONT SIZE=1>";
     for ($i=0;$i<count($list);$i++)  echo $i.". ".$list [$i]."<BR>";
   */
   function BuildFileList ( $path, $recurse=false, $ext='' )
   {
     $list = array ();
     if (!is_dir ($path))
     {
       $list [] = "ERROR, Invalid Path : $path";
       return $list;
     }

     $ext = strtolower($ext);
     if ($handle = opendir($path))
     {
       while (false !== ($file = readdir($handle)))
       {
         if ($file != "." && $file != "..")
         {
             if ( ($recurse == true) and is_dir ( $path."/".$file ))
             {
               $list = array_merge ($list, BuildFileList ( $path."/".$file, $recurse, $ext ) );
             }
             else
             {
               if ($ext == '') $list [] = $path."/".$file;
               else
               {
                 $fileext = strtolower ( ExtractFileExt ( $file ));
                 if ($fileext == $ext) $list [] = $path."/".$file;
               }
             }
         }
      }
      closedir($handle);

      return $list;
     }
   }

   //--------------------------------------------------------------------------------
   /* example of use
     $path = "C:/temp";
   */
   function BuildSubDirList ( $path )
   {
     $list = array ();
     if (!is_dir ($path))
     {
       $list [] = "ERROR, Invalid Path : $path";
       return $list;
     }

     if ($handle = opendir($path))
     {
       while (false !== ($file = readdir($handle)))
       {
         if ($file != "." && $file != "..")
           if ( is_dir ( $path."/".$file ) ) $list [] = $path.'/'.$file;
           
       }
       closedir($handle);

       return $list;
     }
   }

   //==================================================================================================
   function MakeExcelCellAddress ( $row, $col )
   {
     //for ($col=1;$col<255;$col++)
     //{
       $n = intval ($col/26);
       $d = $col - ($n*26);
       if ($n>0) $ccol = chr(64+$n);
       $ccol .= chr(64+$d);
     //}
     return $ccol.$row;
   }

   //==================================================================================================
   function CleanUpString ( $str, $ignorecomma = false )
   {
   	 if (mb_detect_encoding ($str) == 'UTF-8') {
	    $str = mb_ereg_replace ("'", utf8_encode (chr(180)), $str);
		$str = mb_ereg_replace ('"', utf8_encode (chr(180)), $str); 
		
		if (!$ignorecomma) $str = mb_ereg_replace (",", utf8_encode(chr(184)), $str);
	 }

   	 if (mb_detect_encoding ($str) != 'UTF-8') {
   
		 // Edited by David Oshiro 3/24/08
		 $str = str_replace ("'", chr(180), $str);   // acute accent ´
		 $str = str_replace ('"', chr(180), $str);
		 $str = str_replace (chr(194), '', $str);
		 $str = str_replace (chr(160), '', $str);

		 if (!$ignorecomma) $str = str_replace (",", chr(184), $str);   // ceddila ¸
	 }
	 
     return $str;
   }
   //==================================================================================================
	function getExcelColumnLetterFromNumber($num) 
	{
	    /*
		Convert column number to Excel Column format
		1 => A
		2 => B
		27 => AA
		28 => AB
		14558 => UMX
		*/
		$numeric = $num % 26;
		$letter = chr(65 + $numeric);
		$num2 = intval($num / 26);
		if ($num2 > 0) {
			return getExcelColumnLetterFromNumber($num2 - 1) . $letter;
		} else {
			return $letter;
		}
	}
   //==================================================================================================
	function createImageThumbnail($src,$dest,$desired_width)
	{

	  /* read the source image */
	  $source_image = imagecreatefromjpeg($src);
	  $width = imagesx($source_image);
	  $height = imagesy($source_image);
	  
	  /* find the "desired height" of this thumbnail, relative to the desired width  */
	  $desired_height = floor($height*($desired_width/$width));
	  
	  /* create a new, "virtual" image */
	  $virtual_image = imagecreatetruecolor($desired_width,$desired_height);
	  
	  /* copy source image at a resized size */
	  imagecopyresized($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
	  
	  /* create the physical thumbnail image to its destination */
	  imagejpeg($virtual_image,$dest);
	}
   //==================================================================================================
   // more specifics comon functions
   //==================================================================================================
   require_once ('CommonDateTime.php');
   require_once ('CommonJScript.php');
?>
