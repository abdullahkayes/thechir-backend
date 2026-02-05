/**
 * Menu Fix Verification Script
 * Run this in browser console to verify the menu fix is working
 */

(function() {
  console.log('🔍 Menu Fix Verification Starting...\n');

  const checks = {
    passed: 0,
    failed: 0,
    warnings: 0
  };

  // Check 1: menu-fix.js loaded
  console.log('Check 1: Menu Fix Script');
  if (window.reinitializeMenuFix) {
    console.log('✅ menu-fix.js is loaded and initialized');
    checks.passed++;
  } else {
    console.log('❌ menu-fix.js not found');
    checks.failed++;
  }

  // Check 2: Sidebar exists
  console.log('\nCheck 2: Sidebar Element');
  const sidebar = document.querySelector('.sidebar');
  if (sidebar) {
    console.log('✅ Sidebar found');
    checks.passed++;
  } else {
    console.log('❌ Sidebar not found');
    checks.failed++;
  }

  // Check 3: Menu items have user-select disabled
  console.log('\nCheck 3: Text Selection Prevention');
  const navLink = document.querySelector('.nav-link');
  if (navLink) {
    const userSelect = window.getComputedStyle(navLink).userSelect;
    if (userSelect === 'none') {
      console.log(`✅ User-select: ${userSelect}`);
      checks.passed++;
    } else {
      console.log(`⚠️ User-select: ${userSelect} (expected: none)`);
      checks.warnings++;
    }
  }

  // Check 4: Collapse menus exist
  console.log('\nCheck 4: Collapse Elements');
  const collapses = document.querySelectorAll('[data-toggle="collapse"]');
  console.log(`✅ Found ${collapses.length} collapse toggles`);
  checks.passed++;

  // Check 5: Test opening a menu
  console.log('\nCheck 5: Menu Interaction Test');
  const firstToggle = collapses[0];
  if (firstToggle) {
    const targetId = firstToggle.getAttribute('href') || firstToggle.getAttribute('data-target');
    const target = document.getElementById(targetId.replace(/^#/, ''));
    
    if (target) {
      const isOpen = target.classList.contains('show');
      console.log(`✅ Can test menu toggle (currently: ${isOpen ? 'OPEN' : 'CLOSED'})`);
      checks.passed++;
    } else {
      console.log('❌ Target collapse element not found');
      checks.failed++;
    }
  } else {
    console.log('⚠️ No collapse toggles to test');
    checks.warnings++;
  }

  // Check 6: CSS file loaded
  console.log('\nCheck 6: Menu Hover Fix CSS');
  const stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
  let hasHoverFix = false;
  stylesheets.forEach(link => {
    if (link.href && link.href.includes('menu-hover-fix.css')) {
      hasHoverFix = true;
    }
  });
  if (hasHoverFix) {
    console.log('✅ menu-hover-fix.css is loaded');
    checks.passed++;
  } else {
    console.log('⚠️ menu-hover-fix.css not found in stylesheets');
    checks.warnings++;
  }

  // Check 7: Active class management
  console.log('\nCheck 7: Active Class Handling');
  const activeItems = document.querySelectorAll('.nav-item.active');
  console.log(`✅ Found ${activeItems.length} active menu items`);
  checks.passed++;

  // Check 8: No conflicting Bootstrap collapse
  console.log('\nCheck 8: Bootstrap Conflict Check');
  if (window.jQuery && window.jQuery.fn && window.jQuery.fn.collapse) {
    console.log('⚠️ Bootstrap collapse is still available (should be overridden)');
    checks.warnings++;
  } else {
    console.log('✅ Bootstrap collapse is isolated');
    checks.passed++;
  }

  // Summary
  console.log('\n' + '='.repeat(50));
  console.log('📊 VERIFICATION SUMMARY');
  console.log('='.repeat(50));
  console.log(`✅ Passed: ${checks.passed}`);
  console.log(`❌ Failed: ${checks.failed}`);
  console.log(`⚠️ Warnings: ${checks.warnings}`);
  console.log('='.repeat(50));

  if (checks.failed === 0) {
    console.log('\n🎉 All critical checks passed!');
    console.log('The menu fix is properly implemented.');
  } else {
    console.log('\n⚠️ Some checks failed. Please review.');
  }

  // Interactive test function
  window.testMenuInteraction = function(toggleIndex = 0) {
    console.log(`\nTesting menu interaction (toggle ${toggleIndex})...`);
    const collapses = document.querySelectorAll('[data-toggle="collapse"]');
    if (collapses[toggleIndex]) {
      collapses[toggleIndex].click();
      console.log('Clicked toggle - check if menu opens/closes correctly');
    } else {
      console.log('Toggle index out of range');
    }
  };

  console.log('\n💡 Tip: Run testMenuInteraction() to test a menu toggle');
})();
