; IRC Announce for tbsource, mirc script
; by jot81
; Modified for TorrentTrader By FLASH

on *:LOAD:{
set %tbs_ia_port $$?="Enter the port you want this script to listen: "
set %tbs_announce_chan  $$?="Enter announce channel name: "
}

on *:CONNECT: {
if ( $portfree( %tbs_ia_port ) ) {
  socklisten tbs_ia %tbs_ia_port
}
else {
  echo Port %tbs_ia_port is not free! Listener not started!
}
}

on *:DISCONNECT: {
sockclose tbs_ia
}

on *:SOCKLISTEN:tbs_ia: {
sockaccept tbs_ia_inc
}

on *:SOCKREAD:tbs_ia_inc: {
unset %tbs_announce
if ($sockerr > 0) return
:nextread
sockread %temp
if ($sockbr == 0) return
if (%temp == $null) %temp = -
set %tbs_announce %tbs_announce %temp
goto nextread
}

on *:SOCKCLOSE:tbs_ia_inc: {
msg %tbs_announce_chan %tbs_announce

unset %tbs_announce
}