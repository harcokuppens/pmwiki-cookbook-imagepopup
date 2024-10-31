<?php if (!defined('PmWiki')) exit();

//
// (:imagepopup [class="small-image"] [closebutton="true"] [width="400px"] [height="auto"] [padding="15px"] [margin="20px"] [wrapcount="0"]:)
//

# define js and css files
#------------------------
SDV( $ImagePopup_PubDirUrl, "$PubDirUrl/imagepopup" );
SDV( $ImagePopup_CSS, "$ImagePopup_PubDirUrl/imagepopup.css" );
SDV( $ImagePopup_JS, "$ImagePopup_PubDirUrl/imagepopup.js" );


$optional_options='(?:\s+([^\n]*?))?';
$imagepopup='\(:imagepopup' . $optional_options . ':\)';

Markup('imagepopup', 'directives', '/' . $imagepopup  . '/', "mu_imagepopup");

function mu_imagepopup($m){
    global $ImagePopup_JS,$ImagePopup_CSS,$HTMLStylesFmt,$HTMLHeaderFmt,$HTMLFooterFmt;
    
    //  get parameters  
    // -----------------

    $unparsed_args = $m[1];

    // parse arguments in directive .
	$args = ParseArgs($unparsed_args); 

    // get the class given as argument 
    $img_class="small-image";
    if (!empty($args['class'])) {     
        $img_class=$args['class']; 
    } 

    $width="400px";
    if (!empty($args['width'])) {     
        $width=$args['width']; 
        // remove all units:  10px becomes 10  (we always assume px)
        $width = preg_replace("/[^0-9]/", "", $width ); 
    }
     
    $height="auto";
    if (!empty($args['height'])) {     
        $height=$args['height']; 
        // remove all units:  10px becomes 10  (we always assume px)
        $height = preg_replace("/[^0-9]/", "", $height ); 
    } 

    $padding="15px";
    if (!empty($args['padding'])) {     
        $padding=$args['padding'];
        // remove all units:  10px becomes 10  (we always assume px)
        $padding = preg_replace("/[^0-9]/", "", $padding ); 
    }

    $showCloseButton="true";
    if (!empty($args['closebutton'])) {     
        $showCloseButton=$args['closebutton']; 
    } 

    $popupMargin="20";
    if (!empty($args['margin'])) {     
        $popupMargin=$args['margin']; 
        // remove all units:  10px becomes 10  (we always assume px)
        $popupMargin = preg_replace("/[^0-9]/", "", $popupMargin );
    } 

    $wrapCount="0";
    if (!empty($args['wrapcount'])) {     
        $wrapCount=$args['wrapcount']; 
    } 

    //  css, js and html  
    // -----------------

    // load css and javascript files needed for imagepopup
    if (!empty( $ImagePopup_CSS )) $HTMLHeaderFmt['imagepopup-css'] = "\n<link rel='stylesheet' type='text/css' href='$ImagePopup_CSS' />";
    
    if (!empty( $ImagePopup_JS )) {
         $HTMLFooterFmt['imagepopup-js'] ="\n<script type='text/javascript' src='$ImagePopup_JS'></script>";
         $HTMLFooterFmt['imagepopup-js2'] ="\n<script type='text/javascript'>\ndocument.addEventListener('DOMContentLoaded', () => { initModalImage('$img_class',$showCloseButton,$popupMargin,$wrapCount); });\n</script>";
    }

    // set size defaults for image in popup
    $HTMLStylesFmt['imagepopup'] = $HTMLStylesFmt['imagepopup-css'] . <<<EOT
    #large-image{
        width: {$width}px;
        height: {$height}px;
    }
    #image_popup{
        padding: {$padding}px;
    }
    EOT;

    $html =  <<<EOT

    <!-- // image popup modal -->
    <div id="image_popup">
      <div id="close-btn-area">
        <button id="close-btn">X</button> 
      </div>
      <div id="image-show-area">
        <img id="large-image" src="" alt="large image">
      </div>
    </div>

    EOT;


    $HTMLFooterFmt['imagepopup-html'] = $html;

}
