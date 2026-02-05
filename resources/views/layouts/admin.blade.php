<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>NobleUI Responsive Bootstrap 4 Dashboard Template</title>
	<!-- core:css -->
	<link rel="stylesheet" href="{{ asset('assets') }}/vendors/core/core.css">
	<!-- endinject -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<!-- plugin css for this page -->
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
	<!-- end plugin css for this page -->
	<!-- inject:css -->
	<link rel="stylesheet" href="{{ asset('assets') }}/fonts/feather-font/css/iconfont.css">
	<link rel="stylesheet" href="{{ asset('assets') }}/vendors/flag-icon-css/css/flag-icon.min.css">
	<!-- endinject -->
  <!-- Layout styles -->
	<link rel="stylesheet" href="{{ asset('assets') }}/css/demo_1/style.css">
  <!-- End layout styles -->
  <!-- Menu Isolation CSS -->
  <link rel="stylesheet" href="{{ asset('assets') }}/css/menu-isolation.css">
  <!-- End Menu Isolation CSS -->
  <!-- Bootstrap Override CSS -->
  <link rel="stylesheet" href="{{ asset('assets') }}/css/bootstrap-override.css">
  <!-- End Bootstrap Override CSS -->
  <link rel="shortcut icon" href="{{ asset('assets') }}/images/favicon.png" />
