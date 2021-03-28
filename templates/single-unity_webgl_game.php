<?php

var_dump($post);
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Unity WebGL Player | New input system prototype</title>
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
    <script src="Build/UnityLoader.js"></script>
    <script>
        UnityLoader.instantiate("unityContainer", "Build/v0.0.1.json");
    </script>
</head>
<body>
<div id="unityContainer"></div>
</body>
</html>
