<?php
/**
 * PHP WowUuid
 *
 * PHP version 5
 *
 * @category Wow
 * @package  WowUuid
 * @author   Tzeng, Yi-Feng <yftzeng@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/yftzeng/phpWowUuid
 */

/**
 * PHP WowUuid
 *
 * PHP version 5
 *
 * @category Wow
 * @package  WowUuid
 * @author   Tzeng, Yi-Feng <yftzeng@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/yftzeng/phpWowUuid
 */

namespace Wow\Util;

class Uuid
{

    // Change to time() * 1000 of new project start
    private static $_epoch_offset = 1491981505000;

    private static $_alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    private static $_machineid_bits = 6;
    private static $_datacenterid_bits = 4;
    private static $_sequence_bits = 12;

    private static $_last_timestamp = 1;
    private static $_seq = 1;

    /**
     * UUID version 1
     *
     * @param boolean $dashes format with dashed or not
     *
     * @return string
     */
    public static function v1($dashes = true)
    {
        if ($dashes) {
            $format = '%s-%s-%04x-%02x%02x-%s';
        } else {
            $format = '%s%s%04x%02x%02x%s';
        }

        if (isset($_SERVER['SERVER_ADDR'])) {
            $node = substr(md5($_SERVER['SERVER_ADDR']), 0, 12);
        } else {
            $characters = 'abcdef0123456789';
            $node = '';
            for ($p = 0; $p < 12; $p++) {
                $node .= $characters[mt_rand(0, 15)];
            }
        }

        $tp = gettimeofday();
        $time = ($tp['sec'] * 10000000) + ($tp['usec'] * 10) + 0x01B21DD213814000;

        $uuid_low = sprintf('%08x', $time & 0xffffffff);
        $uuid_mid = sprintf('%04x', ($time >> 32) & 0xffff);
        $uuid_hi  = sprintf('%04x', ($time >> 48) & 0x0fff);

        $timeHi = hexdec($uuid_hi) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= 1 << 12;

        $clockSeq = mt_rand(0, 1 << 14);
        $clockSeqHi = ($clockSeq >> 8) & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;
        $clockSeq &= 0xff;

        return sprintf(
            $format,
            $uuid_low,
            $uuid_mid,
            $timeHi,
            $clockSeqHi,
            $clockSeq,
            $node
        );
    }

     /**
     * UUID version 1 ordered
     *
     * @param boolean $dashes format with dashed or not
     *
     * @return string
     */
    public static function v1_order($dashes = true)
    {
        if ($dashes) {
            $format = '%04x-%s-%s-%02x%02x-%s';
        } else {
            $format = '%04x%s%s%02x%02x%s';
        }

        if (isset($_SERVER['SERVER_ADDR'])) {
            $node = substr(md5($_SERVER['SERVER_ADDR']), 0, 12);
        } else {
            $characters = 'abcdef0123456789';
            $node = '';
            for ($p = 0; $p < 12; $p++) {
                $node .= $characters[mt_rand(0, 15)];
            }
        }

        $tp = gettimeofday();
        $time = ($tp['sec'] * 10000000) + ($tp['usec'] * 10) + 0x01B21DD213814000;

        $uuid_low = sprintf('%08x', $time & 0xffffffff);
        $uuid_mid = sprintf('%04x', ($time >> 32) & 0xffff);
        $uuid_hi  = sprintf('%04x', ($time >> 48) & 0x0fff);

        $timeHi = hexdec($uuid_hi) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= 1 << 12;

        $clockSeq = mt_rand(0, 1 << 14);
        $clockSeqHi = ($clockSeq >> 8) & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;
        $clockSeq &= 0xff;

        return sprintf(
            $format,
            $timeHi,
            $uuid_mid,
            $uuid_low,
            $clockSeqHi,
            $clockSeq,
            $node
        );
    }

    /**
     * UUID version 4
     *
     * @param boolean $dashes format with dashed or not
     *
     * @return string
     */
    public static function v4($dashes = true)
    {
        if ($dashes) {
            $format = '%s-%s-%04x-%04x-%s';
        } else {
            $format = '%s%s%04x%04x%s';
        }

        $random_pseudo_bytes = bin2hex(openssl_random_pseudo_bytes(12));

        return sprintf(
            $format,
            substr($random_pseudo_bytes, 0, 8),
            substr($random_pseudo_bytes, 8, 4),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            substr($random_pseudo_bytes, 12)
        );
    }

    /**
     * Twitter Snowflake like implementation
     *
     * @param int $datacenter_id datacenter unique id
     * @param int $machine_id    machine unique id
     *
     * @return int
     */
    public static function snowflake($datacenter_id, $machine_id)
    {
        $datacenter_id = (int)$datacenter_id;
        $machine_id  = (int)$machine_id;

        if ($datacenter_id > (-1 ^ (-1 << self::$_datacenterid_bits)) || $datacenter_id < 0) {
            return false;
        }
        if ($machine_id > (-1 ^ (-1 << self::$_machineid_bits)) || $machine_id < 0) {
            return false;
        }

        $seq = mt_rand(1, (2 << (self::$_sequence_bits-1) - 1));
        $timestamp = floor(microtime(true) * 1000);

        return (($timestamp - self::$_epoch_offset) << (self::$_datacenterid_bits + self::$_machineid_bits + self::$_sequence_bits)) |
            ($datacenter_id << self::$_datacenterid_bits) |
            ($machine_id << self::$_machineid_bits) |
            $seq;
    }

    /**
     * Twitter Snowflake like implemented by random
     *
     * @return int
     */
    public static function snowflake_random()
    {
        $seq = mt_rand(1, (2 << (self::$_sequence_bits + self::$_machineid_bits + self::$_datacenterid_bits -1) - 1));
        $timestamp = floor(microtime(true) * 1000);

        return (($timestamp - self::$_epoch_offset) << (self::$_datacenterid_bits + self::$_machineid_bits + self::$_sequence_bits)) |
            $seq;
    }

    /**
     * Twitter Snowflake like implemented by order
     *
     * @param int $datacenter_id datacenter unique id
     * @param int $machine_id    machine unique id
     *
     * Ref: https://github.com/golangfan/phpsnowflake
     *
     * @return string
     */
    public static function snowflake_order($datacenter_id, $machine_id)
    {
        do {
            $timestamp = floor(microtime(true) * 1000);
        } while ($timestamp < self::$_last_timestamp);

        $datacenter_id = (int)$datacenter_id;
        $machine_id  = (int)$machine_id;

        if ($datacenter_id > (-1 ^ (-1 << self::$_datacenterid_bits)) || $datacenter_id < 0) {
            return false;
        }
        if ($machine_id > (-1 ^ (-1 << self::$_machineid_bits)) || $machine_id < 0) {
            return false;
        }

        if ($timestamp === self::$_last_timestamp) {
            self::$_seq = self::$_seq + 1 & (-1 ^ (-1 << self::$_sequence_bits));
            if (self::$_seq === 1) {
                do {
                    $timestamp = floor(microtime(true) * 1000);
                } while ($timestamp < self::$_last_timestamp);
            }
        } else {
            self::$_seq = 1;
        }
        self::$_last_timestamp = $timestamp;

        return (($timestamp - self::$_epoch_offset) << (self::$_datacenterid_bits + self::$_machineid_bits + self::$_sequence_bits)) |
            ($datacenter_id << self::$_datacenterid_bits) |
            ($machine_id << self::$_machineid_bits) |
            (self::$_seq << self::$_sequence_bits);
    }
}
