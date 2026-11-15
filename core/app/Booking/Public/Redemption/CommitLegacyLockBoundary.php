<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** A lock lease is never silently abandoned after a partial acquire or failed release. */
final class CommitLegacyLockBoundary {
 private bool $active=false;
 private bool $dirty=false;
 public function __construct(private $acquire,private $release) {}
 public function before(\PDO $db,array $selection):void {
  if($this->active||$this->dirty)throw new \RuntimeException('Lock lease not released');
  try{($this->acquire)($db,$selection);$this->active=true;}
  catch(\Throwable $primary){
   try{($this->release)($db);}catch(\Throwable $cleanup){$this->dirty=true;throw new \RuntimeException('Lock acquisition and cleanup failed',0,$primary);}
   throw $primary;
  }
 }
 public function after(\PDO $db):void {
  if(!$this->active&&!$this->dirty)throw new \RuntimeException('No active lock lease');
  try{($this->release)($db);$this->active=false;$this->dirty=false;}
  catch(\Throwable $e){$this->dirty=true;throw $e;}
 }
 public function isHeldOrUncertain():bool{return $this->active||$this->dirty;}
}
