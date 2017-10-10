<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | Class TemplatePower                                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 CodoCAD productions, Holland                      |
// +----------------------------------------------------------------------+
// | http://templatepower.codocad.com                                     |
// +----------------------------------------------------------------------+
// | Author(s): Ron Velzeboer <rovel@codocad.com>                         |
// |                                                                      |
// | 2001-08-10  bugfix by      : Ron Velzeboer                           |
// |             bug reported by: Luca Venuti                             |
// |                              SPRING snc di Venuti Luca & C.          |
// |                                                                      |
// |             made changes in function cleanUp() and in constructor    |
// |                                                                      |
// | 2001-08-29  improved var-parsing by : Ron Velzeboer                  |
// |                                                                      |
// |                                                                      |
// +----------------------------------------------------------------------+
//
// $Id: Version 1.6.2$

class TemplatePower
{
  var $tpl_mainfile;
  var $tpl_includefile;
  var $tpl_count;

  var $index    = Array();        // $index[{blockname}]  = {indexnumber}
  var $parent   = Array();        // $parent[{blockname}] = {parentblockname}
  var $defBlock = Array();        // empty block, just the defenition of the block
  var $content  = Array();        // slightly different structure than $block,
                                  // but than filled with content

  var $rootBlockName;
  var $currentBlock;
  var $outputContentStr;
  var $showUnAssigned;

    function TemplatePower( $tpl_file )
    {
        $this->tpl_mainfile   = $tpl_file;
        $this->tpl_count      = 0;
        $this->showUnAssigned = false;
    }

    function showUnAssigned( $state = true )
    {
        $this->showUnAssigned = $state;
    }

    function prepare()
    {
        $this->rootBlockName                    = '_ROOT';
        $this->index[ $this->rootBlockName ]    = 0;
        $this->defBlock[ $this->rootBlockName ] = Array();

        $tplvar = TemplatePower::prepareTemplate( $this->tpl_mainfile );

        $initdev["varrow"]  = 0;
        $initdev["coderow"] = 0;
        $initdev["index"]   = 0;

        TemplatePower::parseTemplate( $tplvar, $this->rootBlockName, $initdev );
        TemplatePower::makeContentRoot();
        TemplatePower::cleanUp();
    }

    function cleanUp()
    {
        for( $i=0; $i <= $this->tpl_count; $i++ )
        {
            $tplvar = 'tpl_rawContent'. $i;
            unset( $this->{$tplvar} );
        }
    }

    function prepareTemplate( $tpl_file )
    {
        $tplvar = 'tpl_rawContent'. $this->tpl_count;
        $this->{$tplvar}["content"] = @file( $tpl_file ) or die( $this->errorAlert("TemplatePower Error: Couldn't open [ $tpl_file ]!"));
        $this->{$tplvar}["size"]    = sizeof( $this->{$tplvar}["content"] );

        $this->tpl_count++;

        return $tplvar;
    }

