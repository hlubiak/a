var slideIndex = 1;
function plusSlide(){
    plusSlides(1);
}

function plusSlides(n) {
  showSlides(slideIndex += n);
}

var interval_handle = setInterval( plusSlide,4000 );

showSlides(slideIndex);


function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
   
  var i;
  var slides = document.getElementsByClassName("mySlides");
  if (n > slides.length) {
      slideIndex = 1;
  }    
  if (n < 1) {
      slideIndex = slides.length;
  }
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  slides[slideIndex-1].style.display = "block";  
}
