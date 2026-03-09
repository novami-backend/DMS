<?php
// Add this temporary method to your Documents controller for debugging
// You can remove it after testing

public function debugUserRole()
{
    $userId = session()->get('user_id');
    $username = session()->get('username');
    $role = $this->getUserRole();
    
    echo "<h2>User Debug Information</h2>";
    echo "<p><strong>User ID:</strong> " . ($userId ?? 'Not set') . "</p>";
    echo "<p><strong>Username:</strong> " . ($username ?? 'Not set') . "</p>";
    echo "<p><strong>Role:</strong> " . ($role ?? 'Not set') . "</p>";
    echo "<p><strong>Is Super Admin:</strong> " . ($this->isSuperAdmin() ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Is Admin:</strong> " . ($this->isAdmin() ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Can Access Approval Dashboard:</strong> " . (($this->isAdmin() || $this->isSuperAdmin()) ? 'Yes' : 'No') . "</p>";
    
    // Show database role information
    if ($userId) {
        $roleInfo = $this->db->table('users u')
            ->select('r.role_name, r.id as role_id')
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.id', $userId)
            ->get()
            ->getRow();
            
        if ($roleInfo) {
            echo "<p><strong>Database Role:</strong> " . $roleInfo->role_name . " (ID: " . $roleInfo->role_id . ")</p>";
        } else {
            echo "<p><strong>Database Role:</strong> No role assigned in database</p>";
        }
    }
    
    echo "<hr>";
    echo "<p><a href='/documents/approval-dashboard'>Try to access Approval Dashboard</a></p>";
    echo "<p><a href='/documents'>Back to Documents</a></p>";
}

// Add this route to your Routes.php temporarily:
// $routes->get('debug-role', 'Documents::debugUserRole');