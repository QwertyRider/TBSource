<?php

require "include/bittorrent.php";
dbconn();

$nick = ($CURUSER ? $CURUSER["username"] : ("Guest" . rand(1000, 9999)));

stdhead();
begin_main_frame();
begin_frame("IRC");

?>
<p>The official IRC channel is <a href=irc://irc.freequest.net:7000/torrentbits>#torrentbits</a> on the <a href=http://www.freequest.net/>FreeQuest</a> network.</p>
<applet name="jchat" codebase="jirc" archive="jirc_nss.zip,resources.zip"  code=Chat.class 
width=100% height=500 MAYSCRIPT>

<param name="CABBASE" value="jirc_mss.cab,resources.cab">

<param name="isLimitedServers" value="false">
<param name="isLimitedChannels" value="false">
<param name="DirectStart" value="true">
<param name="DisplayConnectButton" value="false">
<param name="DisplayConfigSocks" value="true">

<param name="FilterKeys" value=":) :( :D :P ;) :confused: :mad: :cool: ;( :love: :thumbup: :thumbdown: :evil: :kiss: :hello: :applause: :banghead: :bye:">
<param name="FilterVals" value="smile.gif frown.gif biggrin.gif tongue.gif wink.gif confused.gif mad.gif cool.gif evil.gif love.gif thumbup.gif thumbdown.gif evil.gif kiss.gif hello.gif applause.gif banghead.gif bye.gif">

<param name="HostName" value="jpilot" >

<param name="UseModeIcons" value="true">
<param name="TimeStampFormat" value="hh:mm a" >
<param name="AllowTimeStamp" value="true">

<param name="UserProfileURL" value="http://www.jpilot.com/cgi-bin/products/jirc/viewprofile.cgi?nick" >

<param name="FieldNameProfileButton" value="View profile">
<param name="FieldNameChannelJoined" value="just entered the chat room" >
<param name="FieldNameChannelLeft" value="just left the chat room" >
<param name="FieldNameChannelLeft" value="You just left the chat room" >
<param name="FieldNameNickNotify" value="has changed his/her nickname to">
<param name="FieldNamePrivateIgnore" value="Ignore This User">
<param name="FieldNameConnecting" value="Connecting to the server, please wait ....">
<param name="FieldNameConnected" value="Connected to server, sending login information ...">
<param name="FieldNameConnectionClose" value="Connection to server closed.">
<param name="FieldNameQuitMsg" value="Bye bye">
<param name="FieldNamePrivateClose" value="Close">
<param name="FieldNamePrivateChatTitle" value="Private Chat with: ">
<param name="WelcomeMessage" value="Welcome to Java IRC chat!">
<param name="RealName" value="JPilot jIRC applet user">
<param name="NickName" value="<?=$nick;?>">
<param name="UserName" value="jirc">
<param name="IgnoreUser" value="ignore user : ">
<param name="ActivateUser" value="activate  user : ">

<param name="UserCmdColor" value="yellow">
<param name="BackgroundColor" value="119,124,170">
<param name="TextColor" value="white">
<param name="TextScreenColor" value="black">
<param name="ListTextColor" value="white">
<param name="ListScreenColor" value="50,50,100">
<param name="LogoBorderColor" value="black">
<param name="ConfigBorderColor" value="204,153,51">
<param name="LogoBgColor" value="white">
<param name="TitleForegroundColor" value="white">
<param name="TitleBackgroundColor" value="black">
<param name="TextFontSize" value="12">
<param name="TextFontName" value="Arial">
<param name="FGColor" value="black">
<param name="InputTextColor" value="black">
<param name="InputScreenwhiteColor" value="white">
<param name="LicenseKey" value="2750273987-7023606034048845838863-66888886550909882360603404884483886314236013-7023606034048845838863">
<param name="DisplayAbout" value="false">
<param name="DisplaySoundControl" value="false">
<param name="AllowJoinSound" value="true" >
<param name="AllowLeaveSound" value="true">
<param name="LogoGifName" value="IRClogo.gif">
<param name="RefreshColorCode" value="false">
<param name="SoundMsg" value="Play Sound">
<param name="NickNameColor" value="6">
<param name="NickMaskStart" value="">
<param name="NickMaskEnd" value=":">

<param name="PWindowHeight" value="250">
<param name="PWindowWidth" value="400">
<param name="BorderSpacing" value="0">
<param name="BorderVsp" value="3">

<param name="IgnoreMOTD" value="true">
<param name="IgnoreModeMsg" value="true">
<param name="IgnoreServerMsg" value="true">
<param name="IgnoreCode" value="5" >


<param name="ServerPort" value="7000">
<param name="ServerName1" value="irc.freequest.net">
<param name="Channel1" value="#torrentbits">
<param name="Channel2" value="#dox">

<param name="AllowShowURL" value="true">
<param name="AllowIdentd" value="true">
<param name="AllowURL" value="true">
<param name="AllowPrivateChatWindow" value="false">
<param name="NoConfig" value="true">
<param name="UserListWidth" value="150">

</applet>
<?

end_frame();
end_main_frame();
stdfoot();

?>