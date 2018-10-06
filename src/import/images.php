<?php
require '.auth.php';

// Need to require these files
if (!function_exists('media_handle_upload')) {
    require_once 'wp-admin'.'/includes/image.php';
    require_once 'wp-admin'.'/includes/file.php';
    require_once 'wp-admin'.'/includes/media.php';
}

function addImage($urlImg, $productId, $description)
{
    $html = media_sideload_image($urlImg, $productId, $description);
    if (!is_wp_error($html)) {
        $inserted = true;
        $images = get_attached_media('image', $productId);
        foreach ($images as $image) {
            $thumbnailId=$image->ID;
            break;
        }
        update_post_meta($productId, '_thumbnail_id', $image->ID);
    } else {
        scarica($urlImg, $productId);

        $inserted=false;
        $urlDownload='http://192.168.61.2/images/';
        $imageName = newName($urlImg, $productId);
        $urlName=$urlDownload . $imageName;
        $description=strtolower($description);
        $html = media_sideload_image($urlName, $productId, $description);
        if (!is_wp_error($html)) {
            $images = get_attached_media('image', $productId);
            foreach ($images as $image) {
                $thumbnailId=$image->ID;
                break;
            }
            update_post_meta($productId, '_thumbnail_id', $image->ID);
        }
        // echo "Post: [$productId]\n";
        // echo "Url:  [$urlName]\n";
        //print_r($html);
    }
    return $inserted;
}

function scarica($urlImg, $productId)
{
    $pathUpload="./images/";
    $imageName = newName($urlImg, $productId);
    return copy($urlImg, $pathUpload. $imageName);
}


function newName($urlImg, $productId)
{
    $path_parts = pathinfo($urlImg);
    $ext = $path_parts['extension'];
    $paddedId=str_pad($productId, 4, "0", STR_PAD_LEFT);
    $newName=trim("img-product-" .$paddedId .".". $ext);
    return $newName;
}

//$ret =
