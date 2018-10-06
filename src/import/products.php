<?php

function getPages($categoryPath, $categoryId, $url)
{
    $site='http://www.romanphil.com/';
    $url=substr($url, 0, strlen($url)-1);
    for ($i=1; $i< 10 ; $i++) {
        $url=$url . $i;
        if (!getProducts($categoryPath, $categoryId, $url)) {
            break;
        } else {
            $url=substr($url, 0, strlen($url)-1);
        }
    }
}

function getProducts($categoryPath, $categoryId, $url)
{
    $isFound=false;
    $site='http://www.romanphil.com/';
    $html = file_get_html($site.$url);

    if ($html) {
        $products = $html->find('form');
        foreach ($products as $product) {
            $isFound=true;

            $sku =$product->find('[name*=idProdotto]', 0)->value;
            $codice= cleanString($product->find('p', 0)->plaintext);
            $title= cleanString($product->find('p', 1)->plaintext);
            $descrizione= cleanString($product->find('p', 2)->plaintext);
            $catalogo = estraiCatalogo($product->plaintext);
            $price=estraiPrice($product);
            $excerpt="<em><a href='$site$url'>".$codice. "</a></em><br/>";
            //$excerpt="<b>$codice</b><br/>";
            $excerpt.=$descrizione."<br/><br/>";
            $excerpt.=$catalogo;
            $content=$descrizione;

            foreach ($product->find('img') as $img) {
                $check="gestione/img/prodotti/big/";
                if (substr($img->src, 0, 26)==$check) {
                    $imgSrc=$site.$img->src;
                    $imgAlt=cleanString(strip_tags($img->alt));
                    $imgTitle=cleanString(strip_tags($img->title));
                }
            }


            $imgAlt = preg_replace('/[^(\x20-\x7F)]*/', '', $imgAlt);
            $imgTitle = preg_replace('/[^(\x20-\x7F)]*/', '', $imgAlt);

            //echo "--------------------------------------\n";
            //echo "URL:". $site.$url ."\n";
            //echo "CATEGORY PATH: $categoryPath\n";
            //echo "CATEGORY_ID: $categoryId \n" ;
            //echo "ID_PRODOTTO: $idProdotto \n";
            //echo "SKU: $sku \n";
            //echo "TITLE: $title \n";
            // echo "CONTENT: $content \n";
            //echo "CATALOGO: $catalogo \n";
            //echo "PRICE: $price \n";
            //echo "IMG_SRC: $imgSrc \n";
            // echo "IMG_ALT: $imgAlt \n";
            // echo "IMG_TITLE: $imgTitle \n";
            //echo "--------------------------------------\n";
            $postId=postInsert($title, $content, $excerpt);
            postInsertMeta($postId, $sku, $price);
            if (!addImage($imgSrc, $postId, $imgAlt)){
              //echo "<li><a href='http://127.0.0.1/wordpress/wp-admin/post.php?post=$postId&action=edit' target='_blank'>$postId</a> ";
              //echo "<a href='$imgSrc' target='_blank'>image</a></li>";
              echo "productId: $postId NON inserito!\n";
            };
            wp_set_object_terms($postId, intval($categoryId), 'product_cat');
        }
    }
    return $isFound;
}

function cleanString($string)
{
    return rtrim(ltrim(spacesRemove(strip_tags($string))));
}

/*
function cleanString($input) {
  $input=rtrim(ltrim(spacesRemove(strip_tags($input))));
	$input = trim(preg_replace('/\s*\([^)]*\)/', '', $input));
	$input = preg_replace('/[^a-zA-Z0-9]/s', '', $input);
	return strtolower($input);
}
*/

function estraiPrice($product)
{
    $price =$product->find('td[align="right"]', 0)->outertext;
    $htmlPrice=str_get_html($price);
    $price =$htmlPrice->find('b', 0)->plaintext;
    $pos=strrpos($price, "€");
    $price=substr($price, 0, $pos-1);
    $price= str_replace('.', '', $price);
    $price= str_replace(',', '.', $price);

    return $price;
}

function estraiCatalogo($text)
{
    $pos = strpos($text, "Catalogo:");
    $text=substr($text, $pos);
    $text=substr($text, 0, 30);
    return $text;
}

