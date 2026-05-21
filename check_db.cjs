const { Client } = require('pg');

async function tryConnect(host) {
  const client = new Client({
    host: host,
    port: 5432,
    database: 'cairo_inventory',
    user: 'postgres',
    password: '1234',
    connectionTimeoutMillis: 5000,
  });
  try {
    await client.connect();
    return client;
  } catch (e) {
    console.log(`  ${host}: ${e.message}`);
    return null;
  }
}

async function main() {
  const hostsToTry = ['172.24.0.1', '127.0.0.1', 'localhost', '10.0.0.1', '10.0.0.2', '192.168.1.1'];
  let client = null;
  let usedHost = '';
  for (const host of hostsToTry) {
    console.log(`Trying ${host}:5432...`);
    client = await tryConnect(host);
    if (client) {
      usedHost = host;
      console.log(`Connected via ${host}:5432!\n`);
      break;
    }
  }

  if (!client) {
    console.error('\nCould not connect to PostgreSQL on any host.');
    console.error('Tips: Check pg_hba.conf on Windows, or add a Windows Firewall rule.');
    process.exit(1);
  }

  // 1. List all tables
  const tablesRes = await client.query(
    "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name"
  );
  console.log('=== ALL TABLES IN DATABASE ===');
  tablesRes.rows.forEach((r, i) => {
    console.log(`  ${i + 1}. ${r.table_name}`);
  });
  console.log(`\nTotal tables: ${tablesRes.rows.length}\n`);

  // 2. For each main module table, show columns and record count
  const mainTables = [
    'assets',
    'receivings',
    'asset_placements',
    'asset_disposals',
    'asset_transfers',
    'asset_inspections',
    'asset_maintenances',
    'asset_upgrades',
    'asset_loss_reports',
    'damage_reports',
    'committee_appointments',
    'disposal_sales',
    'disposal_sale_items',
    'sale_bids',
    'vehicle_disposal_assessments',
  ];

  console.log('=== MAIN MODULE TABLES: COLUMNS & RECORD COUNTS ===\n');
  for (const table of mainTables) {
    const existsRes = await client.query(
      "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_schema = 'public' AND table_name = $1)",
      [table]
    );
    if (!existsRes.rows[0].exists) {
      console.log(`[${table}] -- TABLE DOES NOT EXIST --\n`);
      continue;
    }

    const colsRes = await client.query(
      "SELECT column_name, data_type, is_nullable, character_maximum_length FROM information_schema.columns WHERE table_schema = 'public' AND table_name = $1 ORDER BY ordinal_position",
      [table]
    );

    const countRes = await client.query(`SELECT COUNT(*)::int AS cnt FROM "${table}"`);

    console.log(`[${table}] -- ${countRes.rows[0].cnt} record(s)`);
    console.log('  Columns:');
    colsRes.rows.forEach((c) => {
      const nullable = c.is_nullable === 'YES' ? 'nullable' : 'not null';
      const len = c.character_maximum_length ? `(${c.character_maximum_length})` : '';
      console.log(`    - ${c.column_name} (${c.data_type}${len}, ${nullable})`);
    });

    if (countRes.rows[0].cnt > 0) {
      const sampleRes = await client.query(`SELECT * FROM "${table}" LIMIT 3`);
      console.log('  Sample data (first 3 rows):');
      for (const row of sampleRes.rows) {
        console.log(`    ${JSON.stringify(row)}`);
      }
    }
    console.log('');
  }

  await client.end();
}

main().catch((err) => {
  console.error('FATAL:', err.message);
  process.exit(1);
});
