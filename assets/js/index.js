// Script específico para index.php (inicialização do Swiper principal)
document.addEventListener("DOMContentLoaded", () => {
  if (typeof Swiper !== "undefined" && document.querySelector(".mySwiper")) {
    const mainGallerySwiper = new Swiper(".mySwiper", {
      loop: true,
      speed: 800,
      slidesPerView: 1,
      spaceBetween: 20,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
      breakpoints: {
        768: { slidesPerView: 2, spaceBetween: 25 },
        1024: { slidesPerView: 3, spaceBetween: 30 },
      },
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
        dynamicBullets: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      keyboard: {
        enabled: true,
      },
    });
  } else {
    console.warn(
      "Swiper não está definido ou o container .mySwiper não foi encontrado."
    );
  }
});
