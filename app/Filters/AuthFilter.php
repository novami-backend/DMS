<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->has('logged_in')) {
            // Check if it's an AJAX request
            if ($request->isAJAX()) {
                return service('response')
                    ->setJSON(['error' => 'Unauthorized'])
                    ->setStatusCode(401);
            }
            return redirect()->to('/login')->with('error', 'Please login first');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
    }
}