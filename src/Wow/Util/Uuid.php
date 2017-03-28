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
    private static $_epoch_offset = 1490725807000;

    private static $_alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

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
     * 41-bits : Timestamp (millisecond precision, bespoke epoch)
     * 10-bits : Machine ID
     * 12-bits : Sequence number
     *
     * @param int $datacenter_id datacenter unique id
     * @param int $machine_id    machine unique id
     *
     * @return string
     */
    public static function snowflake($datacenter_id, $machine_id)
    {
        /*
         * 41-bits : Timestamp
         */
        $time = decbin(
            (2 << 39)
            - 1 + floor(microtime(true) * 1000) - self::$_epoch_offset
        );

        /*
         * 4-bits : Datacenter ID
         */
        $datacenter_id = decbin(
            (2 << 2) - 1 + $datacenter_id
        );

        /*
         * 6-bits : Machine ID
         */
        $machine_id = decbin((2 << 4) - 1 + $machine_id);

        /*
         * 12-bits : Sequence number
         */
        $seq = decbin((2 << 10) - 1 + (mt_rand(1, (2 << 10) - 1)));

        return bindec($time.$datacenter_id.$machine_id.$seq);
    }

    /**
     * Twitter Snowflake like implementation v4
     *
     * 41-bits : Timestamp (millisecond precision, bespoke epoch)
     * 22-bits : Sequence number
     *
     * @return string
     */
    public static function snowflake_v4()
    {
        /*
         * 41-bits : Timestamp
         */
        $time = decbin(
            (2 << 39)
            - 1 + floor(microtime(true) * 1000) - self::$_epoch_offset
        );

        /*
         * 22-bits : Sequence number
         */
        $seq = decbin((2 << 20) - 1 + (mt_rand(1, (2 << 20) - 1)));

        return bindec($time.$seq);
    }

}
