<?php
function begin_frame($caption = "-", $align = "justify")
{

	print("<br /><TABLE cellSpacing=0 cellPadding=0 width=100% border=0>\n"
."\t<TBODY>\n"
."\t<TR height=20>\n"
."\t\t<TD class=ws background=themes/moobile_attract/images/2be.gif bgColor=#666666 colSpan=6>&nbsp;&nbsp;<strong><FONT color=#FFFFFF>$caption</FONT></strong></TD>\n"
."\t</TR>\n"
."\t<TR>\n"
."\t\t<TD class=box3 style=PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px>");
  }

  function attach_frame($padding = 0)
  {
    print("\n");
  }

  function end_frame()
  {
    print("\t\t</td>\n"
."\t</TR>\n"
."\t</tbody>\n"
."\t</TABLE>");
  }

// some fixes to make the blocks show on firefox
function begin_block($caption = "-", $align = "justify")
{

	print("<br /><TABLE cellSpacing=0 cellPadding=0 width=100% border=0>\n"
           ."\t<TBODY>\n"
           ."\t<TR>\n"
           ."\t\t<TD class=boxtop vAlign=top align=left background=themes/moobile_attract/images/2ge.gif><STRONG><font color=#FFFFFF>$caption</font></STRONG></TD>\n"
           ."\t</TR>\n"
           ."\t<tr>\n"
           ."\t\t<td class=box0>");
  }

  function end_block()
  {
    print("\t\t</td>\n"
."\t</TR>\n"
."\t</tbody>\n"
."\t</TABLE>");
  }
?>