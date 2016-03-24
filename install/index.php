<html><head>
<title>TB-Source Installer</title>
<link rel="stylesheet" href="../styles/default.css" type="text/css">
</head>
<body>

<? 
require "functions.php";  

// Form
function step_1()
{
	echo'
    <form method="post" action="index.php">
	<p class="big">&nbsp;</p>
  <table width="80%" border="0" align="center">
    <tr align="center" valign="middle" bgcolor="#FFFFFF">
      <th height="100" colspan="2" class="colhead"><p class="title big">Post install instructions:</p>
      <p class="title big"> 1) Create the database. </p>
      <p class="title big"> 2) Chmod the /include/secrets.php to 666 if using unix/linux </p></th>
    </tr>
    <tr>
      <td colspan="2" class="colhead">Database Configuration </td>
    </tr>
    <tr>
      <td>Database Server (use localhost if not sure) </td>
      <td><input name="server2" type="text" id="server" value="localhost" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Database Name</td>
      <td><input name="dbname" type="text" id="dbname" value="tbsource" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Database User </td>
      <td><input name="dbuser" type="text" id="dbuser" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Database Password </td>
      <td><input name="dbpass" type="text" id="dbpass" size="40" maxlength="40" /></td>
    </tr>

    <tr>
      <td colspan="2" class="colhead">Sysop User Configuration </td>
    </tr>
    <tr>
      <td>Sysop Username </td>
      <td><input name="sysopuser" type="text" id="sysopuser" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Sysop Password </td>
      <td><input name="sysoppass" type="text" id="sysoppass" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Sysop Password Confirm </td>
      <td><input name="sysoppass2" type="text" id="sysoppass2" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Sysop Email </td>
      <td><input name="sysopmail" type="text" id="sysopmail" size="40" maxlength="40" /></td>
    </tr>

    <tr>
      <td colspan="2" class="colhead">Basic Site Configuration </td>
    </tr>
    <tr>
      <td>Site Name </td>
      <td><input name="sitename" type="text" id="sitename" value="TBsource" size="40" maxlength="40" /></td>
    </tr>


    <tr>
      <td>Domain (no ending slash) </td>
      <td><input name="domain" type="text" id="domain" value="http://tbsource.info" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Site Email Address </td>
      <td><input name="siteemail" type="text" id="siteemail" value="noreply@tbsource.info" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td>Announce Url ( ..../announce.php) </td>
      <td><input name="announce" type="text" id="announce" value="http://tbsource.info/announce.php" size="40" maxlength="40" /></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><div align="center">
        <input name="install" type="submit" class="red" value="Install" />
      </div></td>
    </tr>
  </table>
  <p>&nbsp;</p>
</form>
	';  
}

?></table><?php



include('../include/secrets.php');
if( defined("TB_INSTALLED") )
{
	die('Already installed <a href="../index.php">INDEX</a>');
	exit;
}	
if (isset($_POST['install'])) { 
if( $_POST['install'] || $_GET['install'] )
{
	update_config();
	basic_query();
	insert_sysop();
	config();
	finale();
}
}
else
{
	step_1();
}
?>
