<?php

$unityLoader = get_post_meta($post->ID, 'unity_loader', true);
$gameJson = get_post_meta($post->ID, 'game_json', true);
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Unity WebGL Player</title>
    <style>
        body {
            padding: 0;
            margin: 0;
        }

        #unityContainer {
            position: absolute;
            width: 100%;
            height: 100%;
        }
    </style>
    <script src="<?php echo $unityLoader['url']; ?>"></script>
    <script>
        UnityLoader.instantiate("unityContainer", "<?php echo $gameJson['url']; ?>");
    </script>
</head>
<body>
    <div id="unityContainer"></div>
</body>
</html>

<?php
get_footer();