</head>
<body>
	<div class="main-wrapper">

		<!-- partial:../../partials/_sidebar.html -->
		<nav class="sidebar">
      <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
          Noble<span>UI</span>
        </a>
        <div class="sidebar-toggler not-active">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
      <div class="sidebar-body">
        <ul class="nav">
          <li class="nav-item nav-category">Main</li>
        
       

       
       
     
          
      


          <li class="nav-item nav-category">ERP SYSTEM</li>
          
          <!-- Documentation Menu -->
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link menu-documentation" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="bar-chart-2"></i>
              <span class="link-title">ERP Dashboard</span>
            </a>
          </li>
            <!-- Dropdown 1: Advanced Features -->
          <li class="nav-item">
            <a class="nav-link menu-advanced-features menu-advanced-features-parent" data-toggle="collapse" href="#advancedFeatures" role="button" aria-expanded="false" aria-controls="advancedFeatures">
              <i class="link-icon" data-feather="star"></i>
              <span class="link-title">Master Data </span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="advancedFeatures">
              <ul class="nav sub-menu">
                <!-- Sub-Dropdown 1: Advanced Routing -->
                <li class="nav-item">
                  <a class="nav-link menu-advanced-routing-parent" data-toggle="collapse" href="#advancedRouting" role="button" aria-expanded="false" aria-controls="advancedRouting" style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="display: flex; align-items: center;">
                     Products 
                    </span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                  </a>
                  <div class="collapse" id="advancedRouting">
                    <ul class="nav sub-menu nested-sub-menu">
                      <li class="nav-item">
                        <a href="{{ route('product') }}" class="nav-link" onclick="event.stopPropagation()"> Product Add</a>
                      </li>
                      <li class="nav-item">
                        <a  href="{{ route('product.list') }}" class="nav-link" onclick="event.stopPropagation()"></i>Product List</a>
                      </li>
                      <li class="nav-item">
                        <a  href="{{ route('product.trash') }}" class="nav-link" onclick="event.stopPropagation()">Product Trash</a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route('size') }}"class="nav-link" onclick="event.stopPropagation()">Add Size</a>
                      </li>
                      <li class="nav-item">
                        <a  href="{{ route('color') }}" class="nav-link" onclick="event.stopPropagation()"> Add Color</a>
                      </li>
                    </ul>
                  </div>
                </li>

                <!-- Sub-Dropdown 2: Middleware -->
                <li class="nav-item">
                  <a class="nav-link menu-middleware-parent" data-toggle="collapse" href="#middlewareMenu" role="button" aria-expanded="false" aria-controls="middlewareMenu" style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="display: flex; align-items: center;">
                      Category
                    </span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                  </a>
                  <div class="collapse" id="middlewareMenu">
                    <ul class="nav sub-menu nested-sub-menu">
                      <li class="nav-item">
                        <a href="{{ route('category') }}" class="nav-link" onclick="event.stopPropagation()"></i>Category Add</a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route('category.trash') }}" class="nav-link" onclick="event.stopPropagation()"></i>Category Trash</a>
                      </li>
                     
                    </ul>
                  </div>
                </li>

                <!-- Sub-Dropdown 3: Caching -->
                <li class="nav-item">
                  <a class="nav-link menu-caching-parent" data-toggle="collapse" href="#cachingMenu" role="button" aria-expanded="false" aria-controls="cachingMenu" style="display: flex; align-items: center; justify-content: space-between;">
                    <span style="display: flex; align-items: center;">
                     Subcategory
                    </span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                  </a>
                  <div class="collapse" id="cachingMenu">
                    <ul class="nav sub-menu nested-sub-menu">
                      <li class="nav-item">
                        <a href="{{ route('subcategory') }}" class="nav-link" onclick="event.stopPropagation()"></i>Subcategory Add</a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route('subcategory.trash') }}" class="nav-link" onclick="event.stopPropagation()"></i>Subcategory Trash</a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ route('tag') }}" class="nav-link" onclick="event.stopPropagation()"></i>Tags</a>
                      </li>
                    
                    </ul>
                  </div>
                </li>

                <li class="nav-item">
                  <a href="{{ route('brand.index') }}"  class="nav-link menu-async-operations" onclick="event.stopPropagation()">Brands</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('customers.index') }}" class="nav-link menu-webhooks" onclick="event.stopPropagation()">Customers</a>
                </li>
              </ul>
            </div>
          </li>
          <!-- Dropdown 2: Integration Guide -->
          <li class="nav-item">
            <a class="nav-link menu-integration-guide menu-integration-guide-parent" data-toggle="collapse" href="#integrationGuide" role="button" aria-expanded="false" aria-controls="integrationGuide">
              <i class="link-icon" data-feather="link"></i>
              <span class="link-title">Inventory</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="integrationGuide">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('product-inventory.index') }}" class="nav-link menu-stripe-integration" onclick="event.stopPropagation()">Inventory add </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('inventory.index') }}" class="nav-link menu-paypal-integration" onclick="event.stopPropagation()">Stock Status</a>
                </li>
               
              </ul>
            </div>
          </li>

          <!-- Dropdown 3: Database Guides -->
          <li class="nav-item">
            <a class="nav-link menu-database-guides menu-database-guides-parent" data-toggle="collapse" href="#databaseGuides" role="button" aria-expanded="false" aria-controls="databaseGuides">
              <i class="link-icon" data-feather="database"></i>
              <span class="link-title">Purchasing</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="databaseGuides">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('purchase-orders.index') }}" class="nav-link menu-mysql-guide" onclick="event.stopPropagation()">Purchase Orders</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('suppliers.index') }}" class="nav-link menu-postgresql-guide" onclick="event.stopPropagation()">Suppliers</a>
                </li>
               
              </ul>
            </div>
          </li>

          <!-- Dropdown 4: Testing Framework -->
          <li class="nav-item">
            <a class="nav-link menu-testing-framework menu-testing-framework-parent" data-toggle="collapse" href="#testingFramework" role="button" aria-expanded="false" aria-controls="testingFramework">
              <i class="link-icon" data-feather="check-square"></i>
              <span class="link-title">Sales</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="testingFramework">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('order') }}" class="nav-link menu-unit-testing" onclick="event.stopPropagation()">Order Management</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('returns.index') }}" class="nav-link menu-integration-testing" onclick="event.stopPropagation()">Returns & Refunds</a>
                </li>
             
              </ul>
            </div>
          </li>

          <!-- Dropdown 5: API Versions -->
          <li class="nav-item">
            <a class="nav-link menu-api-versions menu-api-versions-parent" data-toggle="collapse" href="#apiVersions" role="button" aria-expanded="false" aria-controls="apiVersions">
              <i class="link-icon" data-feather="layers"></i>
              <span class="link-title">Accounting</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="apiVersions">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('accounting.index') }}"  class="nav-link menu-api-v1" onclick="event.stopPropagation()">General Ledger</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('reports.profit-loss') }}" class="nav-link menu-api-v2" onclick="event.stopPropagation()">P&L Report</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('reports.sales') }}" class="nav-link menu-api-v3" onclick="event.stopPropagation()">Sales Report</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('reports.inventory') }}"class="nav-link menu-migration-guide" onclick="event.stopPropagation()">Inventory Report</a>
                </li>
              
              </ul>
            </div>
          </li>


          <!-- 11 Single Menus -->
          <li class="nav-item">
            <a  href="{{ route('roll.manager') }}" class="nav-link menu-api-reference" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="code"></i>
              <span class="link-title">Roll Manager</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('coupon') }}" class="nav-link menu-tutorials" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="book-open"></i>
              <span class="link-title">Cupons</span>
            </a>
          </li>
          <li class="nav-item">
            <a  href="{{ route('users') }}" class="nav-link menu-best-practices" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="check-circle"></i>
              <span class="link-title">Admin List</span>
            </a>
          </li>
            <li class="nav-item">
            <a  href="{{ route('analytic') }}" class="nav-link menu-getting-started" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="play-circle"></i>
              <span class="link-title">Analytic Page</span>
            </a>
          </li>
          <li class="nav-item">
            <a  href="{{ route('business-plans.index') }}" class="nav-link menu-troubleshooting" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="alert-triangle"></i>
              <span class="link-title">Business Plans</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.reseller.dashboard') }}"class="nav-link menu-changelog" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="list"></i>
              <span class="link-title">Reseller & B2B Dasboard</span>
            </a>
          </li>
           <li class="nav-item">
             <a  href="{{ route('admin.reviews.index') }}" class="nav-link menu-faq-docs" onclick="event.stopPropagation()">
               <i class="link-icon" data-feather="help-circle"></i>
               <span class="link-title">Customer Revies</span>
             </a>
           </li>
           <li class="nav-item">
             <a  href="{{ route('admin.messages.index') }}" class="nav-link menu-api-reference" onclick="event.stopPropagation()">
               <i class="link-icon" data-feather="mail"></i>
               <span class="link-title">Messages</span>
               @php
                 $unreadMessagesCount = \App\Models\Message::whereNull('read_at')->count();
               @endphp
               @if($unreadMessagesCount > 0)
                 <span class="badge badge-danger">{{ $unreadMessagesCount }}</span>
               @endif
             </a>
           </li>
          <li class="nav-item">
            <a href="{{ route('slider') }}" class="nav-link menu-security" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="shield"></i>
              <span class="link-title">Banner</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('banner.down') }}" class="nav-link menu-performance" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="zap"></i>
              <span class="link-title">Banner Down Slier</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('videoSlider') }}" class="nav-link menu-deployment" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="cloud-upload"></i>
              <span class="link-title">Video Rells</span>
            </a>
          </li>
          <!-- <li class="nav-item">
            <a href="{{ route('seo') }}" class="nav-link menu-support" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="headphones"></i>
              <span class="link-title">SEO Handeling</span>
            </a>
          </li> -->
          <li class="nav-item">
            <a class="nav-link" href="{{ route('brand.index') }}" role="button">
              <i class="link-icon" data-feather="git-branch"></i>
              <span class="link-title">Add Brands</span>
            </a>

          </li>
          <li class="nav-item">
            <a href="{{ route('admin.distributor-points.index') }}" class="nav-link menu-deployment" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="map-pin"></i>
              <span class="link-title">Distributor Points</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.qr-payments.index') }}" class="nav-link menu-qr-payments" onclick="event.stopPropagation()">
              <i class="link-icon" data-feather="bar"></i>
              <span class="link-title">QR Payments</span>
            </a>
          </li>

          <!-- Dropdown 7: Architecture -->
          <!-- <li class="nav-item">
            <a class="nav-link menu-architecture menu-architecture-parent" data-toggle="collapse" href="#architecture" role="button" aria-expanded="false" aria-controls="architecture">
              <i class="link-icon" data-feather="layout"></i>
              <span class="link-title">Architecture</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="architecture">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="#" class="nav-link menu-system-design" onclick="event.stopPropagation()"><i class="link-icon" data-feather="layers"></i>System Design</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-microservices" onclick="event.stopPropagation()"><i class="link-icon" data-feather="grid"></i>Microservices</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-scalability" onclick="event.stopPropagation()"><i class="link-icon" data-feather="trending-up"></i>Scalability</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-load-balancing" onclick="event.stopPropagation()"><i class="link-icon" data-feather="activity"></i>Load Balancing</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-disaster-recovery" onclick="event.stopPropagation()"><i class="link-icon" data-feather="shield"></i>Disaster Recovery</a>
                </li>
              </ul>
            </div>
          </li> -->

          <!-- Dropdown 8: DevOps & CI/CD -->
          <!-- <li class="nav-item">
            <a class="nav-link menu-devops-cicd menu-devops-cicd-parent" data-toggle="collapse" href="#devopsCicd" role="button" aria-expanded="false" aria-controls="devopsCicd">
              <i class="link-icon" data-feather="tool"></i>
              <span class="link-title">DevOps & CI/CD</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="devopsCicd">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="#" class="nav-link menu-docker-guide" onclick="event.stopPropagation()"><i class="link-icon" data-feather="box"></i>Docker Guide</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-kubernetes-guide" onclick="event.stopPropagation()"><i class="link-icon" data-feather="grid"></i>Kubernetes Guide</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-jenkins-guide" onclick="event.stopPropagation()"><i class="link-icon" data-feather="cpu"></i>Jenkins Guide</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-github-actions" onclick="event.stopPropagation()"><i class="link-icon" data-feather="git-branch"></i>GitHub Actions</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-monitoring-logging" onclick="event.stopPropagation()"><i class="link-icon" data-feather="eye"></i>Monitoring & Logging</a>
                </li>
              </ul>
            </div>
          </li> -->

          <!-- Dropdown 9: Community & Resources -->
          <!-- <li class="nav-item">
            <a class="nav-link menu-community-resources menu-community-resources-parent" data-toggle="collapse" href="#communityResources" role="button" aria-expanded="false" aria-controls="communityResources">
              <i class="link-icon" data-feather="users"></i>
              <span class="link-title">Community & Resources</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="communityResources">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="#" class="nav-link menu-community-forum" onclick="event.stopPropagation()"><i class="link-icon" data-feather="message-circle"></i>Community Forum</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-discord-channel" onclick="event.stopPropagation()"><i class="link-icon" data-feather="send"></i>Discord Channel</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-github-discussions" onclick="event.stopPropagation()"><i class="link-icon" data-feather="git-branch"></i>GitHub Discussions</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-blog-articles" onclick="event.stopPropagation()"><i class="link-icon" data-feather="edit-3"></i>Blog Articles</a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link menu-video-tutorials" onclick="event.stopPropagation()"><i class="link-icon" data-feather="play-circle"></i>Video Tutorials</a>
                </li>
              </ul>
            </div>
          </li> -->
       
      
         
         

        </ul>
      </div>
    </nav>
    <nav class="settings-sidebar">
      <div class="sidebar-body">
        <a href="#" class="settings-sidebar-toggler">
          <i data-feather="settings"></i>
        </a>
        <h6 class="text-muted">Sidebar:</h6>
        <div class="form-group border-bottom">
          <div class="form-check form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="sidebarThemeSettings" id="sidebarLight" value="sidebar-light" checked>
              Light
            </label>
          </div>
          <div class="form-check form-check-inline">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="sidebarThemeSettings" id="sidebarDark" value="sidebar-dark">
              Dark
            </label>
          </div>
        </div>
        <div class="theme-wrapper">
          <h6 class="mb-2 text-muted">Light Theme:</h6>
          <a class="theme-item active" href="../../../demo_1/dashboard-one.html">
            <img src="{{ asset('assets') }}/images/screenshots/light.jpg" alt="light theme">
          </a>
          <h6 class="mb-2 text-muted">Dark Theme:</h6>
          <a class="theme-item" href="../../../demo_2/dashboard-one.html">
            <img src="{{ asset('assets') }}/images/screenshots/dark.jpg" alt="light theme">
          </a>
        </div>
      </div>
    </nav>
		<!-- partial -->

		<div class="page-wrapper">

			<!-- partial:../../partials/_navbar.html -->
			<nav class="navbar">
				<a href="#" class="sidebar-toggler">
					<i data-feather="menu"></i>
				</a>
				<div class="navbar-content">
					<form class="search-form">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<i data-feather="search"></i>
								</div>
							</div>
							<input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
						</div>
					</form>
					<ul class="navbar-nav">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="mt-1 flag-icon flag-icon-us" title="us"></i> <span class="ml-1 mr-1 font-weight-medium d-none d-md-inline-block">English</span>
							</a>
							<div class="dropdown-menu" aria-labelledby="languageDropdown">
							         <a href="javascript:;" class="py-2 dropdown-item" onclick="event.stopPropagation()"><i class="flag-icon flag-icon-us" title="us" id="us"></i> <span class="ml-1"> English </span></a>
							         <a href="javascript:;" class="py-2 dropdown-item" onclick="event.stopPropagation()"><i class="flag-icon flag-icon-fr" title="fr" id="fr"></i> <span class="ml-1"> French </span></a>
							         <a href="javascript:;" class="py-2 dropdown-item" onclick="event.stopPropagation()"><i class="flag-icon flag-icon-de" title="de" id="de"></i> <span class="ml-1"> German </span></a>
							         <a href="javascript:;" class="py-2 dropdown-item" onclick="event.stopPropagation()"><i class="flag-icon flag-icon-pt" title="pt" id="pt"></i> <span class="ml-1"> Portuguese </span></a>
							         <a href="javascript:;" class="py-2 dropdown-item" onclick="event.stopPropagation()"><i class="flag-icon flag-icon-es" title="es" id="es"></i> <span class="ml-1"> Spanish </span></a>
							</div>
            </li>
						<li class="nav-item dropdown nav-apps">
							<a class="nav-link dropdown-toggle" href="#" id="appsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i data-feather="grid"></i>
							</a>
							<div class="dropdown-menu" aria-labelledby="appsDropdown">
								<div class="dropdown-header d-flex align-items-center justify-content-between">
									<p class="mb-0 font-weight-medium">Web Apps</p>
									<a href="javascript:;" class="text-muted">Edit</a>
								</div>
								<div class="dropdown-body">
									<div class="d-flex align-items-center apps">
										<a href="../../pages/apps/chat.html" onclick="event.stopPropagation()"><i data-feather="message-square" class="icon-lg"></i><p>Chat</p></a>
										<a href="../../pages/apps/calendar.html" onclick="event.stopPropagation()"><i data-feather="calendar" class="icon-lg"></i><p>Calendar</p></a>
										<a href="../../pages/email/inbox.html" onclick="event.stopPropagation()"><i data-feather="mail" class="icon-lg"></i><p>Email</p></a>
										<a href="../../pages/general/profile.html" onclick="event.stopPropagation()"><i data-feather="instagram" class="icon-lg"></i><p>Profile</p></a>
									</div>
								</div>
								<div class="dropdown-footer d-flex align-items-center justify-content-center">
									<a href="javascript:;" onclick="event.stopPropagation()">View all</a>
								</div>
							</div>
						</li>
						<li class="nav-item dropdown nav-messages">
							<a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i data-feather="mail"></i>
								@php
									$unreadMessagesCount = \App\Models\Message::whereNull('read_at')->count();
								@endphp
								@if($unreadMessagesCount > 0)
									<div class="indicator">
										<div class="circle"></div>
									</div>
								@endif
							</a>
							<div class="dropdown-menu" aria-labelledby="messageDropdown">
								<div class="dropdown-header d-flex align-items-center justify-content-between">
									<p class="mb-0 font-weight-medium">{{ $unreadMessagesCount }} New Messages</p>
									<a href="{{ route('admin.messages.index') }}" class="text-muted">View all</a>
								</div>
								<div class="dropdown-body">
									@php
										$unreadMessages = \App\Models\Message::whereNull('read_at')
											->orderBy('created_at', 'desc')
											->take(5)
											->get();
									@endphp
									@if($unreadMessages->count() > 0)
										@foreach($unreadMessages as $message)
										<a href="{{ route('admin.messages.show', $message->id) }}" class="dropdown-item" onclick="event.stopPropagation()">
											<div class="figure">
												<img src="https://ui-avatars.com/api/?name={{ urlencode($message->name) }}&background=random" alt="{{ $message->name }}">
											</div>
											<div class="content">
												<div class="d-flex justify-content-between align-items-center">
													<p>{{ $message->name }}</p>
													<p class="sub-text text-muted">{{ $message->created_at->diffForHumans() }}</p>
												</div>
												<p class="sub-text text-muted">{{ strlen($message->message) > 50 ? substr($message->message, 0, 50) . '...' : $message->message }}</p>
											</div>
										</a>
										@endforeach
									@else
										<div class="text-center py-4">
											<p class="text-muted">No new messages</p>
										</div>
									@endif
								</div>
								<div class="dropdown-footer d-flex align-items-center justify-content-center">
									<a href="{{ route('admin.messages.index') }}" onclick="event.stopPropagation()">View all messages</a>
								</div>
							</div>
						</li>
						<li class="nav-item dropdown nav-notifications">
							<a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i data-feather="bell"></i>
								<div class="indicator">
									<div class="circle"></div>
								</div>
							</a>
							<div class="dropdown-menu" aria-labelledby="notificationDropdown">
								<div class="dropdown-header d-flex align-items-center justify-content-between">
									<p class="mb-0 font-weight-medium">6 New Notifications</p>
									<a href="javascript:;" class="text-muted">Clear all</a>
								</div>
								<div class="dropdown-body">
									<a href="javascript:;" class="dropdown-item" onclick="event.stopPropagation()">
										<div class="icon">
											<i data-feather="user-plus"></i>
										</div>
										<div class="content">
											<p>New customer registered</p>
											<p class="sub-text text-muted">2 sec ago</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item" onclick="event.stopPropagation()">
										<div class="icon">
											<i data-feather="gift"></i>
										</div>
										<div class="content">
											<p>New Order Recieved</p>
											<p class="sub-text text-muted">30 min ago</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item" onclick="event.stopPropagation()">
										<div class="icon">
											<i data-feather="alert-circle"></i>
										</div>
										<div class="content">
											<p>Server Limit Reached!</p>
											<p class="sub-text text-muted">1 hrs ago</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item" onclick="event.stopPropagation()">
										<div class="icon">
											<i data-feather="layers"></i>
										</div>
										<div class="content">
											<p>Apps are ready for update</p>
											<p class="sub-text text-muted">5 hrs ago</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item" onclick="event.stopPropagation()">
										<div class="icon">
											<i data-feather="download"></i>
										</div>
										<div class="content">
											<p>Download completed</p>
											<p class="sub-text text-muted">6 hrs ago</p>
										</div>
									</a>
								</div>
								<div class="dropdown-footer d-flex align-items-center justify-content-center">
									<a href="javascript:;" onclick="event.stopPropagation()">View all</a>
								</div>
							</div>
						</li>
						<li class="nav-item dropdown nav-profile">
							<a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<img src="{{ asset('upload/user') }}/{{ Auth::user()->photo ?? 'default.png' }}" alt="profile">
							</a>
							<div class="dropdown-menu" aria-labelledby="profileDropdown">
								<div class="dropdown-header d-flex flex-column align-items-center">
									<div class="mb-3 figure">
									<img src="{{ asset('upload/user') }}/{{ Auth::user()->photo ?? 'default.png' }}" alt="">
									</div>
									<div class="text-center info">
										<p class="mb-0 name font-weight-bold">{{ Auth::user()->name }}</p>
										<p class="mb-3 email text-muted">{{ Auth::user()->email }}</p>
									</div>
								</div>
								<div class="dropdown-body">
									<ul class="p-0 pt-3 profile-nav">

										<li class="nav-item">
											<a href="{{  route('user.edit') }}" class="nav-link" onclick="event.stopPropagation()">
												<i data-feather="edit"></i>
												<span>Edit Profile</span>
											</a>
										</li>

										<li class="nav-item">
								               <form method="POST" action="{{ route('logout') }}" onclick="event.stopPropagation()">
								                 @csrf
