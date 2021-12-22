<?php
# 
# check_icmp.php template
# 

# Try to find PING target IP (might be different from HOSTADDRESS)
$TARGET_IP="";
if (isset($NAGIOS_SERVICECHECKCOMMAND)) {
  $COMMAND = explode ("!", $NAGIOS_SERVICECHECKCOMMAND);
  $TARGET_IP = " ".$COMMAND[1];
  if ($TARGET_IP == " \$HOSTADDRESS\$") { $TARGET_IP = ""; }
}

// Initialize
$opt[0] = $def[0] = "";

// Title, legend and options
$ds_name[0] = $NAGIOS_SERVICEDESC;
$opt[0] .= "--vertical-label 'ms' --title '$NAGIOS_DISP_HOSTNAME / $NAGIOS_SERVICEDESC$TARGET_IP' ";
$opt[0] .= "-A ";

// Data sources
$def[0] .= "DEF:rta=$RRDFILE[1]:$DS[1]:AVERAGE ";
$def[0] .= "DEF:pl=$RRDFILE[2]:$DS[2]:AVERAGE ";
$def[0] .= "DEF:rtmax=$RRDFILE[3]:$DS[3]:AVERAGE ";
$def[0] .= "DEF:rtmin=$RRDFILE[4]:$DS[4]:AVERAGE ";
$def[0] .= "DEF:jitter_avg=$RRDFILE[5]:$DS[5]:AVERAGE ";
$def[0] .= "CDEF:rtafill=rta,UN,PREV,rta,IF ";
$def[0] .= "CDEF:rta1=pl,5,LT,rtafill,UNKN,IF ";
$def[0] .= "CDEF:rta2=pl,5,GE,pl,10,LT,*,rtafill,UNKN,IF ";
$def[0] .= "CDEF:rta3=pl,10,GE,pl,15,LT,*,rtafill,UNKN,IF ";
$def[0] .= "CDEF:rta4=pl,15,GE,pl,20,LT,*,rtafill,UNKN,IF ";
$def[0] .= "CDEF:rta5=pl,20,GE,pl,50,LT,*,rtafill,UNKN,IF ";
$def[0] .= "CDEF:rta6=pl,50,GE,pl,95,LT,*,rtafill,UNKN,IF ";
$def[0] .= "CDEF:rta7=pl,95,GE,rtafill,UNKN,IF ";
$def[0] .= "CDEF:rtspan=rtmax,rtmin,- ";
$def[0] .= "CDEF:jitter_avg_low=rtafill,jitter_avg,2,/,- ";
  
// Text
$def[0] .= "GPRINT:rta:AVERAGE:'rtt\: %.1lf $UNIT[1] avg' ";
$def[0] .= "GPRINT:rtmax:MAX:'%.1lf $UNIT[3] max' ";
$def[0] .= "GPRINT:rtmin:MIN:'%.1lf $UNIT[4] min' ";
$def[0] .= "GPRINT:rta:LAST:'%.1lf $UNIT[1] last' ";
if ($WARN[1] != "" || $CRIT[1] != "") {
  $def[0] .= "COMMENT:\\u ";
  if ($WARN[1] != "") { $def[0] .= "HRULE:$WARN[1]#ffff00:'warn ".round($WARN[1])." $UNIT[1]' "; }
  if ($CRIT[1] != "") { $def[0] .= "HRULE:$CRIT[1]#ff0000:'crit ".round($CRIT[1])." $UNIT[1]' "; }
  $def[0] .= "COMMENT:\\r ";
} else {
  $def[0] .= "COMMENT:\\l ";
}
$def[0] .= "GPRINT:jitter_avg:AVERAGE:'jitter\: %.1lf $UNIT[5] avg' ";
$def[0] .= "GPRINT:jitter_avg:MAX:'%.1lf $UNIT[5] max' ";
$def[0] .= "GPRINT:jitter_avg:MIN:'%.1lf $UNIT[5] min' ";
$def[0] .= "GPRINT:jitter_avg:LAST:'%.1lf $UNIT[5] last\l' ";
$def[0] .= "GPRINT:pl:AVERAGE:'packet loss\: %.2lf $UNIT[2] avg' ";
$def[0] .= "GPRINT:pl:MAX:' %.2lf $UNIT[2] max' ";
$def[0] .= "GPRINT:pl:MIN:' %.2lf $UNIT[2] min' ";
$def[0] .= "GPRINT:pl:LAST:' %.2lf $UNIT[2] last\l' ";

// Graph
$def[0] .= "AREA:rtmin ";
$def[0] .= "AREA:rtspan#eeeeee::STACK:skipscale ";

$def[0] .= "AREA:jitter_avg_low ";
$def[0] .= "AREA:jitter_avg#cccccc::STACK ";

$def[0] .= "COMMENT:'loss color\:' ";
$def[0] .= "LINE1:rtafill#202020 ";
$def[0] .= "LINE2:rta1#28ff02:0 ";
$def[0] .= "LINE2:rta2#02b9ff:1/20 ";
$def[0] .= "LINE2:rta3#025aff:2/20 ";
$def[0] .= "LINE2:rta4#5f02ff:3/20 ";
$def[0] .= "LINE2:rta5#7f02ff:4/20 ";
$def[0] .= "LINE2:rta6#dd02ff:10/20 ";
$def[0] .= "LINE2:rta7#ff0202:19/20\l ";

?>
