<?php

class JobeetCategoryPeer extends BaseJobeetCategoryPeer {
  public static function getWithJobs()
  {
    $criteria = new Criteria();
    $criteria->addJoin(self::ID, JobeetJobPeer::CATEGORY_ID, Criteria::INNER_JOIN);
    $criteria->add(JobeetJobPeer::EXPIRES_AT, time(), Criteria::GREATER_THAN);
    $criteria->add(JobeetJobPeer::IS_ACTIVATED, true);
    $criteria->setDistinct();

    return self::doSelect($criteria);
  }
}
