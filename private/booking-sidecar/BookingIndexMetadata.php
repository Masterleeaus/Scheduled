<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Normalize INFORMATION_SCHEMA.STATISTICS without mistaking composite uniqueness for singleton uniqueness. */
final class BookingIndexMetadata {
    public static function normalize(array $rows): array {
        $groups=[];
        foreach ($rows as $row) {
            $table=$row['TABLE_NAME']??null;
            $index=$row['INDEX_NAME']??null;
            $col=$row['COLUMN_NAME']??null;
            $position=$row['SEQ_IN_INDEX']??null;
            $nonUnique=$row['NON_UNIQUE']??null;
            if (!is_string($table)||$table===''||!is_string($index)||$index===''||!is_string($col)||$col===''
                ||!is_numeric($position)|| (int)$position < 1 || !in_array((string)$nonUnique,['0','1'],true)) {
                throw new \RuntimeException('Unexpected database index metadata');
            }
            $key=$table."\0".$index;
            if (!isset($groups[$key])) $groups[$key]=['table'=>$table,'index'=>$index,'unique'=>((int)$nonUnique===0),'columns'=>[]];
            if ($groups[$key]['unique']!==((int)$nonUnique===0) || isset($groups[$key]['columns'][(int)$position])) {
                throw new \RuntimeException('Conflicting database index metadata');
            }
            $groups[$key]['columns'][(int)$position]=$col;
        }
        $indexes=[];
        foreach ($groups as $group) {
            ksort($group['columns']);
            if (array_keys($group['columns'])!==range(1,count($group['columns']))) {
                throw new \RuntimeException('Incomplete database index metadata');
            }
            foreach ($group['columns'] as $col) {
                // A composite unique index does NOT establish unique identity for each member column.
                $indexes[$group['table']][]=['column'=>$col,'unique'=>$group['unique']&&count($group['columns'])===1];
            }
        }
        return $indexes;
    }
}