function postInsert($title, $content, $excerpt)
{
    global $pdo;

    $author = 1;
    $postDate = 'NOW()';
    $postDateGmt = 'NOW()';
    $postContent = $content;
    $postTitle = $title;
    $postExcerpt = $excerpt;
    $postStatus = 'publish'; // draft / pending / publish
    $commentStatus = 'open';
    $pingStatus = 'open';
    $postPassword = '';
    //$postName = postSlug($content);
    $postName = postSlug($title);
    $toPing = '';
    $pinged = '';
    $postModified = 'NOW()';
    $postModifiedGmt = 'NOW()';
    $postContentFiltered = '';
    $postParent = 0;
    $guid = '';
    $menuOrder = 0;
    $postType = 'product';
    $postMimeType = '';
    $commentCount = 0;

    $sql = '
      INSERT INTO wp_posts (
        post_author,
        post_date,
        post_date_gmt,
        post_content,
        post_title,
        post_excerpt,
        post_status,
        comment_status,
        ping_status,
        post_password,
        post_name,
        to_ping,
        pinged,
        post_modified,
        post_modified_gmt,
        post_content_filtered,
        post_parent,
        guid,
        menu_order,
        post_type,
        post_mime_type,
        comment_count) VALUES
      ('.
        $pdo->quote($author).', '.
        $postDate.', '.
        $postDateGmt.', '.
        $pdo->quote($postContent).', '.
        $pdo->quote($postTitle).', '.
        $pdo->quote($postExcerpt).', '.
        $pdo->quote($postStatus).', '.
        $pdo->quote($commentStatus).', '.
        $pdo->quote($pingStatus).', '.
        $pdo->quote($postPassword).', '.
        $pdo->quote($postName).', '.
        $pdo->quote($toPing).', '.
        $pdo->quote($pinged).', '.
        $postModified.', '.
        $postModifiedGmt.', '.
        $pdo->quote($postContentFiltered).', '.
        $pdo->quote($postParent).', '.
        $pdo->quote($guid).', '.
        $pdo->quote($menuOrder).', '.
        $pdo->quote($postType).', '.
        $pdo->quote($postMimeType).', '.
        $pdo->quote($commentCount).
      ');';
    $sql .= "SELECT LAST_INSERT_ID();";
    $stml = $pdo->prepare($sql);
    $stml->execute();

    $last_inserted = $pdo->lastInsertId();
    $guid = get_site_url()."?post_type=product&#038;p=$last_inserted";
    $sql = "UPDATE wp_posts
              SET guid='$guid'
              WHERE id=$last_inserted";
    $stml = $pdo->prepare($sql);
    $stml->execute();
    return $last_inserted;
}

function postInsertMeta($postId, $sku, $price)
{
    global $pdo;

    if ($price=="0") {
        $price="";
    }

    $backorders = '0';
    $crosssell_ids = 'a:0:{}';
    $downloadable = 'no';
    $editLast = '1';
    $editLock = '0';
    $featured = 'no';
    $height = '';
    $length = '';
    $manageStock = 'no';
    $price = $price;
    $productAttributess = '0';
    $productImageGallery = '';
    $productVersion = '3.3.3'; //2.6.6
    $purchaseNote = '';
    $regularPrice = $price;
    $salePrice = '';
    $salePriceDatesFrom = '';
    $salePriceDatesTo = '';
    $sku = $sku;
    $sold_individually = '';
    $stock = '';
    $stockStatus = 'instock';
    $taxClass = '0';
    $taxStatus = '0';
    $upsellIds = 'a:0:{}';
    $virtual = 'no';
    $visibility = 'visible';
    $weight = '';
    $width = '';

    postInsertMetaRow($postId, '_backorders', $backorders);
    postInsertMetaRow($postId, '_crosssell_ids', $crosssellIds);
    postInsertMetaRow($postId, '_downloadable', $downloadable);
    postInsertMetaRow($postId, '_edit_last', $editLast);
    postInsertMetaRow($postId, '_edit_lock', $editLock);
    postInsertMetaRow($postId, '_featured', $featured);
    postInsertMetaRow($postId, '_height', $height);
    postInsertMetaRow($postId, '_length', $length);
    postInsertMetaRow($postId, '_manage_stock', $manageStock);
    postInsertMetaRow($postId, '_price', $price);
    postInsertMetaRow($postId, '_product_attributess', $productAttributess);
    postInsertMetaRow($postId, '_product_image_gallery', $productImageGallery);
    postInsertMetaRow($postId, '_product_version', $productVersion);
    postInsertMetaRow($postId, '_purchase_note', $purchaseNote);
    postInsertMetaRow($postId, '_regular_price', $regularPrice);
    postInsertMetaRow($postId, '_sale_price', $salePrice);
    postInsertMetaRow($postId, '_sale_price_dates_from', $salePriceDatesFrom);
    postInsertMetaRow($postId, '_sale_price_dates_to', $salePriceDatesTo);
    postInsertMetaRow($postId, '_sku', $sku);
    postInsertMetaRow($postId, '_sold_individually', $soldIndividually);
    postInsertMetaRow($postId, '_stock', $stock);
    postInsertMetaRow($postId, '_stock_status', $stockStatus);
    postInsertMetaRow($postId, '_tax_class', $taxClass);
    postInsertMetaRow($postId, '_tax_status', $taxStatus);
    postInsertMetaRow($postId, '_upsell_ids', $upsellIds);
    postInsertMetaRow($postId, '_virtual', $virtual);
    postInsertMetaRow($postId, '_visibility', $visibility);
    postInsertMetaRow($postId, '_weight', $weight);
    postInsertMetaRow($postId, '_width', $width);
}

function postStatusSet($postId, $status)
{
    global $pdo;

    $sql = "UPDATE wp_posts SET post_status='$status' WHERE ID=$postId";
    $stml = $pdo->prepare($sql);
    $stml->execute();
}

function postInsertMetaRow($post_id, $meta_key, $meta_value)
{
    global $pdo;

    $sql = "INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES
  (
      -- meta_id
      $post_id ,    -- post_id
      '$meta_key',    -- meta_key
      '$meta_value'    -- meta_value
  )";

    $stml = $pdo->prepare($sql);
    $stml->execute();
}
