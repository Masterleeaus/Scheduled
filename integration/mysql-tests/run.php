<?php
declare(strict_types=1);
require __DIR__.'/connection.php';
try{$db=pass16_test_db();}catch(Throwable $e){fwrite(STDERR,'SKIP: '.$e->getMessage().PHP_EOL);exit(77);}
$created=[];
try {
 $db->exec('CREATE TABLE titan_test16_transactions (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, token VARCHAR(64) NOT NULL UNIQUE) ENGINE=InnoDB');
 $created[]='titan_test16_transactions';
 $db->exec('CREATE TABLE titan_test16_idempotency (request_key VARCHAR(96) PRIMARY KEY, fingerprint CHAR(64) NOT NULL) ENGINE=InnoDB');
 $created[]='titan_test16_idempotency';
 $db->beginTransaction();$db->prepare('INSERT INTO titan_test16_transactions (token) VALUES (?)')->execute(['should_rollback']);$db->rollBack();
 if((int)$db->query('SELECT COUNT(*) FROM titan_test16_transactions')->fetchColumn()!==0)throw new RuntimeException('Rollback failed');
 $db->beginTransaction();$db->prepare('INSERT INTO titan_test16_transactions (token) VALUES (?)')->execute(['should_commit']);$db->commit();
 if((int)$db->query('SELECT COUNT(*) FROM titan_test16_transactions')->fetchColumn()!==1)throw new RuntimeException('Commit failed');
 $q=$db->prepare('INSERT INTO titan_test16_idempotency (request_key,fingerprint) VALUES (?,?)');$q->execute([str_repeat('a',20),str_repeat('f',64)]);
 try{$q->execute([str_repeat('a',20),str_repeat('0',64)]);throw new RuntimeException('Idempotency duplicate accepted');}catch(PDOException $e){}
 $key='tb:123456789abc:staff:19';$s=$db->prepare('SELECT GET_LOCK(?,1)');$s->execute([$key]);if((int)$s->fetchColumn()!==1)throw new RuntimeException('GET_LOCK failed');
 $s=$db->prepare('SELECT RELEASE_LOCK(?)');$s->execute([$key]);if((int)$s->fetchColumn()!==1)throw new RuntimeException('RELEASE_LOCK failed');
 echo "PASS: transactional rollback/commit, unique request claim, named lock lifecycle\n";
} catch(Throwable $e){fwrite(STDERR,'FAIL: '.$e->getMessage().PHP_EOL);exit(1);}
finally { foreach(array_reverse($created) as $table){try{$db->exec('DROP TABLE IF EXISTS `'.$table.'`');}catch(Throwable $cleanup){fwrite(STDERR,'Cleanup failed: '.$cleanup->getMessage().PHP_EOL);}} }
