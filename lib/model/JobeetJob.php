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

    if (!$this->getToken())
    {
      $this->setToken(sha1($this->getEmail().rand(11111, 99999)));
    }

    if (is_null($con))
    {
      $con = Propel::getConnection(JobeetJobPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    $con->beginTransaction();
    try
    {
      $ret = parent::save($con);

      $this->updateLuceneIndex();

      $con->commit();

      return $ret;
    }
    catch (Exception $e)
    {
      $con->rollBack();
      throw $e;
    }
  }

  public function updateLuceneIndex()
  {
    $index = JobeetJobPeer::getLuceneIndex();

    // remove existing entries
    foreach ($index->find('pk:'.$this->getId()) as $hit)
    {
      $index->delete($hit->id);
    }

    // don't index expired and non-activated jobs
    if ($this->isExpired() || !$this->getIsActivated())
    {
      return;
    }

    $doc = new Zend_Search_Lucene_Document();

    // store job primary key to identify it in the search results
    $doc->addField(Zend_Search_Lucene_Field::Keyword('pk', $this->getId()));

    // index job fields
    $doc->addField(Zend_Search_Lucene_Field::UnStored('position', $this->getPosition(), 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::UnStored('company', $this->getCompany(), 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::UnStored('location', $this->getLocation(), 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $this->getDescription(), 'utf-8'));

    // add job to the index
    $index->addDocument($doc);
    $index->commit();
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

  public function getTypeName()
  {
    return $this->getType() ? JobeetJobPeer::$types[$this->getType()] : '';
  }

  public function isExpired()
  {
    return $this->getDaysBeforeExpires() < 0;
  }

  public function expiresSoon()
  {
    return $this->getDaysBeforeExpires() < 5;
  }

  public function getDaysBeforeExpires()
  {
    return ceil(($this->getExpiresAt('U') - time()) / 86400);
  }

  public function publish()
  {
    $this->setIsActivated(true);
    $this->save();
  }

  public function getCategoryName() {
    return JobeetJob ::getJobeetCategory();
  }

  public function extend($force = false)
  {
    if (!$force && !$this->expiresSoon())
    {
      return false;
    }

    $this->setExpiresAt(time() + 86400 * sfConfig::get('app_active_days'));
    $this->save();

    return true;
  }

  public function asArray($host)
  {
    return array(
      'category'     => $this->getJobeetCategory()->getName(),
      'type'         => $this->getType(),
      'company'      => $this->getCompany(),
      'logo'         => $this->getLogo() ? 'http://'.$host.'/uploads/jobs/'.$this->getLogo() : null,
      'url'          => $this->getUrl(),
      'position'     => $this->getPosition(),
      'location'     => $this->getLocation(),
      'description'  => $this->getDescription(),
      'how_to_apply' => $this->getHowToApply(),
      'expires_at'   => $this->getCreatedAt('c'),
    );
  }

  public function delete(PropelPDO $con = null)
  {
    $index = JobeetJobPeer::getLuceneIndex();

    foreach ($index->find('pk:'.$this->getId()) as $hit)
    {
      $index->delete($hit->id);
    }

    return parent::delete($con);
  }
}
