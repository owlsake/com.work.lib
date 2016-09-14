<?php
  // troubleshooting
  /*
    - find items where menutitle is the same as its parent (this will cause tree not showing)
      select m1.menuid, m1.menutitle, m2.menuid, m2.menutitle from menuitem m1, menuitem m2 where m1.menutitle=m2.menutitle and m2.menuid=m1.parentid
    - find enabled children where the parent is disabled (this will cause tree not showing)
     select * from menuitem m1, menuitem m2 where m1.activeflag='A' and m2.menuid=m1.parentid and m2.activeflag='I'
    - find rows where menulevel is null
     select * from menuitem where menulevel is null
    - find rows with empty parentid
     select * from menuitem where parentid is null
    - find menuurl or menudesc contains char(10) or char(13)  
  */

   require_once ('CommonLib.php');

   /*
   function expand_all () {
    for (var i = 1; i < trees[0].a_index.length; i++) {
    var o_item = trees[0].a_index;
    if (!o_item.b_opened) o_item.open(o_item.b_opened);
    }
    }
    function collapse_all () {
    for (var i = 1; i < trees[0].a_index.length; i++) {
    var o_item = trees[0].a_index;
    if (o_item.b_opened) o_item.open(o_item.b_opened);
    }
    }

    ...just wanted to mention that there was a little typo in the last posted code:

    the var i was missing from the following line

    var o_item = trees[0].a_index[var];


    ...for some reason, the i between brackets doesn't show on the posting ???

    I'm sure opus27 tryied posting it and didn't notice it got removed.

    function expand_all ()
    {
      for (var i = 1; i < trees[0].a_index.length; i++)
      {
        var o_item = trees[0].a_index[i];
        if (!o_item.b_opened) o_item.open(o_item.b_opened);
      }
    }

    function collapse_all ()
    {
      for (var i = 1; i < trees[0].a_index.length; i++)
      {
        var o_item = trees[0].a_index[i];
        if (o_item.b_opened) o_item.open(o_item.b_opened);
      }
    }

   */

   //------------------------------------------------------------------------------------------------
   // if adding items from database with potential same captions, attach a prefix of unique key
   // in the format of "xxxxx~", can be of any length, just need to end it with a tilde (~)
   // for example
   // AddItem ("001~Main Menu", "100~SubMenu1");
   // AddItem ("001~Main Menu", "200~SubMenu2");
   // AddItem ("100~SubMenu1", "800-General");
   // AddItem ("200~SubMenu2", "900-General");
   //
   // the above case, if not using unique prefix, will cause a vague "General" caption
   // this object will automatically strips any prefixes
   //------------------------------------------------------------------------------------------------
   class GenericTreeMenu
   {
     var $itemarr = array();
     var $iconobj;
     var $jspath;
     var $fontname = 'MS Sans-Serif';
     var $fontsize = '10px';

     var $temparr = array ();
     var $assigned = false;

     //................................................................................................
     function GenericTreeMenu()
     {
       $this->iconobj = new TreeIcons();
       $this->SetJSPath('js/'); // location of treemenu.js

       //$this->SetFont ('Verdana', '11px');
     }

     //................................................................................................
     function AddItem($parent, $caption, $link='')
     {
       $this->assigned = false;
       $parent = trim(strtolower( $this->CleanUpString ($parent)));
       //$parent = str_replace ('/', '', $parent);
       $caption = $this->CleanUpString ($caption);
       $link    = $this->CleanUpString ($link);
//_print_to_file (null, "AddItem($parent, $caption, $link)");

       $a = array ();
       $a['caption']   = $caption;
       $a['link']      = $link;
       $a['children']  = array();

       if ($parent=='root') { $this->itemarr = $a; }
       else
       { // save in temporary array
         $this->temparr[$parent][] = $a;
       }
     }

     //................................................................................................
     function CleanUpString ( $str )
     {
       $str = str_replace ("'", chr(180), $str);   // acute accent ?
       $str = str_replace ('"', chr(180), $str);
       $str = str_replace (",", chr(184), $str);   // ceddila ?
       return $str;
     }

     //................................................................................................
     function GenerateItemArray( & $itemarr )
     {
       $parent = trim(strtolower($itemarr ['caption']));
       //$parent = str_replace ('/', '', $parent);
//echo $parent."<BR>";
       if (count($this->temparr[$parent]) > 0)
       {
         $itemarr['children'] = $this->temparr[$parent];
         //unset($this->temparr[$parent]);
         for ($i=0;$i<count($itemarr['children']);$i++)
         {
//echo 'children<BR>';
           $this->GenerateItemArray ( $itemarr['children'][$i]);
         }
       }
     }

     //................................................................................................
     function SetFont ( $name, $size )
     {
       $this->fontname = $name;
       $this->fontsize = $size;
     }

     //................................................................................................
     function SetImagePath ( $path )
     { $this->iconobj->iconpath = $path;  }

     //................................................................................................
     function SetJSPath ( $path )
     { $this->jspath = $path;  }

     //................................................................................................
     function SetLinkTarget ($target)
     { $this->iconobj->SetLinkTarget ($target);   }

     //................................................................................................
     function SetIconFile ($iconkey, $iconfile)
     { $this->iconobj->SetIconFile ($iconkey, $iconfile);  }

     //................................................................................................
     function SetIconFileForBase ($iconfile)
     {
       $this->iconobj->SetIconFile ('root_leaf_icon_normal', $iconfile);
       $this->iconobj->SetIconFile ('root_leaf_icon_selected', $iconfile);
       $this->iconobj->SetIconFile ('root_icon_normal', $iconfile);
       $this->iconobj->SetIconFile ('root_icon_selected', $iconfile);
       $this->iconobj->SetIconFile ('root_icon_opened', $iconfile);
       $this->iconobj->SetIconFile ('root_icon_selected', $iconfile);
     }

     //................................................................................................
     function SetIconFileForFolder ($iconfile)
     {
       $this->iconobj->SetIconFile ('node_icon_normal', $iconfile);
     }

     //................................................................................................
     function SetIconFileForFolderOpen ($iconfile)
     {
       $this->iconobj->SetIconFile ('node_icon_selected', $iconfile);
       $this->iconobj->SetIconFile ('node_icon_opened', $iconfile);
       $this->iconobj->SetIconFile ('node_icon_selected_opened', $iconfile);
     }

     //................................................................................................
     function SetIconFileForPage ($iconfile)
     {
       $this->iconobj->SetIconFile ('leaf_icon_normal', $iconfile);
       $this->iconobj->SetIconFile ('leaf_icon_selected', $iconfile);
     }

     //................................................................................................
     function AssignArray ( $arr, $parent='__root__' )
     {
       $this->assigned = true;
       if ($parent == '__root__')
       {
         $this->AddItem ('', 'Array Root');
         $parent = 'Array Root';
         $temparr = & $this->itemarr['children'];
       }
       else
       {
         $temparr = & $this->temparr ['children'];  // get it from temp pointer
       }

       $count = 0;
       foreach( $arr as $key => $value)
       {
         if ( is_array($value) ) // if value is array
         {
           $a = array ();
           $a['caption']  = "<B>".$key."</B>";
           $a['link']     = '';
           $a['children'] = array();

           $temparr[] = $a;

           $n = count($temparr) -1;
           $tempchild =  & $temparr[$n];

           $this->temparr = & $tempchild;  // set temp pointer
           if ($key =='') { $key = "$count"; }  // avoid empty $key as in numbered keys

           $this->AssignArray ($value, $key);
         }
         else
         {
           $a = array ();
           $a['caption']  = "<B>".$key."</B> : ".$value;
           $a['link']     = '';
           $a['children'] = array();

           $temparr[] = $a;
         }

         $count++;
       }
     }

     //................................................................................................
     function RenderTreeIcons ()
     {
       $this->iconobj->Render();
     }

     //................................................................................................
     function CleanUpCaption ( $caption )
     {
       $n = strpos($caption,"~");
       if ($n === false) return $caption;
       else
       {
         return substr($caption,$n+1);
       }
     }

     //................................................................................................
     function RenderTreeItems ()
     {
       $arr   = $this->itemarr;
//_print_to_file ( null, $arr );
       $level = 1;
       _print ( "var TreeItems = [" );
       $p = str_repeat(' ',$level*7)."['".$this->CleanUpCaption ( $arr['caption'])."', '".$arr['link']."'";

       if ( count($arr['children']) > 0 )
       {
          _print ( $p.", ," );
          $this->RenderTreeChildren( $arr['children'], $level+1 );
       }
       else  { _print ( $p."]" ); }
       _print ( str_repeat(' ',$level*7)."]" );
       if ( count($arr['children']) > 0 )
       {
		_print ( "];" );
		}
		else
		{
		_print ( ";" );
		}
     }

     //................................................................................................
     function RenderTreeChildren ( $aChild, $level )
     {
       for ($i=0;$i<count($aChild);$i++)
       {
         $p = str_repeat(' ',$level*7)."['".$this->CleanUpCaption ( $aChild[$i]['caption'] )."', '".$aChild[$i]['link']."'";

         if ( count($aChild[$i]['children']) > 0 )
         {
            _print ( $p.", ," );
            $this->RenderTreeChildren($aChild[$i]['children'],$level+1);
            _print ( str_repeat(' ',$level*7)."]," );
         }
         else  {  _print ( $p."]," );  }
       }
     }

     //................................................................................................
     function RenderStyle()
     {
       /*
	   $fontname = $this->fontname;
       $fontsize = $this->fontsize;
       _print ("<STYLE TYPE=\"text/css\">");
       _print ("  A {");
       _print ("      font-family: $fontname;");
       _print ("      font-size: $fontsize;");
       _print ("      font-weight: normal;");
       _print ("      color: gray;");
       _print ("    }");
       _print ("  A:hover {");
       _print ("      font-weight: bold;");
       _print ("      color: black;");
       _print ("    }");
       _print ("  A:link, A:visited, A:active { text-decoration: none }");
       _print ("</STYLE>");
	   */
     }

     //................................................................................................
     function _print ($text )
     {
       print ($text."\n");
     }

     //................................................................................................
     function RenderTree ( $renderstyle = true )
     {
       if (!$this->assigned)
       {
         $this->GenerateItemArray ( $this->itemarr );
         $this->temparr = null;
       }
	   
	   echo "<link rel='stylesheet' href='css/GenericTreeMenu.css'>";
       //if ($renderstyle == true) $this->RenderStyle();

       _print ("<script language=\"JavaScript\" src=\"".$this->jspath."treemenu.js\"></script>");

       _print ("<script language=\"JavaScript\">");
       $this->RenderTreeIcons ();
       _print ("</script>");

       _print ("<script language=\"JavaScript\">");
       $this->RenderTreeItems ();
       _print ("</script>");

       _print ("<script language=\"JavaScript\">");
       _print ("<!--");
       _print ("new tree(TreeItems, TreeTpl);");
       _print ("//-->");
       _print ("</script>");
     }

     //................................................................................................
     function RenderExpandCollapseScript()
     {
        _print ("<script language='JavaScript'>");
        _print ("    function tree_expand_all ()");
        _print ("    {");
        //_print ("      alert ('before expand : ' + String(TREES[0].a_index.length));");
        _print ("      for (var i = 1; i < TREES[0].a_index.length; i++)");
        _print ("      {");
        _print ("        var o_item = TREES[0].a_index[i];");
        _print ("        var n = o_item.a_config.length;");
        _print ("        if ((n>3) && !o_item.b_opened) o_item.open(o_item.b_opened);");
        //_print ("        if (!o_item.b_opened) o_item.open(o_item.b_opened);");
        _print ("      }");
        //_print ("      alert ('after expand');");
        _print ("    }");
        _print ("");
        _print ("    function tree_collapse_all ()");
        _print ("    {");
        //_print ("      alert ('before collapse');");
        _print ("      for (var i = 1; i < TREES[0].a_index.length; i++)");
        _print ("      {");
        _print ("        var o_item = TREES[0].a_index[i];");
        _print ("        var n = o_item.a_config.length;");
        _print ("        if (n>3) o_item.open(true);");
        _print ("      }");
        //_print ("      alert ('after collapse');");
        _print ("    }");
        _print ("</script>");
     }

     //................................................................................................
     function MakeOnClick_ExpandAll ()
     {
       return "OnClick='tree_expand_all();'";
     }

     //................................................................................................
     function MakeOnClick_CollapseAll ()
     {
       return "OnClick='tree_collapse_all();'";
     }

   }

   //------------------------------------------------------------------------------------------------
   // Icons Object
   //------------------------------------------------------------------------------------------------
   class TreeIcons
   {
     var $icons = array();
     var $iconpath = 'img/treemenu/';
     var $linktarget = '_blank';

     //................................................................................................
     function TreeIcons()
     {
       $this->AddIcon ('icon_e'  , 'empty.gif', 'empty_image');
       $this->AddIcon ('icon_l'  , 'line.gif',  'vertical_line');

       $this->AddIcon ('icon_32' , 'base.gif',   'root_leaf_icon_normal');
       $this->AddIcon ('icon_36' , 'base.gif',   'root_leaf_icon_selected');
       $this->AddIcon ('icon_48' , 'base.gif',   'root_icon_normal');
       $this->AddIcon ('icon_52' , 'base.gif',   'root_icon_selected');
       $this->AddIcon ('icon_56' , 'base.gif',   'root_icon_opened');
       $this->AddIcon ('icon_60' , 'base.gif',   'root_icon_selected');

       $this->AddIcon ('icon_16' , 'folder.gif', 'node_icon_normal');
       $this->AddIcon ('icon_20' , 'folderopen.gif', 'node_icon_selected');
       $this->AddIcon ('icon_24' , 'folderopen.gif', 'node_icon_opened');
       $this->AddIcon ('icon_28' , 'folderopen.gif', 'node_icon_selected_opened');

       $this->AddIcon ('icon_0'  , 'page.gif', 'leaf_icon_normal');
       $this->AddIcon ('icon_4'  , 'page.gif', 'leaf_icon_selected');

       $this->AddIcon ('icon_2'  , 'joinbottom.gif', 'junction_for_leaf');
       $this->AddIcon ('icon_3'  , 'join.gif',       'junction_for_last_leaf');
       $this->AddIcon ('icon_18' , 'plusbottom.gif', 'junction_for_closed_node');
       $this->AddIcon ('icon_19' , 'plus.gif',       'junction_for_last_closed_node');
       $this->AddIcon ('icon_26' , 'minusbottom.gif','junction_for_opened_node');
       $this->AddIcon ('icon_27' , 'minus.gif',     'junction_for_last_opended_node');
     }

     //................................................................................................
     function AddIcon ($iconname, $iconfile, $iconkey )
     {
       $iconkey = strtolower($iconkey);
       $this->icons[$iconkey]= array();
       $this->icons[$iconkey]['name'] = $iconname;
       $this->icons[$iconkey]['file'] = $iconfile;
     }

     //................................................................................................
     function SetIconFile ($iconkey, $iconfile)
     {
       $iconkey = strtolower($iconkey);
       $this->icons[$iconkey]['file'] = $iconfile;
     }

     //................................................................................................
     function SetLinkTarget ($link)
     {
       $this->linktarget = $link;
     }

     //................................................................................................
     function Render ()
     {
       $i = 0;
       $s = str_repeat(' ',7);
       _print ( "var TreeTpl = {" );
       _print ( $s."'target'  : '".$this->linktarget."'," );

       foreach ( $this->icons  as $key => $value)
       {
          $p = "'".$this->icons[$key]['name']."'  : '".$this->iconpath.$this->icons[$key]['file'];
          $i++;
          if ($i == count($this->icons) )  { _print ( $s.$p."' // ".$key); }
          else                             { _print ( $s.$p."', // ".$key); }
       }
       _print ( "};" );
     }
   }

?>