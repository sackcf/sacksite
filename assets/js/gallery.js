const lightbox = document.getElementById("lightbox");
const lightboxImg = document.getElementById("lightbox-img");

document.querySelectorAll(".gallery-item img").forEach(img => {
    img.addEventListener("click", () => {
        lightboxImg.src = img.src;
        lightbox.style.display = "flex";
    });
});

lightbox.addEventListener("click", () => {
    lightbox.style.display = "none";
});

document.addEventListener("DOMContentLoaded", () => {
  const thumbs = Array.from(document.querySelectorAll(".gallery-item img"));

  const lightbox = document.getElementById("lightbox");
  const lbImg = document.getElementById("lightbox-img");
  const lbClose = document.getElementById("lb-close");
  const lbPrev = document.getElementById("lb-prev");
  const lbNext = document.getElementById("lb-next");
  const lbCounter = document.getElementById("lb-counter");

  if (!thumbs.length || !lightbox || !lbImg) return;

  const images = thumbs.map((img) => ({
    src: img.getAttribute("src"),
    alt: img.getAttribute("alt") || "Gallery image",
  }));

  let currentIndex = 0;

  function updateCounter() {
    if (!lbCounter) return;
    lbCounter.textContent = `${currentIndex + 1} / ${images.length}`;
  }

  function show(index) {
    currentIndex = (index + images.length) % images.length;

    // Preload for smooth transitions (especially on first open / next image)
    const pre = new Image();
    pre.onload = () => {
      lbImg.src = images[currentIndex].src;
      lbImg.alt = images[currentIndex].alt;
      updateCounter();
    };
    pre.src = images[currentIndex].src;
  }

  function open(index) {
    // Start showing image before opening overlay (smoother)
    show(index);

    lightbox.classList.add("open");
    lightbox.setAttribute("aria-hidden", "false");
    document.body.classList.add("lb-open");
  }

  function close() {
    lightbox.classList.remove("open");
    lightbox.setAttribute("aria-hidden", "true");
    document.body.classList.remove("lb-open");
  }

  function next() {
    show(currentIndex + 1);
  }

  function prev() {
    show(currentIndex - 1);
  }

  // Click thumbnails to open
  thumbs.forEach((img, idx) => {
    img.style.cursor = "pointer";
    img.addEventListener("click", () => open(idx));
  });

  // Buttons
  if (lbNext) lbNext.addEventListener("click", (e) => { e.stopPropagation(); next(); });
  if (lbPrev) lbPrev.addEventListener("click", (e) => { e.stopPropagation(); prev(); });
  if (lbClose) lbClose.addEventListener("click", (e) => { e.stopPropagation(); close(); });

  // Click outside image closes (clicking stage buttons won't close because of stopPropagation)
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) close();
  });

  // Keyboard controls
  document.addEventListener("keydown", (e) => {
    if (!lightbox.classList.contains("open")) return;

    if (e.key === "Escape") close();
    if (e.key === "ArrowRight") next();
    if (e.key === "ArrowLeft") prev();
  });

  // Touch swipe controls
  let startX = 0;

  lbImg.addEventListener(
    "touchstart",
    (e) => {
      startX = e.changedTouches[0].clientX;
    },
    { passive: true }
  );

  lbImg.addEventListener(
    "touchend",
    (e) => {
      const endX = e.changedTouches[0].clientX;
      const diff = endX - startX;

      if (Math.abs(diff) > 40) {
        if (diff < 0) next(); // swipe left -> next
        else prev();          // swipe right -> prev
      }
    },
    { passive: true }
  );
});
