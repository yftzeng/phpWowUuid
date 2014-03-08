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

namespace Wow\Util;

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

class Uuid
{

    /**
     * @param boolean $dashes format with dashed or not
     *
     * @comment UUID version 1
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
     * @param boolean $dashes format with dashed or not
     *
     * @comment UUID version 4
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

}
