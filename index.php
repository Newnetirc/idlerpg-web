<?php
    include("config.php");
    include("commonfunctions.php");
    $irpg_page_title = "World Map";
    include("header.php");
?>
      <h1>Welcome</h1>
    <p>The Idle RPG is just what it sounds like: an RPG in which the players
    idle. In addition to merely gaining levels, players can find items and
    battle other players. However, this is all done for you; you just idle.
    There are no set classes; you can name your character anything you like, and
    have its class be anything you like, as well.</p>
<div id="main-content">

  <div id="map-container">
    <h1>World Map</h1>
    <p>[Offline users are red, online users are blue]</p>
    <div id="map">
        <img src="makeworldmap.php" alt="IdleRPG World Map" title="IdleRPG World Map" usemap="#world" border="0" />
        <map id="world" name="world">
    <?php
        $file = fopen($irpg_db,"r");
        fgets($file);
        while($location=fgets($file)) {
            list($who,,,,,,,,,,$x,$y) = explode("\t",trim($location));
            print "        <area shape=\"circle\" coords=\"".$x.",".$y.",4\" alt=\"".htmlentities($who).
                  "\" href=\"playerview.php?player=".urlencode($who)."\" title=\"".htmlentities($who)."\" />\n";
        }
        fclose($file);
    ?>
        </map>
    </div>
  </div>

  <div id="player-list">
    <h1>Players</h1>
    <h2>Pick a player to view</h2>
    <p class="small">[gray=offline]</p>
    <ol>
    <?php
        $file = file($irpg_db);
        unset($file[0]);
        usort($file, 'cmp_level_desc');
        foreach ($file as $line) {
            list($user,,,$level,$class,$secs,,,$online) = explode("\t",trim($line));

            $class = htmlentities($class);
            $next_level = duration($secs);

            print "    <li".(!$online?" class=\"offline\"":"")."><a".
                  (!$online?" class=\"offline\"":"").
                  " href=\"playerview.php?player=".urlencode($user).
                  "\">".htmlentities($user).
                  "</a>, the level $level $class. Next level in $next_level.</li>\n";

        }
    ?>
    </ol>
  </div>
</div>

<?php include("footer.php"); ?>

