<?php

class JobeetJob extends BaseJobeetJob {
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

  public function save(PropelPDO $con = null)
  {
    if ($this->isNew() && !$this->getExpiresAt())
    {
      $now = $this->getCreatedAt() ? $this->getCreatedAt('U') : time();
      $this->setExpiresAt($now + 86400 * sfConfig::get('app_active_days'));
    }

    return parent::save($con);
  }

  public function __toString()
  {
    return sprintf('%s at %s (%s)', $this->getPosition(), $this->getCompany(), $this->getLocation());
  }

  public function getCompanySlug()
  {
    return Jobeet::slugify($this->getCompany());
  }

  public function getPositionSlug()
  {
    return Jobeet::slugify($this->getPosition());
  }

  public function getLocationSlug()
  {
    return Jobeet::slugify($this->getLocation());
  }
}
