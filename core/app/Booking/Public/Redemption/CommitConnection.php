<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitConnection { public static function assert(\PDO $db,string $expectedDatabase):void { if($db->getAttribute(\PDO::ATTR_DRIVER_NAME)!=='pgsql')throw new \RuntimeException('Booking redemption requires PostgreSQL'); if(!preg_match('/^[a-zA-Z0-9_]{1,64}$/D',$expectedDatabase))throw new \InvalidArgumentException('Invalid database');$s=$db->query('SELECT current_database()');if($s===false||$s->fetchColumn()!==$expectedDatabase)throw new \RuntimeException('Tenant database mismatch');if($db->getAttribute(\PDO::ATTR_EMULATE_PREPARES))throw new \RuntimeException('Emulated prepare disallowed'); }}
