<?php
/**
 * Update Log DAO
 * @package com\hawkprime\reader
 */
class UpdateDao {
    public static function insert(Update $update) {
        $update->id = Database::table(Update::getTable())->insert($update);
        return $update;
    }
    public static function findUpdates() {
        $sql = 'SELECT feed_id, updated, total_count, new_count '.
            'FROM update_log '.
            'WHERE updated > DATE_SUB(CURDATE(), INTERVAL 7 DAY) '.
            'ORDER BY feed_id ASC, updated DESC';

        list($statement,) = Database::connection()->execute($sql);

        $updates = array();
        $curr_id = null;
        $last_id = null;
        $sum = 0;
        $count = 0;
        $high = -1;
        $low = 101;
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $curr_id = $row['feed_id'];

            if ($last_id != null && $last_id != $curr_id) {
                $updates[$last_id]['average'] = round($sum / $count, 2);
                $updates[$last_id]['high'] = $high;
                $updates[$last_id]['low'] = $low;
                $count = 0;
                $sum = 0;
                $high = -1;
                $low = 101;
            }

            $last_id = $curr_id;
            $percent = $row['total_count'] == 0 ? 0.00 : round(($row['new_count'] / $row['total_count']) * 100, 2);
            $sum += $percent;
            $count++;

            if ($percent > $high) {
                $high = $percent;
            }

            if ($percent < $low) {
                $low = $percent;
            }

            if ($percent > 80) {
                $level = 'critical';
            } elseif ($percent > 50) {
                $level = 'high';
            } elseif ($percent > 40) {
                $level = 'normal';
            } elseif ($percent > 20) {
                $level = 'medium';
            } else {
                $level = 'low';
            }

            $updates[$curr_id]['updates'][] = array(
                'date' => $row['updated'],
                'total' => $row['total_count'],
                'new' => $row['new_count'],
                'percent' => $percent,
                'level' => $level,
            );

        }

        if ($count) {
            $updates[$curr_id]['average'] = round($sum / $count, 2);
            $updates[$curr_id]['high'] = $high;
            $updates[$curr_id]['low'] = $low;
        }

        return $updates;
    }
    public static function findLatestUpdates() {
        $sql = 'SELECT u.id, u.updated, u.total_count, u.new_count, u.feed_id '.
           'FROM update_log u WHERE updated=(SELECT MAX(updated) FROM update_log WHERE feed_id=u.feed_id)';

        list($statement,) = Database::connection()->execute($sql);
        $updates = $statement->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Update');
        $lookup = array();
        foreach ( $updates as $update ) {
            $lookup[$update->feed_id] = $update;
        }
        return $lookup;
    }
    public static function deleteOldUpdates() {
        $sql = 'DELETE FROM update_log WHERE updated < NOW() - INTERVAL 2 WEEK';
        Database::connection()->execute($sql);
    }
}
