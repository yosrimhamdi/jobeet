<?php


/**
 * Skeleton subclass for representing a row from the 'jobeet_category' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * 09/05/22 17:42:28
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class JobeetCategory extends BaseJobeetCategory {
  public function __toString()
  {
    return $this->getName();
  }
}
