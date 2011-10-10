// JavaScript Document
function question_redirect(text,redirect_url){
	var answer = confirm(text);
	if (answer){
		window.location = redirect_url;
	}
}