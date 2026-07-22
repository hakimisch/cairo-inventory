<?php
/**
 * Seed RBAC test users directly via PDO.
 *
 * Run from Windows (Laravel Herd PHP):
 *   php scripts/seed_rbac_users.php
 *
 * This inserts/updates 5 users covering all roles for RBAC testing.
 * Skips if users already exist (idempotent via email).
 */

$host     = '127.0.0.1';
$port     = 5432;
$dbname   = 'cairo_inventory';
$user     = 'postgres';
$password = '1234';

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// If the role column is still an ENUM type (migration not run yet), alter it.
$colCheck = $pdo->query("
    SELECT data_type
    FROM information_schema.columns
    WHERE table_name = 'users' AND column_name = 'role'
")->fetch(PDO::FETCH_ASSOC);

if ($colCheck && $colCheck['data_type'] !== 'character varying') {
    echo "⚠️  Role column is {$colCheck['data_type']} — altering to string(50)...\n";
    $pdo->exec("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50) USING role::text");
    echo "✅ Column altered.\n";
}

$users = [
    // superadmin — full access, can manage users
    [
        'name'     => 'Super Admin',
        'email'    => 'user@gmail.com',
        'role'     => 'superadmin',
    ],
    // admin — can verify, CRUD assets, view reports
    [
        'name'     => 'Pentadbir Sistem CAIRO',
        'email'    => 'admin@cairo.utm',
        'role'     => 'admin',
    ],
    // manager — can view reports only
    [
        'name'     => 'Dr. Ahmad Faizal',
        'email'    => 'manager@cairo.utm',
        'role'     => 'manager',
    ],
    // auditor — can view reports only
    [
        'name'     => 'En. Azman Hashim',
        'email'    => 'auditor@cairo.utm',
        'role'     => 'auditor',
    ],
    // user/staff — no special permissions, can receive/scan/import
    [
        'name'     => 'Ts. Dr. Mohd Ibrahim',
        'email'    => 'ibrahim@cairo.utm',
        'role'     => 'user',
    ],
];

// Also update AdminUserSeeder's deterministic account
$users[] = [
    'name'     => 'System Admin',
    'email'    => 'admin@cairo.test',
    'role'     => 'admin',
];

$passwordHash = password_hash('password', PASSWORD_BCRYPT);
$now = date('Y-m-d H:i:s');

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at)
     VALUES (:name, :email, :password, :role, :verified_at, :now, :now)
     ON CONFLICT (email)
     DO UPDATE SET
         name  = EXCLUDED.name,
         role  = EXCLUDED.role,
         email_verified_at = COALESCE(users.email_verified_at, EXCLUDED.email_verified_at)"
);

$count = 0;
foreach ($users as $u) {
    $stmt->execute([
        ':name'         => $u['name'],
        ':email'        => $u['email'],
        ':password'     => $passwordHash,
        ':role'         => $u['role'],
        ':verified_at'  => $now,
        ':now'          => $now,
    ]);
    $count++;
}

echo "✅ Seeded {$count} RBAC test users.\n";
echo "   All passwords: password\n\n";
echo "   Email                 | Role        | Permissions\n";
echo "   ----------------------|-------------|------------------------------\n";
echo "   user\@gmail.com        | superadmin  | manage_users, verify, crud_assets, view_reports, view_admin\n";
echo "   admin\@cairo.utm       | admin       | verify, crud_assets, view_reports, view_admin\n";
echo "   manager\@cairo.utm     | manager     | view_reports\n";
echo "   auditor\@cairo.utm     | auditor     | view_reports\n";
echo "   ibrahim\@cairo.utm     | user        | (none)\n";
echo "   admin\@cairo.test      | admin       | verify, crud_assets, view_reports, view_admin\n";
echo "\n";
echo "Note: The script auto-converts the role column from ENUM to VARCHAR(50)\n";
echo "if needed. Run from Windows: php scripts/seed_rbac_users.php\n";
