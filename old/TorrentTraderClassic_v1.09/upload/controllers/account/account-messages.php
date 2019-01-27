<?php
//
// CSS and Language updated 29.NOV.05
//

chdir(dirname(dirname(__DIR__)));
require_once 'backend/functions.php';

dbconn(false);

isNotGuest();

global $CURUSER;

$id = (int) $CURUSER["id"];

$loadmsg = "Загрузка...<br><img src='".ST_IMG_URL."/space.gif' width='1' height='200'>";
$url = ST_URL.'/account-messages2.php';

addJsFile('ajax.js');
addJsFile('cvi_busy_lib.js');

stdhead("Messages");
//PRIVATE MESSAGES FRAME STARTS HERE
begin_frame($txt['ACCOUNT_YOUR_MESSAGES']);

?>

<script><!--
var sent = 0;
var startMsg = 0;
var ofMsg = 0;
var busy = false;

function getMessages()
{
    if ( startMsg == 0 )
		document.getElementById('btnPrev').disabled = true;
    else
		document.getElementById('btnPrev').disabled = false;

    var toMsg = startMsg + 20;

    if ( toMsg > ofMsg ) {
		document.getElementById('btnNext').disabled = true;
        toMsg = ofMsg;
    }
    else {
		document.getElementById('btnNext').disabled = false;
    }

    var d = document.getElementById("statmsg-pane");
    d.innerHTML = "Сообщения " + ( startMsg + 1 ) + "-" + toMsg + " из " + ofMsg;

    var ajax = new tbdev_ajax();
    ajax.onShow = function() { };
    ajax.onCompletion = function() { if(typeof(busy)==='object') {busy.remove(); } };
    ajax.onDisplayed = function() {
        // initSpoilers( 'body' );
    };
    ajax.requestFile = "<?= $url ?>";
    ajax.method = 'GET';
    ajax.element = 'messages-pane';
    ajax.sendAJAX( "sent="+sent+"&start="+startMsg );
}

function msgPrev()
{
    startMsg -= 20;
    if ( startMsg < 0 ) {
		startMsg = 0;
    }

    showMessages();
}

function msgNext()
{
    startMsg += 20;
    showMessages();
}

function showMessages()
{
    var mpane = document.getElementById("messages-pane");
    mpane.innerHTML = "<?= $loadmsg ?>";
    busy = getBusyOverlay( mpane, { opacity: 0.5 } );

    var ajax = new tbdev_ajax();
    ajax.onShow = function() { };
    ajax.onDisplayed = function() { getMessages(); };
    ajax.requestFile = "<?= $url ?>";
    ajax.method = 'GET';
    ajax.element = '';
    ajax.execute = true;
    ajax.sendAJAX( "sent="+sent+"&get=number" );
}

function selAll()
{
    var children = document.getElementsByTagName('input');
    var i = 0;
    var child;
    for (i = 0; i < children.length; i++)
    {
        child = children[i];
        if ( child.name == 'del-my-pm' ) {
            child.checked = true;
        }
    }
}

function msgsDel()
{
    var delids = new Array();
    var children = document.getElementsByTagName('input');
    var i = 0; var child;
    for (i=0; i<children.length; i++)
    {
        child = children[i];
        if ( child.name == 'del-my-pm' ) {
            if ( child.checked ) {
                delids.push( child.value );
            }
        }
    }

    if ( delids.length > 0 )
    {
        var mpane = document.getElementById("messages-pane");
        mpane.innerHTML = "<?= $loadmsg ?>";

        var ajax = new tbdev_ajax();
        ajax.onShow = function() { };
        ajax.onDisplayed = function() { showMessages(); };
        ajax.requestFile = '<?= $url ?>';
        ajax.method = 'POST';
        ajax.element = '';
        ajax.setVar( 'sent', sent );
        ajax.setVar( 'delids', delids );
        ajax.sendAJAX("");
    }
}

function switchType( show, hide, s )
{
    startMsg = 0;
    ofMsg = 0;
    sent = s;
    document.getElementById( 'tab-'+hide ).className='';
    document.getElementById( 'tab-'+show ).className='selected';
    showMessages();
}
--></script>

<ul id="tabnav">
    <li id="tab-m-inbox" class="selected"><a style="cursor:pointer;cursor:hand" onclick="switchType('m-inbox','m-outbox',0)">Принятые</a></li>
    <li id="tab-m-outbox" ><a style="cursor:pointer;cursor:hand" onclick="switchType('m-outbox','m-inbox',1)">Отправленные</a></li>
</ul>

<table border='0' width='100%'>
<tr>
  <td align='left' width='50%'>
    <div id='statmsg-pane' style='padding: 5px'>
    </div>
  </td>
  <td align='right' width='50%'>
    <input value=" << " onclick="msgPrev();" type="button" id="btnPrev" class="inputbt"> |
    <input value=" >> " onclick="msgNext();" type="button" id="btnNext" class="inputbt">
    &nbsp; &nbsp; &nbsp;
    <input value="Удалить выбранные" onclick="msgsDel();" type="button" id="btnDel" class="inputbt">
  </td>
</tr>
<tr>
  <td colspan='2' align='left' class='descr-frame'>
    <div id='messages-pane' style="padding: 5px">
      <?= $loadmsg ?>
    </div>
  </td>
</tr>
</table>

<script>
showMessages();
</script>

<?php
end_frame();

stdfoot();
