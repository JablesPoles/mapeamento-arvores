document.addEventListener("DOMContentLoaded", () => {
  AOS.init({
    duration: 800,
    once: true,
  });

  const swiperContainers = document.querySelectorAll(
    '[class*="gallery-swiper-"]'
  );
  swiperContainers.forEach((container) => {
    if (container.classList.contains("swiper-initialized")) return;

    new Swiper(container, {
      loop: true,
      slidesPerView: 1,
      spaceBetween: 15,
      grabCursor: true,
      pagination: {
        el: container.querySelector(".swiper-pagination"),
        clickable: true,
      },
      navigation: {
        nextEl: container.querySelector(".swiper-button-next"),
        prevEl: container.querySelector(".swiper-button-prev"),
      },
      breakpoints: {
        640: { slidesPerView: 2, spaceBetween: 20 },
        1024: { slidesPerView: 3, spaceBetween: 25 },
      },
      on: {
        init: function () {
          container.classList.add("swiper-initialized");
        },
      },
    });
  });

  const expandButtons = document.querySelectorAll(".expand-btn");
  expandButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const article = this.closest("article.tree-card");
      if (!article) return;

      const detailsId = this.getAttribute("aria-controls");
      const details = document.getElementById(detailsId);
      if (!details) return;

      const isExpanded = details.classList.contains("max-h-[9999px]");

      if (!isExpanded) {
        document
          .querySelectorAll(
            "article.tree-card .tree-details.max-h-\\[9999px\\]"
          )
          .forEach((openDetail) => {
            if (openDetail !== details) {
              openDetail.classList.remove("max-h-[9999px]");
              openDetail.classList.add("max-h-0");
              const otherButton = openDetail
                .closest("article.tree-card")
                .querySelector(".expand-btn");
              if (otherButton) {
                otherButton.textContent = "Expandir";
                otherButton.setAttribute("aria-expanded", "false");
              }
            }
          });
      }

      if (isExpanded) {
        details.classList.remove("max-h-[9999px]");
        details.classList.add("max-h-0");
        this.textContent = "Expandir";
        this.setAttribute("aria-expanded", "false");
      } else {
        details.classList.remove("max-h-0");
        details.classList.add("max-h-[9999px]");
        this.textContent = "Recolher";
        this.setAttribute("aria-expanded", "true");

        const swiperInstanceEl = details.querySelector(
          '[class*="gallery-swiper-"]'
        );
        if (swiperInstanceEl && swiperInstanceEl.swiper) {
          swiperInstanceEl.swiper.update();
          swiperInstanceEl.swiper.lazy.load();
        }
      }
    });
  });
});
