<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoQtiTest\models\runner\time;

use oat\taoTests\models\runner\time\InconsistentCriteriaException;
use oat\taoTests\models\runner\time\InconsistentRangeException;
use oat\taoTests\models\runner\time\InvalidDataException;
use oat\taoTests\models\runner\time\InvalidStorageException;
use oat\taoTests\models\runner\time\TimeException;
use oat\taoTests\models\runner\time\TimeLine;
use oat\taoTests\models\runner\time\TimePoint;
use oat\taoTests\models\runner\time\TimeStorage;
use oat\taoTests\models\runner\time\Timer;

/**
 * Class QtiTimer
 * @package oat\taoQtiTest\models\runner\time
 */
class QtiTimer implements Timer
{
    /**
     * The TimeLine used to compute the duration
     * @var TimeLine
     */
    protected $timeLine;

    /**
     * The storage used to maintain the data
     * @var TimeStorage
     */
    protected $storage;

    /**
     * QtiTimer constructor.
     */
    public function __construct()
    {
        $this->timeLine = new QtiTimeLine();
    }

    /**
     * Adds a "server start" TimePoint at a particular timestamp for the provided ItemRef
     * @param string|array $tags
     * @param float $timestamp
     * @return Timer
     * @throws TimeException
     */
    public function start($tags, $timestamp)
    {
        // check the provided arguments
        if (!is_numeric($timestamp) || $timestamp < 0) {
            throw new InvalidDataException('start() needs a valid timestamp!');
        }
        
        // extract the TimePoint identification from the provided item, and find existing range
        $range = $this->getRange($tags);

        // validate the data consistence
        if ($this->isRangeOpen($range)) {
            // unclosed range found, auto closing
            // auto generate the timestamp for the missing END point, one microsecond earlier
            \common_Logger::i('Missing END TimePoint in QtiTimer, auto add an arbitrary value');
            $point = new TimePoint($tags, $timestamp - (1 / TimePoint::PRECISION), TimePoint::TYPE_END, TimePoint::TARGET_SERVER);
            $this->timeLine->add($point);
            $range[] = $point;
        }
        $this->checkTimestampCoherence($range, $timestamp);

        // append the new START TimePoint
        $point = new TimePoint($tags, $timestamp, TimePoint::TYPE_START, TimePoint::TARGET_SERVER);
        $this->timeLine->add($point);

        return $this;
    }

    /**
     * Adds a "server end" TimePoint at a particular timestamp for the provided ItemRef
     * @param string|array $tags
     * @param float $timestamp
     * @return Timer
     * @throws TimeException
     */
    public function end($tags, $timestamp)
    {
        // check the provided arguments
        if (!is_numeric($timestamp) || $timestamp < 0) {
            throw new InvalidDataException('end() needs a valid timestamp!');
        }

        // extract the TimePoint identification from the provided item, and find existing range
        $range = $this->getRange($tags);

        // validate the data consistence
        if ($this->isRangeOpen($range)) {
            $this->checkTimestampCoherence($range, $timestamp);

            // append the new END TimePoint
            $point = new TimePoint($tags, $timestamp, TimePoint::TYPE_END, TimePoint::TARGET_SERVER);
            $this->timeLine->add($point);    
        } else {
            // already closed range found, just log the info
            \common_Logger::i('Range already closed, or missing START TimePoint in QtiTimer, continue anyway');
        }

        return $this;
    }

    /**
     * Gets the first timestamp of the range for the provided tags
     * @param string|array $tags
     * @return float $timestamp
     */
    public function getFirstTimestamp($tags)
    {
        // extract the TimePoint identification from the provided item, and find existing range
        $range = $this->getRange($tags);
        $last = false;

        if (count($range)) {
            $last = $range[0]->getTimestamp();
        }

        return $last;
    }


    /**
     * Gets the last timestamp of the range for the provided tags
     * @param string|array $tags
     * @return bool|float $timestamp Returns the last timestamp of the range or false if none
     */
    public function getLastTimestamp($tags)
    {
        // extract the TimePoint identification from the provided item, and find existing range
        $range = $this->getRange($tags);
        $length = count($range);
        $last = false;

        if ($length) {
            $last = $range[$length - 1]->getTimestamp();
        }

        return $last;
    }


