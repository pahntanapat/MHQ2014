<table border="0" align="center">
  <tr>
    <td rowspan="2"><img src="securimage/securimage_show.php?sid=<?=md5(uniqid());?>" alt="CAPTCHA Image" name="siimage" id="siimage" /></td>
    <td><object type="application/x-shockwave-flash" data="securimage/securimage_play.swf?bgcol=#ffffff&amp;icon_file=securimage/images/audio_icon.png&amp;audio_file=securimage/securimage_play.php" height="48" width="48">
      <param name="movie" value="securimage/securimage_play.swf?bgcol=#ffffff&amp;icon_file=securimage/images/audio_icon.png&amp;audio_file=securimage/securimage_play.php" />
    </object></td>
  </tr>
  <tr>
    <td><img src="securimage/images/refresh.png" alt="Reload Image" name="reload_img" id="reload_img" align="bottom" border="0" /></td>
  </tr>
</table>
<p><label for="captcha">Answer = </label><input name="captcha" type="text" id="captcha" size="10" maxlength="8"  data-validation="number"  required><script src="captcha.js"></script></p>