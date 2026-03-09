<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->post('verifyToken', 'Auth::verifyToken');
$routes->post('reset-password', 'Auth::resetPasswordRequest');
$routes->get('reset-password', 'Auth::resetPassword');

// Notification Routes
$routes->group('notifications', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Notifications::index');
    $routes->get('create', 'Notifications::create');
    $routes->post('store', 'Notifications::store');
    $routes->get('view/(:num)', 'Notifications::view/$1');
    $routes->get('markAllRead', 'Notifications::markAllRead');
});

// Profile Routes
$routes->get('profile/edit', 'Users::editProfile', ['filter' => 'auth']);
$routes->post('profile/update', 'Users::updateProfile', ['filter' => 'auth']);

// Dashboard
$routes->get('dashboard', 'Auth::dashboard', ['filter' => 'auth']);

// Approval Dashboard (direct route for easier access)
$routes->get('approval-dashboard', 'Documents::approvalDashboard', ['filter' => 'auth']);

// Document Routes
$routes->group('documents', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Documents::index');
    $routes->get('create', 'Documents::create');
    $routes->post('store', 'Documents::store');
    $routes->get('edit/(:num)', 'Documents::edit/$1');
    $routes->post('update/(:num)', 'Documents::update/$1');
    $routes->get('view/(:num)', 'Documents::view/$1');
    $routes->get('delete/(:num)', 'Documents::delete/$1');
    $routes->post('lock/(:num)', 'Documents::lockDocument/$1');
    
    // Template API
    $routes->get('get-template-by-type/(:num)', 'Documents::getTemplateByType/$1');
    
    // Preview and Export
    $routes->post('preview', 'Documents::preview');
    $routes->get('preview/(:num)', 'Documents::preview/$1');
    $routes->get('export-pdf/(:num)', 'Documents::exportPdf/$1');
    
    // Workflow routes
    $routes->get('submit-for-review/(:num)', 'Documents::submitForReview/$1');
    $routes->get('review/(:num)', 'Documents::reviewDocument/$1');
    $routes->post('save-review/(:num)', 'Documents::processReview/$1');
    $routes->post('process-review/(:num)', 'Documents::processReview/$1');
    $routes->get('approve/(:num)', 'Documents::approve/$1');
    $routes->post('save-approval/(:num)', 'Documents::saveApproval/$1');
    
    // Reviewer assignment
    $routes->post('assign-reviewer/(:num)', 'Documents::assignReviewer/$1');
    
    // Quick review and approval
    $routes->post('quick-review/(:num)', 'Documents::quickReview/$1');
    $routes->post('quick-approve/(:num)', 'Documents::quickApprove/$1');
    
    // Resubmit after revision
    $routes->post('resubmit-after-revision/(:num)', 'Documents::resubmitAfterRevision/$1');
    
    // Dashboard routes
    $routes->get('approval-dashboard', 'Documents::approvalDashboard');
    $routes->get('my-reviews', 'Documents::myReviews');
    $routes->get('approval-history/(:num)', 'Documents::approvalHistory/$1');
    
    // Export routes
    $routes->get('export-doc/(:num)', 'Documents::exportDoc/$1');
    $routes->post('upload-image', 'Documents::uploadImage');
    
    // Search
    $routes->get('search', 'DocumentSearch::index');
    $routes->get('search-advanced', 'DocumentSearch::advanced');
});

// Template Management Routes
$routes->group('templates', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Templates::index');
    $routes->get('create', 'Templates::create');
    $routes->post('store', 'Templates::store');
    $routes->get('edit/(:num)', 'Templates::edit/$1');
    $routes->get('view/(:num)', 'Templates::view/$1');
    $routes->post('update/(:num)', 'Templates::update/$1');
    $routes->get('delete/(:num)', 'Templates::delete/$1');
    
    // Field management
    $routes->post('add-field/(:num)', 'Templates::addField/$1');
    $routes->post('update-field/(:num)', 'Templates::updateField/$1');
    $routes->get('delete-field/(:num)', 'Templates::deleteField/$1');
});

// Department Routes
$routes->group('departments', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Departments::index');
    $routes->get('create', 'Departments::create');
    $routes->post('store', 'Departments::store');
    $routes->get('edit/(:num)', 'Departments::edit/$1');
    $routes->post('update/(:num)', 'Departments::update/$1');
    $routes->get('delete/(:num)', 'Departments::delete/$1');
});

// Document Type Routes
$routes->group('document-types', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'DocumentTypes::index');
    $routes->get('create', 'DocumentTypes::create');
    $routes->post('store', 'DocumentTypes::store');
    $routes->get('edit/(:num)', 'DocumentTypes::edit/$1');
    $routes->post('update/(:num)', 'DocumentTypes::update/$1');
    $routes->get('delete/(:num)', 'DocumentTypes::delete/$1');
});

// User Management Routes
$routes->group('users', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Users::index');
    $routes->get('create', 'Users::create');
    $routes->post('store', 'Users::store');
    $routes->get('edit/(:num)', 'Users::edit/$1');
    $routes->post('update/(:num)', 'Users::update/$1');
    $routes->get('delete/(:num)', 'Users::delete/$1');
});

// Role Management Routes
$routes->group('roles', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Roles::index');
    $routes->get('create', 'Roles::create');
    $routes->post('store', 'Roles::store');
    $routes->get('edit/(:num)', 'Roles::edit/$1');
    $routes->post('update/(:num)', 'Roles::update/$1');
    $routes->get('delete/(:num)', 'Roles::delete/$1');
});

// Permission Management Routes
$routes->group('permissions', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Permissions::index');
    $routes->get('create', 'Permissions::create');
    $routes->post('store', 'Permissions::store');
    $routes->get('edit/(:num)', 'Permissions::edit/$1');
    $routes->post('update/(:num)', 'Permissions::update/$1');
    $routes->get('delete/(:num)', 'Permissions::delete/$1');
});

// Activity Logs
$routes->get('activity-logs', 'ActivityLogs::index', ['filter' => 'auth']);

// API Routes for AJAX calls
$routes->group('api', ['filter' => 'auth'], function($routes) {
    // Reviewers API
    $routes->get('reviewers', 'Users::getReviewers');
    $routes->get('reviewers/(:num)', 'Users::getReviewer/$1');
    
    // Users API
    $routes->get('users/(:num)', 'Users::getUser/$1');
});

// Attachment API Route
$routes->group('attachments', ['filter' => 'auth'], function($routes) {
    $routes->post('delete/(:num)', 'Attachments::delete/$1');
});
