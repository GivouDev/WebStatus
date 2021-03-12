var inner = document.getElementById("sidebar").innerHTML;

function closeNav() {
    document.getElementById("sidebar").style.width = "0";
    document.getElementById("sidebar").innerHTML = "";
    document.getElementById("opener").style.visibility = "visible";
}

function openNav() {
    document.getElementById("sidebar").style.width = "35%";
    document.getElementById("sidebar").innerHTML = inner;
    document.getElementById("opener").style.visibility = "hidden";
}

if( /Android|webOS|iPhone|iPad|Mac|Macintosh|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
 closeNav();
}
