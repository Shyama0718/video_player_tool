<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Videos</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            min-height: 100vh;
        }
        video {
            width: 640px;
            height: 360px;
            margin: 10px;
        }
        a {
            text-decoration: none;
            color: #006CFF;
            font-size: 1.5rem;
            position: absolute;
            top: 10px;
            left: 10px;
        }
        h1 {
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
    <a href="index.php">UPLOAD</a>
    <div class="alb">
        <?php 
            include "dbconnect.php";

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT DISTINCT video_base_name FROM videos ORDER BY id DESC";
            $res = $conn->query($sql);

            if ($res->num_rows > 0) {
                while ($video = $res->fetch_assoc()) {
                    $baseName = $video['video_base_name'];
                    echo "<div>";
                    echo "<video id='video_$baseName' controls>";

                    $quality_sql = "SELECT * FROM videos WHERE video_base_name='$baseName'";
                    $quality_res = $conn->query($quality_sql);

                    while ($quality_row = $quality_res->fetch_assoc()) {
                        echo "<source src='" . $quality_row['url'] . "' type='video/mp4' data-quality='" . $quality_row['quality'] . "'>";
                    }

                    echo "</video>";
                    echo "<div>
                             <button onclick=\"changeQuality('$baseName', '144')\">144p</button>
                            <button onclick=\"changeQuality('$baseName', '480')\">480p</button>
                            <button onclick=\"changeQuality('$baseName', '720')\">720p</button>
                            <button onclick=\"changeQuality('$baseName', '1080')\">1080p</button>
                          </div>";
                    echo "</div>";
                }
            } else {
                echo "<h1>No videos found</h1>";
            }

            $conn->close();
        ?>
    </div>
    <script>
        function changeQuality(baseName, quality) {
            var video = document.getElementById('video_' + baseName);
            var sources = video.getElementsByTagName('source');
            for (var i = 0; i < sources.length; i++) {
                if (sources[i].getAttribute('data-quality') === quality) {
                    video.src = sources[i].src;
                    video.play();
                    break;
                }
            }
        }
    </script>
</body>
</html>
