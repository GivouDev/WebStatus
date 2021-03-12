timer();
var checkbox = document.getElementById("cb1");

function timer() {
var timeleft = 300;
var downloadTimer = setInterval(function(){
  if (checkbox.checked) {
  	if(timeleft <= 0){
  		clearInterval(downloadTimer);
  		document.getElementById("timer").innerHTML = "checking";
  		reload();
  	} else {
  		document.getElementById("timer").innerHTML = timeleft + "s";
  	}
		timeleft -= 1;
	} else {
		if(document.getElementById("timer").innerHTML.includes("s")) {
			document.getElementById("timer").innerHTML = "";
		}
	}
}, 1000);
}

function reload() {
  if(checkbox.checked) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var json = JSON.parse(this.responseText);




      	var current = json.overall;

      	var status = document.getElementById("status");
      	var loading = document.querySelector('#status .loading');
      	var text = document.querySelector('#status .status-text');

      	loading.innerHTML = '<div style="width:100%;height:100%" class="dual-ring"><div></div></div>';
      	loading.className = 'loading';
      	status.className = "status status-checking";
      	text.innerHTML = 'Checking...';

      	setTimeout(function () {
      		document.getElementById("timer").innerHTML = "10s";
          switch(current) {
            case "Online":
              loading.innerHTML = '';
              loading.className = 'loading fa fa-check';
              status.className = "status status-okay";
              text.innerHTML = 'All systems operational';
              break;

            case "Maintenance":
              loading.innerHTML = '';
              loading.className = 'loading fa fa-clock-o';
              status.className = "status status-maintenance";
              text.innerHTML = 'Systems currently under maintenance';
              break;
            case "Offline":
              loading.innerHTML = '';
              loading.className = 'loading fa fa-times';
              status.className = "status status-problems";
              text.innerHTML = 'Some Systems may not work correctly';
              break;
          }


          for( let prop in json ){
            if(!(prop === "overall")) {
              var cbox = document.getElementById("status-"+prop);
              var cstatus = json[prop];

              switch(cstatus) {
                case "Online":
                  cbox.className = "service service-okay services-okay";

                  if(document.getElementById("alert-"+prop) != null) {
                    document.getElementById("alert-"+prop).className = 'hidden';
                  }
                  break;

                case "Maintenance":
                  cbox.className = "service service-maintenance services-maintenance";
                  document.getElementById("alert-"+prop).className = 'status-annoucement';
                  break;

                case "Offline":
                  cbox.className = "service service-troubles services-troubles";
                  document.getElementById("alert-"+prop).className = 'status-annoucement';
                  break;
              }
            }
          }

      		timer();
      	}, 1000)
      }
    };
  xmlhttp.open("GET", "json.php", true);
  xmlhttp.send();
  }
}
