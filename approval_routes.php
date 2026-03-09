<?php
// Add these routes to your app/Config/Routes.php file

// Document Approval Routes
$routes->group('documents', function($routes) {
    // Basic document routes (existing)
    $routes->get('/', 'Documents::index');
    $routes->get('view/(:num)', 'Documents::view/$1');
    $routes->get('create', 'Documents::create');
    $routes->post('store', 'Documents::store');
    $routes->get('edit/(:num)', 'Documents::edit/$1');
    $routes->post('update/(:num)', 'Documents::update/$1');
    $routes->post('delete/(:num)', 'Documents::delete/$1');
    
    // Approval System Routes
    
    // Admin Dashboard
    $routes->get('approval-dashboard', 'Documents::approvalDashboard');
    
    // Personal Review/Approval Pages
    $routes->get('my-reviews', 'Documents::myReviews');
    $routes->get('my-approvals', 'Documents::myApprovals');
    
    // Submit for Review
    $routes->get('submit-for-review/(:num)', 'Documents::submitForReview/$1');
    $routes->post('process-submit-for-review/(:num)', 'Documents::processSubmitForReview/$1');
    
    // Review Process
    $routes->get('review/(:num)', 'Documents::reviewDocument/$1');
    $routes->post('process-review/(:num)', 'Documents::processReview/$1');
    
    // Approval Process
    $routes->get('approve/(:num)', 'Documents::approveDocument/$1');
    $routes->post('process-approval/(:num)', 'Documents::processApproval/$1');
    
    // Approval History
    $routes->get('approval-history/(:num)', 'Documents::approvalHistory/$1');
    
    // Lock Document (Admin only)
    $routes->post('lock/(:num)', 'Documents::lockDocument/$1');
});

// Example of how to add these to your existing Routes.php:
/*
// In app/Config/Routes.php, add these lines:

// Document Management with Approval System
$routes->get('documents', 'Documents::index');
$routes->get('documents/view/(:num)', 'Documents::view/$1');
$routes->get('documents/create', 'Documents::create');
$routes->post('documents/store', 'Documents::store');
$routes->get('documents/edit/(:num)', 'Documents::edit/$1');
$routes->post('documents/update/(:num)', 'Documents::update/$1');
$routes->post('documents/delete/(:num)', 'Documents::delete/$1');

// Approval System Routes
$routes->get('approval-dashboard', 'Documents::approvalDashboard');
$routes->get('documents/my-reviews', 'Documents::myReviews');
$routes->get('documents/my-approvals', 'Documents::myApprovals');
$routes->get('documents/submit-for-review/(:num)', 'Documents::submitForReview/$1');
$routes->post('documents/process-submit-for-review/(:num)', 'Documents::processSubmitForReview/$1');
$routes->get('documents/review/(:num)', 'Documents::reviewDocument/$1');
$routes->post('documents/process-review/(:num)', 'Documents::processReview/$1');
$routes->get('documents/approve/(:num)', 'Documents::approveDocument/$1');
$routes->post('documents/process-approval/(:num)', 'Documents::processApproval/$1');
$routes->get('documents/approval-history/(:num)', 'Documents::approvalHistory/$1');
$routes->post('documents/lock/(:num)', 'Documents::lockDocument/$1');
*/