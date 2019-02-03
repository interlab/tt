
function Smilies(Smilie)
{
    document.Form.body.value += Smilie + " ";
    document.Form.body.focus();
}

var myimages = new Array();
function preloadimages() {
    for (i = 0; i < preloadimages.arguments.length; i++) {
        myimages[i] = new Image();
        myimages[i].src=preloadimages.arguments[i]
    }
}

preloadimages("images/space.gif");

var g_nExpando = 0;
// To make the cross clickable in every browser
function putItemInState(n,bState)
{
   var oItem,oGif;
        oItem = document.getElementById("descr"+n);
        oGif = document.getElementById("expandoGif"+n);
   
   if (bState == 'toggle')
        bState=(oItem.style.display=='block');

   if(bState)
   {
       bState=(oItem.style.display='none');
       bState=(oGif.src='images/cross.gif');
   }
   else {
       bState=(oItem.style.display='block');
       bState=(oGif.src='images/noncross.gif');
   }
}

function expand(nItem)
{
    putItemInState(nItem,'toggle');
}

function expandAll()
{
    if (!g_nExpando)
    {
        document.all.chkFlag.checked=false;
        return;
    }
    var bState=!document.all.chkFlag.checked;
    for(var i=0; i<g_nExpando; i++)
        putItemInState(i,bState);
}

var tns6 = document.getElementById&&!document.all
var ie = document.all

function show_text(thetext, whichdiv) {
    if (ie) {eval("document.all." + whichdiv).innerHTML = thetext;}
    else if (tns6) {document.getElementById(whichdiv).innerHTML = thetext;}
}

function resetit(whichdiv) {
    if (ie) eval("document.all." + whichdiv).innerHTML = ''
    else if (tns6) document.getElementById(whichdiv).innerHTML = ''
}