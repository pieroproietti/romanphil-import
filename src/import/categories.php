<?php

function getCategories($html)
{
    $categories=$html->find('a');

    foreach ($categories as $category) {
        $currentName=rtrim(ltrim(strtolower(strip_tags($category->plaintext))));
        //echo $currentName . "\n";
        $url=$category->href;
        switch ($category->href) {
        case '#':
          if ($currentName=="filatelia" || $currentName=="numismatica" || $currentName=="gadget" ||  $currentName=="materiali" || $currentName=="offerte" || $currentName=="novita'") {
              $rootCategoryName=ucwords($currentName);
              $rootCategoryId=addCategory($rootCategoryName, 0);
          } else {
              $subCategoryName=ucwords($currentName);
              $subCategoryId=addCategory($subCategoryName, $rootCategoryId);
          }
          break;

        default:
          if (substr($category->href, 0, 18)=="Lista_Prodotti.asp") {
              $categoryName=spacesRemove(ucwords($currentName));
              $categoryId=addCategory($categoryName, $subCategoryId);
              $categoryPath=categoryPath($rootCategoryName, $subCategoryName, $categoryName);

              $categoryPath=trim($categoryPath);
              getPages($categoryPath, $categoryId, $url);
              break;
          }
        }
    }
}

function categoryPath($rootCategoryName, $subCategoryName, $categoryName)
{
    $cp= "/";
    $cp.=$rootCategoryName;
    $cp.="/";
    $cp.=$subCategoryName;
    $cp.="/";
    $cp.=$categoryName;
    $cp.="\n";
    return $cp;
}

function addCategory($name, $parent)
{
    $termId=0;
    if ($name<>"") {
        $order = '0';
        $slug = postSlug($name);
        $termId = addTerms($name, $parent);
        addCategoryTermMeta($termId);
        addCategoryTaxonomy($termId, $parent);
    }
    return $termId;
}

function addTerms($name, $parent)
{
    global $pdo;
    $slug = postSlug($name);
    $sql = "SELECT name, slug FROM wp_terms WHERE name='$name'";
    if ($rows = $pdo->query($sql)) {
        if ($rows->rowCount() > 0) {
            $sql = "SELECT name FROM wp_terms WHERE term_id=$parent";
            $stml = $pdo->prepare($sql);
            $stml->execute();
            while ($row = $stml->fetch(PDO::FETCH_ASSOC)) {
                $slug .= "-" . postSlug($row["name"]);
                break;
            }
        }
    }
    //echo 'name: '.$name."\r\n";
    $termGroup = '0';
    $name=addslashes($name);
    $sql = "INSERT INTO `wp_terms` (`name`, `slug`, `term_group`) VALUES('$name', '$slug', $termGroup);";
    $sql .=$sql ."SELECT LAST_INSERT_ID();";
    //echo $sql."\n\r";
    $stml = $pdo->prepare($sql);
    $stml->execute();
    $lastInsertId = $pdo->lastInsertId();
    return $lastInsertId;
}

function addCategoryTermMeta($termId)
{
    global $pdo;
    $metaKey = 'order';
    $metaValue = '0';
    $sql = "INSERT INTO `wp_termmeta` (`term_id`, `meta_key`, `meta_value`) VALUES   ($termId, '$metaKey', '$metaValue');";
    $stml = $pdo->prepare($sql);
    $stml->execute();
    $metaKey = 'display_type';
    $metaValue = '0';
    $sql = "INSERT INTO `wp_termmeta` (`term_id`, `meta_key`, `meta_value`) VALUES   ($termId, '$metaKey', '$metaValue');";
    $stml = $pdo->prepare($sql);
    $stml->execute();
    $metaKey = 'thumbnail_id';
    $metaValue = '0';
    $sql = "INSERT INTO `wp_termmeta` (`term_id`, `meta_key`, `meta_value`) VALUES   ($termId, '$metaKey', '$metaValue');";
    $stml = $pdo->prepare($sql);
    $stml->execute();
}

function addCategorytaxonomy($termId, $parent)
{
    global $pdo;
    $count = 0;
    $taxonomy = 'product_cat';
    $description = '';
    $sql = "INSERT INTO `wp_term_taxonomy` (`term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES ($termId, '$taxonomy', '$description', $parent, $count);";
    $stml = $pdo->prepare($sql);
    $stml->execute();
}

function addTermRelationships()
{
    global $pdo;
    $sql = "INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES ($objectId, $termTaxonomyId, $order);";
}
/*
wp_termmeta
wp_terms
wp_term_relationships
wp_term_taxonomy
*/
