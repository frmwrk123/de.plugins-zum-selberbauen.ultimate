<?php
namespace ultimate\system\config\storage;
use ultimate\system\config\ConfigEntry;

/**
 * Stores the important data of any configuration.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage system.config.storage
 * @category Ultimate CMS
 */
class ConfigStorage implements \Serializable {
        
    /**
     * Contains all positions.
     * @var array
     */
    protected $entries = array(
        'left' => array(),
        'center' => array(),
        'right' => array()
    );
    
    /**
     * Returns the entries.
     *
     * @return array
     */
    public function getEntries() {
        return $this->entries;
    }
    
    /**
     * Adds a new entry at the specified position.
     *
     * @param ConfigEntry $entry
     * @param string $column
     * @param int $showorder
     */
    public function addEntry(ConfigEntry $entry, $column, $showorder) {
        $showorder = intval($showorder);
        $column = trim($column);
        if (!array_key_exists($showorder, $this->entries[$column])) $this->entries[$column][] = $entry;
        else {
            array_splice($this->entries[$column], $showorder, 0, array($entry));
        }
    }
    
    /**
     * Removes an entry from the specified position.
     *
     * @param string $column
     * @param int $showorder
     */
    public function removeEntry($column, $showorder) {
        $secondPartOfArray = array_slice($this->entries[$column], $showorder + 1);
        array_splice($this->entries[$column], $showorder, count($this->entries[$column]), $secondPartOfArray);
    }
    
    /**
     * Serializes this object.
     *
     * @see \Serializable::serialize()
     */
    public function serialize() {
        $data = array(
            'entries' => $this->entries
        );
        return serialize($data);
    }
    
    /**
     * Unserializes this object.
     *
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        $data = unserialize($serialized);
        $this->entries = $data['entries'];
    }
}