<button type="submit"  class="bg-transparent border-0 nav-link">
<i data-feather="log-out"></i>
<span>Log Out</span>
</button>
								             </form>
										</li>
									</ul>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</nav>
			<!-- partial -->

			<div class="page-content">
       @yield('content')
			</div>

			<!-- partial:../../partials/_footer.html -->
			<footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between">
				<p class="text-center text-muted text-md-left">Copyright © 2021 <a href="https://www.nobleui.com" target="_blank">NobleUI</a>. All rights reserved</p>
				<p class="mb-0 text-center text-muted text-md-left d-none d-md-block">Handcrafted With <i class="mb-1 ml-1 text-primary icon-small" data-feather="heart"></i></p>
			</footer>
			<!-- partial -->

		</div>
	</div>

	<!-- core:js -->
	<script src="{{ asset('assets') }}/vendors/core/core.js"></script>
	<!-- endinject -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<!-- plugin js for this page -->
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
  <script src="https://www.jsdelivr.com/package/npm/chart.js?path=dist"></script>
	<!-- end plugin js for this page -->


	<!-- inject:js -->
	<script src="{{ asset('assets') }}/vendors/feather-icons/feather.min.js"></script>
	<script src="{{ asset('assets') }}/js/template.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Nested Sub-Dropdown Handler -->
    <script>
      (function() {
        'use strict';
        
        function initNestedCollapses() {
          const nestedToggle = document.querySelectorAll('[data-toggle="collapse"][href*="#advanced"], [data-toggle="collapse"][href*="#middleware"], [data-toggle="collapse"][href*="#caching"]');
          
          nestedToggle.forEach(toggle => {
            // Clone to remove old listeners
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
            
            // Add click handler
            newToggle.addEventListener('click', function(e) {
              e.preventDefault();
              e.stopPropagation();
              e.stopImmediatePropagation();
              
              const targetId = this.getAttribute('href') || this.getAttribute('data-target');
              const target = document.querySelector(targetId);
              
              if (target) {
                target.classList.toggle('show');
                target.style.display = target.classList.contains('show') ? 'block' : 'none';
                
                // Toggle aria-expanded
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !isExpanded);
              }
            }, false);
          });
        }
        
        // Initialize on DOM ready
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initNestedCollapses);
        } else {
          initNestedCollapses();
        }
        
        // Initialize on window load
        window.addEventListener('load', initNestedCollapses);
      })();
    </script>
    
    @yield('script')
	<!-- Menu Fix - MUST BE LAST -->
	<script src="{{ asset('assets') }}/js/menu-fix.js"></script>
	<!-- endinject -->
	<!-- custom js for this page -->
  <!-- end custom js for this page -->
</body>
</html>