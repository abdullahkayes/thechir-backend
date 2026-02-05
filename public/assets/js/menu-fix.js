/**
 * CRITICAL: Menu Interaction Fix - Complete Isolation (v3)
 * Prevents cross-menu item interaction in the sidebar
 * Properly manages active states and prevents hover/select conflicts
 * MUST LOAD LAST
 */

(function() {
  'use strict';

  // Store original Bootstrap collapse behavior
  let isInitialized = false;

  // Initialize the menu fix
  function initMenuFix() {
    if (isInitialized) return;
    isInitialized = true;

    console.log('🔧 Initializing menu isolation fix v3...');

    // ===== CRITICAL: Disable Bootstrap collapse globally =====
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.collapse) {
      const OriginalCollapse = window.jQuery.fn.collapse;
      
      // Override jQuery collapse to prevent auto-triggering
      window.jQuery.fn.collapse = function(option) {
        // Don't call original - we'll handle it ourselves
        return this;
      };
      console.log('✓ Bootstrap collapse intercepted');
    }

    // ===== Remove Bootstrap's data-api listeners =====
    document.removeEventListener('click.bs.collapse.data-api', null);
    document.removeEventListener('click', handleBootstrapCollapse, true);

    // ===== Custom collapse state manager =====
    const collapseManager = {
      openCollapses: new Set(),
      
      close: function(targetId) {
        const target = document.getElementById(targetId.replace(/^#/, ''));
        if (!target) return;
        
        target.classList.remove('show');
        target.style.display = 'none';
        
        const toggle = document.querySelector(`[href="${targetId}"], [data-target="${targetId}"]`);
        if (toggle) {
          toggle.setAttribute('aria-expanded', 'false');
          // Remove active class from parent nav-item
          const navItem = toggle.closest('.nav-item');
          if (navItem) {
            navItem.classList.remove('active');
            toggle.classList.remove('active');
          }
        }
        
        this.openCollapses.delete(targetId);
      },
      
      open: function(targetId) {
        const target = document.getElementById(targetId.replace(/^#/, ''));
        if (!target) return;
        
        // Close all others first
        this.openCollapses.forEach(id => {
          if (id !== targetId) {
            this.close(id);
          }
        });
        
        target.classList.add('show');
        target.style.display = 'block';
        
        const toggle = document.querySelector(`[href="${targetId}"], [data-target="${targetId}"]`);
        if (toggle) {
          toggle.setAttribute('aria-expanded', 'true');
          // Add active class to parent nav-item
          const navItem = toggle.closest('.nav-item');
          if (navItem) {
            navItem.classList.add('active');
            toggle.classList.add('active');
          }
        }
        
        this.openCollapses.add(targetId);
      },
      
      toggle: function(targetId) {
        const target = document.getElementById(targetId.replace(/^#/, ''));
        if (!target) return;
        
        if (target.classList.contains('show')) {
          this.close(targetId);
        } else {
          this.open(targetId);
        }
      }
    };

    // ===== Setup menu click handlers =====
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) {
      console.warn('⚠ Sidebar not found');
      return;
    }

    // ===== Remove active class from all menu items on page load =====
    function clearAllActive() {
      sidebar.querySelectorAll('.nav-item.active, .nav-link.active').forEach(item => {
        item.classList.remove('active');
      });
    }
    clearAllActive();

    // Find all collapse toggles
    const collapseToggles = sidebar.querySelectorAll('[data-toggle="collapse"]');
    console.log(`✓ Found ${collapseToggles.length} collapse toggles`);

    collapseToggles.forEach((toggle, index) => {
      const targetSelector = toggle.getAttribute('href') || toggle.getAttribute('data-target');
      
      if (!targetSelector) {
        console.warn(`⚠ Toggle ${index} has no target`);
        return;
      }

      // Clone the element to remove all existing listeners
      const newToggle = toggle.cloneNode(true);
      toggle.parentNode.replaceChild(newToggle, toggle);

      // Add our custom handler
      newToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        console.log(`🔄 Toggling collapse: ${targetSelector}`);
        collapseManager.toggle(targetSelector);
      }, false);

      // Prevent link navigation
      newToggle.style.cursor = 'pointer';
      newToggle.setAttribute('href', 'javascript:void(0);');
      newToggle.style.pointerEvents = 'auto';
    });

    // ===== Handle regular nav links (non-collapsible) =====
    const regularLinks = sidebar.querySelectorAll('.nav-link:not([data-toggle="collapse"])');
    regularLinks.forEach(link => {
      // Remove any existing active class
      link.classList.remove('active');
      
      // Clone to remove listeners
      const newLink = link.cloneNode(true);
      link.parentNode.replaceChild(newLink, link);
      
      newLink.addEventListener('click', function(e) {
        // Don't prevent navigation for regular links
        e.stopPropagation();
        
        // Remove active from all regular links in same menu level
        const navItems = this.closest('.nav').querySelectorAll('.nav-item:not(:has([data-toggle="collapse"]))');
        navItems.forEach(item => {
          item.classList.remove('active');
          item.querySelector('.nav-link')?.classList.remove('active');
        });
        
        // Add active to this link
        const navItem = this.closest('.nav-item');
        if (navItem) {
          navItem.classList.add('active');
          this.classList.add('active');
        }
      }, false);
    });

    // ===== Block all other click handlers from interfering =====
    document.addEventListener('click', function(e) {
      // If clicking on a nav-link inside sidebar
      if (e.target.closest('.sidebar .nav-link')) {
        const link = e.target.closest('.nav-link');
        
        // If it's a collapse toggle, we already handled it
        if (link.hasAttribute('data-toggle')) {
          e.preventDefault();
          e.stopPropagation();
          e.stopImmediatePropagation();
        } else {
          // For regular links, stop propagation to prevent parent interference
          e.stopPropagation();
        }
      }
    }, true); // Capture phase - executes first

    // ===== Prevent hover effects from cascading =====
    document.querySelectorAll('.sidebar .nav-item').forEach(item => {
      // Prevent mouseenter from triggering any unwanted effects
      item.addEventListener('mouseenter', function(e) {
        e.stopPropagation();
        // Only allow :hover CSS state, don't add classes
      }, true);

      item.addEventListener('mouseleave', function(e) {
        e.stopPropagation();
      }, true);
    });

    // ===== Disable text selection on menu items to prevent auto-select issues =====
    const menuItems = sidebar.querySelectorAll('.nav-item, .nav-link, .link-title');
    menuItems.forEach(item => {
      item.style.userSelect = 'none';
      item.style.WebkitUserSelect = 'none';
      item.style.MozUserSelect = 'none';
      item.style.msUserSelect = 'none';
    });

    // ===== CRITICAL: ERP MENUS SPECIFIC FIX =====
    // Ensure ERP System section menus don't have hover/click issues
    const erpMenuSelectors = [
      '.menu-master-data-parent',
      '.menu-inventory-parent',
      '.menu-purchasing-parent',
      '.menu-sales-parent',
      '.menu-accounting-parent',
      '.menu-product-parent'
    ];

    erpMenuSelectors.forEach(selector => {
      const erpMenus = sidebar.querySelectorAll(selector);
      erpMenus.forEach(menu => {
        // Ensure pointer events are auto
        menu.style.pointerEvents = 'auto';
        
        // Remove any unwanted styles
        menu.style.userSelect = 'none';
        
        // Ensure proper cursor
        menu.style.cursor = 'pointer';
        
        // Get parent nav-item
        const navItem = menu.closest('.nav-item');
        if (navItem) {
          navItem.style.pointerEvents = 'auto';
        }
      });
    });

    // ===== ERP COLLAPSE ELEMENTS - ENSURE PROPER DISPLAY =====
    const erpCollapseIds = ['masterData', 'inventory', 'purchasing', 'sales', 'accounting', 'forms'];
    erpCollapseIds.forEach(id => {
      const collapse = document.getElementById(id);
      if (collapse) {
        collapse.style.pointerEvents = 'auto';
        
        // Ensure proper display state
        if (collapse.classList.contains('show')) {
          collapse.style.display = 'block';
        } else {
          collapse.style.display = 'none';
        }
        
        // Ensure all children can receive events
        collapse.querySelectorAll('.nav-item, .nav-link').forEach(child => {
          child.style.pointerEvents = 'auto';
          child.style.userSelect = 'none';
        });
      }
    });

    // ===== Ensure collapse display states =====
    function fixCollapseDisplay() {
      document.querySelectorAll('.collapse').forEach(collapse => {
        if (collapse.classList.contains('show')) {
          collapse.style.display = 'block';
        } else {
          collapse.style.display = 'none';
        }
      });
    }

    fixCollapseDisplay();

    // Re-check every 100ms to ensure no Bootstrap interference
    const displayCheckInterval = setInterval(fixCollapseDisplay, 100);
    
    // Clear interval after 5 seconds (enough time for page to stabilize)
    setTimeout(() => clearInterval(displayCheckInterval), 5000);

    // ===== Add mutation observer to catch Bootstrap changes =====
    if (window.MutationObserver) {
      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          // If a collapse element's class changes
          if (mutation.type === 'attributes' && 
              mutation.attributeName === 'class' && 
              mutation.target.classList.contains('collapse')) {
            
            const target = mutation.target;
            const shouldBeShown = target.classList.contains('show');
            
            // Enforce our display rules
            if (shouldBeShown && target.style.display !== 'block') {
              target.style.display = 'block';
            } else if (!shouldBeShown && target.style.display !== 'none') {
              target.style.display = 'none';
            }
          }
        });
      });

      // Watch all collapse elements
      document.querySelectorAll('.collapse').forEach(collapse => {
        observer.observe(collapse, {
          attributes: true,
          attributeFilter: ['class', 'style']
        });
      });
    }

    console.log('✅ Menu isolation fix initialized successfully!');
  }

  // Multiple initialization triggers for maximum compatibility

  // 1. DOM Ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMenuFix);
  } else {
    initMenuFix();
  }

  // 2. jQuery Ready
  if (typeof jQuery !== 'undefined') {
    jQuery(function() {
      initMenuFix();
    });
  }

  // 3. Timeout fallback
  setTimeout(initMenuFix, 100);
  setTimeout(initMenuFix, 500);
  
  // 4. Window load
  window.addEventListener('load', initMenuFix);

  // 5. Expose global function for manual initialization if needed
  window.reinitializeMenuFix = initMenuFix;

})();