    function parseTemplate( $tplvar, $blockname, $initdev )
    {
        $coderow = $initdev["coderow"];
        $varrow  = $initdev["varrow"];
        $index   = $initdev["index"];

        while( $index < $this->{$tplvar}["size"] )
        {
            if( preg_match("/<!-- (START|END|INCLUDE) BLOCK : (.+) -->/", $this->{$tplvar}["content"][$index], $regs))
            {
               //remove trailing and leading spaces
                $regs[2] = trim( $regs[2] );

                if( $regs[1] == "INCLUDE")
                {
                    if( isset( $this->tpl_includefile[ $regs[2] ]) )
                    {
                        $initdev["varrow"]  = $varrow;
                        $initdev["coderow"] = $coderow;
                        $initdev["index"]   = 0;

                        $tplvar2 = TemplatePower::prepareTemplate( $this->tpl_includefile[ $regs[2] ] );
                        $initdev = TemplatePower::parseTemplate( $tplvar2, $blockname, $initdev );

                        $coderow = $initdev["coderow"];
                        $varrow  = $initdev["varrow"];
                    }
                }
                else
                {
                    if( $regs[2] == $blockname )     //is it the end of a block
                    {
                        break;                       //end the while loop
                    }
                    else                             //its the start of a block
                    {
                       //make a child block and tell the parent that he has a child
                        $this->defBlock[ $regs[2] ] = Array();
                        $this->defBlock[ $blockname ]["_B:". $regs[2]] = '';

                       //set some vars that we need for the assign functions etc.
                        $this->index[ $regs[2] ]  = 0;
                        $this->parent[ $regs[2] ] = $blockname;

                       //prepare for the recursive call
                        $index++;
                        $initdev["varrow"]  = 0;
                        $initdev["coderow"] = 0;
                        $initdev["index"]   = $index;

                        $initdev = TemplatePower::parseTemplate( $tplvar, $regs[2], $initdev );

                        $index = $initdev["index"];
                    }
                }
            }
            else                                                                           //is it code and/or var(s)
            {
                $sstr = explode( "{", $this->{$tplvar}["content"][$index] );

                reset( $sstr );

                if (current($sstr) != '')
                {
                    $this->defBlock[$blockname]["_C:$coderow"] = current( $sstr );
                    $coderow++;
                }

                $sstrlength = sizeof( $sstr );

                for ( $i=1; $i < $sstrlength; $i++)
                {
                    next($sstr);

                    $strlength = strlen( current($sstr) );

                    if (current( $sstr ) == '')      // chech for '{{', explode returns ''
                    {
                        $this->defBlock[$blockname]["_C:$coderow"] = '{';
                        $coderow++;
                    }
                    else
                    {
                        $pos = strpos( current($sstr), "}" );

                        if ( ($pos !== false) && ($pos > 0) )
                        {
                            $varname = substr( current($sstr), 0, $pos );

                            $this->defBlock[$blockname]["_V:$varrow" ] = $varname;
                            $varrow++;

                            if( ($pos + 1) != $strlength )
                            {
                                $this->defBlock[$blockname]["_C:$coderow"] = substr( current( $sstr ), ($pos + 1), ($strlength - ($pos + 1)) );
                                $coderow++;
                            }
                        }
                        else
                        {
                            $this->defBlock[$blockname]["_C:$coderow"] = '{'. substr( current( $sstr ), 0, $strlength  );
                            $coderow++;
                        }
                    }
                }
            }

            $index++;
        }

        $initdev["varrow"]  = $varrow;
        $initdev["coderow"] = $coderow;
        $initdev["index"]   = $index;

        return $initdev;
    }

    function makeContentRoot()
    {
        $this->content[ $this->rootBlockName ."_0"  ][0] = Array( $this->rootBlockName );
        $this->currentBlock = &$this->content[ $this->rootBlockName ."_0" ][0];
    }

    function assignInclude( $iblockname, $value )
    {
        $this->tpl_includefile["$iblockname"] = $value;
    }

    function newBlock( $blockname )
    {
        $parent = &$this->content[ $this->parent[$blockname] ."_". $this->index[$this->parent[$blockname]] ];

        if( sizeof($parent) > 1 )
        {
            $lastitem = sizeof( $parent )-1;
        }
        else $lastitem = 0;

        if ( !isset( $parent[ $lastitem ]["_B:$blockname"] ))
        {
           //ok, there is no block found in the parentblock with the name of {$blockname}

           //so, increase the index counter and create a new {$blockname} block
            $this->index[ $blockname ] += 1;

            if (!isset( $this->content[ $blockname ."_". $this->index[ $blockname ] ] ) )
            {
                 $this->content[ $blockname ."_". $this->index[ $blockname ] ] = Array();
            }

           //tell the parent where his (possible) children are located
            $parent[ $lastitem ]["_B:$blockname"] = $blockname ."_". $this->index[ $blockname ];
        }

       //now, make a copy of the block defenition
        $blocksize = sizeof( $this->content[$blockname ."_". $this->index[ $blockname ]] );

        $this->content[ $blockname ."_". $this->index[ $blockname ] ][ $blocksize ] = Array( $blockname );

       //link the current block to the block we just created
        $this->currentBlock = &$this->content[ $blockname ."_". $this->index[ $blockname ] ][ $blocksize ];
    }

