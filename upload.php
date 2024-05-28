<?php 
if (isset($_POST['submit']) && isset($_FILES['my_video'])) {
    include "dbconnect.php";
    $video_name = $_FILES['my_video']['name'];
    $tmp_name = $_FILES['my_video']['tmp_name'];
    $error = $_FILES['my_video']['error'];

    if ($error === 0) {
        $video_ex = pathinfo($video_name, PATHINFO_EXTENSION);
        $video_ex_lc = strtolower($video_ex);
        $allowed_exs = array("mp4", 'webm', 'avi', 'flv');

        if (in_array($video_ex_lc, $allowed_exs)) {
            $new_video_name = uniqid("video-", true). '.' . $video_ex_lc;
            $video_upload_path = 'uploads/' . $new_video_name;
            move_uploaded_file($tmp_name, $video_upload_path);

            // Transcode videos to different qualities
            $command_144p = "ffmpeg -i $video_upload_path -c:v libx264 -preset fast -b:v 300k -s 256x144 -c:a aac -b:a 64k uploads/{$new_video_name}_144p.mp4";
            $command_480p = "ffmpeg -i $video_upload_path -c:v libx264 -preset fast -b:v 1500k -s 854x480 -c:a aac -b:a 128k uploads/{$new_video_name}_480p.mp4";
            $command_720p = "ffmpeg -i $video_upload_path -c:v libx264 -preset fast -b:v 3000k -s 1280x720 -c:a aac -b:a 128k uploads/{$new_video_name}_720p.mp4";
            $command_1080p = "ffmpeg -i $video_upload_path -c:v libx264 -preset fast -b:v 5000k -s 1920x1080 -c:a aac -b:a 128k uploads/{$new_video_name}_1080p.mp4";

            // Execute FFmpeg commands and log the output
            exec($command_144p . " 2>&1", $output_144p, $return_var_144p);
            exec($command_480p . " 2>&1", $output_480p, $return_var_480p);
            exec($command_720p . " 2>&1", $output_720p, $return_var_720p);
            exec($command_1080p . " 2>&1", $output_1080p, $return_var_1080p);

            // Log outputs for debugging
            file_put_contents('logs/ffmpeg_144p.log', implode("\n", $output_144p));
            file_put_contents('logs/ffmpeg_480p.log', implode("\n", $output_480p));
            file_put_contents('logs/ffmpeg_720p.log', implode("\n", $output_720p));
            file_put_contents('logs/ffmpeg_1080p.log', implode("\n", $output_1080p));

            if ($return_var_144p === 0 && $return_var_480p === 0 && $return_var_720p === 0 && $return_var_1080p === 0) {
                // Insert paths into the database
                $sql = "INSERT INTO videos (video_base_name, url, quality) VALUES 
                         ('$new_video_name', 'uploads/{$new_video_name}_144p.mp4', '144p'),
                        ('$new_video_name', 'uploads/{$new_video_name}_480p.mp4', '480p'),
                        ('$new_video_name', 'uploads/{$new_video_name}_720p.mp4', '720p'),
                        ('$new_video_name', 'uploads/{$new_video_name}_1080p.mp4', '1080p')";
                mysqli_query($conn, $sql);

                header("Location: view.php");
            } else {
                // Log error
                $em = "Error during transcoding";
                header("Location: index.php?error=$em");
            }
        } else {
            $em = "You can't upload files of this type";
            header("Location: index.php?error=$em");
        }
    }
} else {
    header("Location: index.php");
}
?>
