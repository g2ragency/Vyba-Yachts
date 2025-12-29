document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.querySelector('.yacht-sidebar');
  if (!sidebar) return;

  const sidebarInner = sidebar.querySelector('.yacht-sidebar-inner');
  const mainContent = document.querySelector('.yacht-main-content');
  const gallery = document.querySelector('.yacht-gallery-section');
  
  if (!sidebarInner || !mainContent) return;

  let sidebarWidth = 0;
  let galleryTop = null;
  let mainContentTop = null;
  let maxFixedScroll = null;
  const headerOffset = 120; // px

  function recalc() {
    const sidebarRect = sidebar.getBoundingClientRect();
    sidebarWidth = sidebarRect.width;

    if (gallery) {
      galleryTop = gallery.getBoundingClientRect().top + window.scrollY;
    } else {
      // fallback: use mainContent top
      galleryTop = mainContent.getBoundingClientRect().top + window.scrollY;
    }

    mainContentTop = mainContent.getBoundingClientRect().top + window.scrollY;
    const sidebarHeight = sidebarInner.offsetHeight;
    const mainContentHeight = mainContent.offsetHeight;

    // Y position (document) where sidebar should stop being fixed
    maxFixedScroll = mainContentTop + mainContentHeight - sidebarHeight - headerOffset;
  }

  function updateSidebar() {
    const sidebarHeight = sidebarInner.offsetHeight;
    const mainContentHeight = mainContent.offsetHeight;
    const scrollY = window.scrollY || window.pageYOffset;

    // If sidebar is taller than content, don't fix
    if (sidebarHeight >= mainContentHeight) {
      sidebarInner.style.position = 'static';
      sidebarInner.style.top = 'auto';
      sidebarInner.style.bottom = 'auto';
      sidebarInner.style.width = '100%';
      return;
    }

    // If we haven't computed positions yet, do it
    if (galleryTop === null || mainContentTop === null || maxFixedScroll === null) {
      recalc();
    }

    const triggerY = galleryTop - headerOffset; // when top of gallery reaches headerOffset

    if (scrollY >= triggerY && scrollY <= maxFixedScroll) {
      // fixed
      sidebarInner.style.position = 'fixed';
      sidebarInner.style.top = headerOffset + 'px';
      sidebarInner.style.bottom = 'auto';
      sidebarInner.style.width = sidebarWidth + 'px';
    } else if (scrollY > maxFixedScroll) {
      // stick to bottom of main content
      sidebarInner.style.position = 'absolute';
      sidebarInner.style.top = 'auto';
      sidebarInner.style.bottom = '0';
      sidebarInner.style.width = '100%';
    } else {
      // before trigger
      sidebarInner.style.position = 'static';
      sidebarInner.style.top = 'auto';
      sidebarInner.style.bottom = 'auto';
      sidebarInner.style.width = '100%';
    }
  }

  // Update on scroll and resize
  window.addEventListener('scroll', updateSidebar, { passive: true });
  window.addEventListener('resize', function() {
    // Recalculate positions on resize
    galleryTop = null;
    mainContentTop = null;
    maxFixedScroll = null;
    recalc();
    updateSidebar();
  });

  // Initial calc + update
  setTimeout(function() { recalc(); updateSidebar(); }, 120);
});
