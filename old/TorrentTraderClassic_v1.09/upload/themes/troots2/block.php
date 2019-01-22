<?php
  function begin_frame($caption = "-", $align = "justify")
{

	print("<br /><TABLE width=100% border=0 align=center>\n"
		."\t<TR>\n"
		."\t\t<TD class=white style='PADDING-LEFT: 12px; FONT-WEIGHT: bold; VERTICAL-ALIGN: middle; PADDING-TOP: 2px' bgColor=#dd1212 height=25>&nbsp;<img src=themes/troots2/images/dot_arrow.gif align=middle>&nbsp;&nbsp;$caption</TD></TR>\n"
		."\t<TR>\n"
		."\t\t<TD>\n"
		."\t<TABLE cellSpacing=0 cellPadding=0 width=100% align=center>\n"
		."\t<TBODY>\n"
		."\t<TR>\n"
		."\t\t<TD style='PADDING-TOP: 5px' valign=top align=$align>");
  }

  function attach_frame($padding = 0)
  {
    print("\n");
  }

  function end_frame()
  {
    print("\t\t</TD>\n"
		."\t</TR>\n"
		."\t</TBODY>\n"
		."\t</TABLE>\n"
		."\t\t</TD>\n"
		."\t</TR>\n"
		."\t</TABLE>");
  }

// some fixes to make the blocks show on firefox
function begin_block($caption = "-", $align = "justify")
{

	print("<br /><TABLE width=100% border=0 align=center>\n"
		."\t<TR>\n"
		."\t\t<TD class=white style='PADDING-LEFT: 12px; FONT-WEIGHT: bold; VERTICAL-ALIGN: middle; PADDING-TOP: 2px' bgColor=#4396ca height=25>&nbsp;<img src=themes/troots2/images/dot_arrow.gif align=middle>&nbsp;&nbsp;$caption</TD></TR>\n"
		."\t<TR>\n"
		."\t\t<TD>\n"
		."\t<TABLE cellSpacing=0 cellPadding=0 width=100% align=center>\n"
		."\t<TBODY>\n"
		."\t<TR>\n"
		."\t\t<TD style='PADDING-TOP: 5px' valign=top align=$align>");
  }

  function end_block()
  {
    print("\t\t</TD>\n"
		."\t</TR>\n"
		."\t</TBODY>\n"
		."\t</TABLE>\n"
		."\t\t</TD>\n"
		."\t</TR>\n"
		."\t</TABLE>");
  }
?>