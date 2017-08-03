// JavaScript Document
$(document).ready(function(e){
    
    $(window).scroll(function () { 
      if ($(window).scrollTop() > 200) { 
        $(".top_box").css("display","block"); 
      
      } 
      else { 
        $(".top_box").css("display","none"); 
       
      } 
    });
    
})