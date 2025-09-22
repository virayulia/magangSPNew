window.addEventListener("DOMContentLoaded", (event) => {
  // Variabel force_scrolled (diisi dari PHP)
  const forceScrolled = window.forceScrolled === true;

  // Function atur navbar scrolled
  function handleNavbarScroll() {
    const navbar = document.getElementById("mainNav");
    if (!navbar) return;

    if (window.scrollY > 50) {
      navbar.classList.add("navbar-scrolled");
    } else {
      navbar.classList.remove("navbar-scrolled");
    }
  }

  // Jalankan scroll listener hanya jika tidak force
  if (!forceScrolled) {
    window.addEventListener("scroll", handleNavbarScroll);
    handleNavbarScroll(); // inisialisasi saat load
  } else {
    // Kalau force, langsung aktifkan scrolled
    const navbar = document.getElementById("mainNav");
    if (navbar) {
      navbar.classList.add("navbar-scrolled");
    }
  }

  // Navbar shrink function
  var navbarShrink = function () {
    const navbarCollapsible = document.body.querySelector("#mainNav");
    if (!navbarCollapsible) return;
    if (window.scrollY === 0) {
      navbarCollapsible.classList.remove("navbar-shrink");
    } else {
      navbarCollapsible.classList.add("navbar-shrink");
    }
  };

  // Jalankan shrink
  navbarShrink();
  document.addEventListener("scroll", navbarShrink);

  // Scrollspy
  const mainNav = document.body.querySelector("#mainNav");
  if (mainNav) {
    new bootstrap.ScrollSpy(document.body, {
      target: "#mainNav",
      rootMargin: "0px 0px -40%",
    });
  }

  // Collapse navbar on click
  const navbarToggler = document.body.querySelector(".navbar-toggler");
  const responsiveNavItems = [].slice.call(
    document.querySelectorAll("#navbarResponsive .nav-link")
  );
  responsiveNavItems.map(function (responsiveNavItem) {
    responsiveNavItem.addEventListener("click", () => {
      if (window.getComputedStyle(navbarToggler).display !== "none") {
        navbarToggler.click();
      }
    });
  });

  // SimpleLightbox plugin
  new SimpleLightbox({
    elements: "#portfolio a.portfolio-box",
  });
});
