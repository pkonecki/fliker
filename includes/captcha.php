<div align="center">
<p>Votre compte est presque créé, pour des raisons de sécurité merci de bien vouloir recopier le code suivant dans le champ de texte.</p>
<form action="inscription.php?<?PHP echo SID; ?>" method="post">
<table cellpadding=1>
  <tr><td align="center"><?php dsp_crypt(0,1); ?></td></tr>
  <tr><td align="center">Recopier le code:<br><input type="text" name="code"></td></tr>
  <tr><td align="center"><input type='hidden' name='action' value='check_code' /><input type="submit" name="submit" value="Envoyer"></td></tr>
</table>
</form>
</div>