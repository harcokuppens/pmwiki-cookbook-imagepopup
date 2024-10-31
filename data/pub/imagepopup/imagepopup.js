function initModalImage(smallImageClass, showCloseButton, popup_margin, wrapcount) {

  var small_images = document.getElementsByClassName(smallImageClass);
  var image_popup = document.getElementById("image_popup");
  var large_image = document.getElementById("large-image");

  function closePopup() {
    // before closing the popup window we need to unset the active image, by removing active class from every image
    for (var i = 0; i < small_images.length; i++) {
      small_images[i].classList.remove('active');
    }
    //close the popup window 
    image_popup.style.display = 'none'
  }

  if (showCloseButton) {
    var close_btn = document.getElementById("close-btn");
    close_btn.addEventListener("click", closePopup);
  } else {
    var close_btn_area = document.getElementById("close-btn-area");
    close_btn_area.style = "display:none;";
    image_popup.addEventListener("click", closePopup);
  }

  function handleClick(event) {

    // if current image is already then close popup and return
    if (this.classList.contains('active')) {
      closePopup();
      return;
    }

    // unset previous active image, by removing active class from every image
    for (var j = 0; j < small_images.length; j++) {
      small_images[j].classList.remove('active');
    }
    // make this image active 
    this.classList.add('active');

    var src_val = this.src;
    if (!src_val) {
      const imgNode = this.querySelector('img');
      src_val = imgNode.src;
      if (!src_val) {
        console.log('No image node found');
      }
    }
    large_image.src = src_val;

    //show Modal image 
    image_popup.style.display = 'block';


    /*
    placing algorithm for popup window 
     1) Determine bounding box of image on which image may not overlap. Bounding box is the wrapcount parent of the image.
        When wrapcount=0, the default, then the bounding box is the image itself.  
     2) Place the popup image with popup_margin distance to the right of the bounding_box if enough space , else to the left. 
        If at both sides there is not enough space, the popup will overlap the bounding box. For placing we choose the biggest space to minimize overlap, 
        and position the popup with popup_margin distance to the window. The popup will be fully visible but overlaps a bit the bounding box.
     3) Place the popup image such that its center is at some height as the center of the image clicked on. 
        Place the popup with popup_margin distance on top or bottom of window if it would be partly placed outside the top or bottom of the window.
        If there is not enough height to display the whole height of the popup image, then just display it centered causing only the sides of the popup window
        not  visible.
     */

    // size popup window
    widthX = image_popup.offsetWidth;
    heightY = image_popup.offsetHeight;

    // get bounding box around image on which we do not want popup box placed
    bb_element = event.target;
    counter = wrapcount;
    while (counter > 0) {
      bb_element = bb_element.parentNode;
      counter = counter - 1;
    }

    var viewportOffset = bb_element.getBoundingClientRect();
    // these are relative to the viewport, i.e. the window
    var bb_topleftY = viewportOffset.top;
    var bb_topleftX = viewportOffset.left;
    var bb_width = viewportOffset.width;
    var bb_height = viewportOffset.height;

    // calculate left position of popup
    spaceRight = window.innerWidth - (bb_topleftX + bb_width);
    spaceLeft = bb_topleftX;

    if (spaceRight > widthX) {
      // prefer placing on right side if enough space
      posX = bb_topleftX + bb_width + popup_margin;
    } else if (spaceLeft > widthX) {
      // else place on left side if enough space
      posX = bb_topleftX - widthX - popup_margin;
    } else if (spaceRight > spaceLeft) {
      // not enough space: we have at right more space then on left: stick to rightside of window
      posX = window.innerWidth - widthX - popup_margin;
    } else {
      // not enough space: we have at left more space then on right: stick to leftside of window      
      posX = popup_margin;
    }

    // calculate top position of popup
    if (heightY > window.innerHeight) {
      // not enough space: put in middle height of window
      posY = window.innerHeight / 2 - heightY / 2;
    } else {
      // enough space: put center of popup at same height as center bounding box
      bb_centerY = bb_topleftY + bb_height / 2;
      posY = bb_centerY - heightY / 2;
      if (posY < 0) posY = popup_margin;
      if ((posY + heightY) > window.innerHeight) {
        posY = window.innerHeight - heightY - popup_margin;
      }
    }

    pos_x = posX + window.scrollX;
    pos_y = posY + window.scrollY;

    // finally place popup window
    image_popup.style.left = posX + "px";
    image_popup.style.top = pos_y + "px";

  }
  for (var i = 0; i < small_images.length; i++) {
    small_images[i].addEventListener("click", handleClick);
  }




}
