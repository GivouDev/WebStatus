var url = window.location.href;
var hash = url.substring(url.indexOf('#'));
var check = hash.includes("#");

if(check == false) {
  window.location.replace(url+"#dashboard");
}
