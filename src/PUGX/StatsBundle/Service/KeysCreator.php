<?php

namespace PUGX\StatsBundle\Service;


class KeysCreator
{
    const KEY_PREFIX = 'STAT';
    const KEY_TOTAL = 'TOTAL';
    const KEY_HASH_NAME = 'REPO';
    const KEY_LIST_NAME = 'LIST';
    const KEY_REFERER_SUFFIX = 'REFE';

    const FORMAT_YEAR = 'Y';
    const FORMAT_MONTH = 'Y_m';
    const FORMAT_DAY = 'Y_m_d';

    private $keyTotal;
    private $keyPrefix;
    private $keyHash;
    private $keyList;

    public function __construct($keyTotal = self::KEY_TOTAL, $keyPrefix = self::KEY_PREFIX, $keyHash = self::KEY_HASH_NAME, $keyList = self::KEY_LIST_NAME, $keyReferer = self::KEY_REFERER_SUFFIX)
    {
        $this->keyPrefix = $keyPrefix;
        $this->keyTotal = $this->concatenateKeys($keyPrefix, $keyTotal);
        $this->keyHash = $this->concatenateKeys($keyPrefix, $keyHash);
        $this->keyList = $this->concatenateKeys($keyPrefix, $keyList);
        $this->keyReferer = $this->concatenateKeys($this->keyList , $keyReferer);
    }

    /**
     * Create the yearly key with prefix eg. 'total_2003'
     *
     * @param \DateTime $datetime
     *
     * @return string
     */
    public function createYearlyKey(\DateTime $datetime = null)
    {
        return sprintf("%s_%s", $this->keyTotal, $this->formatDate($datetime, self::FORMAT_YEAR));
    }

    /**
     * Create the monthly key with prefix eg. 'total_2003_11'
     *
     * @param \DateTime $datetime
     *
     * @return string
     */
    public function createMonthlyKey(\DateTime $datetime = null)
    {
        return sprintf("%s_%s", $this->keyTotal, $this->formatDate($datetime, self::FORMAT_MONTH));
    }

    /**
     * Create the daily key with prefix eg. 'total_2003_11_29'
     *
     * @param \DateTime $datetime
     *
     * @return string
     */
    public function createDailyKey(\DateTime $datetime = null)
    {
        return sprintf("%s_%s", $this->keyTotal, $this->formatDate($datetime, self::FORMAT_DAY));
    }

    /**
     * @return string
     */
    public function getKeyTotal()
    {
        return $this->keyTotal;
    }

    /**
     * @return string
     */
    public function getKeyPrefix()
    {
        return $this->keyPrefix;
    }

    /**
     * @return string
     */
    public function getKeyList()
    {
        return $this->keyList;
    }

    /**
     * @param null $repository
     * @return string
     */
    public function getKeyHash($repository = null)
    {
        if (null !== $repository) {
            return $this->concatenateKeys($this->getKeyHash(), $repository);
        }

        return $this->keyHash;
    }

    public function getRefererKey()
    {
        return $this->keyReferer;
    }

    /**
     * format a date.
     *
     * @param \DateTime $datetime
     * @param string    $format
     *
     * @return string
     */
    private function formatDate(\DateTime $datetime = null, $format = self::FORMAT_DAY)
    {
        if (null == $datetime) {
            $datetime = new \DateTime('now');
        }

        return $datetime->format($format);
    }

    /**
     * Generate the Key with the default prefix.
     *
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    private function concatenateKeys($prefix, $suffix)
    {
        return sprintf("%s.%s", $prefix, $suffix);
    }
} 