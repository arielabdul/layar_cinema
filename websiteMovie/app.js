document.addEventListener("DOMContentLoaded", function () {
  const arrowsRight = document.querySelectorAll(".arrow");
  const arrowsLeft = document.querySelectorAll(".arrow-left");
  const movieLists = document.querySelectorAll(".movie-list");

  arrowsRight.forEach((arrow, i) => {
      arrow.addEventListener("click", () => {
          // Dapatkan lebar container (wrapper) dan total lebar konten (movie list)
          const wrapperWidth = movieLists[i].parentElement.offsetWidth;
          const listWidth = movieLists[i].scrollWidth;
          // Hitung jarak maksimum yang boleh digeser
          const maxScroll = listWidth - wrapperWidth;

          let currentTransform = parseInt(
              movieLists[i].style.transform.replace("translateX(", "").replace("px)", "")
          ) || 0;
          
          // Hitung pergeseran baru (di sini tetap menggunakan 230px per klik)
          let newTransform = currentTransform - 230;
          // Jika pergeseran melebihi batas, batasi dengan -maxScroll
          if (Math.abs(newTransform) > maxScroll) {
              newTransform = -maxScroll;
          }
          movieLists[i].style.transform = `translateX(${newTransform}px)`;
      });
  });

  arrowsLeft.forEach((arrow, i) => {
      arrow.addEventListener("click", () => {
          let currentTransform = parseInt(
              movieLists[i].style.transform.replace("translateX(", "").replace("px)", "")
          ) || 0;
          
          // Geser ke kanan 230px
          let newTransform = currentTransform + 230;
          // Jangan biarkan lebih dari batas awal (0)
          if (newTransform > 0) {
              newTransform = 0;
          }
          movieLists[i].style.transform = `translateX(${newTransform}px)`;
      });
  });
});
