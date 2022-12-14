<?php

class JobeetJobPeer extends BaseJobeetJobPeer
{
  static public $types = array(
    'full-time' => 'Full time',
    'part-time' => 'Part time',
    'freelance' => 'Freelance',
  );

  static public function getActiveJobs(Criteria $criteria = null)
  {
    return self::doSelect(self::addActiveJobsCriteria($criteria));
  }

  static public function countActiveJobs(Criteria $criteria = null)
  {
    return self::doCount(self::addActiveJobsCriteria($criteria));
  }

  static public function addActiveJobsCriteria(Criteria $criteria = null)
  {
    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->add(self::EXPIRES_AT, time(), Criteria::GREATER_THAN);
    $criteria->addDescendingOrderByColumn(self::CREATED_AT);
    $criteria->add(self::IS_ACTIVATED, true);

    return $criteria;
  }

  static public function doSelectActive(Criteria $criteria)
  {
    return self::doSelectOne(self::addActiveJobsCriteria($criteria));
  }

  static public function cleanup($days)
  {
    $criteria = new Criteria();
    $criteria->add(self::IS_ACTIVATED, false);
    $criteria->add(self::CREATED_AT, time() - 86400 * $days, Criteria::LESS_THAN);

    return self::doDelete($criteria);
  }

  static public function getForToken(array $parameters)
  {
    $affiliate = JobeetAffiliatePeer::getByToken($parameters['token']);
    if (!$affiliate || !$affiliate->getIsActive())
    {
      throw new sfError404Exception(sprintf('Affiliate with token "%s" does not exist or is not activated.', $parameters['token']));
    }

    return $affiliate->getActiveJobs();
  }

  static public function getLuceneIndex()
  {
    ProjectConfiguration::registerZend();

    if (file_exists($index = self::getLuceneIndexFile()))
    {
      return Zend_Search_Lucene::open($index);
    }

    return Zend_Search_Lucene::create($index);
  }

  static public function getLuceneIndexFile()
  {
    return sfConfig::get('sf_data_dir').'/job.'.sfConfig::get('sf_environment').'.index';
  }

  public static function doDeleteAll($con = null)
  {
    if (file_exists($index = self::getLuceneIndexFile()))
    {
      sfToolkit::clearDirectory($index);
      rmdir($index);
    }

    return parent::doDeleteAll($con);
  }

  static public function getForLuceneQuery($query)
  {
    $hits = self::getLuceneIndex()->find($query);

    $pks = array();
    foreach ($hits as $hit)
    {
      $pks[] = $hit->pk;
    }

    $criteria = new Criteria();
    $criteria->add(self::ID, $pks, Criteria::IN);
    $criteria->setLimit(20);

    return self::doSelect(self::addActiveJobsCriteria($criteria));
  }
}
