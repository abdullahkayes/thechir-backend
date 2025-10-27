
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
          <li class="nav-item">
            <a href="../../dashboard-one.html" class="nav-link">
              <i class="link-icon" data-feather="box"></i>
              <span class="link-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item nav-category">web apps</li>
          <li class="nav-item">
            <a class="nav-link"  href="{{ route('users') }}" >
              <i class="link-icon" data-feather="mail"></i>
              <span class="link-title">User List</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>

          </li>
          <li class="nav-item">
            <a href="" class="nav-link">
              <i class="link-icon" data-feather="message-square"></i>
              <span class="link-title">Email</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="../../pages/apps/calendar.html" class="nav-link">
              <i class="link-icon" data-feather="calendar"></i>
              <span class="link-title">Calendar</span>
            </a>
          </li>
          <li class="nav-item nav-category">Components</li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#advancedUI" >
              <i class="link-icon" data-feather="anchor"></i>
              <span class="link-title">Category</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="advancedUI">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('category') }}" class="nav-link">Catgeory Add</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('category.trash') }}" class="nav-link">Category Trash</a>
                  </li>
                <li class="nav-item">
                  <a href="../../pages/advanced-ui/sweet-alert.html" class="nav-link">Sweet Alert</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#uiComponents" role="button" aria-expanded="false" aria-controls="uiComponents">
              <i class="link-icon" data-feather="feather"></i>
              <span class="link-title">Subcategory</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="uiComponents">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('subcategory') }}" class="nav-link">Subcategory Add</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('subcategory.trash') }}" class="nav-link">Subcategory Trash</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('tag') }}" class="nav-link">Tags</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/ui-components/spinners.html" class="nav-link">Spinners</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/ui-components/tooltips.html" class="nav-link">Tooltips</a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#forms" role="button" aria-expanded="false" aria-controls="forms">
              <i class="link-icon" data-feather="inbox"></i>
              <span class="link-title">Product</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="forms">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('product') }}" class="nav-link">Product Add</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('product.list') }}" class="nav-link">Product List</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('product.trash') }}" class="nav-link">Product Trash</a>
                </li>
                <li class="nav-item">
                  <a href="" class="nav-link">Inventory</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('color') }}" class="nav-link"> Add Color</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('size') }}" class="nav-link">Add Size</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('coupon') }}" >
              <i class="link-icon" data-feather="pie-chart"></i>
              <span class="link-title">Coupon</span>
            </a>

          </li>
          <li class="nav-item">
            <a class="nav-link"  href="{{ route('order') }}" >
              <i class="link-icon" data-feather="layout"></i>
              <span class="link-title">Orders</span>
            </a>

          </li>
          <li class="nav-item">
            <a class="nav-link"  href="{{ route('roll.manager') }}">
              <i class="link-icon" data-feather="smile"></i>
              <span class="link-title">Roll Manager</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="icons">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="../../pages/icons/feather-icons.html" class="nav-link">Feather Icons</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/icons/flag-icons.html" class="nav-link">Flag Icons</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/icons/mdi-icons.html" class="nav-link">Mdi Icons</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item nav-category">Pages</li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#general-pages" role="button" aria-expanded="false" aria-controls="general-pages">
              <i class="link-icon" data-feather="book"></i>
              <span class="link-title">Special pages</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="general-pages">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="../../pages/general/blank-page.html" class="nav-link">Blank page</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/general/faq.html" class="nav-link">Faq</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/general/invoice.html" class="nav-link">Invoice</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/general/profile.html" class="nav-link">Profile</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/general/pricing.html" class="nav-link">Pricing</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/general/timeline.html" class="nav-link">Timeline</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#authPages" role="button" aria-expanded="false" aria-controls="authPages">
              <i class="link-icon" data-feather="unlock"></i>
              <span class="link-title">Authentication</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="authPages">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="../../pages/auth/login.html" class="nav-link">Login</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/auth/register.html" class="nav-link">Register</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#errorPages" role="button" aria-expanded="false" aria-controls="errorPages">
              <i class="link-icon" data-feather="cloud-off"></i>
              <span class="link-title">Error</span>
              <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="errorPages">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="../../pages/error/404.html" class="nav-link">404</a>
                </li>
                <li class="nav-item">
                  <a href="../../pages/error/500.html" class="nav-link">500</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item nav-category">Docs</li>
          <li class="nav-item">
            <a href="https://www.nobleui.com/html/documentation/docs.html" target="_blank" class="nav-link">
              <i class="link-icon" data-feather="hash"></i>
              <span class="link-title">Documentation</span>
            </a>
          </li>
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
                <a href="javascript:;" class="py-2 dropdown-item"><i class="flag-icon flag-icon-us" title="us" id="us"></i> <span class="ml-1"> English </span></a>
                <a href="javascript:;" class="py-2 dropdown-item"><i class="flag-icon flag-icon-fr" title="fr" id="fr"></i> <span class="ml-1"> French </span></a>
                <a href="javascript:;" class="py-2 dropdown-item"><i class="flag-icon flag-icon-de" title="de" id="de"></i> <span class="ml-1"> German </span></a>
                <a href="javascript:;" class="py-2 dropdown-item"><i class="flag-icon flag-icon-pt" title="pt" id="pt"></i> <span class="ml-1"> Portuguese </span></a>
                <a href="javascript:;" class="py-2 dropdown-item"><i class="flag-icon flag-icon-es" title="es" id="es"></i> <span class="ml-1"> Spanish </span></a>
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
										<a href="../../pages/apps/chat.html"><i data-feather="message-square" class="icon-lg"></i><p>Chat</p></a>
										<a href="../../pages/apps/calendar.html"><i data-feather="calendar" class="icon-lg"></i><p>Calendar</p></a>
										<a href="../../pages/email/inbox.html"><i data-feather="mail" class="icon-lg"></i><p>Email</p></a>
										<a href="../../pages/general/profile.html"><i data-feather="instagram" class="icon-lg"></i><p>Profile</p></a>
									</div>
								</div>
								<div class="dropdown-footer d-flex align-items-center justify-content-center">
									<a href="javascript:;">View all</a>
								</div>
							</div>
						</li>
						<li class="nav-item dropdown nav-messages">
							<a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i data-feather="mail"></i>
							</a>
							<div class="dropdown-menu" aria-labelledby="messageDropdown">
								<div class="dropdown-header d-flex align-items-center justify-content-between">
									<p class="mb-0 font-weight-medium">9 New Messages</p>
									<a href="javascript:;" class="text-muted">Clear all</a>
								</div>
								<div class="dropdown-body">
									<a href="javascript:;" class="dropdown-item">
										<div class="figure">
											<img src="https://via.placeholder.com/30x30" alt="userr">
										</div>
										<div class="content">
											<div class="d-flex justify-content-between align-items-center">
												<p>Leonardo Payne</p>
												<p class="sub-text text-muted">2 min ago</p>
											</div>
											<p class="sub-text text-muted">Project status</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item">
										<div class="figure">
											<img src="https://via.placeholder.com/30x30" alt="userr">
										</div>
										<div class="content">
											<div class="d-flex justify-content-between align-items-center">
												<p>Carl Henson</p>
												<p class="sub-text text-muted">30 min ago</p>
											</div>
											<p class="sub-text text-muted">Client meeting</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item">
										<div class="figure">
											<img src="https://via.placeholder.com/30x30" alt="userr">
										</div>
										<div class="content">
											<div class="d-flex justify-content-between align-items-center">
												<p>Jensen Combs</p>
												<p class="sub-text text-muted">1 hrs ago</p>
											</div>
											<p class="sub-text text-muted">Project updates</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item">
										<div class="figure">
											<img src="https://via.placeholder.com/30x30" alt="userr">
										</div>
										<div class="content">
											<div class="d-flex justify-content-between align-items-center">
												<p>Amiah Burton</p>
												<p class="sub-text text-muted">2 hrs ago</p>
											</div>
											<p class="sub-text text-muted">Project deadline</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item">
										<div class="figure">
											<img src="https://via.placeholder.com/30x30" alt="userr">
										</div>
										<div class="content">
											<div class="d-flex justify-content-between align-items-center">
												<p>Yaretzi Mayo</p>
												<p class="sub-text text-muted">5 hr ago</p>
											</div>
											<p class="sub-text text-muted">New record</p>
										</div>
									</a>
								</div>
								<div class="dropdown-footer d-flex align-items-center justify-content-center">
									<a href="javascript:;">View all</a>
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
									<a href="javascript:;" class="dropdown-item">
										<div class="icon">
											<i data-feather="user-plus"></i>
										</div>
										<div class="content">
											<p>New customer registered</p>
											<p class="sub-text text-muted">2 sec ago</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item">
										<div class="icon">
											<i data-feather="gift"></i>
										</div>
										<div class="content">
											<p>New Order Recieved</p>
											<p class="sub-text text-muted">30 min ago</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item">
										<div class="icon">
											<i data-feather="alert-circle"></i>
										</div>
										<div class="content">
											<p>Server Limit Reached!</p>
											<p class="sub-text text-muted">1 hrs ago</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item">
										<div class="icon">
											<i data-feather="layers"></i>
										</div>
										<div class="content">
											<p>Apps are ready for update</p>
											<p class="sub-text text-muted">5 hrs ago</p>
										</div>
									</a>
									<a href="javascript:;" class="dropdown-item">
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
									<a href="javascript:;">View all</a>
								</div>
							</div>
						</li>
						<li class="nav-item dropdown nav-profile">
							<a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<img src="{{ asset('upload/user') }}/{{ Auth::user()->photo }}" alt="profile">
							</a>
							<div class="dropdown-menu" aria-labelledby="profileDropdown">
								<div class="dropdown-header d-flex flex-column align-items-center">
									<div class="mb-3 figure">
										<img src="{{ asset('upload/user') }}/{{ Auth::user()->photo }}" alt="">
									</div>
									<div class="text-center info">
										<p class="mb-0 name font-weight-bold">{{ Auth::user()->name }}</p>
										<p class="mb-3 email text-muted">{{ Auth::user()->email }}</p>
									</div>
								</div>
								<div class="dropdown-body">
									<ul class="p-0 pt-3 profile-nav">

										<li class="nav-item">
											<a href="{{  route('user.edit') }}" class="nav-link">
												<i data-feather="edit"></i>
												<span>Edit Profile</span>
											</a>
										</li>

										<li class="nav-item">
                      <form method="POST" action="{{ route('logout') }}">
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
    @yield('script')
	<!-- endinject -->
	<!-- custom js for this page -->
  <!-- end custom js for this page -->
</body>
</html>
