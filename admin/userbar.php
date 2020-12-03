<script>
  var $buoop = {
    required:{e:-5,f:-3,o:-3,s:-1,c:-3},
    insecure:true,
    api:2020.02,
    text: {
       'msg':'Je web browser ({brow_name}) is niet meer up-to-date.',
       'msgmore': 'We kunnen geen correcte werking garanderen voor deze browser. Update je browser voor een veiligere, snellere en betere ervaring op deze website.',
       'bupdate': 'Update browser',
       'bignore': 'Negeren',
       'remind': 'Je krijgt hiervan een herinnering binnen {days} dagen.',
       'bnever': 'Nooit meer tonen.'
    }
  };
  function $buo_f(){
   var e = document.createElement("script");
   e.src = "//browser-update.org/update.min.js";
   document.body.appendChild(e);
  };
  try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
  catch(e){window.attachEvent("onload", $buo_f)}
</script>

<?php
if(!$userInfo):
  $auth0->login();
else:
?>
  <div class="bg-warning p-3 text-white d-flex justify-content-between" style="font-size:12px">
    <div class="">
      <?php if($_SERVER['PHP_SELF'] == "/admin/aanpassen.php"): ?>
        <a href="/admin/index.php?">&larr; Terug naar overzicht</a>
      <?php elseif($_SERVER['PHP_SELF'] == "/admin/createuser.php"): ?>
        <a href="/admin/users.php">&larr; Terug naar overzicht</a>
      <?php elseif($_SERVER['PHP_SELF'] != "/admin/index.php"): ?>
        <a href="/admin/index.php">&larr; Terug naar beginscherm</a>
      <?php endif; ?>
    </div>
    <div>Ingelogd als <?php echo $userInfo['name']; ?> &mdash; <a href="logout.php">Uitloggen</a></div>
  </div>
<?php
endif;
