<?php
namespace Tests\Feature\Sites;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
/** Source-side migration availability; live DB acceptance runs on staging. */
final class AgentAuditIntegrationTest extends TestCase {
 public function test_audit_migration_exists(): void {
  $this->assertFileExists(database_path('migrations/2026_11_11_150001_create_titan_agent_audit_events.php'));
 }
}