    /**
     * Adds "client start" and "client end" TimePoint based on the provided duration for a particular ItemRef
     * @param string|array $tags
     * @param float $duration
     * @return Timer
     * @throws TimeException
     */
    public function adjust($tags, $duration)
    {
        // check the provided arguments
        if (!is_null($duration) && (!is_numeric($duration) || $duration < 0)) {
            throw new InvalidDataException('adjust() needs a valid duration!');
        }

        // extract the TimePoint identification from the provided item, and find existing range
        $itemTimeLine = $this->timeLine->filter($tags, TimePoint::TARGET_SERVER);
        $range = $itemTimeLine->getPoints();

        // validate the data consistence
        $rangeLength = count($range);
        if (!$rangeLength || ($rangeLength % 2)) {
            throw new InconsistentRangeException('The time range does not seem to be consistent, the range is not complete!');
        }

        $serverDuration = $itemTimeLine->compute();

        // take care of existing client range
        $clientTimeLine = $this->timeLine->filter($tags, TimePoint::TARGET_CLIENT);
        $clientRange = $clientTimeLine->getPoints();
        $clientRangeLength = count($clientRange);
        if ($clientRangeLength) {
            $clientDuration = 0;
            try {
                $clientDuration = $clientTimeLine->compute();
            } catch(TimeException $e) {
                \common_Logger::i('Handled client range error');
            }

            if (is_null($duration)) {
                if ($clientDuration) {
                    $duration = $clientDuration;
                    \common_Logger::i("No client duration provided to adjust the timer, but a range already exist: ${duration}");
                } else {
                    $duration = $serverDuration;
                    \common_Logger::i("No client duration provided to adjust the timer, fallback to server duration: ${duration}");
                }
            }

            $removed = $this->timeLine->remove($tags, TimePoint::TARGET_CLIENT);
            if ($removed == $clientRangeLength) {
                \common_Logger::i("Replace client duration in timer: ${clientDuration} to ${duration}");
            } else {
                \common_Logger::w("Unable to replace client duration in timer: ${clientDuration} to ${duration}");
            }
        }

        // check if the client side duration is bound by the server side duration
        if (is_null($duration)) {
            $duration = $serverDuration;
            \common_Logger::i("No client duration provided to adjust the timer, fallback to server duration: ${duration}");
        } else if ($duration > $serverDuration) {
            \common_Logger::w("A client duration must not be larger than the server time range! (${duration} > ${serverDuration})");
            $duration = $serverDuration;
        }

        // extract range boundaries
        TimePoint::sort($range);
        $serverStart = $range[0];
        $serverEnd = $range[$rangeLength - 1];

        // adjust the range by inserting the client duration between the server overall time range boundaries
        $overallDuration = $serverEnd->getTimestamp() - $serverStart->getTimestamp();
        $delay = ($overallDuration - $duration) / 2;
        
        $start = new TimePoint($tags, $serverStart->getTimestamp() + $delay, TimePoint::TYPE_START, TimePoint::TARGET_CLIENT);
        $this->timeLine->add($start);
        
        $end = new TimePoint($tags, $serverEnd->getTimestamp() - $delay, TimePoint::TYPE_END, TimePoint::TARGET_CLIENT);
        $this->timeLine->add($end);

        return $this;
    }

    /**
     * Computes the total duration represented by the filtered TimePoints
     * @param string|array $tags A tag or a list of tags to filter
     * @param int $target The type of target TimePoint to filter
     * @return float Returns the total computed duration
     * @throws TimeException
     */
    public function compute($tags, $target)
    {
        // cannot compute a duration across different targets
        if (!$this->onlyOneFlag($target)) {
            throw new InconsistentCriteriaException('Cannot compute a duration across different targets!');    
        }
        
        return $this->timeLine->compute($tags, $target);
    }

    /**
     * Checks if the duration of a TimeLine subset reached the timeout
     * @param float $timeLimit The time limit against which compare the duration
     * @param string|array $tags A tag or a list of tags to filter
     * @param int $target The type of target TimePoint to filter
     * @return bool Returns true if the timeout is reached
     * @throws TimeException
     */
    public function timeout($timeLimit, $tags, $target)
    {
        $duration = $this->compute($tags, $target);
        return $duration >= $timeLimit;
    }

    /**
     * Sets the storage used to maintain the data
     * @param TimeStorage $storage
     * @return Timer
     */
    public function setStorage(TimeStorage $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Gets the storage used to maintain the data
     * @return TimeStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Saves the data to the storage
     * @return Timer
     * @throws InvalidStorageException
     * @throws InvalidDataException
     * @throws \common_exception_Error
     */
    public function save()
    {
        if (!$this->storage) {
            throw new InvalidStorageException('A storage must be defined in order to store the data!');
        }
        
        $this->storage->store(serialize($this->timeLine));
        
        return $this;
    }

    /**
     * Loads the data from the storage
     * @return Timer
     * @throws InvalidStorageException
     * @throws InvalidDataException
     * @throws \common_exception_Error
     */
    public function load()
    {
        if (!$this->storage) {
            throw new InvalidStorageException('A storage must be defined in order to store the data!');
        }
        
        $data = $this->storage->load();
        
        if (isset($data)) {
            $this->timeLine = unserialize($data);

            if (!$this->timeLine instanceof TimeLine) {
                throw new InvalidDataException('The storage did not provide acceptable data when loading!');
            }
        }

        return $this;
    }

    /**
     * Checks if a timestamp is consistent with existing TimePoint within a range
     * @param array $points
     * @param float $timestamp
     * @throws InconsistentRangeException
     */
    protected function checkTimestampCoherence($points, $timestamp)
    {
        foreach($points as $point) {
            if ($point->getTimestamp() > $timestamp) {
                throw new InconsistentRangeException('A new TimePoint cannot be set before an existing one!');
            }
        }
    }

    /**
     * Check if the provided range is open (START TimePoint and no related END)
     * @param array $range
     * @return bool
     */
    protected function isRangeOpen($range)
    {
        $nb = count($range);
        return $nb && ($nb % 2) && ($range[$nb - 1]->getType() == TimePoint::TYPE_START);
    }

    /**
     * Extracts a sorted range of TimePoint
     *
     * @param array $tags
     * @return array
     */
    protected function getRange($tags)
    {
        $range = $this->timeLine->find($tags, TimePoint::TARGET_SERVER);

        TimePoint::sort($range);

        return $range;
    }
    
    /**
     * Checks if a binary flag contains exactly one flag set
     * @param $value
     * @return bool
     */
    protected function onlyOneFlag($value)
    {
        return $this->binaryPopCount($value) == 1;
    }

    /**
     * Count the number of bits set in a 32bits integer
     * @param int $value
     * @return int
     */
    protected function binaryPopCount($value)
    {
        $value -= (($value >> 1) & 0x55555555);
        $value = ((($value >> 2) & 0x33333333) + ($value & 0x33333333));
        $value = ((($value >> 4) + $value) & 0x0f0f0f0f);
        $value += ($value >> 8);
        $value += ($value >> 16);
        return $value & 0x0000003f;
    }
}
