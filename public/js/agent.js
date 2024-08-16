function openTips() {
    let x = document.querySelector(".tips");
    console.log(x)
    x.classList.toggle('d-none');
    // if (x.style.display == "none") {
    //   x.style.display = "block";
    // } else {
    //   x.style.display = "none";
    // }
  }
  //Make the DIV element draggable:
  dragElement(document.getElementById("mydiv"));

  function closeDragElement() {
      mydiv.style.display = "none"
  }



    function closeDragElement() {
      /* stop moving when mouse button is released:*/
      document.onmouseup = null;
      document.onmousemove = null;
    }
  }
