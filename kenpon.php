<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);


function getPageManifest($URL) {
  
  $Page_Manifest_URL = $URL;
  $Page_Manifest_URL = str_replace('https://'.$_SERVER['HTTP_HOST'], '/.assets/content/pages', $URL);
  $Page_Manifest_URL =  $_SERVER['DOCUMENT_ROOT'].$Page_Manifest_URL;
  $Page_Manifest_URL .= '/page.json';

  $Page_Manifest_JSON = file_get_contents($Page_Manifest_URL);
  $Page_Manifest_Array = json_decode($Page_Manifest_JSON, TRUE);

  return $Page_Manifest_Array;

}



function findFirstPage($Previous_Page_URL) {

    $Page_Series_Array = array();

    while ($Previous_Page_URL !== FALSE) {
    
      if (in_array($Previous_Page_URL, $Page_Series_Array)) {

        echo '<p><strong>Error:</strong> Repeated <strong>Previous Page</strong> reference on <em>'.$Page_Series_Array[(count($Page_Series_Array) - 1)].'</em> Page Manifest</p>';

        return array_reverse($Page_Series_Array);  
      }

      $Page_Series_Array[] = $Previous_Page_URL;
      $First_Page_URL = $Previous_Page_URL;
      $Page_Manifest_Array = getPageManifest($Previous_Page_URL);
      $Previous_Page_URL = $Page_Manifest_Array['Document_Overview']['Document_Information']['Document_Series'][1]['Previous_Page'];
    }

  return $First_Page_URL;
}


function getPageSeries($Next_Page_URL) {
  
  $Page_Series_Array = array();

  while ($Next_Page_URL !== FALSE) {
    
    if (in_array($Next_Page_URL, $Page_Series_Array)) {

      echo '<p><strong>Error:</strong> Repeated <strong>Next Page</strong> reference on <em>'.$Page_Series_Array[(count($Page_Series_Array) - 1)].'</em> Page Manifest</p>';

      return $Page_Series_Array;  
    }

    $Page_Series_Array[] = $Next_Page_URL;
    $Page_Manifest_Array = getPageManifest($Next_Page_URL);
    $Next_Page_URL = $Page_Manifest_Array['Document_Overview']['Document_Information']['Document_Series'][1]['Next_Page'];
  }
  
  return $Page_Series_Array;
}


function renderPageCollection($Page_Series_Array) {

  $Page_Collection = '';
  $Page_Collection .= '<ol class="pageCollection">';

  for ($i = 0; $i < count($Page_Series_Array); $i++) {
  
    $Page_Collection .= '<li class="pageCollectionItem">';
    $Page_Collection .= '<a class="pageCollectionLink" href="'.$Page_Series_Array[$i].'" target="_blank">';
    $Page_Collection .= '<span class="pageCollectionTitle">'.getPageManifest($Page_Series_Array[$i])['Document_Overview']['Editorial_Elements']['Page_Heading'].'</span>';
    $Page_Collection .= '<span class="pageCollectionURL">'.$Page_Series_Array[$i].'</span>';
    $Page_Collection .= '</a></li>';
  }

  $Page_Collection .= '</ol>';

  return $Page_Collection;
}



$Submitted_URL  = '';
if (isset($_POST['startLink'])) {$Submitted_URL = $_POST['startLink'];}


echo '
<!doctype html>

<html lang="en-GB">
<head>
<meta charset="utf-8">
<title>Kenpon by Rounin Media</title>
<meta name="viewport" content="initial-scale=1.0" />

<style>
#POSITION,
.pageCollectionLink {
position: absolute;
}

.pageCollectionItem {
position: relative;
}

#DISPLAY,
body {
display: flex;
}

.startLinkLabelText,
.startLinkSubmit,
.pageCollectionLink,
.pageCollectionURL {
display: block;
}

#CO-ORDINATES,
.pageCollectionLink {
top: 0;
left: 0;
}

#FLEX-PARENT,
body {
justify-content: center;  
}

#WIDTH_HEIGHT,
.kenpon {
width: 800px;
}

.pageCollectionLink {
width: 100%;
height: 100%;
}

.startLinkLabelText,
.startLinkInput {
width: 100%;  
}

.startLinkFieldset,
.pageCollection {
width: 100%;
}

.pageCollectionItem {
height: 48px;
line-height: 24px;
}

#MARGIN_PADDING,
.startLinkSubmit {
margin: 24px auto;
}

