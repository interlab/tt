<?php

//	THIS FILE IS WHERE YOU DEFINE THE BLOCKS AND FRAMES LAYOUT

// BEGIN FRAME
function begin_frame($caption = "-", $align = "justify")
{
    print("<br><table cellpadding=0 cellspacing=0 border=0 width=95% align=center>\n"
            ."\t<tr>\n"
            ."\t\t<TD class=frame_top_left><img src=themes/" . $GLOBALS['ss_uri'] . "/images/frame_top_left.gif></TD>\n"
            ."\t\t<TD class=frame_top align=center><b>$caption</b></TD>\n"
            ."\t\t<TD class=frame_top_right><img src=themes/" . $GLOBALS['ss_uri'] . "/images/frame_top_right.gif></TD>\n"
            ."\t</tr>\n"
            ."\t<tr>\n"
            ."\t\t<TD class=frame_middle_left><span class=space></span></TD>\n"
            ."\t\t<TD class=frame_middle width=100% align=$align valign=top><br>
            ");
}

function begin_frame1($caption = "-", $align = "justify")
{
    echo '<br>
        <div class="tt-frame" style="width: 95%; margin: 0 auto; padding: 0;">
        <div style="background-image: url(\'themes/default/images/frame_top.gif\'); border-radius: 8px 8px 0 0;'.
            ' border-right: solid #9a9a9a 1px; border-left:solid #9a9a9a 1px; height: 24px;">
            <div align="center" style="line-height: 24px;"><b>'.$caption.'</b></div>
        </div>
        <div style="background-color: white; border: 1px #9a9a9a solid; border-top: none; border-bottom: none; padding: 0px 8px;">
        <br>';
}

// ATTACH FRAME
function attach_frame($padding = 0)
{
    print("\n");
}

// END FRAME
function end_frame1()
{
    echo '</div><div style="height: 10px; background-image: url(\'themes/default/images/frame_bottom.jpg\');'.
        'background-color: white; border: 1px #9a9a9a solid; border-top: none; border-bottom: none; border-radius: 0 0 6px 6px;"></div>
        </div>';
}

function end_frame()
{
    print("\t\t</td>\n"
            ."\t\t<td class=frame_middle_right><span class=space></span></td>\n"
            ."\t</tr>\n"
            ."\t<tr>\n"
            ."\t\t<td class=frame_bottom_left><span class=space></span></td>\n"
            ."\t\t<td class=frame_bottom></td>\n"
            ."\t\t<td class=frame_bottom_right><span class=space></span></td>\n"
            ."\t</tr>\n"
            ."</table>");
}

// BEGIN BLOCK
function begin_block($caption = "-", $align = "justify")
{
    // $GLOBALS['ss_uri']
    echo '
    <br>
    <div class="tt-block">
        <div class="tt-block-center-top"><span>', $caption, '</span></div>
        <div class="tt-block-content">';
}

// END BLOCK
function end_block()
{
    echo '
        </div>
        <div class="tt-block-bottom"><div class="tt-block-center-bottom"><span></span></div></div>
    </div>';
}
