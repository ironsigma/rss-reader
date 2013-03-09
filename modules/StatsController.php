<?php
/**
 * User Login controller
 * @package com\izylab\reader
 */
class StatsController {
    public function handleRequest($args) {
        $template = new Template('stats');

        $db = Database::getInstance();
        $stm = $db->prepare(
            "SELECT ".
                "feed_id AS \"id\", ".
                "name, ".
                "DATE(updated, 'unixepoch') AS \"date\", ".
                "SUM(total_count) AS \"total\", ".
                "SUM(new_count) AS \"new\" ".
            "FROM update_log u ".
            "JOIN feed f ON u.feed_id=f.id ".
            "WHERE updated > strftime('%s', date('now', '-14 day')) ".
            "GROUP BY feed_id, \"date\""
        );

        // get data
        $res = $stm->execute();
        $raw_data = array();
        $feed_meta = array();
        while ( $row = $res->fetchArray(SQLITE3_ASSOC) ) {
            $raw_data[$row['id']][$row['date']] = $row['new'];
            $feed_meta[$row['id']] = array('name' => $row['name']);
        }

        // x-axis: date labels and peeks
        $labels = array();
        $i = 0;
        foreach ( $raw_data as $feed ) {
            foreach ( $feed as $date => $x ) {
                $l = substr($date, 5);
                $labels[] = "[$i, '$l']";
                $i ++;
            }
            break; // only need one for all feeds
        }

        // find tier
        foreach ( $raw_data as $id => $feed ) {
            $sum = 0;
            foreach ( $feed as $date => $count ) {
                $sum += $count;
            }
            $avg = $sum / count($feed);
            if ( $avg >= 15 ) {
                 $feed_meta[$id]['tier'] = 'high';
            } elseif ( $avg >= 1.5 ) {
                 $feed_meta[$id]['tier'] = 'med';
            } elseif ( $avg >= 0.8 ) {
                 $feed_meta[$id]['tier'] = 'low';
            } else {
                 $feed_meta[$id]['tier'] = 'rare';
            }
        }

        // y-axis point data
        $data = array();
        foreach ( $raw_data as $id => $feed ) {
            $feed_data = array();
            $i = 0;
            foreach ( $feed as $date => $count ) {
                $feed_data[] = "[$i, '$count']";
                $i ++;
            }

            $data[$feed_meta[$id]['tier']][] = array(
                'label' => $feed_meta[$id]['name'],
                'points' => join(',', $feed_data)
            );
        }

        $template->db_size = Database::getSize();
        $template->labels = join(',', $labels);
        $template->data = $data;
        $template->display();
    }
}
