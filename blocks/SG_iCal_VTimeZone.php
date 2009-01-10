<?php // BUILD: Remove line

/**
 * The wrapper for vtimezones. Stores the timezone-id and the setup for
 * daylight savings and standard time.
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_VTimeZone {
	private $tzid;
	private $daylight;
	private $standard;
	private $cache = array();
	
	/**
	 * Constructs a new SG_iCal_VTimeZone
	 */
	public function __construct( $data ) {
		require_once dirname(__FILE__).'/../helpers/SG_iCal_Freq.php'; // BUILD: Remove line
	
		$this->tzid = $data['tzid'];
		$this->daylight = $data['daylight'];
		$this->standard = $data['standard'];
	}
	
	/**
	 * Returns the timezone-id for this timezone. (Used to 
	 * differentiate between different tzs in a calendar)
	 * @return string
	 */
	public function getTimeZoneId() {
		return $this->tzid;
	}
	
	/**
	 * Returns the given offset in this timezone for the given
	 * timestamp. (eg +0200)
	 * @param int $ts
	 * @return string
	 */
	public function getOffset( $ts ) {
		$act = $this->getActive($ts);
		return $this->{$act}['tzoffsetto'];
	}
	
	/**
	 * Returns the timezone name for the given timestamp (eg CEST)
	 * @param int $ts
	 * @return string
	 */
	public function getTimeZoneName($ts) {
		$act = $this->getActive($ts);
		return $this->{$act}['tzname'];
	}
	
	/**
	 * Determines which of the daylight or standard is the active
	 * setting.
	 * The call is cached for a given timestamp, so a call to
	 * getOffset and getTimeZoneName with the same ts won't calculate
	 * the answer twice.
	 * @param int $ts
	 * @return string standard|daylight
	 */
	private function getActive( $ts ) {
		if( isset($this->cache[$ts]) ) {
			return $this->cache[$ts];
		}
		
		$daylight_freq = new SG_iCal_Freq($this->daylight['rrule'], strtotime($this->daylight['dtstart']));
		$last_dst = $daylight_freq->lastOccurrence($ts);
		if( date('Y') == date('Y', $last_dst) ) {
			$this->cache[$ts] = 'daylight';
		} else {
			$this->cache[$ts] = 'standard';
		}
		
		return $this->cache[$ts];
	}
}