.startLinkLabelText {
padding: 6px 0 12px;  
}

.pageCollection {
margin-left: 0;
padding-left: 0;
}

.pageCollectionItem {
padding: 0;
}

.pageCollectionLink {
padding-left: 36px;
}

#TEXT-COLOR,
.pageCollectionLink {
color: rgb(255, 255, 255);   
}

#TEXT-SIZE,
.pageCollectionItem {
font-size: 18px;
}

.pageCollectionURL {
font-size: 14px;
}

#TEXT-PRESENTATION,
.kenponHeading {
text-align: center;
}

.pageCollectionURL {
font-style: italic;
}

.startLinkLabel,
.pageCollectionTitle {
font-weight: 900;
}

.pageCollectionItem:hover .pageCollectionLink {
text-shadow: 2px 2px 2px rgb(191, 191, 255);   
}

#BORDER,
.pageCollection li {
border-top: 1px solid rgba(255, 255, 255, 0.2) ;
border-bottom: 1px solid rgba(0, 0, 0, 0.2);
}

.pageCollection li:first-of-type {
border-top: none;
}

.pageCollection li:last-of-type {
border-bottom: none;
}

#BACKGROUND,

.pageCollection li:nth-of-type(odd) {
background-color: rgb(63, 63, 227);
}

.pageCollection li:nth-of-type(even) {
background-color: rgb(63, 63, 255);
}

.pageCollection li:hover {
background-color: rgb(91, 91, 255);
}

#ELEMENT-PRESENTATION,
.pageCollection {
overflow: hidden;
}

.pageCollectionItem {
white-space: nowrap;
}

.pageCollectionLink {
text-decoration: none;   
}

.pageCollectionItem:hover {
cursor: pointer;
}

.pageCollectionItem:hover .pageCollectionTitle {
text-decoration: underline;   
}

.pageCollectionItem:hover .pageCollectionURL {
text-decoration: none;   
}




.pageCollection {
  counter-reset: section;
  list-style-type: none;
}

.pageCollectionItem::before {
  content: counter(section) ".";
  counter-increment: section;
  position: absolute;
  display: block;
  top: 0;
  left: 0;
  z-index: 12;
  height: 24px;
  margin: -2px 12px 0 0;
  padding: 13px 6px;
  color: rgb(255, 255, 255);
  font-size: 24px;
  font-weight: 900;
  background-color: rgba(0, 0, 0, 0.3);
}

</style>

</head>

<body>

<form class="kenpon" method="post" action="https://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'">

<h1 class="kenponHeading">Kenpon by Rounin Media</h1>

<fieldset class="startLinkFieldset">
<label class="startLinkLabel">
<span class="startLinkLabelText">Enter Start URL here:</span>
<input type="url" class="startLinkInput" name="startLink" value="'.$Submitted_URL .'" placeholder="Enter Start URL Here..." required />
</label>
</fieldset>

<input type="submit" class="startLinkSubmit" value="Submit Start Link" />
';


if ($Submitted_URL  != '') {

  $Page_Manifest = str_replace('https://'.$_SERVER['HTTP_HOST'], $_SERVER['DOCUMENT_ROOT'].'/.assets/content/pages/', $Submitted_URL).'/page.json';

  if (!file_exists($Page_Manifest)) {

    echo '<p><strong>Error:</strong> <em>This URL does not exist.</em></p>';
  }

  else {

    $Page_Manifest_Array = getPageManifest($Submitted_URL);

    if ($Page_Manifest_Array['Document_Overview']['Document_Information']['Document_Series'][0] !== TRUE) {

      echo '<p><strong>Error:</strong> <em>This Page is not part of a Page Series.</em></p>';
    }

    else {
    
      $First_Page_URL = $Submitted_URL ;
      $Previous_Page_URL = $Page_Manifest_Array['Document_Overview']['Document_Information']['Document_Series'][1]['Previous_Page'];

      if ($Previous_Page_URL !== FALSE) {

        $First_Page_URL = findFirstPage($Previous_Page_URL);
      }

      if (!is_array($First_Page_URL)) {

        $Page_Series_Array = getPageSeries($First_Page_URL);
      }

      else {

        $Page_Series_Array = $First_Page_URL;      
      }


      echo renderPageCollection($Page_Series_Array);
    }
  }
}

echo '
</form>

<script>



</script>

</body>
</html>
';

?>


