<?php
    include("config.php");
    include("commonfunctions.php");

    $_GET['player'] = substr($_GET['player'], 0, 30);

    if ($_GET['player'] == "") {
        header('Location: http://' . $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '') . $BASEURL . 'players.php');
    }

    $irpg_page_title = "Player Info: " . htmlentities($_GET['player']);
    $showmap = $_GET['showmap'];

    include("header.php");
?>

<style>
    .card {
        border: 1px solid #ccc;
        padding: 16px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .status-online {
        color: green;
    }
    .status-offline {
        color: red;
    }
    .alignment-good {
        color: blue;
    }
    .alignment-evil {
        color: red;
    }
    .alignment-neutral {
        color: grey;
    }
</style>

<?php
    $file = fopen($irpg_db, "r");
    fgets($file, 1024); // skip top comment
    $found = 0;
    while ($line = fgets($file, 1024)) {
        if (substr($line, 0, strlen($_GET['player']) + 1) == $_GET['player'] . "\t") {
            list($user, , $isadmin, $level, $class, $secs, , $uhost, $online, $idled,
                 $x, $y,
                 $pen['mesg'],
                 $pen['nick'],
                 $pen['part'],
                 $pen['kick'],
                 $pen['quit'],
                 $pen['quest'],
                 $pen['logout'],
                 $created,
                 $lastlogin,
                 $item['amulet'],
                 $item['charm'],
                 $item['helm'],
                 $item['boots'],
                 $item['gloves'],
                 $item['ring'],
                 $item['leggings'],
                 $item['shield'],
                 $item['tunic'],
                 $item['weapon'],
                 $alignment,
            ) = explode("\t", trim($line));
            $found = 1;
            break;
        }
    }
?>

<?php if (!$found): ?>
    <h1>Error</h1><p><b>No such user.</b></p>
<?php else: ?>

<div class="card">
    <h1>Player Info</h1>
    <p><b>User:</b> <?= htmlentities($user) ?></p>
    <p><b>Class:</b> <?= htmlentities($class) ?></p>
    <p><b>Admin?:</b> <?= $isadmin ? "Yes" : "No" ?></p>
    <p><b>Level:</b> <?= $level ?></p>
    <p><b>Next level:</b> <?= duration($secs) ?></p>
    <p><b>Status:</b> <?= $online ? "Online" : "Offline" ?></p>
    <p><b>Host:</b> <?= $uhost ? $uhost : "Unknown" ?></p>
    <p><b>Account Created:</b> <?= date("D M j H:i:s Y", $created) ?></p>
    <p><b>Last login:</b> <?= date("D M j H:i:s Y", $lastlogin) ?></p>
    <p><b>Total time idled:</b> <?= duration($idled) ?></p>
    <p><b>Current position:</b> [<?= $x ?>,<?= $y ?>]</p>
    <p><b>Alignment:</b> <?= $alignment == 'e' ? "Evil" : ($alignment == 'n' ? "Neutral" : "Good") ?></p>
    <p><b>XML:</b> [<a href="xml.php?player=<?= urlencode($user) ?>">link</a>]</p>
    <p><?= $showmap ? "<div id=\"map\"><img src=\"makemap.php?player=" . urlencode($user) . "\"></div>" : "<a href=\"?player=" . urlencode($user) . "&showmap=1\">Show map</a>" ?></p>
</div>

<div class="card">
    <h2>Items</h2>
    <?php
        ksort($item);
        $sum = 0;
        foreach ($item as $key => $val) {
                     $valInt = intval($val);  // Extract integer value
                     $sum += $valInt;  // Add to sum
            $uniquecolor="#be9256";
            if ($key == "helm" && substr($val,-1,1) == "a") {
                $val = intval($val)." [<font color=\"$uniquecolor\">Mattt's Omniscience Grand Crown</font>]";
            }
            if ($key == "tunic" && substr($val,-1,1) == "b") {
                $val = intval($val)." [<font color=\"$uniquecolor\">Res0's Protectorate Plate Mail</font>]";
            }
            if ($key == "amulet" && substr($val,-1,1) == "c") {
                $val = intval($val)." [<font color=\"$uniquecolor\">Dwyn's Storm Magic Amulet</font>]";
            }
            if ($key == "weapon" && substr($val,-1,1) == "d") {
                $val = intval($val)." [<font color=\"$uniquecolor\">Jotun's Fury Colossal Sword</font>]";
            }
            if ($key == "weapon" && substr($val,-1,1) == "e") {
                $val = intval($val)." [<font color=\"$uniquecolor\">Drdink's Cane of Blind Rage</font>]";
            }
            if ($key == "boots" && substr($val,-1,1) == "f") {
                $val = intval($val)." [<font color=\"$uniquecolor\">Mrquick's Magical Boots of Swiftness</font>]";
            }
            if ($key == "weapon" && substr($val,-1,1) == "g") {
                $val = intval($val)." [<font color=\"$uniquecolor\">Jeff's Cluehammer of Doom</font>]";
            }
            if ($key == "ring" && substr($val,-1,1) == "h") {
                $val = intval($val)." [<font color=\"$uniquecolor\">Juliet's Glorious Ring of Sparkliness</font>]";
            }            echo "<p><b>$key:</b> $val</p>";
        }
        echo "<p><b>Sum:</b> $sum</p>";
    ?>
</div>

<div class="card">
    <h2>Penalties</h2>
    <?php
        ksort($pen);
        $sum = 0;
        foreach ($pen as $key => $val) {
                      $sum += $val;  // Add to sum
            echo "<p><b>$key:</b> " . duration($val) . "</p>";
        }
        echo "<p><b>Total:</b> " . duration($sum) . "</p>";
    ?>
</div>

<div class="card">
    <h2>Character Modifiers</h2>
    <?php
        $file = fopen($irpg_mod, "r");
        $temp = array();
        while ($line = fgets($file, 1024)) {
            if (strstr($line, " " . $_GET['player'] . " ") ||
                strstr($line, " " . $_GET['player'] . ", ") ||
                substr($line, 0, strlen($_GET['player']) + 1) == $_GET['player'] . " " ||
                substr($line, 0, strlen($_GET['player']) + 3) == $_GET['player'] . "'s ") {
                array_push($temp, $line);
            }
        }
        fclose($file);
        if (!is_null($temp) && count($temp)) {
            if ($_GET['allmods'] == 1 || count($temp) < 6) {
                foreach ($temp as $line) {
                    echo "<p>" . htmlentities(trim($line)) . "</p>";
                }
            } else {
                end($temp);
                for ($i = 0; $i < 4; ++$i) prev($temp);
                for ($line = trim(current($temp)); $line; $line = trim(next($temp))) {
                    echo "<p>" . htmlentities(trim($line)) . "</p>";
                }
            }
        }
        if ($_GET['allmods'] != 1 && count($temp) > 5) {
            echo "<p>[<a href=\"" . $_SERVER['PHP_SELF'] . "?player=" . urlencode($user) . "&amp;allmods=1\">View all Character Modifiers</a> (" . count($temp) . ")</p>";
        }
    ?>
</div>

<?php endif; ?>

<?php
    include("footer.php");
?>