    function assign( $varname, $value )
    {
       //filter block and varname out of $varname string in case of "blockname.varname"
       // if ( preg_match("/(.*)\.(.*)/", $varname, $regs ))

        if( sizeof( $regs = explode(".", $varname ) ) == 2 )  //this is faster then preg_match
        {
            //$blockSize = @key( end( $this->content[ $regs[1] ."_". $this->index[$regs[1]] ] ) );

            $lastitem = sizeof( $this->content[ $regs[0] ."_". $this->index[$regs[0]] ] );

            $lastitem > 1 ? $lastitem-- : $lastitem = 0;

            $block = &$this->content[ $regs[0] ."_". $this->index[ $regs[0] ] ][$lastitem];
            $varname = $regs[1];
        }
        else
        {
            $block = &$this->currentBlock;
        }

        $block["_V:$varname"] = $value;
    }


    function gotoBlock( $blockname )
    {
        if ( isset( $this->defBlock[ $blockname ] ) )
        {
           //get lastitem indexnumber
            $lastitem = sizeof( $this->content[$blockname ."_". $this->index[ $blockname ]] );

            $lastitem > 1 ? $lastitem-- : $lastitem = 0;

           //link the current block
            $this->currentBlock = &$this->content[ $blockname ."_". $this->index[ $blockname ] ][ $lastitem ];
        }
    }

    function getVarValue( $varname )
    {
       //filter block and varname out of $varname string in case of "blockname.varname"
       //if ( preg_match("/(.*)\.(.*)/", $varname, $regs ))

        if( sizeof( $regs = explode(".", $varname ) ) == 2 )  //this is faster then preg_match
        {
            $lastitem = sizeof( $this->content[ $regs[0] ."_". $this->index[$regs[0]] ] );

            $lastitem > 1 ? $lastitem-- : $lastitem = 0;

            $block = &$this->content[ $regs[0] ."_". $this->index[ $regs[0] ] ][$lastitem];
            $varname = $regs[1];
        }
        else
        {
            $block = &$this->currentBlock;
        }

        return $block["_V:$varname"];
    }

    function outputContent( $blockname, $str_out )
    {
        $numrows = sizeof( $this->content[ $blockname ] );

        for( $i=0; $i < $numrows; $i++)
        {
            $defblockname = $this->content[ $blockname ][$i][0];

            for( reset( $this->defBlock[ $defblockname ]);  $k = key( $this->defBlock[ $defblockname ]);  next( $this->defBlock[ $defblockname ] ) )
            {
                if( preg_match("/C:/", $k) )
                {
                   if( $str_out )
                   {
                       $this->outputContentStr .= $this->defBlock[ $defblockname ][$k];
                   }
                   else
                   {
                       print( $this->defBlock[ $defblockname ][$k] );
                   }
                }
                else
                if( preg_match("/V:/", $k) )
                {
                   $defValue = $this->defBlock[ $defblockname ][$k];

                   if( !isset( $this->content[ $blockname ][$i][ "_V:". $defValue ] ) )
                   {
                       if( $this->showUnAssigned )
                       {
                           $value = '{'. $this->defBlock[ $defblockname ][$k] .'}';
                       }
                       else $value = '';

                   }
                   else
                   {
                       $value = $this->content[ $blockname ][$i][ "_V:". $defValue ];
                   }

                   if( $str_out )
                   {
                       $this->outputContentStr .= $value;
                   }
                   else
                   {
                       print( $value );
                   }
                }
                else
                if ( preg_match("/B:/", $k) )
                {
                    if( $this->content[ $blockname ][$i][$k] != '' )
                    {
                        TemplatePower::outputContent( $this->content[ $blockname ][$i][$k], $str_out );
                    }
                }
            }
        }
    }

    function printToScreen()
    {
        TemplatePower::outputContent( $this->rootBlockName ."_0", false);
    }

    function getOutputContent()
    {
        TemplatePower::outputContent( $this->rootBlockName ."_0", true);

        return $this->outputContentStr;
    }

    function errorAlert( $message )
    {
        print( $message ."<br>");
    }

    function printVars()
    {
        var_dump($this->defBlock);
    }
}
?>