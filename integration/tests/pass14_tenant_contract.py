from pathlib import Path
p=Path(__file__).resolve().parents[2]
read=lambda x:(p/x).read_text()
assert 'titan_business_memberships' in read('core/app/Services/Tenants/TenantAccess.php')
assert 'TenantAccess::require' in read('core/app/Http/Controllers/Admin/TenantDomainController.php')
assert 'TenantAccess::require' in read('core/app/Http/Controllers/Admin/TitanSiteController.php')
assert 'TenantAccess::require' in read('core/app/Http/Controllers/Admin/LandingCampaignController.php')
assert 'DomainBinding::assertPublicHost' in read('core/app/Http/Controllers/Front/TitanSiteController.php')
assert 'DomainBinding::assertPublicHost' in read('core/app/Http/Controllers/Front/LandingCampaignController.php')
assert 'tenant_id' in read('core/database/migrations/2026_11_11_140001_create_titan_business_tenants.php')
print('tenant source contracts passed')
