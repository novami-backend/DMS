# Sidebar Menu Items

## For Superadmin User

When logged in as **superadmin**, you should see ALL of the following menu items:

1. 🏠 **Dashboard** - `/dashboard`
2. 👥 **Users** - `/users`
3. 🏷️ **Roles** - `/roles`
4. 🔑 **Permissions** - `/permissions`
5. 🏢 **Departments** - `/departments`
6. 📋 **Document Types** - `/document-types`
7. 📄 **Documents** - `/documents`
8. ✅ **Documents Approval** - `/approval-dashboard`
9. 🚪 **Logout** - `/logout`

## How It Works

### Superadmin Bypass
The `userHasPermission()` helper function has been updated to automatically return `true` for superadmin users:

```php
// Superadmin has all permissions
$roleName = session()->get('role_name');
if ($roleName === 'superadmin') {
    return true;
}
```

This means superadmin users bypass ALL permission checks and can see every menu item.

### Admin Users
Admin users also have all 28 permissions assigned to their role, so they will see all menu items as well.

### Other Roles
Other roles (manager, user, etc.) will only see menu items for which they have the corresponding permission.

## Required Permissions

Each menu item requires a specific permission:

| Menu Item | Permission Key |
|-----------|---------------|
| Dashboard | `dashboard_access` |
| Users | `user_read` |
| Roles | `role_read` |
| Permissions | `permission_read` |
| Departments | `department_read` |
| Document Types | `document_type_read` |
| Documents | `document_read` |
| Documents Approval | `document_approve` |

## Verification

To verify permissions are working:

```bash
# Test permissions for superadmin and admin
php spark test:permissions
```

Expected output:
- Superadmin: 28 permissions assigned
- Admin: 28 permissions assigned
- All test permissions should show ✓ (green checkmark)

## Troubleshooting

If sidebar items are still not showing:

1. **Clear your browser cache and cookies**
2. **Logout and login again** to refresh the session
3. **Check session data:**
   - `user_id` should be set
   - `role_name` should be 'superadmin' or 'admin'
   - `logged_in` should be true
4. **Verify helper is loaded:**
   - Check `app/Config/Autoload.php`
   - Should have `'permission'` in the `$helpers` array
5. **Run permission seeder:**
   ```bash
   php spark permissions:seed
   ```
