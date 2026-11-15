<?php
declare(strict_types=1);
namespace App\Booking\Widget;
/** Server-validated, read-only booking catalogue component. */
final class CatalogScope {public static function assertTenant(array $scope):void {if(empty($scope['tenant_verified'])||empty($scope['database_verified'])||!empty($scope['customer_tenant_id']))throw new \DomainException('Catalog scope not authorised');} }
